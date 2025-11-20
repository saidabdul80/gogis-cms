<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndividualCustomer;
use App\Models\CorporateCustomer;
use Illuminate\Http\Request;

class CustomerSearchController extends Controller
{
    /**
     * Search for customers (both individual and corporate)
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $customers = [];

        // Search Individual Customers
        $individualCustomers = IndividualCustomer::where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
                ->orWhere('middle_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->orWhere('nin', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone_number', 'like', "%{$query}%");
        })
        ->limit(10)
        ->get()
        ->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => trim("{$customer->first_name} {$customer->middle_name} {$customer->last_name}"),
                'type' => 'individual',
                'email' => $customer->email,
                'phone_number' => $customer->phone_number,
                'customer_id' => $customer->id,
                'customer_type' => 'App\\Models\\IndividualCustomer'
            ];
        });

        // Search Corporate Customers
        $corporateCustomers = CorporateCustomer::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('registration_number', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone_number', 'like', "%{$query}%");
        })
        ->limit(10)
        ->get()
        ->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'type' => 'corporate',
                'email' => $customer->email,
                'phone_number' => $customer->phone_number,
                'customer_id' => $customer->id,
                'customer_type' => 'App\\Models\\CorporateCustomer'
            ];
        });

        // Merge and sort by relevance
        $customers = $individualCustomers->concat($corporateCustomers)
            ->sortBy('name')
            ->values()
            ->take(20);

        return response()->json($customers);
    }
}

