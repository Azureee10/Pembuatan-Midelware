<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    public function index(): View
    {
        $sellerId = Auth::id();
        $sellerProducts = Product::query()->where('seller_id', $sellerId);
        $totalProducts = (clone $sellerProducts)->count();
        $totalStock = (clone $sellerProducts)->sum('stock');
        $productsSold = Sale::query()->where('seller_id', $sellerId)->sum('quantity');
        $totalRevenue = Sale::query()->where('seller_id', $sellerId)->sum('total_amount');
        $products = Auth::user()->role === 'pemilik_toko'
            ? (clone $sellerProducts)->latest()->paginate(10)
            : (clone $sellerProducts)->latest()->take(8)->get();
        $stockedProducts = (clone $sellerProducts)
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $firstMonth = now()->startOfMonth()->subMonths(5);
        $recentSales = Sale::query()
            ->where('seller_id', $sellerId)
            ->where('sold_at', '>=', $firstMonth)
            ->get(['sold_at', 'total_amount'])
            ->groupBy(fn(Sale $sale) => $sale->sold_at->format('Y-m'));

        $revenueChart = collect(range(5, 0))->map(function (int $monthsAgo) use ($recentSales) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);

            return [
                'label' => $month->translatedFormat('M'),
                'total' => (float) $recentSales->get($month->format('Y-m'), collect())->sum('total_amount'),
            ];
        });
        $maxRevenue = max(1, $revenueChart->max('total'));

        $dashboardView = Auth::user()->role === 'pemilik_toko'
            ? 'owner.dashboard'
            : 'seller.dashboard';

        return view($dashboardView, compact(
            'products',
            'stockedProducts',
            'totalProducts',
            'totalStock',
            'productsSold',
            'totalRevenue',
            'revenueChart',
            'maxRevenue',
        ));
    }
}
