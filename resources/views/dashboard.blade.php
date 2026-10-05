<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="dashboard-page admin-dashboard">
        <div class="dashboard-wrapper">
            <header class="admin-dashboard-intro">
                <div>
                    <p class="admin-eyebrow">Ruang kontrol</p>
                    <h1 class="dashboard-title">Dashboard Admin</h1>
                    <p class="admin-intro-copy">Pantau katalog dan tindak lanjuti produk yang menunggu pemeriksaan.</p>
                </div>
                @isset($pendingProducts)
                <a href="{{ route('admin.products.pending') }}" class="admin-review-cta">
                    <span class="admin-review-indicator" aria-hidden="true"></span>
                    <span>{{ $pendingProducts > 0 ? 'Tinjau ' . number_format($pendingProducts) . ' produk' : 'Buka antrean persetujuan' }}</span>
                    <span class="admin-cta-arrow" aria-hidden="true">&rarr;</span>
                </a>
                @endisset
            </header>

            <section class="dashboard-stats admin-stat-grid" aria-label="Ringkasan katalog">
                <div class="dashboard-stat-card admin-stat-products">
                    <p class="dashboard-stat-label">Total Produk</p>
                    <h3 class="dashboard-stat-value">{{ $totalProducts }}</h3>
                    <span class="admin-stat-note">Seluruh katalog</span>
                </div>

                <div class="dashboard-stat-card admin-stat-stock">
                    <p class="dashboard-stat-label">Stok Keseluruhan</p>
                    <h3 class="dashboard-stat-value">{{ $totalStock }}</h3>
                    <span class="admin-stat-note">Unit tersedia</span>
                </div>

                <div class="dashboard-stat-card admin-stat-active">
                    <p class="dashboard-stat-label">Produk Aktif</p>
                    <h3 class="dashboard-stat-value">{{ $activeProducts }}</h3>
                    <span class="admin-stat-note">Tampil di katalog</span>
                </div>
                @isset($pendingProducts)
                <div class="dashboard-stat-card admin-stat-pending">
                    <p class="dashboard-stat-label">Menunggu Persetujuan</p>
                    <h3 class="dashboard-stat-value">{{ $pendingProducts }}</h3>
                    <span class="admin-stat-note">Perlu ditinjau</span>
                </div>
                @endisset
            </section>

            <section class="dashboard-panel admin-products-panel">
                <div class="dashboard-panel-header">
                    <div>
                        <p class="admin-eyebrow">Aktivitas katalog</p>
                        <h2 class="dashboard-panel-title">Produk Terbaru</h2>
                    </div>
                    <div class="admin-panel-actions">
                        <a href="{{ route('products.index') }}" class="admin-secondary-action">Semua produk</a>
                        <a href="{{ route('products.create') }}" class="dashboard-add-btn">Tambah Produk</a>
                    </div>
                </div>

                <div class="dashboard-table-wrap">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category ?? '-' }}</td>
                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    @if ($product->approval_status !== 'approved')
                                    <span class="dashboard-status inactive">Belum Aktif</span>
                                    @elseif ($product->is_active)
                                    <span class="dashboard-status active">Aktif</span>
                                    @else
                                    <span class="dashboard-status inactive">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="dashboard-empty">Belum ada produk yang ditambahkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
