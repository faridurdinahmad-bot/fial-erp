@forelse ($categories as $category)
    <tr>
        <td class="px-4 py-2">
            @if ($category->image)
                <div style="width:60px;height:60px;overflow:hidden;border-radius:8px;">
                    <img src="{{ asset('storage/' . $category->thumbnail_path) }}" alt=""
                        style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
            @else
                <div
                    style="width:60px;height:60px;overflow:hidden;border-radius:8px;display:flex;align-items:center;justify-content:center;border:1px dashed #d4d4d4;background:#fafafa;font-size:0.75rem;color:#a3a3a3;">
                    No image
                </div>
            @endif
        </td>
        <td class="px-4 py-2 font-mono text-neutral-700">{{ $category->code }}</td>
        <td class="px-4 py-2">{{ $category->name }}</td>
        <td class="px-4 py-2 text-neutral-600">
            {{ $category->parent?->name ?? '—' }}
        </td>
        <td class="px-4 py-2">{{ $category->is_featured ? 'Yes' : 'No' }}</td>
        <td class="px-4 py-2">{{ $category->show_in_menu ? 'Yes' : 'No' }}</td>
        <td class="px-4 py-2">{{ $category->status ? 'Active' : 'Inactive' }}</td>
        <td class="px-4 py-2">
            <a href="{{ route('categories.edit', $category) }}"
                class="text-neutral-600 underline hover:text-neutral-800">Edit</a>
            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline ml-2"
                onsubmit="return confirm('Delete this category?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 underline hover:text-red-800">Delete</button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-4 py-4 text-neutral-500">No categories yet.</td>
    </tr>
@endforelse

