@extends('frontend.layouts.app')

@section('content')
    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">

                <a href="{{ route('product.details', $product->id) }}">
                    <div class="card h-100 product-card shadow-sm border-0">

                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST"
                            class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                            @csrf
                            <button type="submit"
                                class="wishlist-icon border border-soft-primary shadow-sm bg-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 35px; height: 35px;">
                                @if (isset($wishlistIds) && in_array($product->id, $wishlistIds))
                                    <i class="fa-solid fa-heart text-danger"></i>
                                @else
                                    <i class="fa-regular fa-heart text-dark"></i>
                                @endif
                            </button>
                        </form>

                        <img src="{{ asset($product->thumbnail) }}" class="card-img-top p-3"
                            style="height: 200px; object-fit: contain;" alt="{{ $product->name }}">


                        <div class="card-body pt-0">
                            <h6 class="fw-bold mb-1">
                                <a href="{{ route('product.details', $product->id) }}"
                                    class="text-decoration-none text-dark">
                                    {{ $product->name }}
                                </a>
                            </h6>

                            <p class="text-muted small mb-2">
                                {{ Str::limit($product->short_description, 40) }}
                                @if (strlen($product->short_description) > 40)
                                    <a href="{{ route('product.details', $product->id) }}"
                                        class="text-primary text-decoration-none">See more</a>
                                @endif
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-3  py-3">
                                <span class="h6 fw-bold text-primary mb-0">৳{{ number_format($product->price, 0) }}</span>

                                @php
                                    $cart = session()->get('cart', []);
                                    $isInCart = isset($cart[$product->id]);
                                @endphp

                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">

                                    <button type="submit" {{ $isInCart ? 'disabled' : '' }}
                                        class="btn btn-sm rounded-pill px-3 shadow-sm {{ $isInCart ? 'btn-success text-white opacity-100' : 'btn-soft-primary' }}">
                                        @if ($isInCart)
                                            <i class="fa-solid fa-check me-1"></i> In Cart
                                        @else
                                            <i class="fa-solid fa-plus me-1"></i> Add to Cart
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="alert alert-info">No products found.</p>
            </div>
        @endforelse
    </div>
@endsection
