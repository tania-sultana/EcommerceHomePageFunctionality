@extends('frontend.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <h3 class="fw-bold mb-4"><i class="fa-solid fa-bag-shopping me-2 text-primary"></i>Checkout</h3>

                <form action="{{ route('order.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                <h5 class="fw-bold mb-4 border-bottom pb-2">
                                    <i class="fa-solid fa-truck text-primary me-2"></i>Shipping Details
                                </h5>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">Full Name</label>
                                        <input type="text" name="name"
                                            class="form-control rounded-pill bg-light border-0 px-3 py-2"
                                            placeholder="Enter your name" required>
                                        @error('name')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">Phone Number</label>
                                        <input type="text" name="phone"
                                            class="form-control rounded-pill bg-light border-0 px-3 py-2"
                                            placeholder="017XXXXXXXX" required>
                                        @error('phone')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">Full Address</label>
                                        <textarea name="address" class="form-control rounded-4 bg-light border-0 px-3 py-2" rows="4"
                                            placeholder="House no, Street, Area, District..." required></textarea>
                                        @error('address')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4 p-3 rounded-4 bg-light border-start border-primary border-4">
                                    <p class="mb-0 small text-muted">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Currently, we only support <strong>Cash on Delivery (COD)</strong>.
                                        Please pay the amount to the delivery agent.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark text-white h-100">
                                <h5 class="fw-bold mb-3 border-bottom pb-2 text-white">Order Summary</h5>

                                <div class="cart-items-scroll pe-1" style="max-height: 300px; overflow-y: auto;">
                                    @php $total = 0; @endphp
                                    @foreach (session('cart') as $item)
                                        @php $total += $item['price'] * $item['quantity']; @endphp
                                        <div class="d-flex justify-content-between mb-2 small align-items-center">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset($item['thumbnail']) }}" width="40"
                                                    class="rounded me-2 border border-secondary">
                                                <span>{{ Str::limit($item['name'], 20) }} (x{{ $item['quantity'] }})</span>
                                            </div>
                                            <span
                                                class="fw-bold">৳{{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <hr class="bg-light opacity-25 mt-3">

                                <div class="d-flex justify-content-between mb-2 small opacity-75">
                                    <span>Subtotal:</span>
                                    <span>৳{{ number_format($total, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 small opacity-75">
                                    <span>Delivery Fee:</span>
                                    <span class="text-success">Free</span>
                                </div>

                                <div class="d-flex justify-content-between h5 fw-bold mb-4">
                                    <span>Total Amount:</span>
                                    <span class="text-primary">৳{{ number_format($total, 0) }}</span>
                                </div>

                                <button type="submit"
                                    class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm">
                                    PLACE ORDER NOW
                                </button>

                                <div class="text-center mt-3">
                                    <small class="text-secondary">
                                        <i class="fa-solid fa-lock me-1"></i> Secure Checkout
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
