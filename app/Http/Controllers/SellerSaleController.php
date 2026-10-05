<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellerSaleController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $product = Product::query()
                ->where('seller_id', $request->user()->id)
                ->whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (! $product->is_active || $product->approval_status !== 'approved') {
                throw ValidationException::withMessages([
                    'product_id' => 'Produk harus aktif dan disetujui admin untuk dicatat sebagai penjualan.',
                ]);
            }

            if ($validated['quantity'] > $product->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah penjualan melebihi stok yang tersedia.',
                ]);
            }

            $unitPrice = (float) $product->price;
            $product->decrement('stock', $validated['quantity']);

            Sale::create([
                'seller_id' => $request->user()->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $validated['quantity'],
                'unit_price' => $unitPrice,
                'total_amount' => round($unitPrice * $validated['quantity'], 2),
                'sold_at' => now(),
            ]);
        });

        return redirect()->route('seller.dashboard')->with('success', 'Penjualan berhasil dicatat dan stok diperbarui.');
    }
}
