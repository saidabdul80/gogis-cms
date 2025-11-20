<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PropertyType::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $propertyTypes = $query->orderBy('name', 'asc')->paginate(15);

        return Inertia::render('Admin/PropertyTypes/Index', [
            'propertyTypes' => $propertyTypes,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/PropertyTypes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:property_types,name',
            'description' => 'nullable|string',
        ]);

        PropertyType::create($validated);

        return redirect()->route('admin.property-types.index')
            ->with('success', 'Property type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PropertyType $propertyType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PropertyType $propertyType)
    {
        return Inertia::render('Admin/PropertyTypes/Edit', [
            'propertyType' => $propertyType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PropertyType $propertyType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:property_types,name,' . $propertyType->id,
            'description' => 'nullable|string',
        ]);

        $propertyType->update($validated);

        return redirect()->route('admin.property-types.index')
            ->with('success', 'Property type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PropertyType $propertyType)
    {
        // Check if property type is being used by any properties
        if ($propertyType->properties()->count() > 0) {
            return redirect()->route('admin.property-types.index')
                ->with('error', 'Cannot delete property type that is being used by properties.');
        }

        $propertyType->delete();

        return redirect()->route('admin.property-types.index')
            ->with('success', 'Property type deleted successfully.');
    }
}

