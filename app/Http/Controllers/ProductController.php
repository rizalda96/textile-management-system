<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $items = Product::latest()->with(['category'])->paginate(10);
        return Inertia::render(ucfirst('products').'/Index', [
            'products' => $items
        ]);
    }

    public function create()
    {
        return Inertia::render(ucfirst('products').'/Create', ['categories' => \App\Models\Category::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer'
        ]);

        Product::create($data);
        return redirect()->route('products.index')->with('message', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return Inertia::render(ucfirst('products').'/Edit', ['product' => $product, 'categories' => \App\Models\Category::all()]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer'
        ]);

        $product->update($data);
        return redirect()->route('products.index')->with('message', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('message', 'Product deleted successfully.');
    }
}