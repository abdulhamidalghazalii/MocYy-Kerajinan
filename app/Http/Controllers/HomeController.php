<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->take(8)->get();
        $featuredProducts = Product::active()->featured()->with(['category', 'variants'])->take(8)->get();
        $latestProducts = Product::active()->with(['category', 'variants'])->latest()->take(8)->get();
        $whatsappNumber = StoreSetting::get('store_whatsapp', config('store.whatsapp', '628135487734'));
        $storeName = StoreSetting::get('store_name', config('store.name', 'MocYy'));

        return view('frontend.home', compact(
            'categories',
            'featuredProducts',
            'latestProducts',
            'whatsappNumber',
            'storeName'
        ));
    }
}
