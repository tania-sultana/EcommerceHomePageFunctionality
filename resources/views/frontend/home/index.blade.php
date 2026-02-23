@extends('frontend.layouts.app')

@section('content')
    {{-- ------------ Main Product Grid Section ------------ --}}
    <div class="container py-5">
        <div class="row g-4" id="product-grid-container">
            <div class="col-12 text-center py-5" id="main-loader">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Loading our catalog...</p>
            </div>
        </div>
    </div>

    {{-- ------------ Wishlist Offcanvas Section ------------ --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="wishlistOffcanvas">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold">My Wishlist</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="wishlist-items-container"></div>
    </div>

    {{-- ------------ Product Details Modal Section ------------ --}}
    <div class="modal fade" id="productDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-body p-0" id="modal-loader-area"></div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="orderHistoryOffcanvas" style="width: 400px; border-radius: 20px 0 0 20px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>My Orders</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3" id="order-list-container" style="background: #f8f9fa;">
        </div>
</div>

    {{-- ------------ Dynamic UI Containers ------------ --}}
    <div id="cart-ui-wrapper"></div>
    <div id="checkout-ui-wrapper"></div>
    <div id="order-ui-wrapper"></div> {{-- Order container added --}}
@endsection

@push('scripts')
    <script>
        let allProductsData = [];

        $(document).ready(function() {
            renderCartStructure();
            renderCheckoutStructure();
            renderOrderStructure(); // Initialization
            loadHomeProducts();

            $(document).on('click', '.trigger-cart', function(e) {
                e.preventDefault();
                updateCartUI();
                new bootstrap.Offcanvas(document.getElementById('cartOffcanvas')).show();
            });

            // Trigger Orders from Nav
            $(document).on('click', '.trigger-orders', function(e) {
                e.preventDefault();
                fetchOrderHistory();
                new bootstrap.Offcanvas(document.getElementById('orderHistoryOffcanvas')).show();
            });
        });

        // ==========================================
        // 1. HOME PAGE & PRODUCT GRID SECTION
        // ==========================================
        function loadHomeProducts() {
            $.ajax({
                url: "{{ route('index') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $('#main-loader').hide();
                    allProductsData = response.data;
                    renderProductGrid();
                    updateWishlistUI();
                    updateCartUI();
                }
            });
        }

        function renderProductGrid() {
            let wishlist = getWishlist();
            let cart = getCart();
            let html = '';

            allProductsData.forEach(function(product) {
                let isWish = wishlist.includes(product.id);
                let inCart = cart.some(item => item.id === product.id);
                let cartBtnText = inCart ? '<i class="fa-solid fa-check me-1"></i> In Cart' :
                    '<i class="fa-solid fa-plus me-1"></i> Add to cart';
                let cartBtnClass = inCart ? 'btn-success disabled' : 'btn-soft-primary text-primary';

                let displayDescription = product.short_description.length > 40 ?
                    product.short_description.substring(0, 40) + '...' :
                    product.short_description;

                let seeMoreLink = product.short_description.length > 40 ?
                    `<a href="#" class="text-primary text-decoration-none fw-bold" style="font-size: 13px;">see more</a>` :
                    '';

                html += `
                <div class="col-6 col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden premium-card p-2 pb-3" onclick="showProductDetails(${product.id})" style="cursor: pointer;">
                        <div class="position-relative w-100 overflow-hidden" style="height: 200px;">
                            <button class="wishlist-btn-modern shadow-sm wish-btn-${product.id}" onclick="event.stopPropagation(); toggleWishlist(this, ${product.id})">
                                <i class="${isWish ? 'fa-solid' : 'fa-regular'} fa-heart text-danger"></i>
                            </button>
                            <img src="${product.thumbnail}" class="w-100 h-100 p-2" style="object-fit: contain;">
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <h5 class="fw-bold mb-1 text-dark">${product.name}</h5>
                            <p class="text-muted mb-3 flex-grow-1">${displayDescription} ${seeMoreLink}</p>
                            <div class="mt-auto d-flex justify-content-between align-items-center border-top pt-3">
                                <span class="text-primary fw-bolder fs-5">৳ ${product.price}</span>
                                <button class="btn ${cartBtnClass} btn-sm rounded-pill px-3 py-1 shadow-sm fw-bold add-btn" onclick="event.stopPropagation(); addToCart(${product.id})">
                                    ${cartBtnText}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            $('#product-grid-container').html(html);
        }

        // ==========================================
        // 2. PRODUCT DETAILS SECTION
        // ==========================================
        function showProductDetails(id) {
            $('.offcanvas').each(function() {
                let inst = bootstrap.Offcanvas.getInstance(this);
                if (inst) inst.hide();
            });
            $('#productDetailsModal').modal('show');

            $.get("/product/" + id, function(res) {
                let p = res.data;
                let cart = getCart();
                let inCart = cart.some(item => item.id === p.id);
                let wishlist = getWishlist();

                let content = `
                    <div class="row g-0">
                        <div class="col-md-5 d-flex align-items-center justify-content-center p-4">
                            <div class="p-3 rounded-4 bg-light shadow-sm text-center w-100">
                                <img src="${p.thumbnail}" class="img-fluid" style="max-height: 350px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="col-md-7 p-4 p-lg-5 position-relative">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                            <h2 class="fw-bold mb-2 mt-3 text-dark">${p.name}</h2>
                            <div class="text-warning mb-2"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i></div>
                            <h3 class="text-primary fw-bold mb-3">৳ <span id="modal-price-display">${p.price}</span></h3>
                            <p class="text-muted mb-4 small">${p.short_description || ''}</p>

                            <div class="d-flex align-items-center gap-2 mb-4">
                                <div class="d-flex align-items-center border rounded-pill bg-light px-2" style="height: 40px;">
                                    <button class="btn btn-sm border-0" onclick="updateModalQty(-1, ${p.price})">-</button>
                                    <input type="number" id="modal-qty" class="border-0 bg-transparent text-center fw-bold" value="1" readonly style="width: 40px;">
                                    <button class="btn btn-sm border-0" onclick="updateModalQty(1, ${p.price})">+</button>
                                </div>
                                <button class="btn ${inCart ? 'btn-success disabled' : 'btn-primary'} flex-grow-1 rounded-pill py-2 fw-bold"
                                    onclick="addToCartFromModal(${p.id})">
                                    ${inCart ? 'In Cart' : 'Add to Cart'}
                                </button>
                            </div>

                            <div class="d-flex gap-2 border-top pt-4">
                                <button class="btn btn-light rounded-circle border p-2" onclick="toggleWishlist(this, ${p.id})" style="width: 45px; height: 40px;">
                                    <i class="${wishlist.includes(p.id) ? 'fa-solid' : 'fa-regular'} fa-heart text-danger"></i>
                                </button>
                                <button class="btn btn-light rounded-circle border p-2" style="width: 40px; height: 40px;"><i class="fa-solid fa-share-nodes text-muted"></i></button>
                            </div>
                        </div>
                    </div>`;
                $('#modal-loader-area').html(content);
            });
        }

        function updateModalQty(delta, unitPrice) {
            let qtyInput = $('#modal-qty');
            let newQty = parseInt(qtyInput.val()) + delta;
            if (newQty < 1) newQty = 1;
            qtyInput.val(newQty);
            $('#modal-price-display').text(unitPrice * newQty);
        }

        function addToCartFromModal(id) {
            let qty = $('#modal-qty').val();
            addToCart(id, qty);
            $('#productDetailsModal').modal('hide');
        }

        // ==========================================
        // 3. WISHLIST SECTION
        // ==========================================
        function getWishlist() {
            return JSON.parse(localStorage.getItem('my_wishlist')) || [];
        }

        function toggleWishlist(btn, productId) {
            let wishlist = getWishlist();
            let index = wishlist.indexOf(productId);
            let action = (index === -1) ? 'add' : 'remove';

            if (action === 'add') wishlist.push(productId);
            else wishlist.splice(index, 1);

            localStorage.setItem('my_wishlist', JSON.stringify(wishlist));

            renderProductGrid();
            updateWishlistUI();

            let modalHeart = $('#productDetailsModal').find('i.fa-heart');
            if (modalHeart.length) {
                if (action === 'add') {
                    modalHeart.removeClass('fa-regular').addClass('fa-solid');
                } else {
                    modalHeart.removeClass('fa-solid').addClass('fa-regular');
                }
            }

            $.post(`/product-toggle/${productId}`, {
                _token: "{{ csrf_token() }}",
                action: action
            });
        }

        function updateWishlistUI() {
            let ids = getWishlist();
            $('.wishlist-count-nav').text(ids.length);
            let container = $('#wishlist-items-container');
            if (ids.length === 0) return container.html('<div class="text-center py-5 text-muted">Wishlist empty!</div>');

            let html = '';
            allProductsData.filter(p => ids.includes(p.id)).forEach(product => {
                html += `
                <div class="d-flex align-items-center gap-3 mb-3 p-3 border rounded-4 bg-white shadow-sm">
                    <img src="${product.thumbnail}" style="width: 50px; height: 50px; object-fit: contain;" class="bg-light rounded p-1">
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold small">${product.name}</h6>
                        <span class="text-primary fw-bold small">৳ ${product.price}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light text-primary border rounded-circle"
                                onclick="showProductDetails(${product.id})"
                                style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger border rounded-circle"
                                onclick="toggleWishlist(null, ${product.id})"
                                style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>`;
            });
            container.html(html);
        }

        // ==========================================
        // 4. CART SECTION
        // ==========================================
        function renderCartStructure() {
            let html = `
            <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" style="width: 400px; border-radius: 20px 0 0 20px;">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold">Shopping Cart <span id="cart-count-title" class="text-muted small"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column p-0">
                    <div id="free-delivery-banner" class="p-2 text-center small fw-bold" style="background: #e7f1ff; color: #0d6efd; display:none;"></div>
                    <div class="flex-grow-1 p-3" id="cart-items-container" style="background: #ffffff;"></div>
                </div>
                <div class="offcanvas-footer p-4 border-top bg-white" id="cart-footer" style="display:none;">
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Subtotal</span>
                        <span class="fw-bold">৳ <span id="cart-subtotal">0</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Delivery Charge</span>
                        <span class="fw-bold" id="delivery-status">৳ 60</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 align-items-center">
                        <h4 class="fw-bold mb-0">Total</h4>
                        <h4 class="fw-bold text-primary mb-0">৳ <span id="cart-total-display">0</span></h4>
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill fw-bold py-3 fs-5 shadow-sm" onclick="showCheckout()">
                        Proceed to Checkout <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <div class="modal fade" id="cartItemQuickViewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-5 border-0 shadow-lg">
                        <div class="modal-body p-0" id="cart-quickview-content"></div>
                    </div>
                </div>
            </div>`;
            $('#cart-ui-wrapper').html(html);
        }

        function getCart() {
            return JSON.parse(localStorage.getItem('my_cart')) || [];
        }

        function addToCart(productId, qty = 1) {
            let cart = getCart();
            let existingItem = cart.find(item => item.id === productId);
            if (!existingItem) {
                cart.push({
                    id: productId,
                    qty: parseInt(qty)
                });
                localStorage.setItem('my_cart', JSON.stringify(cart));
                renderProductGrid();
                updateCartUI();
                Toast.fire({
                    icon: 'success',
                    title: 'Added to cart'
                });
            }
        }

        function updateQty(id, delta) {
            let cart = getCart();
            let item = cart.find(i => i.id === id);
            if (item) {
                item.qty += delta;
                if (item.qty < 1) item.qty = 1;
                localStorage.setItem('my_cart', JSON.stringify(cart));
                updateCartUI();
            }
        }

        function removeFromCart(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "Remove this item from cart?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete!"
            }).then((result) => {
                if (result.isConfirmed) {
                    let cart = getCart().filter(i => i.id !== id);
                    localStorage.setItem('my_cart', JSON.stringify(cart));
                    updateCartUI();
                    renderProductGrid();
                    Toast.fire({
                        icon: 'success',
                        title: 'Removed from cart'
                    });
                }
            });
        }

        function updateCartUI() {
            let cart = getCart();
            $('.cart-count-nav').text(cart.length);
            $('#cart-count-title').text('(' + cart.length + ')');
            let container = $('#cart-items-container');
            let subtotal = 0;
            let delivery = 60;
            let freeDeliveryThreshold = 2000;

            if (cart.length === 0) {
                container.html(
                    '<div class="text-center py-5 text-muted"><i class="fa-solid fa-cart-shopping mb-3 fs-1 opacity-25"></i><br>Your cart is empty!</div>'
                );
                $('#cart-footer').hide();
                $('#free-delivery-banner').hide();
                return;
            }

            let html = '';
            cart.forEach(item => {
                let p = allProductsData.find(x => x.id === item.id);
                if (p) {
                    let totalItemPrice = p.price * item.qty;
                    subtotal += totalItemPrice;
                    html += `
                    <div class="p-3 mb-3 bg-white border rounded-4 shadow-sm position-relative">
                        <div class="d-flex align-items-center gap-3">
                            <img src="${p.thumbnail}" style="width: 70px; height: 70px; object-fit: contain;" class="bg-light p-1 rounded">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">${p.name}</h6>
                                <span class="text-primary fw-bold">৳ ${p.price}</span>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <div class="d-flex align-items-center border rounded-pill bg-light">
                                        <button class="btn btn-sm border-0 px-2" onclick="updateQty(${p.id}, -1)">-</button>
                                        <span class="px-2 fw-bold small" style="min-width:30px; text-align:center">${item.qty}</span>
                                        <button class="btn btn-sm border-0 px-2" onclick="updateQty(${p.id}, 1)">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="d-flex gap-1 mb-3 justify-content-end">
                                    <button class="btn btn-sm btn-light text-primary border rounded-circle" onclick="viewCartItemQuickly(${p.id})" style="width:32px; height:32px;"><i class="fa-solid fa-eye" ></i></button>
                                    <button class="btn btn-sm btn-light text-danger border rounded-circle" onclick="removeFromCart(${p.id})" style="width:32px; height:32px;"><i class="fa-solid fa-trash-can"></i></button>
                                </div>
                                <h6 class="fw-bold mb-0">৳ ${totalItemPrice}</h6>
                            </div>
                        </div>
                    </div>`;
                }
            });

            if (subtotal >= freeDeliveryThreshold) {
                delivery = 0;
                $('#free-delivery-banner').html(
                        '<i class="fa-solid fa-circle-check me-2"></i> Congratulations! You have <b>Free Delivery</b>.')
                    .css({
                        'background': '#d1e7dd',
                        'color': '#0f5132'
                    }).show();
                $('#delivery-status').html('৳ 60 <span class="text-success ms-1">FREE</span>');
            } else {
                let amountLeft = freeDeliveryThreshold - subtotal;
                $('#free-delivery-banner').html(
                        `<i class="fa-solid fa-truck me-2"></i> Add <b>৳ ${amountLeft}</b> more for <b>Free Delivery</b>.`)
                    .css({
                        'background': '#e7f1ff',
                        'color': '#0d6efd'
                    }).show();
                $('#delivery-status').text('৳ 60');
            }

            container.html(html);
            $('#cart-subtotal').text(subtotal);
            $('#cart-total-display').text(subtotal + delivery);
            $('#cart-footer').show();
        }

        function viewCartItemQuickly(id) {
            let cart = getCart();
            let item = cart.find(i => i.id === id);
            let p = allProductsData.find(x => x.id === id);

            if (p && item) {
                let totalItemPrice = p.price * item.qty;
                let html = `
                <div class="row g-0 p-4 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 shadow-none border rounded-circle p-2" data-bs-dismiss="modal" style="z-index: 10; font-size: 12px;"></button>
                    <div class="col-md-5 d-flex align-items-center justify-content-center p-3">
                        <img src="${p.thumbnail}" class="img-fluid rounded-4 shadow-sm" style="max-height: 280px; object-fit: contain;">
                    </div>
                    <div class="col-md-7 p-4">
                        <h2 class="fw-bold text-dark mb-1">${p.name}</h2>
                        <h3 class="text-primary fw-bold mb-4">৳ ${p.price}</h3>
                        <div class="p-3 bg-light rounded-4 border mb-4" style="background: #f8f9fa !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted fw-bold">Quantity:</span>
                                <span class="fs-5 fw-bolder text-dark">${item.qty}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                <span class="fw-bold text-dark">Subtotal:</span>
                                <span class="fs-4 fw-bolder text-success">৳ ${totalItemPrice}</span>
                            </div>
                        </div>
                        <button class="btn btn-success w-100 rounded-pill py-3 fw-bold fs-5 shadow-sm disabled" style="background-color: #67b295; border:none; opacity: 1;">
                            <i class="fa-solid fa-circle-check me-2"></i> Already In Cart
                        </button>
                    </div>
                </div>`;
                $('#cart-quickview-content').html(html);
                $('#cartItemQuickViewModal').modal('show');
            }
        }

        // ==========================================
        // 5. CHECKOUT SECTION
        // ==========================================

        function renderCheckoutStructure() {
            let html = `
    <div class="offcanvas offcanvas-end" tabindex="-1" id="checkoutOffcanvas" style="width: 400px; border-radius: 20px 0 0 20px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold">Checkout Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="p-3 bg-light rounded-4 mb-4">
                <h6 class="fw-bold border-bottom pb-2">Order Summary</h6>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-bold">৳ <span id="check-subtotal">0</span></span>
                </div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Delivery Charge:</span>
                    <span class="fw-bold">৳ <span id="check-delivery">60</span></span>
                </div>
                <div class="d-flex justify-content-between fw-bold mt-2 pt-2 border-top text-primary fs-5">
                    <span>Total:</span>
                    <span>৳ <span id="check-total">0</span></span>
                </div>
            </div>
            <form id="checkout-form">
                <div class="mb-3">
                    <label class="small fw-bold mb-1">Full Name</label>
                    <input type="text" name="name" class="form-control shadow-none rounded-3" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold mb-1">Phone Number</label>
                    <input type="text" name="phone" class="form-control shadow-none rounded-3" placeholder="017xxxxxxxx" required>
                </div>
                <div class="mb-4">
                    <label class="small fw-bold mb-1">Full Address</label>
                    <textarea name="address" class="form-control shadow-none rounded-3" rows="3" placeholder="House, Road, Area..." required></textarea>
                </div>
                <button type="submit" id="confirm-order-btn" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm">
                    Confirm Order
                </button>
            </form>
        </div>
    </div>`;
            $('#checkout-ui-wrapper').html(html);
        }


        function showCheckout() {

            let cartDrawer = bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas'));
            if (cartDrawer) cartDrawer.hide();

            let subtotal = parseInt($('#cart-subtotal').text()) || 0;
            let delivery = (subtotal >= 2000) ? 0 : 60;

            $('#check-subtotal').text(subtotal);
            $('#check-delivery').text(delivery);
            $('#check-total').text(subtotal + delivery);

            setTimeout(() => {
                let checkoutOffcanvas = new bootstrap.Offcanvas(document.getElementById('checkoutOffcanvas'));
                checkoutOffcanvas.show();
            }, 400);
        }

        $(document).on('submit', '#checkout-form', function(e) {
            e.preventDefault();

            let submitBtn = $('#confirm-order-btn');
            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

            let cart = getCart();
            let orderItems = cart.map(item => {
                let product = allProductsData.find(p => p.id === item.id);
                return {
                    id: item.id,
                    quantity: item.qty,
                    price: product ? product.price : 0
                };
            });

            let formData = {
                _token: "{{ csrf_token() }}",
                name: $(this).find('input[name="name"]').val(),
                phone: $(this).find('input[name="phone"]').val(),
                address: $(this).find('textarea[name="address"]').val(),
                subtotal: parseFloat($('#check-subtotal').text()),
                delivery_charge: parseFloat($('#check-delivery').text()),
                total_amount: parseFloat($('#check-total').text()),
                items: orderItems
            };

            $.post("{{ route('checkout') }}", formData, function(res) {

                Toast.fire({
                    icon: 'success',
                    title: 'Order Successful!'
                });

                $('#checkout-form')[0].reset();

                localStorage.removeItem('my_cart');

                $('.cart-count-nav').text(0);
                $('#cart-count-title').text('(0)');

                if (typeof renderProductGrid === "function") {
                    renderProductGrid();
                }

                updateCartUI();

                bootstrap.Offcanvas.getInstance(document.getElementById('checkoutOffcanvas')).hide();

                fetchOrderHistory();

                submitBtn.prop('disabled', false).text('Confirm Order');

            }).fail(function(xhr) {
                submitBtn.prop('disabled', false).text('Confirm Order');
                let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong!';
                Swal.fire('Error', errorMsg, 'error');
            });
        });

        // ==========================================
        // 6. ORDER HISTORY SECTION
        // ==========================================
        function renderOrderStructure() {
            let html = `
    <div class="offcanvas offcanvas-end" tabindex="-1" id="orderHistoryOffcanvas" style="width: 400px; border-radius: 20px 0 0 20px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Order History</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-3" id="order-list-container" style="background: #f8f9fa;"></div>
    </div>

    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-5 border-0 shadow">
                <div class="modal-header border-bottom px-4">
                    <h5 class="fw-bold mb-0">Order Details</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="order-details-content"></div>
            </div>
        </div>
    </div>`;
            $('#order-ui-wrapper').html(html);
        }

        function fetchOrderHistory() {
            let container = $('#order-list-container');
            container.html(
                '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');

            $.get("{{ route('orders.list') }}", function(res) {
                if (!res.data || res.data.length === 0) {
                    container.html('<div class="text-center py-5 text-muted">No orders found!</div>');
                    return;
                }

                let html = '';
                res.data.forEach(order => {
                    html += `
            <div class="p-3 mb-3 bg-white border rounded-4 shadow-sm" id="order-row-${order.id}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-primary mb-0">ORD-${order.order_number}</h6>
                    <span class="badge bg-success-subtle text-success rounded-pill px-3">${order.status}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted" style="font-size: 11px;">${order.created_at}</div>
                        <div class="fw-bold text-dark">৳ ${order.total_amount}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light border rounded-circle" onclick="viewOrderDetails(${order.id})" style="width:35px; height:35px;">
                            <i class="fa-solid fa-eye text-primary"></i>
                        </button>
                        <button class="btn btn-sm btn-light border rounded-circle" onclick="deleteOrder(${order.id})" style="width:35px; height:35px;">
                            <i class="fa-solid fa-trash-can text-danger"></i>
                        </button>
                    </div>
                </div>
            </div>`;
                });
                container.html(html);
            });
        }

        function viewOrderDetails(orderId) {
            $('#order-details-content').html(
                '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
            $('#orderDetailsModal').modal('show');

            $.get(`/order-details/${orderId}`, function(res) {
                let order = res.data;
                let itemsHtml = '';

                order.items.forEach(item => {
                    // Price string থেকে কমা সরিয়ে নম্বর এ কনভার্ট করা
                    let unitPrice = parseFloat(String(item.price).replace(/,/g, '')) || 0;
                    let quantity = parseInt(item.qty) || 0;
                    let itemTotal = unitPrice * quantity;

                    itemsHtml += `
            <div class="d-flex align-items-center gap-3 mb-2 pb-2 border-bottom">
                <img src="${item.thumbnail}" style="width: 50px; height: 50px; object-fit: contain;" class="bg-light rounded">
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold small">${item.name}</h6>
                    <span class="text-muted small">${quantity} x ৳ ${unitPrice.toLocaleString()}</span>
                </div>
                <div class="fw-bold">৳ ${itemTotal.toLocaleString()}</div>
            </div>`;
                });

                let html = `
        <div class="row mb-4">
            <div class="col-6">
                <p class="text-muted small mb-1">Customer Info:</p>
                <h6 class="fw-bold mb-0">${order.name}</h6>
                <p class="small mb-0 text-dark">${order.phone}</p>
            </div>
            <div class="col-6 text-end">
                <p class="text-muted small mb-1">Shipping Address:</p>
                <p class="small mb-0 fw-bold">${order.address}</p>
            </div>
        </div>
        <div class="mb-3">
            <h6 class="fw-bold border-bottom pb-2 mb-3">Ordered Items</h6>
            ${itemsHtml}
        </div>
        <div class="p-3 bg-light rounded-4">
            <div class="d-flex justify-content-between small mb-1"><span>Subtotal:</span><span class="fw-bold">৳ ${order.subtotal}</span></div>
            <div class="d-flex justify-content-between small mb-1"><span>Delivery:</span><span class="fw-bold">৳ ${order.delivery_charge}</span></div>
            <div class="d-flex justify-content-between fs-5 fw-bold text-primary border-top pt-2 mt-2"><span>Total Amount:</span><span>৳ ${order.total_amount}</span></div>
        </div>`;

                $('#order-details-content').html(html);
            });
        }

        function deleteOrder(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/order-delete/${id}`, {
                        _token: "{{ csrf_token() }}",
                        _method: 'DELETE'
                    }, function(res) {
                        $(`#order-row-${id}`).fadeOut();
                        Toast.fire({
                            icon: 'success',
                            title: 'Order deleted successfully'
                        });
                    });
                }
            });
        }
    </script>
@endpush
