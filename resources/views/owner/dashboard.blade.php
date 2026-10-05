<x-app-layout>
    <x-slot name="header">
        <header class="owner-heading">
            <div class="owner-heading-copy">
                <p class="owner-eyebrow">Ruang Pemilik Toko · {{ auth()->user()->name }}</p>
                <h1 class="owner-title">Toko dalam kendali.</h1>
                <p class="owner-subtitle">Pantau performa, stok, dan transaksi bisnismu dari satu tempat.</p>
            </div>
            <div class="owner-heading-action">
                <span>Mulai kembangkan etalase</span>
                <a href="{{ route('products.create') }}" class="owner-primary-action"><span aria-hidden="true">+</span> Tambah Produk</a>
            </div>
        </header>
    </x-slot>

    <main class="owner-page">
        <div class="owner-container">
            @if (session('success'))
            <div class="owner-notice" role="status">{{ session('success') }}</div>
            @endif

            <section class="owner-metrics" aria-label="Ringkasan bisnis toko">
                <article class="owner-metric">
                    <span class="owner-metric-label">Produk di etalase</span>
                    <strong>{{ number_format($totalProducts) }}</strong>
                </article>
                <article class="owner-metric">
                    <span class="owner-metric-label">Unit dalam stok</span>
                    <strong>{{ number_format($totalStock) }}</strong>
                </article>
                <article class="owner-metric">
                    <span class="owner-metric-label">Unit terjual</span>
                    <strong>{{ number_format($productsSold) }}</strong>
                </article>
                <article class="owner-metric owner-metric-revenue">
                    <span class="owner-metric-label">Pendapatan tercatat</span>
                    <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
                </article>
            </section>

            <section class="owner-content-grid">
                <article class="owner-panel">
                    <div class="owner-panel-heading">
                        <div>
                            <p class="owner-eyebrow">Performa usaha</p>
                            <h2>Pendapatan 6 Bulan</h2>
                            <p class="owner-panel-caption">Pergerakan penjualan toko per bulan</p>
                        </div>
                    </div>
                    @if ($productsSold === 0)
                    <p class="owner-empty-note">Grafik akan terisi setelah transaksi pertama dicatat.</p>
                    @endif
                    <div class="owner-chart" role="img" aria-label="Grafik pendapatan enam bulan terakhir">
                        @foreach ($revenueChart as $month)
                        @php($barHeight = $month['total'] > 0 ? max(4, ($month['total'] / $maxRevenue) * 100) : 2)
                        <div class="owner-chart-column" title="{{ $month['label'] }}: Rp {{ number_format($month['total'], 0, ',', '.') }}">
                            <span class="owner-chart-total">{{ $month['total'] > 0 ? number_format($month['total'] / 1000, 0) . 'k' : '' }}</span>
                            <div class="owner-chart-track">
                                <div @style(['height'=> $barHeight . '%'])></div>
                            </div>
                            <span class="owner-chart-label">{{ $month['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </article>

                <article class="owner-panel">
                    <div class="owner-panel-heading">
                        <div>
                            <p class="owner-eyebrow">Operasional</p>
                            <h2>Catat Penjualan</h2>
                            <p class="owner-panel-caption">Stok akan diperbarui otomatis</p>
                        </div>
                    </div>
                    @if ($stockedProducts->isEmpty())
                    <p class="owner-empty-note">Tambahkan produk aktif dengan stok untuk mulai mencatat transaksi.</p>
                    @else
                    <form method="POST" action="{{ route('seller.sales.store') }}" class="owner-sale-form">
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
                        @error('product_id') <p class="owner-field-error">{{ $message }}</p> @enderror

                        <label for="quantity">Jumlah terjual</label>
                        <input id="quantity" type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
                        @error('quantity') <p class="owner-field-error">{{ $message }}</p> @enderror

                        <button type="submit">Simpan Penjualan</button>
                    </form>
                    @endif
                </article>
            </section>

            <section class="owner-products-section">
                <div class="owner-products-heading">
                    <div>
                        <p class="owner-eyebrow">Inventaris</p>
                        <h2>Produk Toko</h2>
                    </div>
                </div>
                <div class="owner-table-wrap">
                    <table class="owner-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Persetujuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>{{ number_format($product->stock) }}</td>
                                <td>{{ $product->approval_status !== 'approved' ? 'Belum Aktif' : ($product->is_active ? 'Aktif' : 'Nonaktif') }}</td>
                                <td>{{ $product->approval_status === 'approved' ? 'Disetujui' : ($product->approval_status === 'rejected' ? 'Ditolak' : 'Menunggu') }}</td>
                                <td>
                                    <div class="owner-product-actions">
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
                                <td colspan="6" class="owner-empty-row">Belum ada produk di toko. Tambahkan produk pertama untuk mulai berjualan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($products instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="owner-pagination">{{ $products->links() }}</div>
                @endif
            </section>
        </div>
    </main>
</x-app-layout>
