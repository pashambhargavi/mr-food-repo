<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductAdminController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['variants', 'category'])->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('display_order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:150',
            'name_te' => 'nullable|string|max:150',
            'name_hi' => 'nullable|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'description_en' => 'required|string',
            'description_te' => 'nullable|string',
            'description_hi' => 'nullable|string',
            'dietary_type' => 'required|string',
            'is_veg' => 'boolean',
            'shelf_life_days' => 'required|integer|min:1',
            'storage_en' => 'nullable|string',
            'storage_te' => 'nullable|string',
            'storage_hi' => 'nullable|string',
            'ingredients_en' => 'nullable|string',
            'ingredients_te' => 'nullable|string',
            'ingredients_hi' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_bestseller' => 'boolean',
            'image_file' => 'nullable|image|max:5120', // Up to 5MB file upload
            'image_url' => 'nullable|url',
            // Variants array
            'variants' => 'required|array|min:1',
            'variants.*.weight_label' => 'required|string',
            'variants.*.weight_grams' => 'required|integer',
            'variants.*.price_rupees' => 'required|numeric|min:1',
            'variants.*.compare_at_rupees' => 'nullable|numeric',
            'variants.*.stock_quantity' => 'required|integer|min:0',
        ]);

        // Process Image: either uploaded file or URL
        $finalImageUrl = 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=800&q=80';
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            // Full public URL
            $finalImageUrl = asset('storage/' . $path);
        } elseif (!empty($request->image_url)) {
            $finalImageUrl = $request->image_url;
        }

        $slug = Str::slug($request->name_en);
        if (Product::where('slug', $slug)->exists()) {
            $slug .= '-' . rand(100, 999);
        }

        $product = Product::create([
            'id' => 'prod-' . Str::uuid(),
            'slug' => $slug,
            'category_id' => $request->category_id,
            'name_en' => $request->name_en,
            'name_te' => $request->name_te ?: $request->name_en,
            'name_hi' => $request->name_hi ?: $request->name_en,
            'description_en' => $request->description_en,
            'description_te' => $request->description_te ?: $request->description_en,
            'description_hi' => $request->description_hi ?: $request->description_en,
            'image_url' => $finalImageUrl,
            'dietary_type' => $request->dietary_type,
            'is_veg' => $request->boolean('is_veg', true),
            'shelf_life_days' => $request->shelf_life_days,
            'storage_en' => $request->storage_en ?: 'Store in an airtight container in a cool dry place.',
            'storage_te' => $request->storage_en ?: 'Store in an airtight container in a cool dry place.',
            'storage_hi' => $request->storage_en ?: 'Store in an airtight container in a cool dry place.',
            'ingredients_en' => $request->ingredients_en ?: 'Pure Cow Desi Ghee, Fine Sugar, Dry Fruits.',
            'ingredients_te' => $request->ingredients_en ?: 'Pure Cow Desi Ghee, Fine Sugar, Dry Fruits.',
            'ingredients_hi' => $request->ingredients_en ?: 'Pure Cow Desi Ghee, Fine Sugar, Dry Fruits.',
            'allergens' => $request->filled('allergens') ? array_map('trim', explode(',', $request->allergens)) : ['Dairy'],
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
            'is_bestseller' => $request->boolean('is_bestseller', false),
            'rating' => 4.9,
            'review_count' => rand(12, 180),
        ]);

        // Create variants
        foreach ($request->variants as $index => $v) {
            ProductVariant::create([
                'id' => 'var-' . Str::uuid(),
                'product_id' => $product->id,
                'weight_label' => $v['weight_label'],
                'weight_grams' => (int) $v['weight_grams'],
                'price_paise' => (int) round(((float) $v['price_rupees']) * 100),
                'compare_at_price_paise' => !empty($v['compare_at_rupees']) ? (int) round(((float) $v['compare_at_rupees']) * 100) : null,
                'stock_quantity' => (int) $v['stock_quantity'],
                'is_available' => (int) $v['stock_quantity'] > 0,
                'sku' => strtoupper(Str::slug($product->name_en)) . '-' . $v['weight_label'] . '-' . rand(10, 99),
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name_en}' created & published successfully!");
    }

    public function edit(string $id): View
    {
        $product = Product::with('variants')->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('display_order')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:150',
            'name_te' => 'nullable|string|max:150',
            'name_hi' => 'nullable|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'description_en' => 'required|string',
            'dietary_type' => 'required|string',
            'shelf_life_days' => 'required|integer|min:1',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $product->image_url = asset('storage/' . $path);
        } elseif (!empty($request->image_url)) {
            $product->image_url = $request->image_url;
        }

        $product->update([
            'name_en' => $request->name_en,
            'name_te' => $request->name_te ?: $request->name_en,
            'name_hi' => $request->name_hi ?: $request->name_en,
            'category_id' => $request->category_id,
            'description_en' => $request->description_en,
            'description_te' => $request->description_te ?: $product->description_te,
            'description_hi' => $request->description_hi ?: $product->description_hi,
            'dietary_type' => $request->dietary_type,
            'is_veg' => $request->boolean('is_veg', true),
            'shelf_life_days' => $request->shelf_life_days,
            'storage_en' => $request->storage_en ?: $product->storage_en,
            'ingredients_en' => $request->ingredients_en ?: $product->ingredients_en,
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
            'is_bestseller' => $request->boolean('is_bestseller', false),
        ]);

        // Update existing variants
        if ($request->has('variants')) {
            foreach ($request->variants as $varId => $vData) {
                $variant = ProductVariant::where('id', $varId)->where('product_id', $product->id)->first();
                if ($variant) {
                    $stock = (int) $vData['stock_quantity'];
                    $variant->update([
                        'weight_label' => $vData['weight_label'],
                        'price_paise' => (int) round(((float) $vData['price_rupees']) * 100),
                        'stock_quantity' => $stock,
                        'is_available' => $stock > 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', "Sweet '{$product->name_en}' updated successfully!");
    }

    public function destroy(string $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $name = $product->name_en;
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', "Product '{$name}' deleted successfully!");
    }

    public function togglePublish(string $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'Published' : 'Unpublished (Draft)';
        return back()->with('success', "{$product->name_en} is now {$status}!");
    }
}
