<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $items = Category::latest()->paginate(10);
        return Inertia::render(ucfirst('categories').'/Index', [
            'categories' => $items
        ]);
    }

    public function create()
    {
        return Inertia::render(ucfirst('categories').'/Create', []);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        Category::create($data);
        return redirect()->route('categories.index')->with('message', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return Inertia::render(ucfirst('categories').'/Edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category->update($data);
        return redirect()->route('categories.index')->with('message', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('message', 'Category deleted successfully.');
    }
}