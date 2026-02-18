<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Shop | Premium Electronics</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/frontstyle.css') }}">

    @stack('styles')
</head>

<body>

    @include('frontend.layouts.header')

    <main class="container min-vh-100">
        @yield('content')
    </main>

    {{-- details modal --}}
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                    aria-label="Close" style="z-index: 1055;"></button>

                <div id="product-modal-content">
                </div>
            </div>
        </div>
    </div>

    {{-- cart offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel">

        <div class="offcanvas-body p-0" id="cart-drawer-content">
        </div>
    </div>

    {{-- wishlist offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="wishlistDrawer">

        <div class="offcanvas-body p-0" id="wishlist-drawer-content">
        </div>
    </div>
    {{-- order history --}}
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="orderHistoryDrawer"
        style="width: 400px;">
        <div class="offcanvas-header bg-dark text-white py-4">
            <h5 class="offcanvas-title fw-bold">
                <i class="fa-solid fa-clock-rotate-left me-2"></i>Order History
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0" id="order-history-drawer-content">
        </div>
    </div>

  
   {{-- order details modal --}}
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" id="orderDetailModalContent">
            </div>
    </div>
</div>

    {{-- checkout offcanvas --}}
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="checkoutDrawer" style="width: 480px;">
        <div class="offcanvas-header bg-primary text-white py-4">
            <h5 class="offcanvas-title fw-bold">
                <i class="fa-solid fa-shield-check me-2"></i>Secure Checkout
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-0">
            <form id="checkoutForm" class="d-flex flex-column h-100">
                <div class="p-4 flex-grow-1 overflow-auto">
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark border-start border-primary border-4 ps-2 mb-3">Shipping Details
                        </h6>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Full Name</label>
                            <input type="text" id="cust_name"
                                class="form-control bg-light border-0 px-3 py-2 rounded-3" placeholder="Enter name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Phone Number</label>
                            <input type="text" id="cust_phone"
                                class="form-control bg-light border-0 px-3 py-2 rounded-3" placeholder="017XXXXXXXX"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Full Address</label>
                            <textarea id="cust_address" class="form-control bg-light border-0 px-3 py-2 rounded-3" rows="2"
                                placeholder="Address..." required></textarea>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark border-start border-primary border-4 ps-2 mb-3">Order Summary</h6>
                        <div id="checkout-items-inner" class="bg-light rounded-4 p-3 shadow-sm">
                        </div>
                    </div>

                    <div class="alert border-0 rounded-4 d-flex align-items-center mb-0"
                        style="background-color: #e7f1ff; color: #0c41ff;">
                        <i class="fa-solid fa-truck-fast h4 mb-0 me-3"></i>
                        <div>
                            <strong class="d-block small">Cash on Delivery</strong>
                            <span style="font-size: 0.75rem;">Product hate peye taka porishodh korun.</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-white border-top shadow-lg">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Subtotal:</span>
                        <span class="fw-bold text-dark" id="chk-subtotal">৳0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted small">Delivery Charge:</span>
                        <span class="text-success fw-bold">FREE</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h5 fw-bold text-dark mb-0">Total Amount:</span>
                        <span class="h4 fw-bold text-primary mb-0" id="chk-total">৳0</span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm">
                        CONFIRM ORDER <i class="fa-solid fa-circle-check ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Toast.fire({
                    icon: 'warning',
                    title: "{{ $error }}"
                });
            @endforeach
        @endif
    </script>

    @stack('scripts')
</body>

</html>
