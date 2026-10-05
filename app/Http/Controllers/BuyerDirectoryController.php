<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class BuyerDirectoryController extends Controller
{
    public function sellers(): View
    {
        return $this->directory('penjual', 'Penjual Pilihan', 'Temukan produk dari penjual independen.');
    }

    public function stores(): View
    {
        return $this->directory('pemilik_toko', 'Toko Pilihan', 'Jelajahi toko dan produk yang tersedia.');
    }

    private function directory(string $role, string $title, string $description): View
    {
        $availableProducts = fn(Builder $query) => $query
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->where('stock', '>', 0);

        $accounts = User::query()
            ->where('role', $role)
            ->whereHas('products', $availableProducts)
            ->withCount(['products as available_products_count' => $availableProducts])
            ->orderBy('name')
            ->paginate(12);

        return view('buyer.directory', compact('accounts', 'title', 'description'));
    }
}
