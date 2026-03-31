<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index()
    {
        $items = Sale::latest()->with(['customer'])->paginate(10);
        return Inertia::render(ucfirst('sales').'/Index', [
            'sales' => $items
        ]);
    }

    public function create()
    {
        return Inertia::render(ucfirst('sales').'/Create', ['customers' => \App\Models\Customer::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|integer',
            'total_amount' => 'required|numeric',
            'sale_date' => 'required|string|max:255'
        ]);

        Sale::create($data);
        return redirect()->route('sales.index')->with('message', 'Sale created successfully.');
    }

    public function edit(Sale $sale)
    {
        return Inertia::render(ucfirst('sales').'/Edit', ['sale' => $sale, 'customers' => \App\Models\Customer::all()]);
    }

    public function update(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'customer_id' => 'required|integer',
            'total_amount' => 'required|numeric',
            'sale_date' => 'required|string|max:255'
        ]);

        $sale->update($data);
        return redirect()->route('sales.index')->with('message', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index')->with('message', 'Sale deleted successfully.');
    }
}