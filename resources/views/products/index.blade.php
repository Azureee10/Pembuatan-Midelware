<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Produk
            </h2>
            <a href="{{ route('products.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                + Tambah Produk
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid gap-4 md:grid-cols-3 mb-8">
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-5">
                    <p class="text-sm text-gray-500">Total Produk</p>
                    <h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $products->total() }}</h3>
                </div>
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-5">
                    <p class="text-sm text-gray-500">Stok Keseluruhan</p>
                    <h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $products->sum('stock') }}</h3>
                </div>
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm p-5">
                    <p class="text-sm text-gray-500">Produk Aktif</p>
                    <h3 class="mt-2 text-3xl font-bold text-gray-900">{{ $products->where('is_active', true)->count() }}</h3>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Aktif</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Persetujuan</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($products as $product)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center font-bold text-lg">
                                            {{ strtoupper(substr($product->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($product->description ?? '-', 35) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $product->category ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $product->stock ?? 0 }}</td>
                                <td class="px-6 py-4">
                                    @if ($product->approval_status !== 'approved')
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Belum Aktif</span>
                                    @elseif ($product->is_active)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                                    @else
                                    <span class="inline-flex rounded-full bg-gray-200 px-2.5 py-1 text-xs font-semibold text-gray-700">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($product->approval_status === 'approved')
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Disetujui</span>
                                    @elseif ($product->approval_status === 'rejected')
                                    <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Ditolak</span>
                                    @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Menunggu</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('products.edit', $product) }}" class="rounded-lg bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-200">Edit</a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada produk.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                <div class="border-t border-gray-200 px-4 py-4">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
