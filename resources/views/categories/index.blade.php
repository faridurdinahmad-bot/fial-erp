@extends('layouts.dashboard')

@section('title', 'Categories')

@section('content')
    @if (session('success'))
        <p class="mb-4 text-sm text-green-600">{{ session('success') }}</p>
    @endif

    <div class="mb-4 flex items-center justify-between">
        <div class="space-y-2">
            <p class="text-sm text-neutral-600">{{ $categories->total() }} category(ies)</p>
            <div class="flex flex-wrap gap-2">
                <div>
                    <label for="filter_search" class="block text-xs font-medium text-neutral-600">Search</label>
                    <input type="text" id="filter_search" name="search" value="{{ request('search') }}"
                        placeholder="Search name or code"
                        class="mt-1 block w-44 rounded border border-neutral-300 px-2 py-1 text-xs">
                </div>
                <div>
                    <label for="filter_type" class="block text-xs font-medium text-neutral-600">Type</label>
                    <select id="filter_type" name="type"
                        class="mt-1 block w-32 rounded border border-neutral-300 px-2 py-1 text-xs">
                        <option value="">All</option>
                        <option value="main" {{ request('type') === 'main' ? 'selected' : '' }}>Main</option>
                        <option value="sub" {{ request('type') === 'sub' ? 'selected' : '' }}>Sub</option>
                    </select>
                </div>
                <div>
                    <label for="filter_parent_id" class="block text-xs font-medium text-neutral-600">Parent</label>
                    <select id="filter_parent_id" name="parent_id"
                        class="mt-1 block w-48 rounded border border-neutral-300 px-2 py-1 text-xs">
                        <option value="">Any</option>
                    </select>
                </div>
            </div>
        </div>
        <a href="{{ route('categories.create') }}"
            class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Add
            Category</a>
    </div>

    <div class="overflow-hidden rounded border border-neutral-200 bg-white">
        <table class="min-w-full divide-y divide-neutral-200 text-left text-sm">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-2 font-medium text-neutral-600">Image</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Code</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Name</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Parent</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Featured</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Menu</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Status</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Actions</th>
                </tr>
            </thead>
            <tbody id="categories-table-body" class="divide-y divide-neutral-200">
                @include('categories.partials.table', ['categories' => $categories])
            </tbody>
        </table>
    </div>

    <div id="categories-pagination">
        @include('categories.partials.pagination', ['categories' => $categories])
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        (function() {
            const searchInput = document.getElementById('filter_search');
            const typeSelect = document.getElementById('filter_type');
            const parentSelect = document.getElementById('filter_parent_id');
            const tableBody = document.getElementById('categories-table-body');
            const paginationWrapper = document.getElementById('categories-pagination');
            const filterUrl = '{{ route('categories.filter') }}';

            if (!tableBody || !paginationWrapper) return;

            function buildParams(page) {
                const params = new URLSearchParams();
                if (searchInput && searchInput.value.trim() !== '') {
                    params.set('search', searchInput.value.trim());
                }
                if (typeSelect && typeSelect.value) {
                    params.set('type', typeSelect.value);
                }
                if (parentSelect && parentSelect.value) {
                    params.set('parent_id', parentSelect.value);
                }
                if (page) {
                    params.set('page', page);
                }
                return params.toString();
            }

            function fetchCategories(page) {
                const query = buildParams(page);
                const url = filterUrl + (query ? ('?' + query) : '');

                fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (typeof data.rows === 'string') {
                            tableBody.innerHTML = data.rows;
                        }
                        if (typeof data.pagination === 'string') {
                            paginationWrapper.innerHTML = data.pagination;
                        }
                    })
                    .catch(() => {});
            }

            // Expose for TomSelect change handler
            window.__categoriesFilterFetch = fetchCategories;

            function debounce(fn, delay) {
                let t = null;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            if (searchInput) {
                searchInput.addEventListener('input', debounce(() => fetchCategories(1), 300));
            }
            if (typeSelect) {
                typeSelect.addEventListener('change', () => fetchCategories(1));
            }

            // Handle pagination via AJAX
            paginationWrapper.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;

                const nav = link.closest('nav[role="navigation"]');
                if (!nav) return;

                e.preventDefault();
                try {
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page') || 1;
                    fetchCategories(page);
                } catch {
                    fetchCategories(1);
                }
            });

            // Parent filter TomSelect
            if (parentSelect && typeof TomSelect !== 'undefined') {
                const searchUrl = '{{ route('categories.search') }}';

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
                    onChange: function() {
                        fetchCategories(1);
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
            }
        })();
    </script>
@endpush

