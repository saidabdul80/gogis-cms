<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndividualCustomer;
use App\Models\Lga;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndividualCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = IndividualCustomer::with('lga');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('nin', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by gender
        if ($request->has('gender') && $request->gender) {
            $query->where('gender', $request->gender);
        }

        // Filter by LGA
        if ($request->has('lga_id') && $request->lga_id) {
            $query->where('lga_id', $request->lga_id);
        }

        $customers = $query->latest()->paginate(15);
        $lgas = Lga::orderBy('name')->get();

        return Inertia::render('Admin/IndividualCustomers/Index', [
            'customers' => $customers,
            'lgas' => $lgas,
            'filters' => $request->only(['search', 'gender', 'lga_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lgas = Lga::orderBy('name')->get();

        return Inertia::render('Admin/IndividualCustomers/Create', [
            'lgas' => $lgas,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'nin' => 'nullable|string|unique:individual_customers,nin',
            'gender' => 'nullable|in:Male,Female',
            'dob' => 'nullable|date',
            'lga_id' => 'nullable|exists:lgas,id',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $customer = IndividualCustomer::create($validated);

        // If this is an AJAX request (from CustomerSelector), return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => trim("{$customer->first_name} {$customer->middle_name} {$customer->last_name}"),
                    'type' => 'individual',
                    'email' => $customer->email,
                    'phone_number' => $customer->phone_number,
                    'customer_id' => $customer->id,
                    'customer_type' => 'App\\Models\\IndividualCustomer'
                ]
            ]);
        }

        return redirect()->route('admin.individual-customers.index')
            ->with('success', 'Individual customer created successfully.')
            ->with('customerId', $customer->id)
            ->with('customerType', 'individual');
    }

    /**
     * Display the specified resource.
     */
    public function show(IndividualCustomer $individualCustomer)
    {
        $individualCustomer->load(['lga']);

        // Get properties with their types and latest invoice
        $properties = $individualCustomer->properties()
            ->with(['propertyType', 'customer'])
            ->get()
            ->map(function ($property) {
                // Get the latest invoice for this specific property
                $latestInvoice = \App\Models\Invoice::where('property_id', $property->id)
                    ->latest('due_date')
                    ->first();

                return [
                    'id' => $property->id,
                    'number_type' => $property->number_type,
                    'number_value' => $property->number_value,
                    'address' => $property->address,
                    'property_type' => $property->propertyType,
                    'amount' => $property->amount,
                    'payment_frequency' => $property->payment_frequency,
                    'start_date' => $property->start_date,
                    'last_invoice_due_date' => $latestInvoice ? $latestInvoice->due_date : null,
                    'last_invoice_status' => $latestInvoice ? $latestInvoice->payment_status : null,
                ];
            });

        // Calculate total properties
        $totalProperties = $properties->count();

        // Calculate overall total paid amount
        $totalPaidAmount = $individualCustomer->payments()
            ->where('status', 'SUCCESS')
            ->sum('paid_amount');

        // Group properties by property type
        $propertiesByType = $properties->groupBy('property_type.name')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
            ];
        });

        return Inertia::render('Admin/IndividualCustomers/Show', [
            'customer' => $individualCustomer,
            'properties' => $properties,
            'analytics' => [
                'total_properties' => $totalProperties,
                'total_paid_amount' => $totalPaidAmount,
                'properties_by_type' => $propertiesByType,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IndividualCustomer $individualCustomer)
    {
        $lgas = Lga::orderBy('name')->get();

        return Inertia::render('Admin/IndividualCustomers/Edit', [
            'customer' => $individualCustomer,
            'lgas' => $lgas,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IndividualCustomer $individualCustomer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'nin' => 'nullable|string|unique:individual_customers,nin,' . $individualCustomer->id,
            'gender' => 'nullable|in:Male,Female',
            'dob' => 'nullable|date',
            'lga_id' => 'nullable|exists:lgas,id',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $individualCustomer->update($validated);

        return redirect()->route('admin.individual-customers.index')
            ->with('success', 'Individual customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IndividualCustomer $individualCustomer)
    {
        $individualCustomer->delete();

        return redirect()->route('admin.individual-customers.index')
            ->with('success', 'Individual customer deleted successfully.');
    }

    /**
     * Get payment history for a specific property
     */
    public function propertyPayments(Request $request, IndividualCustomer $individualCustomer)
    {
        $propertyId = $request->input('property_id');

        if (!$propertyId) {
            return response()->json(['error' => 'Property ID is required'], 400);
        }

        // Verify the property belongs to this customer
        $property = $individualCustomer->properties()->find($propertyId);

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        // Get last 5 payments for this specific property
        $payments = \App\Models\Payment::where('property_id', $propertyId)
            ->where('customer_id', $individualCustomer->id)
            ->where('customer_type', get_class($individualCustomer))
            ->where('status', 'SUCCESS')
            ->latest('payment_date')
            ->take(5)
            ->get()
            ->map(function ($payment) use ($property) {
                return [
                    'id' => $payment->id,
                    'reference' => $payment->reference,
                    'amount' => $payment->paid_amount,
                    'payment_date' => $payment->payment_date,
                    'frequency' => $property->payment_frequency,
                ];
            });

        return response()->json($payments);
    }
}
