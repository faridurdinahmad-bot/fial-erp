<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        $units = Unit::orderBy('name')->paginate(15);

        return view('units.index', compact('units'));
    }

    public function create(): View
    {
        return view('units.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'decimal_allowed' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
        ]);
        $validated['status'] = $request->boolean('status');
        $validated['decimal_allowed'] = $request->boolean('decimal_allowed');

        Unit::create($validated);

        return redirect()->route('units.index')->with('success', 'Unit created.');
    }

    public function edit(Unit $unit): View
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'decimal_allowed' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
        ]);
        $validated['status'] = $request->boolean('status');
        $validated['decimal_allowed'] = $request->boolean('decimal_allowed');

        $unit->update($validated);

        return redirect()->route('units.index')->with('success', 'Unit updated.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit deleted.');
    }
}
