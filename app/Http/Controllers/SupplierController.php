<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index()
    {
        $items = Supplier::latest()->paginate(10);
        return Inertia::render(ucfirst('suppliers').'/Index', [
            'suppliers' => $items
        ]);
    }

    public function create()
    {
        return Inertia::render(ucfirst('suppliers').'/Create', []);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string'
        ]);

        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('message', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::render(ucfirst('suppliers').'/Edit', ['supplier' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string'
        ]);

        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('message', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('message', 'Supplier deleted successfully.');
    }
}