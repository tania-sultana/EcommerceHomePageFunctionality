<div class="modal-body p-4 pt-5">
    <div class="row g-4 align-items-center">
        <div class="col-md-5 text-center">
            <img src="{{ asset($product->thumbnail) }}" class="img-fluid rounded-4 shadow-sm" style="max-height: 300px;">
        </div>
        <div class="col-md-7">
            <h2 class="fw-bold text-dark">{{ $product->name }}</h2>

            {{-- Price Display --}}
            <h3 class="text-primary fw-bold mb-3 display-price"></h3>

            <div class="p-3 bg-light rounded-4 border">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Quantity:</span>
                    <span class="fw-bold display-qty"></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Subtotal:</span>
                    <span class="fw-bold text-success display-subtotal" style="font-size: 1.3rem;"></span>
                </div>
            </div>

            <button class="btn btn-success w-100 rounded-pill fw-bold mt-4 disabled">
                <i class="fa-solid fa-check me-2"></i> Already In Cart
            </button>
        </div>
    </div>
</div>
