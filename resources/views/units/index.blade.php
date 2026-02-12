@extends('layouts.dashboard')

@section('title', 'Units')

@section('content')
    @if (session('success'))
        <p class="mb-4 text-sm text-green-600">{{ session('success') }}</p>
    @endif

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-neutral-600">{{ $units->total() }} unit(s)</p>
        <a href="{{ route('units.create') }}" class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Add Unit</a>
    </div>

    <div class="overflow-hidden rounded border border-neutral-200 bg-white">
        <table class="min-w-full divide-y divide-neutral-200 text-left text-sm">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-2 font-medium text-neutral-600">Name</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Short</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Type</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Decimal</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Status</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @forelse ($units as $unit)
                    <tr>
                        <td class="px-4 py-2">{{ $unit->name }}</td>
                        <td class="px-4 py-2 font-mono text-neutral-700">{{ $unit->short_name ?? '—' }}</td>
                        <td class="px-4 py-2 capitalize text-neutral-700">{{ $unit->type ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $unit->decimal_allowed ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-2">{{ $unit->status ? 'Active' : 'Inactive' }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('units.edit', $unit) }}" class="text-neutral-600 underline hover:text-neutral-800">Edit</a>
                            <form action="{{ route('units.destroy', $unit) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this unit?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 underline hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-neutral-500">No units yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($units->hasPages())
        <div class="mt-4">{{ $units->links() }}</div>
    @endif
@endsection
