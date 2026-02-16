@extends('frontend.layouts.app')

@section('content')
    <div class="container py-5">
        <h3 class="fw-bold mb-4">Shopping Cart</h3>

        @if (session('cart'))
            <div class="table-responsive bg-white shadow-sm rounded-4 p-4">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted">
                            <th>Product</th>
                            <th>Price</th>
                            <th width="150">Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0 @endphp
                        @foreach (session('cart') as $id => $details)
                            @php $total += $details['price'] * $details['quantity'] @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset($details['thumbnail']) }}" width="50" class="rounded me-3">
                                        <span class="fw-bold">{{ $details['name'] }}</span>
                                    </div>
                                </td>
                                <td>৳{{ number_format($details['price'], 0) }}</td>
                                <td>
                                    <form action="{{ route('cart.update', $id) }}" method="POST">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="quantity" value="{{ $details['quantity'] }}"
                                                class="form-control text-center" min="1">
                                            <button class="btn btn-outline-primary" type="submit">Update</button>
                                        </div>
                                    </form>
                                </td>
                                <td class="fw-bold">৳{{ number_format($details['price'] * $details['quantity'], 0) }}</td>
                                <td>
                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-4">
                    <div class="text-end">
                        <h5 class="text-muted mb-1">Subtotal:</h5>
                        <h3 class="fw-bold text-primary">৳{{ number_format($total, 0) }}</h3>

                        @php
                            $first_id = array_key_first(session('cart'));
                        @endphp

                        <a href="{{ route('checkout', ['product_id' => $first_id]) }}"
                            class="btn btn-primary btn-lg rounded-pill px-5 mt-3 shadow-sm">
                            Checkout
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fa-solid fa-cart-shopping fs-1 text-muted mb-3"></i>
                <h4>Your cart is empty!</h4>
                <a href="{{ route('index') }}" class="btn btn-primary mt-3 rounded-pill">Shop Now</a>
            </div>
        @endif
    </div>
@endsection
