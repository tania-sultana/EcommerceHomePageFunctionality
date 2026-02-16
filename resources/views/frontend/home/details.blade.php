@extends('frontend.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row g-5 bg-white shadow-sm rounded-4 p-4 p-md-5">

            <div class="col-md-6 text-center">
                <div class="product-image-container p-3 rounded-4 bg-light shadow-sm">
                    <img src="{{ asset($product->thumbnail) }}" class="img-fluid rounded" alt="{{ $product->name }}"
                        style="max-height: 500px; object-fit: contain; width: 100%;">
                </div>
            </div>

            <div class="col-md-6">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('index') }}" class="text-decoration-none">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Product Details</li>
                    </ol>
                </nav>

                <h1 class="fw-bold mb-2 text-dark">{{ $product->name }}</h1>

                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <span class="text-muted small">(4.5 Rating / 120 Reviews)</span>
                </div>

                <h2 class="text-primary fw-bold mb-4">৳{{ number_format($product->price, 0) }}</h2>

                <h5 class="fw-semibold">Description:</h5>
                <p class="text-muted mb-4 lead">
                    {{ $product->short_description ?? 'No description available for this product.' }}
                </p>

                <hr class="my-4 text-muted">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Quantity</label>
                        <input type="number" name="quantity" value="1" min="1"
                            class="form-control form-control-lg text-center rounded-pill border-primary">
                    </div>

                    <div class="col-md-8 d-flex align-items-end">
                        @php
                            $cart = session()->get('cart', []);
                            $isInCart = isset($cart[$product->id]);
                        @endphp

                        <div class="row g-3">
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-flex gap-3">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Quantity</label>
                                    <input type="number" name="quantity" value="1" min="1"
                                        class="form-control form-control-lg text-center rounded-pill border-primary">
                                </div>

                                <div class="col-md-8 d-flex align-items-end">
                                    <button type="submit"
                                        class="btn btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm {{ $isInCart ? 'btn-success' : 'btn-primary' }}">
                                        @if ($isInCart)
                                            <i class="fa-solid fa-check-double me-2"></i> In Cart
                                        @else
                                            <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
                                        @endif
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-3">
                    @php
                        $isWishlisted = in_array($product->id, $wishlistIds);
                    @endphp

                    <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="btn {{ $isWishlisted ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-4 py-2 shadow-sm fw-semibold">
                            <i class="fa-{{ $isWishlisted ? 'solid' : 'regular' }} fa-heart me-1"></i>
                            {{ $isWishlisted ? 'Remove Wishlist' : 'Add to Wishlist' }}
                        </button>
                    </form>

                    <button class="btn btn-outline-secondary rounded-pill px-4 py-2">
                        <i class="fa-solid fa-share-nodes me-1"></i> Share
                    </button>
                </div>

                <div class="mt-5 p-3 bg-light rounded-3 border">
                    <div class="row text-center text-muted small">
                        <div class="col-4 border-end">
                            <i class="fa-solid fa-truck-fast d-block mb-1 text-primary fs-5"></i> Free Delivery
                        </div>
                        <div class="col-4 border-end">
                            <i class="fa-solid fa-rotate-left d-block mb-1 text-primary fs-5"></i> 7 Days Return
                        </div>
                        <div class="col-4">
                            <i class="fa-solid fa-shield-halved d-block mb-1 text-primary fs-5"></i> Secure Payment
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
