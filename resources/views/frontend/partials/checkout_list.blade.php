
@php
    $subtotal = 0;
    $delivery_charge = 60; 
    $free_delivery_threshold = 2000;

    foreach ($products as $p) {
        $subtotal += $p['price'] * $p['qty'];
    }

    if ($subtotal >= $free_delivery_threshold) {
        $delivery_charge = 0;
    }
@endphp

@if(count($products) > 0)
    <div class="checkout-items-list">

        @foreach($products as $item)
            <div class="d-flex align-items-center mb-3 p-2 bg-white rounded-3 border shadow-sm">
                <div class="flex-shrink-0 bg-light rounded" style="width: 50px; height: 50px;">
                    <img src="{{ $item['thumbnail'] }}" class="img-fluid p-1" style="height: 100%; object-fit: contain;">
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-0 small fw-bold text-dark text-truncate" style="max-width: 200px;">{{ $item['name'] }}</h6>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Qty: {{ $item['qty'] }}</span>
                        <span class="fw-bold text-primary">৳{{ number_format($item['price'] * $item['qty']) }}</span>
                    </div>
                </div>
            </div>
        @endforeach

        <input type="hidden" id="calc-subtotal" value="{{ $subtotal }}">
        <input type="hidden" id="calc-delivery" value="{{ $delivery_charge }}">
        <input type="hidden" id="calc-total" value="{{ $subtotal + $delivery_charge }}">
    </div>
@else
    <p class="text-center text-muted">No items in checkout.</p>
@endif

