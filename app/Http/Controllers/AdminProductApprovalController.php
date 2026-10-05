<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductApprovalController extends Controller
{
    public function index(): View
    {
        $sellerProducts = Product::query()
            ->with('seller')
            ->where('approval_status', 'pending')
            ->whereHas('seller', fn($query) => $query->where('role', 'penjual'))
            ->latest()
            ->paginate(20, ['*'], 'seller_page')
            ->withQueryString();

        $ownerProducts = Product::query()
            ->with('seller')
            ->where('approval_status', 'pending')
            ->whereHas('seller', fn($query) => $query->where('role', 'pemilik_toko'))
            ->orderBy('seller_id')
            ->latest()
            ->paginate(20, ['*'], 'owner_page')
            ->withQueryString();

        $ownerPendingCounts = Product::query()
            ->where('approval_status', 'pending')
            ->whereIn('seller_id', $ownerProducts->getCollection()->pluck('seller_id')->unique())
            ->selectRaw('seller_id, COUNT(*) as pending_count')
            ->groupBy('seller_id')
            ->pluck('pending_count', 'seller_id');

        return view('admin.products.pending', compact('sellerProducts', 'ownerProducts', 'ownerPendingCounts'));
    }

    public function approve(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->approval_status === 'pending', 404);

        $product->update([
            'approval_status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.products.pending')->with('success', 'Produk disetujui dan dapat tampil di katalog pembeli.');
    }

    public function reject(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->approval_status === 'pending', 404);

        $product->update([
            'approval_status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.products.pending')->with('success', 'Produk ditolak dan tidak ditampilkan kepada pembeli.');
    }

    public function approveAllForOwner(Request $request, User $owner): RedirectResponse
    {
        abort_unless($owner->role === 'pemilik_toko', 404);

        $reviewedAt = now();
        $approvedCount = DB::transaction(fn() => Product::query()
            ->where('seller_id', $owner->id)
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => $reviewedAt,
                'updated_at' => $reviewedAt,
            ]));

        return redirect()->route('admin.products.pending')->with(
            'success',
            $approvedCount . ' produk milik ' . $owner->name . ' disetujui dan dapat tampil di katalog pembeli.'
        );
    }
}
