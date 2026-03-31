<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $items = Customer::latest()->paginate(10);
        return Inertia::render(ucfirst('customers').'/Index', [
            'customers' => $items
        ]);
    }

    public function create()
    {
        return Inertia::render(ucfirst('customers').'/Create', []);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string'
        ]);

        Customer::create($data);
        return redirect()->route('customers.index')->with('message', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return Inertia::render(ucfirst('customers').'/Edit', ['customer' => $customer]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string'
        ]);

        $customer->update($data);
        return redirect()->route('customers.index')->with('message', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('message', 'Customer deleted successfully.');
    }
}