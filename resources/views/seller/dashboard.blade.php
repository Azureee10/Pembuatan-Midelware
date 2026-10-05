<x-app-layout>
    <x-slot name="header">
        <div class="seller-heading">
            <div class="seller-heading-copy">
                <p class="seller-eyebrow">Ruang Penjual <span class="seller-heading-dot" aria-hidden="true"></span> {{ auth()->user()->name }}</p>
                <h1 class="seller-title">Ringkasan Toko</h1>
                <p class="seller-subtitle">Pantau inventaris dan catat transaksi tokomu.</p>
            </div>
            <a href="{{ route('products.create') }}" class="seller-primary-action"><span aria-hidden="true">+</span> Tambah Produk</a>
        </div>
    </x-slot>

    <main class="seller-page">
        <div class="seller-container">
            @if (session('success'))
            <div class="seller-notice">{{ session('success') }}</div>
            @endif

            <section class="seller-metrics" aria-label="Ringkasan toko">
                <article class="seller-metric seller-metric-products">
                    <span>Produk dijual</span>
                    <strong>{{ number_format($totalProducts) }}</strong>
                </article>
                <article class="seller-metric seller-metric-stock">
                    <span>Stok tersedia</span>
                    <strong>{{ number_format($totalStock) }}</strong>
                </article>
                <article class="seller-metric seller-metric-sold">
                    <span>Unit terjual</span>
                    <strong>{{ number_format($productsSold) }}</strong>
                </article>
                <article class="seller-metric seller-metric-revenue">
                    <span>Total pendapatan tercatat</span>
                    <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
                </article>
            </section>

            <section class="seller-content-grid">
                <article class="seller-panel seller-chart-panel">
                    <div class="seller-panel-heading">
                        <div>
                            <p class="seller-eyebrow">Performa</p>
                            <h2>Pendapatan 6 Bulan</h2>
                            <p class="seller-panel-caption">Ringkasan penjualan per bulan</p>
                        </div>
                    </div>
                    @if ($productsSold === 0)
                    <p class="seller-chart-empty">Grafik akan terisi setelah penjualan pertama dicatat.</p>
                    @endif
                    <div class="seller-chart" role="img" aria-label="Grafik pendapatan enam bulan terakhir">
                        @foreach ($revenueChart as $month)
                        @php($barHeight = $month['total'] > 0 ? max(4, ($month['total'] / $maxRevenue) * 100) : 2)
                        <div class="seller-chart-column" title="{{ $month['label'] }}: Rp {{ number_format($month['total'], 0, ',', '.') }}">
                            <span class="seller-chart-total">{{ $month['total'] > 0 ? number_format($month['total'] / 1000, 0) . 'k' : '' }}</span>
                            <div class="seller-chart-track">
                                <div style="height: {{ $barHeight }}%"></div>
                            </div>
                            <span class="seller-chart-label">{{ $month['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </article>

                <article class="seller-panel seller-sale-panel">
                    <div class="seller-panel-heading">
                        <div>
                            <p class="seller-eyebrow">Transaksi</p>
                            <h2>Catat Penjualan</h2>
                            <p class="seller-panel-caption">Kurangi stok dengan transaksi baru</p>
                        </div>
                    </div>
                    @if ($stockedProducts->isEmpty())
                    <p class="seller-chart-empty">Tambahkan produk aktif dengan stok sebelum mencatat penjualan.</p>
                    @else
                    <form method="POST" action="{{ route('seller.sales.store') }}" class="seller-sale-form">
                        @csrf
                        <label for="product_id">Produk</label>
                        <select id="product_id" name="product_id" required>
                            <option value="">Pilih produk</option>
                            @foreach ($stockedProducts as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id')==$product->id)>
                                {{ $product->name }} · stok {{ $product->stock }}
                            </option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="seller-field-error">{{ $message }}</p> @enderror

                        <label for="quantity">Jumlah terjual</label>
                        <input id="quantity" type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                        @error('quantity') <p class="seller-field-error">{{ $message }}</p> @enderror

                        <button type="submit">Simpan Penjualan</button>
                    </form>
                    @endif
                </article>
            </section>

            <section class="seller-panel seller-products-panel">
                <div class="seller-panel-heading">
                    <div>
                        <p class="seller-eyebrow">Inventaris</p>
                        <h2>Produk Toko</h2>
                    </div>
                    <a href="{{ route('products.index') }}" class="seller-text-link">Kelola semua produk</a>
                </div>
                <div class="seller-table-wrap">
                    <table class="seller-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aktif</th>
                                <th>Persetujuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    <span class="seller-status {{ $product->approval_status === 'approved' && $product->is_active ? 'is-positive' : 'is-muted' }}">
                                        {{ $product->approval_status !== 'approved' ? 'Belum Aktif' : ($product->is_active ? 'Aktif' : 'Nonaktif') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($product->approval_status === 'approved')
                                    <span class="seller-status is-positive">Disetujui</span>
                                    @elseif ($product->approval_status === 'rejected')
                                    <span class="seller-status is-danger">Ditolak</span>
                                    @else
                                    <span class="seller-status is-pending">Menunggu</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="seller-product-actions">
                                        <a href="{{ route('products.edit', $product) }}">Edit</a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="seller-empty">Belum ada produk. Tambahkan produk pertama toko Anda.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</x-app-layout>
