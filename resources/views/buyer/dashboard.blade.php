<x-app-layout>
    <main class="buyer-catalog">
        <section class="buyer-intro">
            <div class="buyer-intro-copy">
                <p class="buyer-kicker">TOKO MINIMARKET</p>
                <h1>Temukan yang kamu butuhkan.</h1>
                <p>Pilihan segar dan kebutuhan harian dari toko-toko di sekitarmu.</p>
            </div>
            <div class="buyer-intro-mark" aria-hidden="true">
                <span>Belanja<br>lebih mudah</span>
            </div>
        </section>

        <section class="buyer-products-section" aria-labelledby="buyer-products-title">
            <div class="buyer-section-heading">
                <div>
                    <p class="buyer-kicker">KATALOG</p>
                    <h2 id="buyer-products-title">{{ $seller ? 'Produk dari ' . $seller->name : 'Produk pilihan' }}</h2>
                </div>
                <span class="buyer-result-count">{{ $products->total() }} produk tersedia</span>
            </div>

            <form method="GET" action="{{ route('buyer.dashboard') }}" class="buyer-filter-bar">
                <label class="buyer-search-field" for="product-search">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
                        <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8" />
                        <path d="m16 16 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                    <input id="product-search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau kategori produk">
                </label>
                <label class="buyer-category-field" for="product-category">
                    <span>Kategori</span>
                    <select id="product-category" name="category">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(($filters['category'] ?? '' )===$category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </label>
                @if ($seller)
                <input type="hidden" name="seller" value="{{ $seller->id }}">
                @endif
                <button type="submit" class="buyer-filter-button">Cari produk</button>
                @if (!empty($filters['search']) || !empty($filters['category']) || !empty($filters['seller']))
                <a href="{{ route('buyer.dashboard') }}" class="buyer-reset-link">Reset</a>
                @endif
            </form>

            @if ($products->isEmpty())
            <div class="buyer-empty-state">
                <span class="buyer-empty-symbol" aria-hidden="true">⌕</span>
                <h3>Produk belum ditemukan</h3>
                <p>Coba kata kunci atau kategori lain.</p>
                @if (!empty($filters['search']) || !empty($filters['category']))
                <a href="{{ route('buyer.dashboard') }}">Lihat semua produk</a>
                @endif
            </div>
            @else
            <div class="buyer-product-grid">
                @foreach ($products as $product)
                <article class="buyer-product-card">
                    <div class="buyer-product-image buyer-product-image-{{ $loop->iteration % 4 }}">
                        @if ($product->image)
                        @php($imageUrl = \Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : \Illuminate\Support\Facades\Storage::disk('public')->url($product->image))
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                        <span class="buyer-product-monogram">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                        <span class="buyer-product-art-label">{{ $product->category ?: 'Pilihan toko' }}</span>
                        @endif
                    </div>
                    <div class="buyer-product-details">
                        <p class="buyer-product-category">{{ $product->category ?: 'Produk' }}</p>
                        <h3>{{ $product->name }}</h3>
                        <p class="buyer-product-description">{{ $product->description ?: 'Produk pilihan tersedia di toko.' }}</p>
                        <div class="buyer-product-bottom">
                            <strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong>
                            <span>Stok {{ $product->stock }}</span>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <div class="buyer-pagination">{{ $products->links() }}</div>
            @endif
        </section>
    </main>
</x-app-layout>
