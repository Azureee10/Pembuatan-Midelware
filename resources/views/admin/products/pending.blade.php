<x-app-layout>
    <x-slot name="header">
        <div class="admin-page-heading">
            <div>
                <p class="admin-eyebrow">Ruang kontrol</p>
                <h1>Persetujuan Produk</h1>
                <p>Periksa detail produk sebelum ditampilkan kepada pembeli.</p>
            </div>
            <span class="admin-queue-count">{{ number_format($sellerProducts->total() + $ownerProducts->total()) }} menunggu</span>
        </div>
    </x-slot>

    <main class="admin-review-page">
        <div class="admin-review-shell">
            @if (session('success'))
            <div class="admin-review-notice" role="status">
                <span class="admin-notice-mark" aria-hidden="true">&#10003;</span>
                {{ session('success') }}
            </div>
            @endif

            <section class="admin-review-queue" id="seller-queue" aria-labelledby="seller-queue-title">
                <div class="admin-review-toolbar">
                    <div>
                        <h2 id="seller-queue-title">Antrean penjual</h2>
                        <p>Produk yang diajukan oleh akun penjual.</p>
                    </div>
                    <span class="admin-results-count">{{ number_format($sellerProducts->total()) }} produk</span>
                </div>
                @include('admin.products._approval-queue', [
                'products' => $sellerProducts,
                'emptyMessage' => 'Tidak ada produk penjual yang menunggu persetujuan.',
                ])
            </section>

            <section class="admin-review-queue" id="owner-queue" aria-labelledby="owner-queue-title">
                <div class="admin-review-toolbar">
                    <div>
                        <h2 id="owner-queue-title">Antrean pemilik toko</h2>
                        <p>Produk yang diajukan oleh akun pemilik toko.</p>
                    </div>
                    <span class="admin-results-count">{{ number_format($ownerProducts->total()) }} produk</span>
                </div>
                @forelse ($ownerProducts->getCollection()->groupBy('seller_id') as $accountProducts)
                @php($account = $accountProducts->first()->seller)
                <section class="admin-owner-account" id="owner-account-{{ $account->id }}" data-owner-id="{{ $account->id }}" aria-labelledby="owner-account-title-{{ $account->id }}">
                    <header class="admin-owner-account-heading">
                        <div>
                            <h3 id="owner-account-title-{{ $account->id }}">{{ $account->name }}</h3>
                            <p>{{ $account->email }}</p>
                        </div>
                        <div class="admin-owner-account-actions">
                            <span>{{ number_format($ownerPendingCounts[$account->id] ?? 0) }} produk menunggu</span>
                            @if (($ownerPendingCounts[$account->id] ?? 0) > 0)
                            <form method="POST" action="{{ route('admin.products.owners.approve-all', $account) }}" onsubmit="return confirm('Setujui semua produk pending akun ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="admin-approve-all-button">Setujui Semua</button>
                            </form>
                            @endif
                        </div>
                    </header>
                    @include('admin.products._approval-queue', [
                    'products' => $accountProducts,
                    'emptyMessage' => 'Tidak ada produk dalam antrean akun ini.',
                    'showAccount' => false,
                    ])
                </section>
                @empty
                <section class="admin-review-panel admin-owner-empty">
                    <p>Tidak ada produk pemilik toko yang menunggu persetujuan.</p>
                </section>
                @endforelse
                @if ($ownerProducts->hasPages())
                <div class="admin-review-pagination">{{ $ownerProducts->links() }}</div>
                @endif
            </section>
        </div>
    </main>
</x-app-layout>
