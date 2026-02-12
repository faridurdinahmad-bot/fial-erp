<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarrantyController extends Controller
{
    public function index(): View
    {
        $warranties = Warranty::orderBy('name')->paginate(15);

        return view('warranties.index', compact('warranties'));
    }

    public function create(): View
    {
        return view('warranties.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ]);
        $validated['status'] = $request->boolean('status');

        Warranty::create($validated);

        return redirect()->route('warranties.index')->with('success', 'Warranty created.');
    }

    public function edit(Warranty $warranty): View
    {
        return view('warranties.edit', compact('warranty'));
    }

    public function update(Request $request, Warranty $warranty): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ]);
        $validated['status'] = $request->boolean('status');

        $warranty->update($validated);

        return redirect()->route('warranties.index')->with('success', 'Warranty updated.');
    }

    public function destroy(Warranty $warranty): RedirectResponse
    {
        $warranty->delete();

        return redirect()->route('warranties.index')->with('success', 'Warranty deleted.');
    }
}
