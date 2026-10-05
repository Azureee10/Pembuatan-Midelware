<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Produk
            </h2>
            <a href="{{ route('products.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1.3fr_0.7fr]">
                <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-8 border border-gray-200">
                    <div class="space-y-6">
                        <div>
                            <p class="text-sm font-medium text-indigo-600 uppercase tracking-wide">Produk</p>
                            <h3 class="mt-2 text-2xl font-bold text-gray-900">{{ $product->name ?? 'Nama Produk' }}</h3>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                                <input type="text" value="{{ $product->name ?? '' }}" readonly
                                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-gray-900 focus:outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                <input type="text" value="{{ $product->category ?? '-' }}" readonly
                                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-gray-900 focus:outline-none">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea rows="5" readonly
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-gray-900 focus:outline-none">{{ $product->description ?? '-' }}</textarea>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Harga</label>
                                <input type="text" value="Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}" readonly
                                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-gray-900 focus:outline-none">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Stock</label>
                                <input type="text" value="{{ $product->stock ?? 0 }}" readonly
                                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-gray-900 focus:outline-none">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Gambar</label>
                            <input type="text" value="{{ $product->image ?? 'nama-file.jpg' }}" readonly
                                class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-gray-900 focus:outline-none">
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input id="is_active" type="checkbox" {{ ($product->is_active ?? true) ? 'checked' : '' }} disabled
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="is_active" class="text-sm font-medium text-gray-700">Produk Aktif</label>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">Preview</span>
                        <span class="rounded-full bg-emerald-400/90 px-2.5 py-1 text-xs font-bold text-emerald-950">
                            {{ ($product->is_active ?? true) ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="overflow-hidden rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20">
                        @if (!empty($product->image))
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name ?? 'Produk' }}"
                            class="h-64 w-full object-cover">
                        @else
                        <div class="h-64 w-full flex items-center justify-center bg-white/10 text-5xl text-white/80">
                            🛒
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 space-y-3 text-sm text-indigo-50">
                        <div class="flex items-center justify-between">
                            <span>Harga</span>
                            <strong>Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Stok</span>
                            <strong>{{ $product->stock ?? 0 }} pcs</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Kategori</span>
                            <strong>{{ $product->category ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
