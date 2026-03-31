<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function index()
    {
        $items = Purchase::latest()->with(['supplier'])->paginate(10);
        return Inertia::render(ucfirst('purchases').'/Index', [
            'purchases' => $items
        ]);
    }

    public function create()
    {
        return Inertia::render(ucfirst('purchases').'/Create', ['suppliers' => \App\Models\Supplier::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|integer',
            'total_amount' => 'required|numeric',
            'purchase_date' => 'required|string|max:255'
        ]);

        Purchase::create($data);
        return redirect()->route('purchases.index')->with('message', 'Purchase created successfully.');
    }

    public function edit(Purchase $purchase)
    {
        return Inertia::render(ucfirst('purchases').'/Edit', ['purchase' => $purchase, 'suppliers' => \App\Models\Supplier::all()]);
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'supplier_id' => 'required|integer',
            'total_amount' => 'required|numeric',
            'purchase_date' => 'required|string|max:255'
        ]);

        $purchase->update($data);
        return redirect()->route('purchases.index')->with('message', 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('purchases.index')->with('message', 'Purchase deleted successfully.');
    }
}