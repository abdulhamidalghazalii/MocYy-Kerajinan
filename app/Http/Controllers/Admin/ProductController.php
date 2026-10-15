<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\AdminActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::all();

        $query = Product::with(['category', 'variants']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('sku', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['sku'] = $validated['sku'] ?: 'BKT-' . strtoupper(Str::random(8));
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['allows_greeting_card'] = $request->boolean('allows_greeting_card', true);
        $validated['allows_custom_note'] = $request->boolean('allows_custom_note', true);

        // Handle Image upload
        if ($request->hasFile('main_image_file')) {
            $path = $request->file('main_image_file')->store('products', 'public');
            $validated['main_image'] = $path;
        }

        $product = Product::create($validated);

        if (!empty($validated['main_image'])) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $validated['main_image'],
                'is_primary' => true,
                'sort_order' => 1,
            ]);
        }

        // Handle Variants if submitted
        if ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $variantData) {
                if (!empty($variantData['variant_name']) && !empty($variantData['variant_value'])) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_name' => $variantData['variant_name'],
                        'variant_value' => $variantData['variant_value'],
                        'additional_price' => (float) ($variantData['additional_price'] ?? 0),
                        'stock' => (int) ($variantData['stock'] ?? 0),
                        'sku' => $product->sku . '-' . strtoupper(Str::slug($variantData['variant_value'])),
                    ]);
                }
            }
        }

        AdminActivityLog::record(
            Auth::id(),
            'product_created',
            Product::class,
            $product->id,
            ['name' => $product->name, 'sku' => $product->sku]
        );

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$product->name}' berhasil ditambahkan ke katalog!");
    }

    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $product->load(['variants', 'images']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['allows_greeting_card'] = $request->boolean('allows_greeting_card');
        $validated['allows_custom_note'] = $request->boolean('allows_custom_note');

        if ($request->hasFile('main_image_file')) {
            // Delete old if stored locally
            if ($product->main_image && !str_starts_with($product->main_image, 'http')) {
                Storage::disk('public')->delete($product->main_image);
            }
            $path = $request->file('main_image_file')->store('products', 'public');
            $validated['main_image'] = $path;
        }

        $product->update($validated);

        // Process Variants (replace or create)
        if ($request->has('variants') && is_array($request->variants)) {
            $product->variants()->delete();
            foreach ($request->variants as $variantData) {
                if (!empty($variantData['variant_name']) && !empty($variantData['variant_value'])) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_name' => $variantData['variant_name'],
                        'variant_value' => $variantData['variant_value'],
                        'additional_price' => (float) ($variantData['additional_price'] ?? 0),
                        'stock' => (int) ($variantData['stock'] ?? 0),
                        'sku' => $product->sku . '-' . strtoupper(Str::slug($variantData['variant_value'])),
                    ]);
                }
            }
        }

        AdminActivityLog::record(
            Auth::id(),
            'product_updated',
            Product::class,
            $product->id,
            ['name' => $product->name, 'sku' => $product->sku]
        );

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$product->name}' berhasil diperbarui!");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $productName = $product->name;
        $product->delete();

        AdminActivityLog::record(
            Auth::id(),
            'product_deleted',
            Product::class,
            $product->id,
            ['name' => $productName]
        );

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$productName}' berhasil dihapus (soft delete).");
    }
}
