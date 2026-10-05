<div class="mb-3">
    <label class="form-label">Nama Product</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name ?? '') }}">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Kategori</label>
    <input type="text" name="category" class="form-control" value="{{ old('category', $product->category ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Harga</label>
        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price ?? '') }}">
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}">
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Image</label>
    <input type="text" name="image" class="form-control" value="{{ old('image', $product->image ?? '') }}" placeholder="nama-file.jpg">
</div>
<div class="form-check mb-3">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Product Aktif</label>
</div>
