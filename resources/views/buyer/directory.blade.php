<x-app-layout>
    <main class="buyer-catalog">
        <section class="buyer-intro">
            <div class="buyer-intro-copy">
                <p class="buyer-kicker">BELANJA LOKAL</p>
                <h1>{{ $title }}</h1>
                <p>{{ $description }}</p>
            </div>
            <div class="buyer-intro-mark" aria-hidden="true">
                <span>{{ $title === 'Toko Pilihan' ? 'Toko dekatmu' : 'Kenali penjual' }}</span>
            </div>
        </section>

        <section class="buyer-products-section" aria-labelledby="buyer-directory-title">
            <div class="buyer-section-heading">
                <div>
                    <p class="buyer-kicker">JELAJAHI</p>
                    <h2 id="buyer-directory-title">{{ $title }}</h2>
                </div>
                <span class="buyer-result-count">{{ $accounts->total() }} tersedia</span>
            </div>

            @if ($accounts->isEmpty())
            <div class="buyer-directory-empty">Belum ada {{ strtolower($title) }} dengan produk tersedia.</div>
            @else
            <div class="buyer-directory-grid">
                @foreach ($accounts as $account)
                <article class="buyer-directory-card">
                    <div class="buyer-directory-card-top">
                        <span class="buyer-directory-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($account->name, 0, 1)) }}</span>
                        <span class="buyer-kicker">{{ $title === 'Toko Pilihan' ? 'TOKO' : 'PENJUAL' }}</span>
                    </div>
                    <h3>{{ $account->name }}</h3>
                    <p>{{ number_format($account->available_products_count) }} produk tersedia</p>
                    <a href="{{ route('buyer.dashboard', ['seller' => $account->id]) }}">Lihat Produk</a>
                </article>
                @endforeach
            </div>
            <div class="buyer-pagination">{{ $accounts->links() }}</div>
            @endif
        </section>
    </main>
</x-app-layout>
