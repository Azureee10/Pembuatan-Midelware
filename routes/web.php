<?php

use App\Http\Controllers\BuyerDashboardController;
use App\Http\Controllers\BuyerDirectoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerSaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return view('welcome');
});

Route::get('/dashboard', function (Request $request) {
    return match ($request->user()->role) {
        'pembeli' => redirect()->route('buyer.dashboard'),
        'penjual', 'pemilik_toko' => redirect()->route('seller.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'kasir' => redirect()->route('kasir.dashboard'),
        default => abort(403),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:pembeli'])->group(function () {
    Route::get('/buyer/dashboard', [BuyerDashboardController::class, 'index'])->name('buyer.dashboard');
    Route::get('/buyer/sellers', [BuyerDirectoryController::class, 'sellers'])->name('buyer.sellers');
    Route::get('/buyer/stores', [BuyerDirectoryController::class, 'stores'])->name('buyer.stores');
});

Route::middleware(['auth', 'verified', 'role:penjual,pemilik_toko'])->group(function () {
    Route::get('/seller/dashboard', [SellerDashboardController::class, 'index'])->name('seller.dashboard');
    Route::post('/seller/sales', [SellerSaleController::class, 'store'])->name('seller.sales.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:penjual,pemilik_toko,admin,kasir'])->group(function () {
    Route::resource('products', ProductController::class);
});

require __DIR__ . '/auth.php';
