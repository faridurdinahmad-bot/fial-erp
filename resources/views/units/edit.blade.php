@extends('layouts.dashboard')

@section('title', 'Edit Unit')

@section('content')
    <div class="max-w-md rounded border border-neutral-200 bg-white p-6">
        <form action="{{ route('units.update', $unit) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $unit->name) }}" required
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="short_name" class="block text-sm font-medium text-neutral-700">Short name</label>
                    <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $unit->short_name) }}"
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm" placeholder="e.g. PCS, CTN">
                    @error('short_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-neutral-700">Type</label>
                    <select name="type" id="type"
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">
                        <option value="">Select type</option>
                        <option value="quantity" {{ old('type', $unit->type) === 'quantity' ? 'selected' : '' }}>Quantity</option>
                        <option value="weight" {{ old('type', $unit->type) === 'weight' ? 'selected' : '' }}>Weight</option>
                        <option value="volume" {{ old('type', $unit->type) === 'volume' ? 'selected' : '' }}>Volume</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="decimal_allowed" value="0">
                    <input type="checkbox" name="decimal_allowed" id="decimal_allowed" value="1" {{ old('decimal_allowed', $unit->decimal_allowed) ? 'checked' : '' }}
                        class="rounded border-neutral-300">
                    <label for="decimal_allowed" class="text-sm text-neutral-700">Allow decimal quantities</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" id="status" value="1" {{ old('status', $unit->status) ? 'checked' : '' }}
                        class="rounded border-neutral-300">
                    <label for="status" class="text-sm text-neutral-700">Active</label>
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                <button type="submit" class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Update</button>
                <a href="{{ route('units.index') }}" class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
