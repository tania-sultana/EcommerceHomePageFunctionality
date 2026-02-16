@extends('layouts.app')

@section('content')
    <h2 class="mb-4">{{ isset($product) ? 'Edit' : 'Add' }} Product</h2>

    <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST" class=" border p-3"
        enctype="multipart/form-data">
        @csrf
        @if (isset($product))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" name="name" id="name" class="form-control"
                value="{{ old('name', $product->name ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" name="price" id="price" class="form-control"
                value="{{ old('price', $product->price ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="short_description" class="form-label">Short Description</label>
            <textarea name="short_description" id="short_description" class="form-control">{{ old('short_description', $product->short_description ?? '') }}</textarea>
        </div>

        <div class="mb-3 ">
            <div class="card-body">
                <label for="thumbnail" class="additionThumbnail">
                    <img src="{{ asset('assets/images/default.jpg') }}" id="preview" alt="Thumbnail" width="25%">
                </label>

                <input id="thumbnail" accept="image/*" type="file" name="thumbnail" class="d-none w-25 h-25"
                    onchange="previewFile(event, 'preview')">
                @error('thumbnail')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
@endsection
<script>
    function previewFile(event, previewId) {
        const input = event.target;
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
