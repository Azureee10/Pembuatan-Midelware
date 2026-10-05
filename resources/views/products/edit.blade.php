<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Produk
            </h2>
            <a href="{{ route('products.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('products.update', $product) }}" method="POST" class="bg-white shadow-lg rounded-2xl p-6 sm:p-8 border border-gray-200">
                @csrf
                @method('PUT')

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama produk">
                        @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" name="category" id="category" value="{{ old('category', $product->category ?? '') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Contoh: Makanan">
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock ?? 0) }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                        <input type="number" name="price" id="price" min="0" step="1000" value="{{ old('price', $product->price ?? '') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('price') border-red-500 @enderror">
                        @error('price')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
                        <input type="text" name="image" id="image" value="{{ old('image', $product->image ?? '') }}"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="nama-file.jpg">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Deskripsikan produk Anda...">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    <div class="sm:col-span-2 flex items-center gap-3 pt-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                        <label for="is_active" class="text-sm font-medium text-gray-700">Produk Aktif</label>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        Update Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
