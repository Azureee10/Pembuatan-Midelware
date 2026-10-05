<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function dashboard()
    {
        $products = Product::latest()->take(5)->get();
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $activeProducts = Product::where('is_active', true)->count();

        return view('dashboard', compact('products', 'totalProducts', 'totalStock', 'activeProducts'));
    }
}
