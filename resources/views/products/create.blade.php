<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Produk
            </h2>
            <a href="{{ auth()->user()->role === 'pemilik_toko' ? route('seller.dashboard') : route('products.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (auth()->user()->role === 'pemilik_toko')
            @php($productRows = old('products', [[]]))
            @php($productRows = is_array($productRows) && $productRows !== [] ? $productRows : [[]])
            @php($nextProductIndex = max(array_map('intval', array_keys($productRows))) + 1)
            <form action="{{ route('products.store') }}" method="POST" class="owner-batch-form" data-next-index="{{ $nextProductIndex }}">
                @csrf
                <header class="owner-batch-heading">
                    <div>
                        <p class="owner-eyebrow">Katalog Toko</p>
                        <h1>Tambah produk sekaligus</h1>
                    </div>
                    <button type="button" class="owner-batch-add" id="add-product-row">
                        <span aria-hidden="true">+</span> Tambah Produk
                    </button>
                </header>

                @if ($errors->any())
                <div class="owner-batch-errors" role="alert">
                    <strong>Periksa kembali data produk.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="owner-batch-rows" id="product-rows">
                    @foreach ($productRows as $rowIndex => $row)
                    <section class="owner-batch-row">
                        <header class="owner-batch-row-heading">
                            <h2 data-product-number>Produk {{ $loop->iteration }}</h2>
                            <button type="button" class="owner-batch-remove" aria-label="Hapus produk ini">Hapus</button>
                        </header>
                        <div class="owner-batch-fields">
                            <div class="owner-batch-field owner-batch-field-name">
                                <label for="product-name-{{ $rowIndex }}">Nama Produk</label>
                                <input id="product-name-{{ $rowIndex }}" type="text" name="products[{{ $rowIndex }}][name]" value="{{ old("products.$rowIndex.name") }}" required maxlength="255" placeholder="Nama produk">
                            </div>
                            <div class="owner-batch-field">
                                <label for="product-category-{{ $rowIndex }}">Kategori</label>
                                <input id="product-category-{{ $rowIndex }}" type="text" name="products[{{ $rowIndex }}][category]" value="{{ old("products.$rowIndex.category") }}" maxlength="100" placeholder="Contoh: Makanan">
                            </div>
                            <div class="owner-batch-field">
                                <label for="product-price-{{ $rowIndex }}">Harga</label>
                                <input id="product-price-{{ $rowIndex }}" type="number" name="products[{{ $rowIndex }}][price]" value="{{ old("products.$rowIndex.price") }}" min="0" step="1000" required placeholder="0">
                            </div>
                            <div class="owner-batch-field">
                                <label for="product-stock-{{ $rowIndex }}">Stok</label>
                                <input id="product-stock-{{ $rowIndex }}" type="number" name="products[{{ $rowIndex }}][stock]" value="{{ old("products.$rowIndex.stock", 0) }}" min="0" required>
                            </div>
                            <div class="owner-batch-field">
                                <label for="product-image-{{ $rowIndex }}">Gambar</label>
                                <input id="product-image-{{ $rowIndex }}" type="text" name="products[{{ $rowIndex }}][image]" value="{{ old("products.$rowIndex.image") }}" maxlength="255" placeholder="nama-file.jpg">
                            </div>
                            <div class="owner-batch-field owner-batch-field-description">
                                <label for="product-description-{{ $rowIndex }}">Deskripsi</label>
                                <textarea id="product-description-{{ $rowIndex }}" name="products[{{ $rowIndex }}][description]" rows="2" placeholder="Deskripsi singkat">{{ old("products.$rowIndex.description") }}</textarea>
                            </div>
                        </div>
                        <label class="owner-batch-active">
                            <input type="checkbox" name="products[{{ $rowIndex }}][is_active]" value="1" @checked(old("products.$rowIndex.is_active", true))>
                            <span>Produk aktif</span>
                        </label>
                    </section>
                    @endforeach
                </div>

                <footer class="owner-batch-footer">
                    <a href="{{ route('seller.dashboard') }}">Batal</a>
                    <button type="submit">Ajukan Semua Produk</button>
                </footer>
            </form>

            <script>
                (() => {
                    const form = document.querySelector('.owner-batch-form');
                    const rows = document.getElementById('product-rows');
                    const addButton = document.getElementById('add-product-row');
                    let nextIndex = Number(form.dataset.nextIndex);

                    const updateRows = () => {
                        const productRows = rows.querySelectorAll('.owner-batch-row');
                        productRows.forEach((row, index) => {
                            row.querySelector('[data-product-number]').textContent = `Produk ${index + 1}`;
                            row.querySelector('.owner-batch-remove').disabled = productRows.length === 1;
                        });
                        addButton.disabled = productRows.length >= 50;
                    };

                    addButton.addEventListener('click', () => {
                        if (rows.querySelectorAll('.owner-batch-row').length >= 50) return;
                        const row = rows.querySelector('.owner-batch-row').cloneNode(true);
                        row.querySelectorAll('[name]').forEach((field) => {
                            field.name = field.name.replace(/products\[\d+\]/, `products[${nextIndex}]`);
                            if (field.type === 'checkbox') field.checked = true;
                            else field.value = '';
                        });
                        row.querySelectorAll('[id]').forEach((field) => {
                            field.id = field.id.replace(/-\d+$/, `-${nextIndex}`);
                        });
                        row.querySelectorAll('label[for]').forEach((label) => {
                            label.htmlFor = label.htmlFor.replace(/-\d+$/, `-${nextIndex}`);
                        });
                        rows.appendChild(row);
                        nextIndex += 1;
                        updateRows();
                    });

                    rows.addEventListener('click', (event) => {
                        const removeButton = event.target.closest('.owner-batch-remove');
                        if (!removeButton || rows.querySelectorAll('.owner-batch-row').length === 1) return;
                        removeButton.closest('.owner-batch-row').remove();
                        updateRows();
                    });

                    updateRows();
                })();
            </script>
            @else
            <form action="{{ route('products.store') }}" method="POST" class="bg-white shadow-lg rounded-2xl p-6 sm:p-8 border border-gray-200">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama produk">
                        @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" name="category" id="category" value="{{ old('category') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Contoh: Makanan">
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                        <input type="number" name="price" id="price" min="0" step="1000" value="{{ old('price') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('price') border-red-500 @enderror">
                        @error('price')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
                        <input type="text" name="image" id="image" value="{{ old('image') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="nama-file.jpg">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Deskripsikan produk Anda...">{{ old('description') }}</textarea>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-3 pt-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
                        <label for="is_active" class="text-sm font-medium text-gray-700">Produk Aktif</label>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        Simpan Produk
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>
