@extends('frontend.layouts.app')
@section('content')
    <div class="row g-4 py-4" id="product-grid-container">
        <div class="col-12 text-center py-5" id="main-loader">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Loading premium electronics...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            "use strict";

            // LocalStorage Helpers
            const getStoredItems = (key) => JSON.parse(localStorage.getItem(key)) || [];
            const setStoredItems = (key, data) => localStorage.setItem(key, JSON.stringify(data));

            // Global AJAX Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Fetch Products on Page Load
            fetchProducts();

            function fetchProducts() {
                $.ajax({
                    url: "{{ route('api.products') }}",
                    method: "GET",
                    success: function(res) {
                        $('#main-loader').fadeOut(300, function() {
                            renderProducts(res);
                        });
                        updateNavCount();
                    },
                    error: function() {
                        $('#main-loader').html(
                            '<p class="text-danger">Failed to load products from server.</p>');
                    }
                });
            }

            // Dynamic Product Grid Rendering
            function renderProducts(data) {
                let html = '';
                let wishlist = getStoredItems('user_wishlist');
                let cart = getStoredItems('user_cart');

                if (!data.products || data.products.length === 0) {
                    html =
                        '<div class="col-12 text-center py-5"><p class="alert alert-info">No products found.</p></div>';
                } else {
                    data.products.forEach(product => {
                        let isWishlisted = wishlist.includes(product.id.toString());
                        let isInCart = cart.some(item => item.id == product.id.toString());

                        html += `
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 product-card shadow-sm border-0 position-relative quick-view"
                             data-id="${product.id}" style="cursor: pointer;">
                            <button type="button" class="wishlist-toggle-btn position-absolute top-0 end-0 m-2 border-0 shadow-sm bg-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 35px; height: 35px; z-index: 20;" data-id="${product.id}">
                                <i class="${isWishlisted ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart text-dark'}"></i>
                            </button>
                            <div class="text-center">
                                <img src="${product.thumbnail}" class="card-img-top p-3" style="height: 180px; object-fit: contain;" alt="${product.name}">
                            </div>
                            <div class="card-body pt-0">
                                <h5 class="fw-bold mb-1 text-dark text-truncate">${product.name}</h5>
                                <p class="text-muted small mb-2">${product.short_description ? product.short_description.substring(0, 35) + '...' : 'High quality product'}</p>
                                <div class="d-flex justify-content-between mt-3 py-3 border-top">
                                    <span class="h6 fw-bold text-primary mb-0">৳${new Intl.NumberFormat().format(product.price)}</span>
                                    <button type="button" class="add-to-cart-btn btn btn-sm rounded-pill px-3 shadow-sm ${isInCart ? 'btn-success disabled' : 'btn-soft-primary'}"
                                        data-id="${product.id}" ${isInCart ? 'disabled' : ''}>
                                        <i class="${isInCart ? 'fa-solid fa-check' : 'fa-solid fa-plus'} me-1"></i>
                                        ${isInCart ? 'In Cart' : 'Add to Cart'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    });
                }
                $('#product-grid-container').html(html);
            }

            // Quick View Modal Logic
            $(document).on('click', '.quick-view', function(e) {
                if ($(e.target).closest('.wishlist-toggle-btn, .add-to-cart-btn').length) return;
                let id = $(this).data('id');
                let $modalContent = $('#product-modal-content');
                $modalContent.html(
                    `<div class="modal-body text-center py-5"><div class="spinner-border text-primary"></div></div>`
                );
                $('#productModal').modal('show');
                $.get(`/product-details/${id}`, function(res) {
                    if (res.status === 'success') {
                        $modalContent.hide().html(res.html).fadeIn(500);
                        let wishlist = getStoredItems('user_wishlist');
                        let cart = getStoredItems('user_cart');
                        if (wishlist.includes(id.toString())) $modalContent.find(
                            '.wishlist-toggle-btn i').removeClass('fa-regular').addClass(
                            'fa-solid text-danger');
                        if (cart.some(item => item.id == id.toString())) $modalContent.find(
                            '.add-to-cart-btn').html(
                            '<i class="fa-solid fa-check me-2"></i> In Cart').addClass(
                            'btn-success disabled').attr('disabled', true);
                    }
                });
            });

            // Wishlist Management
            $(document).on('click', '.wishlist-toggle-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let id = $(this).data('id').toString();
                let wishlist = getStoredItems('user_wishlist');
                let isAdding = !wishlist.includes(id);

                if (isAdding) {
                    wishlist.push(id);
                } else {
                    wishlist = wishlist.filter(itemId => itemId !== id);
                }

                setStoredItems('user_wishlist', wishlist);

                syncWishlistUI(id, isAdding);
                updateNavCount();

                if ($('#wishlistDrawer').hasClass('show')) {
                    loadWishlistDrawer();
                }

                Toast.fire({
                    icon: 'success',
                    title: isAdding ? "Added to wishlist" : "Removed from wishlist"
                });
            });

            function syncWishlistUI(id, isAdded) {
                $(`.wishlist-toggle-btn[data-id="${id}"] i`).each(function() {
                    if (isAdded) {
                        $(this).removeClass('fa-regular text-dark').addClass('fa-solid text-danger');
                    } else {
                        $(this).removeClass('fa-solid text-danger').addClass('fa-regular text-dark');
                    }
                });
            }

            // Initial Page Sync
            setTimeout(() => {
                getStoredItems('user_wishlist').forEach(id => syncWishlistUI(id, true));
                updateNavCount();
            }, 500);


            // Add to cart section
            $(document).on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                let id = $(this).data('id').toString();
                let qty = parseInt($('#modal-qty').val()) || 1;
                let cart = getStoredItems('user_cart');
                if (!cart.some(item => item.id === id)) {
                    cart.push({
                        id: id,
                        qty: qty
                    });
                    setStoredItems('user_cart', cart);
                    $(`.add-to-cart-btn[data-id="${id}"]`).each(function() {
                        $(this).html('<i class="fa-solid fa-check me-1"></i> In Cart').removeClass(
                            'btn-soft-primary').addClass('btn-success disabled').attr(
                            'disabled', true);
                    });
                    updateNavCount();
                    if ($('#cartDrawer').hasClass('show')) loadCartDrawer();
                    Toast.fire({
                        icon: 'success',
                        title: 'Added to cart!'
                    });
                }
            });

            $(document).on('click', '.update-qty', function(e) {
                e.preventDefault();
                let id = $(this).data('id').toString();
                let action = $(this).data('action');
                let cart = getStoredItems('user_cart');
                let item = cart.find(i => i.id === id);
                if (item) {
                    if (action === 'plus') item.qty = parseInt(item.qty) + 1;
                    else if (action === 'minus' && item.qty > 1) item.qty = parseInt(item.qty) - 1;
                    setStoredItems('user_cart', cart);
                    loadCartDrawer();
                    updateNavCount();
                }
            });

            $(document).on('click', '.remove-cart-item', function(e) {
                e.preventDefault();
                let id = $(this).data('id').toString();
                let cart = getStoredItems('user_cart').filter(item => item.id !== id);
                setStoredItems('user_cart', cart);
                $(`.add-to-cart-btn[data-id="${id}"]`).html(
                    '<i class="fa-solid fa-plus me-1"></i> Add to Cart').removeClass(
                    'btn-success disabled').addClass('btn-soft-primary').attr('disabled', false);
                updateNavCount();
                loadCartDrawer();
            });

            $(document).on('click', '.view-cart-details', function(e) {
                e.preventDefault();
                let cartItem = getStoredItems('user_cart').find(i => i.id === $(this).data('id')
                    .toString());

                if (cartItem) {
                    let $modalContent = $('#product-modal-content');
                    // $('#cartDrawer').offcanvas('hide');
                    $modalContent.html(
                        '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>'
                    );
                    $('#productModal').modal('show');

                    $.post('/cart-item-details', {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        item: cartItem
                    }, function(res) {
                        if (res.status === 'success') {
                            $modalContent.html(res.html);

                            let qty = parseInt(cartItem.qty);
                            let price = parseFloat(res.product.price);
                            let subtotal = qty * price;

                            $('.display-qty').text(qty);
                            $('.display-price').text('৳' + price.toLocaleString());
                            $('.display-subtotal').text('৳' + subtotal.toLocaleString());
                        }
                    });
                }
            });


            // Common Nav Helpers and Drawer Loaders
            function updateNavCount() {
                $('.wishlist-count-nav').text(getStoredItems('user_wishlist').length);
                $('.cart-count-nav').text(getStoredItems('user_cart').length);
            }

            window.loadWishlistDrawer = function() {
                let ids = getStoredItems('user_wishlist');
                $.get("{{ route('wishlist.index') }}", {
                    ids: ids
                }, function(res) {
                    $('#wishlist-drawer-content').html(res.html);
                });
            };

            window.loadCartDrawer = function() {
                let items = getStoredItems('user_cart');
                $.get("{{ route('cart.index') }}", {
                    items: items
                }, function(res) {
                    $('#cart-drawer-content').html(res.html);
                });
            };

            $(document).on('click', '.trigger-wishlist', function(e) {
                e.preventDefault();
                new bootstrap.Offcanvas('#wishlistDrawer').show();
                loadWishlistDrawer();
            });
            $(document).on('click', '.trigger-cart', function(e) {
                e.preventDefault();
                new bootstrap.Offcanvas('#cartDrawer').show();
                loadCartDrawer();
            });

            // Checkout Process Logic
            $(document).on('click', '.proceed-checkout', function(e) {
                e.preventDefault();
                let cart = getStoredItems('user_cart');
                if (cart.length === 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Your cart is empty!'
                    });
                    return;
                }

                let fullCartDetails = [];
                $('.cart-item-card').each(function() {
                    fullCartDetails.push({
                        name: $(this).find('h6').text().trim(),
                        price: parseFloat($(this).find('.text-primary').text().replace(
                            /[^\d.]/g, '')),
                        thumbnail: $(this).find('img').attr('src'),
                        qty: parseInt($(this).find('input').val()) || 1
                    });
                });

                let subtotal = fullCartDetails.reduce((total, item) => total + (item.price * item.qty), 0);

                $.ajax({
                    url: "{{ route('checkout.details') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        items: fullCartDetails
                    },
                    beforeSend: function() {
                        $('.proceed-checkout').html(
                            '<span class="spinner-border spinner-border-sm"></span> Loading...'
                        );
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#checkout-items-inner').html(res.html);
                            const freeThreshold = 2000;
                            const deliveryCharge = (subtotal >= freeThreshold) ? 0 : 60;
                            let total = subtotal + deliveryCharge;

                            $('#chk-subtotal').text('৳' + new Intl.NumberFormat().format(
                                subtotal));
                            $('#chk-total').text('৳' + new Intl.NumberFormat().format(total));

                            let deliveryText = (deliveryCharge === 0) ?
                                '<span class="text-success fw-bold">FREE</span>' : '৳' +
                                deliveryCharge;
                            $('#chk-delivery-charge').html(deliveryText);

                            bootstrap.Offcanvas.getInstance('#cartDrawer')?.hide();
                            setTimeout(() => {
                                new bootstrap.Offcanvas('#checkoutDrawer').show();
                            }, 400);
                        }
                    },
                    complete: function() {
                        $('.proceed-checkout').html(
                            'Checkout <i class="fa-solid fa-arrow-right ms-2"></i>');
                    }
                });
            });

            // Final Order Confirmation Logic
            $(document).on('submit', '#checkoutForm', function(e) {
                e.preventDefault();
                let orderData = {
                    order_id: 'ORD-' + Math.floor(Math.random() * 900000 + 100000),
                    customer_name: $('#cust_name').val(),
                    amount: $('#chk-total').text(),
                    date: new Date().toLocaleDateString('en-GB'),
                    status: 'Pending'
                };

                let orders = getStoredItems('user_orders');
                orders.unshift(orderData);
                setStoredItems('user_orders', orders);

                localStorage.removeItem('user_cart');
                updateNavCount();
                $('.add-to-cart-btn').html('<i class="fa-solid fa-plus me-1"></i> Add to Cart').removeClass(
                    'btn-success disabled').addClass('btn-soft-primary').attr('disabled', false);
                bootstrap.Offcanvas.getInstance('#checkoutDrawer')?.hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Order Successful!',
                    text: 'Order ID: ' + orderData.order_id,
                    timer: 3000,
                    showConfirmButton: false
                });
                this.reset();
            });

            // Order History Rendering
            function renderOrderHistory() {
                let orders = getStoredItems('user_orders');
                let html = '';
                if (orders.length === 0) {
                    html =
                        `<div class="text-center py-5"><i class="fa-solid fa-box-open fa-3x text-light mb-3"></i><p class="text-muted">No orders found yet!</p></div>`;
                } else {
                    orders.forEach(order => {
                        html += `
            <div class="card border rounded-4 mb-3 bg-white shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-primary">${order.order_id}</span>
                        <span class="badge bg-success-subtle text-success rounded-pill">${order.status}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">${order.date}</span>
                        <span class="fw-bold text-dark">${order.amount}</span>
                    </div>
                </div>
            </div>`;
                    });
                }
                $('#order-list-render').html(html);
            }

            $(document).on('click', '.trigger-orders', function(e) {
                e.preventDefault();
                $.get("{{ route('order.index') }}", function(res) {
                    $('#order-history-drawer-content').html(res);
                    new bootstrap.Offcanvas('#orderHistoryDrawer')
                        .show();
                    renderOrderHistory();
                });
            });
        });



        // Custom CSS Injection
        $("<style>").prop("type", "text/css").html(`
        .cart-item-card { transition: all 0.3s ease; }
        .cart-item-card:hover { transform: translateY(-2px); border-color: #0d6efd !important; }
        .update-qty:active { background: #e9ecef; }
    `).appendTo("head");
    </script>
@endpush
