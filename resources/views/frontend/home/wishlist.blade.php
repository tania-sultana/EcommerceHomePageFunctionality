@extends('frontend.layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-heart text-danger"></i> My Wishlist</h3>
    <div class="row g-4">
        @forelse($wishlistItems as $item)
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <img src="{{ $item->product->thumbnail }}" class="card-img-top p-3" style="height: 180px; object-fit: contain;">
                    <div class="card-body">
                        <h6 class="fw-bold">{{ $item->product->name }}</h6>
                        <p class="text-primary fw-bold">${{ number_format($item->product->price, 2) }}</p>

                        <div class="d-flex gap-2">
                            <form action="{{ route('wishlist.toggle', $item->product_id) }}" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Your wishlist is empty!</p>
                <a href="{{ route('index') }}" class="btn btn-primary rounded-pill">Shop Now</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
