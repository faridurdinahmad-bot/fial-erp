@extends('layouts.dashboard')

@section('title', 'Edit Category')

@section('content')
    <div class="max-w-2xl rounded border border-neutral-200 bg-white p-6">
        <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
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
                        @if (old('parent_id', $category->parent_id) && $category->parent)
                            <option value="{{ $category->parent->id }}" selected>
                                {{ $category->parent->name }}
                            </option>
                        @endif
                    </select>
                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-neutral-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">{{ old('description', $category->description) }}</textarea>
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

                    @if ($category->image)
                        <div class="mt-2">
                            <p class="text-xs text-neutral-500 mb-1">Current image:</p>
                            <div style="width:60px;height:60px;overflow:hidden;border-radius:8px;">
                                <img src="{{ asset('storage/' . $category->thumbnail_path) }}" alt=""
                                    style="width:100%;height:100%;object-fit:cover;display:block;">
                            </div>
                        </div>
                    @endif
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-neutral-700">Sort order</label>
                        <input type="number" name="sort_order" id="sort_order"
                            value="{{ old('sort_order', $category->sort_order) }}"
                            class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-neutral-700">Meta title</label>
                        <input type="text" name="meta_title" id="meta_title"
                            value="{{ old('meta_title', $category->meta_title) }}"
                            class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">
                        @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-neutral-700">Meta description</label>
                    <textarea name="meta_description" id="meta_description" rows="2"
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">{{ old('meta_description', $category->meta_description) }}</textarea>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                            {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}
                            class="rounded border-neutral-300">
                        <label for="is_featured" class="text-sm text-neutral-700">Featured</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="show_in_menu" value="0">
                        <input type="checkbox" name="show_in_menu" id="show_in_menu" value="1"
                            {{ old('show_in_menu', $category->show_in_menu) ? 'checked' : '' }}
                            class="rounded border-neutral-300">
                        <label for="show_in_menu" class="text-sm text-neutral-700">Show in menu</label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <button type="submit"
                    class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Update</button>
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

            const searchUrl = '{{ route('categories.search', ['exclude_id' => $category->id]) }}';

            new TomSelect(parentSelect, {
                valueField: 'id',
                labelField: 'text',
                searchField: 'text',
                maxItems: 1,
                preload: false,
                loadThrottle: 300,
                load: function(query, callback) {
                    if (!query.length) return callback();
                    const url = searchUrl + '?q=' + encodeURIComponent(query);
                    fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(json => {
                            callback(json);
                        }).catch(() => {
                            callback();
                        });
                },
                render: {
                    option: function(data, escape) {
                        return `<div class="text-xs font-medium text-neutral-800">${escape(data.text)}</div>`;
                    },
                    item: function(data, escape) {
                        return `<div class="text-xs font-medium text-neutral-800">${escape(data.text)}</div>`;
                    }
                }
            });
        });
    </script>
@endpush

