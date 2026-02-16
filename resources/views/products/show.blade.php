@extends('layouts.app')

@section('content')
<h2 class="mb-4">Product Details</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-3">
            <img src="{{ $product->thumbnail }}" class="product-thumb-show rounded"  style="width:150px; height:150px;" alt="product">
        </div>

        <h4>{{ $product->name }}</h4>
        <p><strong>Price:</strong> {{ $product->price }}</p>
        <p><strong>Short Description:</strong> {{ $product->short_description ?? 'N/A' }}</p>
    </div>
</div>

<div class="d-flex justify-content-end">
<a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Back to List</a>
</div>
@endsection
