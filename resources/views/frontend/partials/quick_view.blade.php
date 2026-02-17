
<div class="modal-body p-4 pt-5">
    <div class="row g-4">
        {{-- Product Image Section --}}
        <div class="col-md-5">
            <div class="p-3 rounded-4 bg-light shadow-sm text-center">

                <img src="{{ $product->thumbnail }}" class="img-fluid rounded" alt="product image"
                    style="max-height: 350px; object-fit: contain;">
            </div>
        </div>

        {{-- Product Info Section --}}
        <div class="col-md-7">
            <h2 class="fw-bold mb-2 text-dark">{{ $product->name }}</h2>

            <div class="d-flex align-items-center mb-3">
                <div class="text-warning me-2 small">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                        class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                </div>
                <span class="text-muted small">(4.5 Rating)</span>
            </div>

            <h3 class="text-primary fw-bold mb-3">৳{{ number_format($product->price, 0) }}</h3>

            <p class="text-muted mb-4 small">
                {{ $product->short_description ?? 'No detailed description available for this product.' }}
            </p>

            {{-- Quantity & Add to Cart --}}
            <div class="row g-2 mb-4">
                <div class="col-4">
                    <input type="number" id="modal-qty" value="1" min="1"
                        class="form-control text-center rounded-pill border-primary">
                </div>
                <div class="col-8">

                    <button type="button" class="add-to-cart-btn btn btn-primary w-100 rounded-pill fw-bold shadow-sm"
                        data-id="{{ $product->id }}">
                        <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
                    </button>
                </div>
            </div>

            {{-- Wishlist & Share --}}
            <div class="d-flex gap-2 border-top pt-4">
                <button type="button" class="wishlist-toggle-btn btn btn-outline-danger btn-sm rounded-pill px-4"
                    data-id="{{ $product->id }}">
                    <i class="fa-regular fa-heart me-1"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                    <i class="fa-solid fa-share-nodes"></i>
                </button>
            </div>
        </div>
    </div>
</div>
