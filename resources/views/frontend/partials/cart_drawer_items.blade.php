<div class="offcanvas-header border-bottom bg-light">
    <h5 class="fw-bold mb-0">Your Shopping Cart ({{ count($products) }})</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body d-flex flex-column p-0">
    @php
        $subtotal = 0;
        $delivery_charge = 60;
        $free_delivery_threshold = 2000;
    @endphp

    @if (count($products) > 0)
        <div class="flex-grow-1 overflow-auto p-3">
            {{-- Free Delivery Badge --}}
            <div class="alert alert-primary border-0 rounded-4 p-2 mb-3 text-center small fw-bold">
                <i class="fa-solid fa-truck-fast me-2"></i>
                @php
                    foreach ($products as $p) {
                        $subtotal += $p->price * $p->cart_qty;
                    }
                @endphp

                @if ($subtotal >= $free_delivery_threshold)
                    <span class="text-success">Congratulations! You've unlocked FREE Delivery.</span>
                @else
                    Buy ৳{{ number_format($free_delivery_threshold - $subtotal) }} more for <span
                        class="text-primary">FREE Delivery</span>.
                @endif
            </div>

            @foreach ($products as $product)
                @php $itemTotal = $product->price * $product->cart_qty; @endphp
                <div class="cart-item-card d-flex align-items-center mb-3 p-3 border rounded-4 bg-white shadow-sm">
                    <div class="position-relative">
                        <img src="{{ $product->thumbnail }}" width="70" height="70" class="rounded-3 border"
                            style="object-fit: cover;">
                    </div>

                    <div class="flex-grow-1 ms-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1 fw-bold text-dark small text-truncate" style="max-width: 140px;">
                                {{ $product->name }}</h6>

                            <div class=" d-flex gap-3">
                                <button class="btn btn-link text-primary p-0 border-0 view-cart-details"
    data-id="{{ $product->id }}"
    title="View Cart Details">
    <i class="fa-solid fa-eye shadow-sm p-1 rounded bg-light"></i>
</button>

                                <button class="btn btn-link text-danger p-0 border-0 remove-cart-item"
                                    data-id="{{ $product->id }}">
                                    <i class="fa-solid fa-trash-can shadow-sm p-1 rounded bg-light"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-primary fw-bold mb-2 small">৳{{ number_format($product->price) }}</div>

                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <div class="input-group input-group-sm border rounded-pill overflow-hidden"
                                style="width: 100px;">
                                <button class="btn btn-light border-0 py-0 update-qty" data-id="{{ $product->id }}"
                                    data-action="minus"><i class="fa-solid fa-minus fa-xs"></i></button>
                                <input type="text" class="form-control border-0 text-center fw-bold bg-white small"
                                    value="{{ $product->cart_qty }}" readonly>
                                <button class="btn btn-light border-0 py-0 update-qty" data-id="{{ $product->id }}"
                                    data-action="plus"><i class="fa-solid fa-plus fa-xs"></i></button>
                            </div>
                            <div class="fw-bold text-dark small">৳{{ number_format($itemTotal) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Calculations Section --}}
        <div class="cart-footer p-4 bg-white border-top shadow-lg mt-auto">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Subtotal</span>
                <span class="text-dark fw-bold small">৳{{ number_format($subtotal) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted small">Delivery Charge</span>
                @if ($subtotal >= $free_delivery_threshold)
                    <span class="text-success fw-bold small"><del class="text-muted">৳{{ $delivery_charge }}</del>
                        FREE</span>
                    @php $delivery_charge = 0; @endphp
                @else
                    <span class="text-dark fw-bold small">৳{{ number_format($delivery_charge) }}</span>
                @endif
            </div>

            <hr class="my-3 opacity-10">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Total Amount</h5>
                <h4 class="fw-bold mb-0 text-primary">৳{{ number_format($subtotal + $delivery_charge) }}</h4>
            </div>

            <div class="d-grid gap-2 d-flex justify-content-end">
                <button type="button"
                    class="btn btn-primary rounded-pill fw-bold shadow-sm px-4 py-2 proceed-checkout">
                    Checkout <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    @else
        <div class="h-100 d-flex flex-column align-items-center justify-content-center py-5 px-4">
            <i class="fa-solid fa-cart-shopping fa-4x text-light mb-4"></i>
            <h5 class="fw-bold text-dark">Your cart is empty!</h5>
            <button class="btn btn-primary rounded-pill px-5 mt-3" data-bs-dismiss="offcanvas">Shop Now</button>
        </div>
    @endif
</div>
