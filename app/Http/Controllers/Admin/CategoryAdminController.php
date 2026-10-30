<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryAdminController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('display_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:100',
            'name_te' => 'nullable|string|max:100',
            'name_hi' => 'nullable|string|max:100',
            'description_en' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|url',
            'display_order' => 'nullable|integer',
        ]);

        $finalImageUrl = 'http://localhost:8000/storage/products/kaju-katli.jpg';
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $finalImageUrl = asset('storage/' . $path);
        } elseif (!empty($request->image_url)) {
            $finalImageUrl = $request->image_url;
        }

        $slug = Str::slug($request->name_en);
        if (Category::where('slug', $slug)->exists()) {
            $slug .= '-' . rand(10, 99);
        }

        Category::create([
            'id' => 'cat-' . Str::uuid(),
            'slug' => $slug,
            'name_en' => $request->name_en,
            'name_te' => $request->name_te ?: $request->name_en,
            'name_hi' => $request->name_hi ?: $request->name_en,
            'description_en' => $request->description_en ?: $request->name_en,
            'icon' => 'Sparkles',
            'image_url' => $finalImageUrl,
            'display_order' => $request->display_order ?: (Category::max('display_order') + 1),
            'is_active' => true,
        ]);

        return redirect()->route('admin.categories')->with('success', "Category '{$request->name_en}' created successfully!");
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:100',
            'name_te' => 'nullable|string|max:100',
            'name_hi' => 'nullable|string|max:100',
            'description_en' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|url',
            'display_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $category->image_url = asset('storage/' . $path);
        } elseif (!empty($request->image_url)) {
            $category->image_url = $request->image_url;
        }

        $category->update([
            'name_en' => $request->name_en,
            'name_te' => $request->name_te ?: $category->name_te,
            'name_hi' => $request->name_hi ?: $category->name_hi,
            'description_en' => $request->description_en ?: $category->description_en,
            'display_order' => $request->display_order ?? $category->display_order,
        ]);

        return redirect()->route('admin.categories')->with('success', "Category '{$category->name_en}' updated successfully!");
    }

    public function toggle(string $id): RedirectResponse
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'Enabled' : 'Disabled';
        return back()->with('success', "Category '{$category->name_en}' is now {$status}!");
    }

    public function destroy(string $id): RedirectResponse
    {
        $category = Category::withCount('products')->findOrFail($id);
        $name = $category->name_en;

        // Disassociate products from this category safely before deleting
        \App\Models\Product::where('category_id', $category->id)->update(['category_id' => null]);

        $category->delete();

        return redirect()->route('admin.categories')->with('success', "Category '{$name}' has been permanently deleted!");
    }
}
