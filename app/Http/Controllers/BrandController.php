<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::orderBy('name')->paginate(15);

        return view('brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app()->bound('currentCompany') ? app('currentCompany')->id : null;
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands')->where('company_id', $companyId),
            ],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'mimetypes:image/webp', 'max:2048'],
            'status' => ['sometimes', 'boolean'],
        ]);
        $validated['status'] = $request->boolean('status');

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->storeResizedLogo($request->file('logo'));
        }

        Brand::create($validated);

        return redirect()->route('brands.index')->with('success', 'Brand created.');
    }

    public function edit(Brand $brand): View
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands')->ignore($brand->id)->where('company_id', $brand->company_id),
            ],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'mimetypes:image/webp', 'max:2048'],
            'status' => ['sometimes', 'boolean'],
        ]);
        $validated['status'] = $request->boolean('status');

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $validated['logo'] = $this->storeResizedLogo($request->file('logo'));
        }

        $brand->update($validated);

        return redirect()->route('brands.index')->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }
        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand deleted.');
    }

    /**
     * Resize image to 500x500, encode as WebP, and store under storage/app/public/brands. Returns path for DB.
     */
    private function storeResizedLogo(\Illuminate\Http\UploadedFile $file): string
    {
        $filename = Str::uuid() . '.webp';
        $path = 'brands/' . $filename;
        Storage::disk('public')->makeDirectory('brands');
        $fullPath = Storage::disk('public')->path($path);

        Image::read($file->getRealPath())
            ->cover(500, 500)
            ->encodeByExtension('webp', quality: 90)
            ->save($fullPath);

        return $path;
    }
}
