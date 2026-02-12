@extends('layouts.dashboard')

@section('title', 'Add Category')

@section('content')
    <div class="max-w-2xl rounded border border-neutral-200 bg-white p-6">
        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="parent_id" class="block text-sm font-medium text-neutral-700">Parent category</label>
                    <select name="parent_id" id="parent_id"
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">
                        <option value="">None</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->code }} - {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-neutral-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-neutral-700">Image (WebP, min 800x800)</label>
                    <input type="file" name="image" id="image"
                        accept="image/webp"
                        class="mt-1 block w-full text-sm text-neutral-700">
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-neutral-700">Sort order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                            class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-neutral-700">Meta title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                            class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">
                        @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-neutral-700">Meta description</label>
                    <textarea name="meta_description" id="meta_description" rows="2"
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">{{ old('meta_description') }}</textarea>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                            {{ old('is_featured') ? 'checked' : '' }}
                            class="rounded border-neutral-300">
                        <label for="is_featured" class="text-sm text-neutral-700">Featured</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="show_in_menu" value="0">
                        <input type="checkbox" name="show_in_menu" id="show_in_menu" value="1"
                            {{ old('show_in_menu', true) ? 'checked' : '' }}
                            class="rounded border-neutral-300">
                        <label for="show_in_menu" class="text-sm text-neutral-700">Show in menu</label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <button type="submit"
                    class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Save</button>
                <a href="{{ route('categories.index') }}"
                    class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('alpine:init', function () {
            const parentSelect = document.getElementById('parent_id');
            if (!parentSelect || typeof TomSelect === 'undefined') return;

            new TomSelect('#parent_id', {
                create: false,
                sortField: { field: 'text', direction: 'asc' },
                openOnFocus: true
            });
        });
    </script>
@endpush

