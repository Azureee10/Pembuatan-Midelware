<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="storefront-body">
    <header class="storefront-header">
        <div class="storefront-topbar">
            <div class="storefront-brand">
                <span class="storefront-logo">B</span>
                <span>BeliAja</span>
            </div>

            <nav class="storefront-nav">
                <a href="#">Home</a>
                <a href="#">Kategori</a>
                <a href="#">Promo</a>
                <a href="#">Tentang</a>
            </nav>

            <div class="storefront-tools">
                @auth
                <a href="{{ route('dashboard') }}" class="storefront-link">Dashboard</a>
                <a href="{{ route('register') }}" class="storefront-link">Daftar</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="storefront-button">Keluar</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="storefront-link">Masuk</a>
                <a href="{{ route('register') }}" class="storefront-button">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="storefront-main">
        @if (session('status'))
        <div class="storefront-session-notice" role="status">{{ session('status') }}</div>
        @endif

        <section class="storefront-hero">
            <div class="storefront-hero-copy">
                <span class="storefront-badge">Belanja lebih hemat</span>
                <h1>Temukan kebutuhan harianmu dengan harga terbaik.</h1>
                <p>
                    Produk favorit, pilihan berkualitas, dan promo menarik hadir dalam satu storefront yang cepat, aman, dan nyaman untuk dibeli.
                </p>

                <div class="storefront-cta">
                    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="storefront-primary-btn">Mulai Belanja</a>
                    <a href="#categories" class="storefront-secondary-btn">Lihat Promo</a>
                </div>

                <div class="storefront-highlights">
                    <div>
                        <strong>20K+</strong>
                        <span>pelanggan puas</span>
                    </div>
                    <div>
                        <strong>4.9/5</strong>
                        <span>rating toko</span>
                    </div>
                    <div>
                        <strong>1.2K</strong>
                        <span>produk terjual</span>
                    </div>
                </div>
            </div>

            <div class="storefront-hero-visual">
                <div class="storefront-product-card large">
                    <div class="card-badge">Diskon 40%</div>
                    <div class="product-visual orange"></div>
                    <div class="product-info">
                        <span>Smart Watch</span>
                        <strong>Rp 899.000</strong>
                    </div>
                </div>

                <div class="storefront-product-card small">
                    <div class="product-visual cream"></div>
                    <div class="product-info">
                        <span>Headset</span>
                        <strong>Rp 249.000</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="storefront-categories" id="categories">
            <div class="section-heading">
                <span>Jelajahi kategori</span>
                <h2>Pilihan favorit pelanggan</h2>
            </div>

            <div class="category-grid">
                <div class="category-item">
                    <div class="category-icon orange">🧴</div>
                    <h3>Skincare</h3>
                    <p>Perawatan harian</p>
                </div>
                <div class="category-item">
                    <div class="category-icon pink">👟</div>
                    <h3>Fashion</h3>
                    <p>Gaya terbaru</p>
                </div>
                <div class="category-item">
                    <div class="category-icon blue">💻</div>
                    <h3>Elektronik</h3>
                    <p>Teknologi modern</p>
                </div>
                <div class="category-item">
                    <div class="category-icon green">🏠</div>
                    <h3>Rumah</h3>
                    <p>Perlengkapan tinggal</p>
                </div>
            </div>
        </section>

        <section class="storefront-promo-strip">
            <div>
                <span>Flash Sale</span>
                <h3>Diskon sampai 50% untuk produk pilihan hari ini.</h3>
            </div>
            <a href="#categories">Belanja Sekarang</a>
        </section>
    </main>

    <footer class="storefront-footer">
        <p>© 2026 BeliAja. Semua hak dilindungi.</p>
    </footer>
</body>

</html>
