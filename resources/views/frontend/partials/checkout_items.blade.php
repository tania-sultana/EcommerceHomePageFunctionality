@if(count($products) > 0)
    <div class="checkout-items-list">
        @foreach($products as $item)
            <div class="checkout-item-row d-flex align-items-center mb-3 p-2 bg-white rounded-3 border shadow-sm"
                 data-id="{{ $item['id'] }}" data-price="{{ $item['price'] }}">
                <div class="flex-shrink-0 bg-light rounded" style="width: 50px; height: 50px;">
                    <img src="{{ $item['thumbnail'] }}" class="img-fluid p-1" style="height: 100%; object-fit: contain;">
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-0 small fw-bold text-dark text-truncate" style="max-width: 150px;">{{ $item['name'] }}</h6>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <div class="d-flex align-items-center border rounded-pill px-2 bg-light">
                            <button type="button" class="btn btn-sm p-0 border-0 chk-qty-minus"><i class="fa-solid fa-minus fa-xs"></i></button>
                            <input type="text" class="chk-qty-input border-0 bg-transparent text-center fw-bold"
                                   value="{{ $item['qty'] }}" readonly style="width: 30px; font-size: 0.8rem;">
                            <button type="button" class="btn btn-sm p-0 border-0 chk-qty-plus"><i class="fa-solid fa-plus fa-xs"></i></button>
                        </div>
                        <span class="fw-bold text-primary small item-total-price">৳{{ number_format($item['price'] * $item['qty']) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-center text-muted">No items in checkout.</p>
@endif
