<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('parent')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('categories.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app()->bound('currentCompany') ? app('currentCompany')->id : null;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image' => [
                'nullable',
                'mimetypes:image/webp',
                'dimensions:min_width=800,min_height=800',
            ],
            'sort_order' => ['nullable', 'integer'],
            'is_featured' => ['sometimes', 'boolean'],
            'show_in_menu' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);

        $validated['status'] = true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['show_in_menu'] = $request->boolean('show_in_menu', true);

        if ($request->hasFile('image')) {
            $paths = $this->storeCategoryImage($request->file('image'));
            $validated['image'] = $paths['main'];
        }

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image' => [
                'nullable',
                'mimetypes:image/webp',
                'dimensions:min_width=800,min_height=800',
            ],
            'sort_order' => ['nullable', 'integer'],
            'is_featured' => ['sometimes', 'boolean'],
            'show_in_menu' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['show_in_menu'] = $request->boolean('show_in_menu', true);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
                if ($category->thumbnail_path) {
                    Storage::disk('public')->delete($category->thumbnail_path);
                }
            }

            $paths = $this->storeCategoryImage($request->file('image'));
            $validated['image'] = $paths['main'];
        }

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
            if ($category->thumbnail_path) {
                Storage::disk('public')->delete($category->thumbnail_path);
            }
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }

    /**
     * Search categories for parent selection (AJAX).
     */
    public function search(Request $request)
    {
        $query = Category::query();

        // Tenant scope: Category extends BaseTenantModel with CompanyScope,
        // but we also explicitly restrict by the authenticated user's company for safety.
        if ($user = $request->user()) {
            $query->where('company_id', $user->company_id);
        }

        // Exclude current category when editing (avoid self-parenting)
        if ($excludeId = $request->query('exclude_id')) {
            $query->where('id', '!=', $excludeId);
        }

        // If q is provided, search by name or code; otherwise just take first 20
        if ($term = (string) $request->query('q', '')) {
            $like = '%' . $term . '%';
            $query->where(function ($q) use ($like): void {
                $q->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like);
            });
        }

        $categories = $query
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'code']);

        $results = $categories->map(function (Category $category): array {
            return [
                'id' => $category->id,
                'text' => trim(($category->code ? $category->code . ' - ' : '') . $category->name),
            ];
        })->values();

        \Log::info('Category search', [
            'q' => $request->query('q'),
            'exclude_id' => $request->query('exclude_id'),
            'count' => $results->count(),
        ]);

        return response()->json($results);
    }

    /**
     * Filter categories for index (AJAX).
     */
    public function filter(Request $request)
    {
        $query = Category::with('parent');

        // Type filter
        $type = $request->query('type');
        if ($type === 'main') {
            $query->whereNull('parent_id');
        } elseif ($type === 'sub') {
            $query->whereNotNull('parent_id');
        }

        // Parent filter
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->query('parent_id'));
        }

        // Search by name or code
        if ($search = $request->query('search')) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like): void {
                $q->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like);
            });
        }

        $categories = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        $rows = view('categories.partials.table', compact('categories'))->render();
        $pagination = view('categories.partials.pagination', compact('categories'))->render();

        return response()->json([
            'rows' => $rows,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Store main and thumbnail category images as WebP.
     *
     * @return array{main: string, thumb: string}
     */
    private function storeCategoryImage(\Illuminate\Http\UploadedFile $file): array
    {
        $filenameBase = (string) Str::uuid();
        $mainPath = 'categories/' . $filenameBase . '.webp';
        $thumbPath = 'categories/' . $filenameBase . '_thumb.webp';

        Storage::disk('public')->makeDirectory('categories');

        $mainFull = Storage::disk('public')->path($mainPath);
        $thumbFull = Storage::disk('public')->path($thumbPath);

        $image = Image::read($file->getRealPath());

        // Main 800x800
        $image->clone()
            ->cover(800, 800)
            ->encodeByExtension('webp', quality: 90)
            ->save($mainFull);

        // Thumbnail 150x150
        $image->clone()
            ->cover(150, 150)
            ->encodeByExtension('webp', quality: 90)
            ->save($thumbFull);

        return [
            'main' => $mainPath,
            'thumb' => $thumbPath,
        ];
    }
}

