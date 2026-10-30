<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['variants', 'category'])
            ->where('is_active', true);

        // Category filter
        if ($request->filled('cat') && $request->cat !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->cat);
            });
        }

        // Dietary filter
        if ($request->filled('diet') && $request->diet !== 'all') {
            $query->where('dietary_type', $request->diet);
        }

        // Search query across 3 languages
        if ($request->filled('q')) {
            $term = '%' . strtolower($request->q) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(name_en) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(name_te) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(name_hi) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(description_en) LIKE ?', [$term]);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'popular');
        if ($sort === 'rating') {
            $query->orderByDesc('rating');
        } elseif ($sort === 'bestseller') {
            $query->orderByDesc('is_bestseller');
        } else {
            $query->orderByDesc('is_bestseller')->orderByDesc('rating');
        }

        $products = $query->get();

        // If sorting by price, sort in memory using variant price
        if ($sort === 'price-low') {
            $products = $products->sortBy(function ($p) {
                return $p->variants->min('price_paise');
            })->values();
        } elseif ($sort === 'price-high') {
            $products = $products->sortByDesc(function ($p) {
                return $p->variants->min('price_paise');
            })->values();
        }

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'data' => $products,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['variants', 'category'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        // Fetch related products in same category
        $related = Product::with('variants')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $product,
            'related' => $related,
        ]);
    }
}
