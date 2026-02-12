<div class="mt-6 rounded border border-neutral-200 bg-white p-4">
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-medium text-neutral-800">Package types</h3>
        <button type="button" id="add-package-row"
            class="rounded border border-neutral-300 bg-white px-2 py-1 text-xs text-neutral-700 hover:bg-neutral-50">
            + Add package
        </button>
    </div>

    <table class="w-full table-fixed text-left text-xs">
        <thead>
            <tr class="border-b border-neutral-200 text-neutral-600">
                <th class="w-1/2 px-2 py-1 font-medium">Package name</th>
                <th class="w-1/4 px-2 py-1 font-medium">Quantity (base unit)</th>
                <th class="w-1/6 px-2 py-1 font-medium">Active</th>
                <th class="w-1/12 px-2 py-1"></th>
            </tr>
        </thead>
        <tbody id="package-rows" class="align-top">
            @php
                $existingPackages = isset($product) ? $product->packages : collect();
            @endphp

            @forelse ($existingPackages as $index => $package)
                <tr class="border-b border-neutral-100">
                    <td class="px-2 py-1">
                        <input type="text" name="packages[{{ $index }}][name]"
                            value="{{ old('packages.' . $index . '.name', $package->name) }}"
                            class="mt-1 block w-full rounded border border-neutral-300 px-2 py-1 text-xs"
                            placeholder="e.g. Carton 10">
                    </td>
                    <td class="px-2 py-1">
                        <input type="number" name="packages[{{ $index }}][quantity]" step="0.01" min="0"
                            value="{{ old('packages.' . $index . '.quantity', $package->quantity) }}"
                            class="mt-1 block w-full rounded border border-neutral-300 px-2 py-1 text-xs">
                    </td>
                    <td class="px-2 py-1">
                        <input type="hidden" name="packages[{{ $index }}][status]" value="0">
                        <input type="checkbox" name="packages[{{ $index }}][status]" value="1"
                            {{ old('packages.' . $index . '.status', $package->status) ? 'checked' : '' }}
                            class="rounded border-neutral-300">
                    </td>
                    <td class="px-2 py-1 text-right">
                        <button type="button"
                            class="remove-package-row text-xs text-red-600 hover:text-red-800">Remove</button>
                    </td>
                </tr>
            @empty
                <tr class="border-b border-neutral-100">
                    <td class="px-2 py-1">
                        <input type="text" name="packages[0][name]"
                            value="{{ old('packages.0.name') }}"
                            class="mt-1 block w-full rounded border border-neutral-300 px-2 py-1 text-xs"
                            placeholder="e.g. Carton 10">
                    </td>
                    <td class="px-2 py-1">
                        <input type="number" name="packages[0][quantity]" step="0.01" min="0"
                            value="{{ old('packages.0.quantity') }}"
                            class="mt-1 block w-full rounded border border-neutral-300 px-2 py-1 text-xs">
                    </td>
                    <td class="px-2 py-1">
                        <input type="hidden" name="packages[0][status]" value="0">
                        <input type="checkbox" name="packages[0][status]" value="1"
                            {{ old('packages.0.status', true) ? 'checked' : '' }}
                            class="rounded border-neutral-300">
                    </td>
                    <td class="px-2 py-1 text-right">
                        <button type="button"
                            class="remove-package-row text-xs text-red-600 hover:text-red-800">Remove</button>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="mt-2 text-xs text-neutral-500">
        Packages define multiples of the base unit (stock is still stored in the base unit only).
    </p>
</div>

@push('scripts')
    <script>
        (function () {
            const container = document.getElementById('package-rows');
            const addBtn = document.getElementById('add-package-row');
            if (!container || !addBtn) return;

            let index = container.querySelectorAll('tr').length;

            function addRow() {
                const row = document.createElement('tr');
                row.className = 'border-b border-neutral-100';
                row.innerHTML = `
                    <td class="px-2 py-1">
                        <input type="text" name="packages[${index}][name]"
                            class="mt-1 block w-full rounded border border-neutral-300 px-2 py-1 text-xs"
                            placeholder="e.g. Carton 10">
                    </td>
                    <td class="px-2 py-1">
                        <input type="number" name="packages[${index}][quantity]" step="0.01" min="0"
                            class="mt-1 block w-full rounded border border-neutral-300 px-2 py-1 text-xs">
                    </td>
                    <td class="px-2 py-1">
                        <input type="hidden" name="packages[${index}][status]" value="0">
                        <input type="checkbox" name="packages[${index}][status]" value="1" checked
                            class="rounded border-neutral-300">
                    </td>
                    <td class="px-2 py-1 text-right">
                        <button type="button"
                            class="remove-package-row text-xs text-red-600 hover:text-red-800">Remove</button>
                    </td>
                `;
                container.appendChild(row);
                index++;
            }

            function handleRemoveClick(e) {
                const btn = e.target.closest('.remove-package-row');
                if (!btn) return;
                const row = btn.closest('tr');
                if (row && container.children.length > 1) {
                    row.remove();
                }
            }

            addBtn.addEventListener('click', addRow);
            container.addEventListener('click', handleRemoveClick);
        })();
    </script>
@endpush

