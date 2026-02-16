@extends('layouts.app')

@section('content')
<h2 class="mb-4">Edit Product</h2>

<form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow p-4 rounded">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" name="price" value="{{ old('price', $product->price) }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Short Description</label>
        <textarea name="short_description" class="form-control">{{ old('short_description', $product->short_description) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Thumbnail</label>
        <input type="file" name="thumbnail" accept="image/*" class="form-control">
        <img id="preview" src="{{ $product->thumbnail }}" class="img-thumbnail mt-2" style="width:100px; height:100px;">
    </div>

    <div class="d-flex justify-content-end gap-3">
        <a href="{{ route('products.index') }}" class="btn btn-secondary ">Back to List</a>
        <button type="submit" class="btn btn-primary">Update Product</button>
    </div>
</form>

<script>
    const thumbnailInput = document.querySelector('input[name="thumbnail"]');
    const preview = document.getElementById('preview');
    thumbnailInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
