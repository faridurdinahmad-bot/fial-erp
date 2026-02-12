@extends('layouts.dashboard')

@section('title', 'Add Warranty')

@section('content')
    <div class="max-w-md rounded border border-neutral-200 bg-white p-6">
        <form action="{{ route('warranties.store') }}" method="POST">
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
                <div class="flex items-center gap-2">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" id="status" value="1" {{ old('status', true) ? 'checked' : '' }}
                        class="rounded border-neutral-300">
                    <label for="status" class="text-sm text-neutral-700">Active</label>
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                <button type="submit" class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Save</button>
                <a href="{{ route('warranties.index') }}" class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
