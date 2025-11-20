<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorporateCustomer;
use App\Models\IndividualCustomer;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Property::with(['customer', 'propertyType']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number_value', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by number type
        if ($request->has('number_type') && $request->number_type) {
            $query->where('number_type', $request->number_type);
        }

        // Filter by property type
        if ($request->has('property_type_id') && $request->property_type_id) {
            $query->where('property_type_id', $request->property_type_id);
        }

        $properties = $query->latest()->paginate(15);

        return Inertia::render('Admin/Properties/Index', [
            'properties' => $properties,
            'propertyTypes' => PropertyType::orderBy('name')->get(),
            'filters' => $request->only(['search', 'number_type', 'property_type_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Properties/Create', [
            'propertyTypes' => PropertyType::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_type_id' => 'required|exists:property_types,id',
            'address' => 'required|string',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
            'description' => 'nullable|string',
            'number_type' => 'required|in:GSL,GM_Number',
            'number_value' => 'required|string|unique:properties,number_value',
            'start_date' => 'nullable|date',
            'payment_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'amount' => 'nullable|numeric|min:0',
            'customer_type' => 'nullable|in:'.IndividualCustomer::class.','.CorporateCustomer::class,
            'customer_id' => 'required_if:customer_type,'.IndividualCustomer::class.','.CorporateCustomer::class,
        ]);

        Property::create($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        return Inertia::render('Admin/Properties/Show', [
            'property' => $property,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        $property->load(['customer', 'propertyType']);

        return Inertia::render('Admin/Properties/Edit', [
            'property' => $property,
            'propertyTypes' => PropertyType::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'property_type_id' => 'required|exists:property_types,id',
            'address' => 'required|string',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
            'description' => 'nullable|string',
            'number_type' => 'required|in:GSL,GM_Number',
            'number_value' => 'required|string|unique:properties,number_value,' . $property->id,
            'start_date' => 'nullable|date',
            'payment_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'amount' => 'nullable|numeric|min:0',
            'customer_type' => 'nullable|in:'.IndividualCustomer::class.','.CorporateCustomer::class,
            'customer_id' => 'required_if:customer_type,'.IndividualCustomer::class.','.CorporateCustomer::class,
        ]);

        $property->update($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Property deleted successfully.');
    }
}
