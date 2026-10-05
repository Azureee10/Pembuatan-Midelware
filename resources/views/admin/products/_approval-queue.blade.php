@php($showAccount = $showAccount ?? true)
<section class="admin-review-panel" aria-label="{{ $emptyMessage }}">
    <div class="admin-review-table-wrap">
        <table class="admin-review-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    @if ($showAccount)
                    <th>Pemilik akun</th>
                    @endif
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr>
                    <td class="admin-review-product">
                        <p>{{ $product->name }}</p>
                        <span class="admin-category-label">{{ $product->category ?: 'Tanpa kategori' }}</span>
                        @if ($product->description)
                        <span class="admin-product-description">{{ $product->description }}</span>
                        @endif
                    </td>
                    @if ($showAccount)
                    <td class="admin-review-seller">
                        <span class="admin-seller-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($product->seller?->name ?? '?', 0, 1)) }}</span>
                        <span>{{ $product->seller?->name ?? 'Akun tidak tersedia' }}</span>
                    </td>
                    @endif
                    <td class="admin-review-price">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td><span class="admin-stock-label">{{ number_format($product->stock) }} unit</span></td>
                    <td class="admin-review-actions">
                        <div>
                            <form method="POST" action="{{ route('admin.products.approve', $product) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="admin-approve-button"><span aria-hidden="true">&#10003;</span> Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('admin.products.reject', $product) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="admin-reject-button">Tolak</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $showAccount ? 5 : 4 }}" class="admin-review-empty">
                        <span aria-hidden="true">&#10003;</span>
                        <strong>Antrean kosong</strong>
                        <p>{{ $emptyMessage }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($products, 'hasPages') && $products->hasPages())
    <div class="admin-review-pagination">{{ $products->links() }}</div>
    @endif
</section>
