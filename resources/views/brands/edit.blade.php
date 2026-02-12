@extends('layouts.dashboard')

@section('title', 'Edit Brand')

@section('content')
    <div class="max-w-md rounded border border-neutral-200 bg-white p-6">
        <form action="{{ route('brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $brand->name) }}" required
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-neutral-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded border border-neutral-300 px-3 py-2 text-sm">{{ old('description', $brand->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="logo" class="block text-sm font-medium text-neutral-700">Logo (image, max 2MB)</label>
                    @if ($brand->logo)
                        <p class="mt-1 mb-1 text-xs text-neutral-500">Current: <img src="{{ asset('storage/' . $brand->logo) }}" alt="" class="inline h-8 w-auto rounded object-contain"></p>
                    @endif
                    <input type="file" name="logo" id="logo" accept="image/*"
                        class="mt-1 block w-full text-sm text-neutral-600 file:mr-2 file:rounded file:border-0 file:px-3 file:py-1.5 file:text-sm file:font-medium file:bg-neutral-100 file:text-neutral-700 hover:file:bg-neutral-200">
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" id="status" value="1" {{ old('status', $brand->status) ? 'checked' : '' }}
                        class="rounded border-neutral-300">
                    <label for="status" class="text-sm text-neutral-700">Active</label>
                </div>
            </div>
            <div class="mt-6 flex gap-2">
                <button type="submit" class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Update</button>
                <a href="{{ route('brands.index') }}" class="rounded border border-neutral-300 bg-white px-3 py-1.5 text-sm text-neutral-700 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
