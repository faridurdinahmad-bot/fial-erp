@extends('layouts.dashboard')

@section('title', 'Brands')

@section('content')
    @if (session('success'))
        <p class="mb-4 text-sm text-green-600">{{ session('success') }}</p>
    @endif

    <div class="mb-4 flex items-center justify-between">
        <div>
            <p class="text-sm text-neutral-600">{{ $brands->total() }} brand(s)</p>
            <p class="mt-1 text-xs text-neutral-400">If logos are not visible, run <code>php artisan storage:link</code>.</p>
        </div>
        <a href="{{ route('brands.create') }}" class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Add Brand</a>
    </div>

    <div class="overflow-hidden rounded border border-neutral-200 bg-white">
        <table class="min-w-full divide-y divide-neutral-200 text-left text-sm">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-2 font-medium text-neutral-600">Logo</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Code</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Name</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Description</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Status</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @forelse ($brands as $brand)
                    <tr>
                        <td class="px-4 py-2">
                            @if ($brand->logo)
                                <div style="width:60px;height:60px;overflow:hidden;border-radius:8px;">
                                    <img src="{{ asset('storage/'.$brand->logo) }}" alt=""
                                         style="width:100%;height:100%;object-fit:cover;display:block;">
                                </div>
                            @else
                                <div style="width:60px;height:60px;overflow:hidden;border-radius:8px;display:flex;align-items:center;justify-content:center;border:1px dashed #d4d4d4;background:#fafafa;font-size:0.75rem;color:#a3a3a3;">
                                    No logo
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 font-mono text-neutral-700">{{ $brand->brand_code }}</td>
                        <td class="px-4 py-2">{{ $brand->name }}</td>
                        <td class="max-w-xs px-4 py-2 text-neutral-600">
                            {{ $brand->description ? \Illuminate\Support\Str::limit($brand->description, 60) : '—' }}
                        </td>
                        <td class="px-4 py-2">{{ $brand->status ? 'Active' : 'Inactive' }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('brands.edit', $brand) }}" class="text-neutral-600 underline hover:text-neutral-800">Edit</a>
                            <form action="{{ route('brands.destroy', $brand) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this brand?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 underline hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-neutral-500">No brands yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($brands->hasPages())
        <div class="mt-4">{{ $brands->links() }}</div>
    @endif
@endsection
