<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * List products with filtering, search, and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->active()
            ->with(['category', 'images', 'variants']);

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->query('category'));
            });
        }

        // Search by keyword
        if ($request->filled('search')) {
            $keyword = trim($request->query('search'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'ilike', "%{$keyword}%")
                  ->orWhere('description', 'ilike', "%{$keyword}%");
            });
        }

        // Sorting
        $sort = $request->query('sort', 'popular');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
            default:
                $query->orderByDesc('is_featured')->orderBy('name', 'asc');
                break;
        }

        $perPage = (int) $request->query('per_page', 12);
        $products = $query->paginate($perPage);

        // Map data to ensure clean API structure with calculated price attributes
        $data = $products->through(function (Product $product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'description' => $product->description,
                'price' => (float) $product->price,
                'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
                'effective_price' => (float) $product->effective_price,
                'has_discount' => $product->has_discount,
                'discount_percentage' => $product->discount_percentage,
                'stock' => $product->stock,
                'weight_grams' => (float) $product->weight_grams,
                'main_image' => $product->main_image,
                'is_featured' => $product->is_featured,
                'lead_time_days' => $product->lead_time_days,
                'allows_greeting_card' => $product->allows_greeting_card,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'images' => $product->images->map(fn($img) => [
                    'id' => $img->id,
                    'image_path' => $img->image_path,
                    'is_primary' => $img->is_primary,
                ]),
                'variants' => $product->variants->map(fn($v) => [
                    'id' => $v->id,
                    'variant_name' => $v->variant_name,
                    'variant_value' => $v->variant_value,
                    'additional_price' => (float) $v->additional_price,
                    'effective_variant_price' => (float) ($product->effective_price + $v->additional_price),
                    'stock' => $v->stock,
                ]),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Show single product details.
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::active()
            ->with(['category', 'images', 'variants'])
            ->where('slug', $slug)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->limit(4)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => (float) $p->price,
                'effective_price' => (float) $p->effective_price,
                'main_image' => $p->main_image,
                'stock' => $p->stock,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'description' => $product->description,
                'price' => (float) $product->price,
                'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
                'effective_price' => (float) $product->effective_price,
                'has_discount' => $product->has_discount,
                'discount_percentage' => $product->discount_percentage,
                'stock' => $product->stock,
                'weight_grams' => (float) $product->weight_grams,
                'main_image' => $product->main_image,
                'is_featured' => $product->is_featured,
                'lead_time_days' => $product->lead_time_days,
                'allows_greeting_card' => $product->allows_greeting_card,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                    'description' => $product->category->description,
                ] : null,
                'images' => $product->images->map(fn($img) => [
                    'id' => $img->id,
                    'image_path' => $img->image_path,
                    'is_primary' => $img->is_primary,
                ]),
                'variants' => $product->variants->map(fn($v) => [
                    'id' => $v->id,
                    'variant_name' => $v->variant_name,
                    'variant_value' => $v->variant_value,
                    'additional_price' => (float) $v->additional_price,
                    'effective_variant_price' => (float) ($product->effective_price + $v->additional_price),
                    'stock' => $v->stock,
                ]),
                'related_products' => $related,
            ],
        ]);
    }
}
