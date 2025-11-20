<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RevenueHead;
use App\Models\RevenueSubHead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RevenueHeadController extends Controller
{
    public function index()
    {
        $revenueHeads = RevenueHead::with('subHeads')->latest()->paginate(15);

        return Inertia::render('Admin/RevenueHeads/Index', [
            'revenueHeads' => $revenueHeads,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/RevenueHeads/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:revenue_heads,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        RevenueHead::create($validated);

        return redirect()->route('admin.revenue-heads.index')->with('success', 'Revenue Head created successfully!');
    }

    public function edit(RevenueHead $revenueHead)
    {
        $revenueHead->load('subHeads');

        return Inertia::render('Admin/RevenueHeads/Edit', [
            'revenueHead' => $revenueHead,
        ]);
    }

    public function update(Request $request, RevenueHead $revenueHead)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:revenue_heads,code,' . $revenueHead->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $revenueHead->update($validated);

        return redirect()->route('admin.revenue-heads.index')->with('success', 'Revenue Head updated successfully!');
    }

    public function destroy(RevenueHead $revenueHead)
    {
        $revenueHead->delete();

        return redirect()->route('admin.revenue-heads.index')->with('success', 'Revenue Head deleted successfully!');
    }
}

