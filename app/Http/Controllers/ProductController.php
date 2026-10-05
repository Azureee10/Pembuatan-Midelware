<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function index()
    {
        if (Auth::user()->role === 'pemilik_toko') {
            return redirect()->route('seller.dashboard');
        }

        $query = Product::latest();
        if (in_array(Auth::user()->role, ['penjual', 'pemilik_toko'], true)) {
            $query->where('seller_id', Auth::id());
        }

        $products = $query->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $isSeller = in_array($request->user()->role, ['penjual', 'pemilik_toko'], true);

        if ($request->user()->role === 'pemilik_toko') {
            $validated = $request->validate([
                'products' => ['required', 'array', 'min:1', 'max:50'],
                'products.*.name' => ['required', 'string', 'max:255'],
                'products.*.category' => ['nullable', 'string', 'max:100'],
                'products.*.description' => ['nullable', 'string'],
                'products.*.price' => ['required', 'numeric', 'min:0'],
                'products.*.stock' => ['required', 'integer', 'min:0'],
                'products.*.image' => ['nullable', 'string', 'max:255'],
                'products.*.is_active' => ['sometimes', 'boolean'],
            ]);

            $products = $validated['products'];
            DB::transaction(function () use ($products, $request): void {
                foreach ($products as $productData) {
                    $productData['is_active'] = (bool) ($productData['is_active'] ?? false);
                    $productData['seller_id'] = $request->user()->id;
                    $productData['approval_status'] = 'pending';
                    Product::create($productData);
                }
            });

            return redirect()->route('seller.dashboard')->with(
                'success',
                count($products) . ' produk berhasil dikirim dan menunggu persetujuan admin.'
            );
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|string|max:255',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['seller_id'] = $isSeller ? $request->user()->id : null;
        $validated['approval_status'] = $isSeller ? 'pending' : 'approved';
        Product::create($validated);

        $message = $isSeller
            ? 'Produk berhasil dikirim dan menunggu persetujuan admin.'
            : 'Produk berhasil ditambahkan.';

        $redirectRoute = $request->user()->role === 'pemilik_toko' ? 'seller.dashboard' : 'products.index';

        return redirect()->route($redirectRoute)->with('success', $message);
    }

    public function show(Product $product)
    {
        $this->ensureProductAccess($product);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->ensureProductAccess($product);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->ensureProductAccess($product);
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|string|max:255',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $isSeller = in_array($request->user()->role, ['penjual', 'pemilik_toko'], true);
        if ($isSeller) {
            $validated['approval_status'] = 'pending';
            $validated['reviewed_by'] = null;
            $validated['reviewed_at'] = null;
        }
        $product->update($validated);

        $message = $isSeller
            ? 'Produk diperbarui dan menunggu persetujuan admin.'
            : 'Product berhasil diperbarui.';

        return redirect()->route('products.index')->with('success', $message);
    }

    public function destroy(Product $product)
    {
        $this->ensureProductAccess($product);
        $product->delete();
        $redirectRoute = Auth::user()->role === 'pemilik_toko' ? 'seller.dashboard' : 'products.index';

        return redirect()->route($redirectRoute)->with('success', 'Product berhasil dihapus.');
    }

    private function ensureProductAccess(Product $product): void
    {
        $user = Auth::user();
        abort_unless(! in_array($user->role, ['penjual', 'pemilik_toko'], true) || $product->seller_id === $user->id, 403);
    }
}
