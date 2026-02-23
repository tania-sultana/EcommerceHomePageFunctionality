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

    {{-- Dynamic UI Containers --}}
    <div id="cart-ui-wrapper"></div>
    <div id="checkout-ui-wrapper"></div>
@endsection

@push('scripts')
    <script>
        let allProductsData = [];

        $(document).ready(function() {
            renderCartStructure();
            renderCheckoutStructure();
            loadHomeProducts();

            $(document).on('click', '.trigger-cart', function(e) {
                e.preventDefault();
                updateCartUI();
                new bootstrap.Offcanvas(document.getElementById('cartOffcanvas')).show();
            });
        });

        function renderCartStructure() {
            let html = `
            <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" style="width: 400px;">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold">Shopping Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column" id="cart-items-container" style="background: #f8f9fa;"></div>
                <div class="offcanvas-footer p-3 border-top bg-white" id="cart-footer" style="display:none;">
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>Subtotal:</span>
                        <span>৳ <span id="cart-subtotal">0</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>Delivery Charge:</span>
                        <span>৳ <span id="cart-delivery-charge">60</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 fw-bold border-top pt-2">
                        <span>Total:</span>
                        <span class="text-primary">৳ <span id="cart-total-display">0</span></span>
                    </div>
                    <button class="btn btn-primary w-100 rounded-pill fw-bold py-2" onclick="showCheckout()">Proceed to Checkout</button>
                </div>
            </div>`;
            $('#cart-ui-wrapper').html(html);
        }

        function renderCheckoutStructure() {
            let html = `
            <div class="offcanvas offcanvas-end" tabindex="-1" id="checkoutOffcanvas" style="width: 400px;">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title fw-bold">Checkout Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="p-3 bg-light rounded-4 mb-4">
                        <h6 class="fw-bold border-bottom pb-2">Order Summary</h6>
                        <div class="d-flex justify-content-between small mb-1"><span>Subtotal:</span><span>৳ <span id="check-subtotal">0</span></span></div>
                        <div class="d-flex justify-content-between small mb-1"><span>Delivery Charge:</span><span id="check-delivery">60</span></div>
                        <div class="d-flex justify-content-between fw-bold mt-2 pt-2 border-top text-primary"><span>Total:</span><span>৳ <span id="check-total">0</span></span></div>
                    </div>
                    <form id="checkout-form">
                        <div class="mb-3"><label class="small fw-bold">Full Name</label><input type="text" name="name" class="form-control shadow-none" required></div>
                        <div class="mb-3"><label class="small fw-bold">Phone Number</label><input type="text" name="phone" class="form-control shadow-none" required></div>
                        <div class="mb-4"><label class="small fw-bold">Address</label><textarea name="address" class="form-control shadow-none" rows="3" required></textarea></div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">Confirm Order</button>
                    </form>
                </div>
            </div>`;
            $('#checkout-ui-wrapper').html(html);
        }

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
                                <button class="btn ${cartBtnClass} btn-sm rounded-pill px-3 py-1 shadow-sm fw-bold" onclick="event.stopPropagation(); addToCart(${product.id})">
                                    ${cartBtnText}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            $('#product-grid-container').html(html);
        }

        /**
         * SECTION: CART LOGIC
         */
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
            }
        }

        function updateQty(id, delta) {
            let cart = getCart();
            let item = cart.find(i => i.id === id);
            if (item) {
                item.qty += delta;
                if (item.qty < 1) item.qty = 1; // Minimum value 1
                localStorage.setItem('my_cart', JSON.stringify(cart));
                updateCartUI();
            }
        }

        function removeFromCart(id) {
            let cart = getCart().filter(i => i.id !== id);
            localStorage.setItem('my_cart', JSON.stringify(cart));
            updateCartUI();
            renderProductGrid();
        }

        function updateCartUI() {
            let cart = getCart();
            $('.cart-count-nav').text(cart.length);
            let container = $('#cart-items-container');
            let subtotal = 0;
            let delivery = 60;

            if (cart.length === 0) {
                container.html('<div class="text-center py-5 text-muted">Your cart is empty!</div>');
                $('#cart-footer').hide();
                return;
            }

            let html = '';
            cart.forEach(item => {
                let p = allProductsData.find(x => x.id === item.id);
                if (p) {
                    let totalItemPrice = p.price * item.qty; // Price level updates with quantity
                    subtotal += totalItemPrice;
                    html += `
                    <div class="d-flex align-items-center gap-3 mb-3 p-2 bg-white border rounded-4 shadow-sm">
                        <img src="${p.thumbnail}" style="width: 60px; height: 60px; object-fit: contain;" class="bg-light p-1 rounded">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold small">${p.name}</h6>
                            <span class="text-primary fw-bold small">৳ ${totalItemPrice}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center border rounded-pill bg-light">
                                <button class="btn btn-sm border-0 px-2" onclick="updateQty(${p.id}, -1)">-</button>
                                <span class="px-1 fw-bold small" style="min-width:20px; text-align:center">${item.qty}</span>
                                <button class="btn btn-sm border-0 px-2" onclick="updateQty(${p.id}, 1)">+</button>
                            </div>
                            <button class="btn btn-sm text-danger p-1" onclick="removeFromCart(${p.id})"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </div>`;
                }
            });

            container.html(html);
            $('#cart-subtotal').text(subtotal);
            $('#cart-delivery-charge').text(delivery);
            $('#cart-total-display').text(subtotal + delivery);
            $('#cart-footer').show();
        }

        /**
         * SECTION: DETAILS MODAL (WITH QUANTITY LOGIC)
         */
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

        // Modal Specific Qty Handler
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

        /**
         * OTHER UTILS
         */
        function showCheckout() {
            bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas')).hide();
            let subtotal = parseInt($('#cart-subtotal').text());
            let delivery = 60;
            $('#check-subtotal').text(subtotal);
            $('#check-delivery').text(delivery);
            $('#check-total').text(subtotal + delivery);
            setTimeout(() => {
                new bootstrap.Offcanvas(document.getElementById('checkoutOffcanvas')).show();
            }, 400);
        }
// ---------------------wishlist------------
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
                html += `<div class="d-flex align-items-center gap-3 mb-3 p-3 border rounded-4 bg-white shadow-sm">
                    <img src="${product.thumbnail}" style="width: 50px; height: 50px; object-fit: contain;" class="bg-light rounded p-1">
                    <div class="flex-grow-1"><h6 class="mb-0 fw-bold small">${product.name}</h6><span class="text-primary fw-bold small">৳ ${product.price}</span></div>
                    <button class="btn btn-sm btn-light text-danger border rounded-circle" onclick="toggleWishlist(null, ${product.id})" style="width: 32px; height: 32px;"><i class="fa-solid fa-trash-can"></i></button>
                </div>`;
            });
            container.html(html);
        }

        $(document).on('submit', '#checkout-form', function(e) {
            e.preventDefault();
            let data = $(this).serialize() + '&cart=' + JSON.stringify(getCart()) + '&_token={{ csrf_token() }}';
            $.post("{{ route('checkout') }}", data, function(res) {
                alert('Order Successful!');
                localStorage.removeItem('my_cart');
                renderProductGrid();
                updateCartUI();
                bootstrap.Offcanvas.getInstance(document.getElementById('checkoutOffcanvas')).hide();
            });
        });
    </script>
@endpush
