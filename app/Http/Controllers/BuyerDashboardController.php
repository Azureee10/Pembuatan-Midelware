<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'seller' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $query = Product::query()
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->where('stock', '>', 0);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($productQuery) use ($search) {
                $productQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        $seller = null;
        if (! empty($filters['seller'])) {
            $seller = User::query()
                ->whereIn('role', ['penjual', 'pemilik_toko'])
                ->find($filters['seller']);

            if ($seller) {
                $query->where('seller_id', $seller->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Product::query()
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->where('stock', '>', 0)
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('buyer.dashboard', compact('products', 'categories', 'filters', 'seller'));
    }
}
