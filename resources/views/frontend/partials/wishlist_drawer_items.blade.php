<div class="offcanvas-header border-bottom bg-light">
    <h5 class="fw-bold mb-0">My Wishlist ({{ count($products) }})</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>

<div class="offcanvas-body">
    @if (count($products) > 0)
        @foreach ($products as $product)
            <div class="d-flex align-items-center mb-3 p-2 border rounded shadow-sm bg-white">
                <img src="{{ $product->thumbnail }}" width="60" height="60" class="rounded me-3"
                    style="object-fit: cover;">
                <div class="flex-grow-1">
                    <h6 class="mb-0 small fw-bold">{{ $product->name }}</h6>
                    <span class="text-primary fw-bold small">৳{{ number_format($product->price) }}</span>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-link p-0 border-0 quick-view" data-id="{{ $product->id }}">
                        <i class="fa-solid fa-eye "></i>
                    </button>

                    <button class="btn btn-sm text-danger wishlist-toggle-btn" data-id="{{ $product->id }}">
                        <i class="fa-solid fa-trash-can "></i>
                    </button>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-5">
            <i class="fa-regular fa-heart fa-3x text-muted mb-3 opacity-25"></i>
            <p class="text-muted">Wishlist is empty!</p>
        </div>
    @endif
</div>
