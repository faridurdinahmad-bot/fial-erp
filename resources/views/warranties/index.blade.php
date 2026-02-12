@extends('layouts.dashboard')

@section('title', 'Warranties')

@section('content')
    @if (session('success'))
        <p class="mb-4 text-sm text-green-600">{{ session('success') }}</p>
    @endif

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-neutral-600">{{ $warranties->total() }} warranty(ies)</p>
        <a href="{{ route('warranties.create') }}" class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Add Warranty</a>
    </div>

    <div class="overflow-hidden rounded border border-neutral-200 bg-white">
        <table class="min-w-full divide-y divide-neutral-200 text-left text-sm">
            <thead class="bg-neutral-50">
                <tr>
                    <th class="px-4 py-2 font-medium text-neutral-600">Name</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Status</th>
                    <th class="px-4 py-2 font-medium text-neutral-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
                @forelse ($warranties as $warranty)
                    <tr>
                        <td class="px-4 py-2">{{ $warranty->name }}</td>
                        <td class="px-4 py-2">{{ $warranty->status ? 'Active' : 'Inactive' }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('warranties.edit', $warranty) }}" class="text-neutral-600 underline hover:text-neutral-800">Edit</a>
                            <form action="{{ route('warranties.destroy', $warranty) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this warranty?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 underline hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-neutral-500">No warranties yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($warranties->hasPages())
        <div class="mt-4">{{ $warranties->links() }}</div>
    @endif
@endsection
