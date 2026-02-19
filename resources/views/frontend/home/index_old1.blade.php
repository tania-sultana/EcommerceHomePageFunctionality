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
    <div class="offcanvas-body" id="wishlist-items-container">
    </div>
</div>

{{-- ------------ Product Details Modal Section ------------ --}}
<div class="modal fade" id="productDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-body p-0" id="modal-loader-area">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * GLOBAL DATA STORE
 */
let allProductsData = [];

/**
 * SECTION: INITIALIZATION & HOME PRODUCT LOADING
 */
$(document).ready(function() {
    loadHomeProducts();
});

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
        }
    });
}

function renderProductGrid() {
    let wishlist = getWishlist();
    let html = '';

    allProductsData.forEach(function(product) {
        let isWish = wishlist.includes(product.id);
        let heartIcon = isWish ? 'fa-solid' : 'fa-regular';

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
                                <i class="${heartIcon} fa-heart text-danger"></i>
                            </button>
                            <img src="${product.thumbnail}" class="w-100 h-100 p-2" style="object-fit: contain;">
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <h5 class="fw-bold mb-1 text-dark">${product.name}</h5>
                            <p class="text-muted mb-3 flex-grow-1">${displayDescription} ${seeMoreLink}</p>
                            <div class="mt-auto d-flex justify-content-between align-items-center border-top pt-3">
                                <span class="text-primary fw-bolder fs-5">৳ ${product.price}</span>
                                <button class="btn btn-soft-primary add-btn text-primary btn-sm rounded-pill px-3 py-1 shadow-sm fw-bold" onclick="event.stopPropagation(); addToCart(${product.id})">
                                    <i class="fa-solid fa-plus me-1"></i> Add to cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
    });
    $('#product-grid-container').html(html);
}

/**
 * SECTION: WISHLIST FUNCTIONALITY (LOCAL STORAGE & SERVER SYNC)
 */
function getWishlist() {
    return JSON.parse(localStorage.getItem('my_wishlist')) || [];
}

function toggleWishlist(btn, productId) {
    let wishlist = getWishlist();
    let index = wishlist.indexOf(productId);
    let currentAction = (index === -1) ? 'add' : 'remove';

    // Local Storage Logic
    if (currentAction === 'add') {
        wishlist.push(productId);
        $(`.wish-btn-${productId} i`).removeClass('fa-regular').addClass('fa-solid');
    } else {
        wishlist.splice(index, 1);
        $(`.wish-btn-${productId} i`).removeClass('fa-solid').addClass('fa-regular');
    }

    localStorage.setItem('my_wishlist', JSON.stringify(wishlist));
    updateWishlistUI();

    // Server Sync via POST (Clean URL)
    $.ajax({
        url: `/product-toggle/${productId}`,
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            action: currentAction
        },
        success: function(res) {
            // Success callback
        }
    });
}

function updateWishlistUI() {
    let wishlistIds = getWishlist();
    $('.wishlist-count-nav').text(wishlistIds.length);

    let container = $('#wishlist-items-container');
    if (wishlistIds.length === 0) {
        container.html('<div class="text-center py-5 text-muted">Wishlist is empty!</div>');
        return;
    }

    let filtered = allProductsData.filter(p => wishlistIds.includes(p.id));
    let html = '';
    filtered.forEach(product => {
        html += `
                <div class="d-flex align-items-center gap-3 mb-3 p-3 border rounded-4 bg-white shadow-sm">
                    <img src="${product.thumbnail}" style="width: 50px; height: 50px; object-fit: contain;" class="bg-light rounded p-1">
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold" style="font-size: 14px;">${product.name}</h6>
                        <span class="text-primary fw-bold" style="font-size: 13px;">৳ ${product.price}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light text-primary border rounded-circle" onclick="showProductDetails(${product.id})" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger border rounded-circle" onclick="toggleWishlist(null, ${product.id})" style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>`;
    });
    container.html(html);
}

/**
 * SECTION: PRODUCT DETAILS MODAL
 */
function showProductDetails(id) {
    // Close offcanvas if open
    let offcanvasElement = document.getElementById('wishlistOffcanvas');
    let offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
    if (offcanvas) offcanvas.hide();

    $('#productDetailsModal').modal('show');
    $('#productDetailsModal').removeAttr('aria-hidden');

    $.ajax({
        url: "/product/" + id,
        type: "GET",
        success: function(res) {
            let product = res.data;
            let wishlist = getWishlist();
            let heartIcon = wishlist.includes(product.id) ? 'fa-solid' : 'fa-regular';

            let content = `
                        <div class="row g-0">
                            <div class="col-md-5 d-flex align-items-center justify-content-center p-4">
                                <div class="p-3 rounded-4 bg-light shadow-sm text-center w-100">
                                    <img src="${product.thumbnail}" class="img-fluid" style="max-height: 350px; object-fit: contain;">
                                </div>
                            </div>
                            <div class="col-md-7 p-4 p-lg-5 position-relative">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                                <h2 class="fw-bold mb-2 mt-3 text-dark">${product.name}</h2>
                                <div class="text-warning mb-2">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                                <h3 class="text-primary fw-bold mb-3">৳ ${product.price}</h3>
                                <p class="text-muted mb-4 small">${product.short_description || 'Details not available'}</p>
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <input type="number" class="form-control rounded-pill text-center shadow-none border" value="1" min="1" style="width: 85px; height: 40px;">
                                    <button class="btn btn-primary flex-grow-1 rounded-pill py-2 fw-bold" style="height: 40px;" onclick="addToCart(${product.id})">Add to Cart</button>
                                </div>
                                <div class="d-flex gap-2 border-top pt-4">
                                    <button class="btn btn-light rounded-circle border p-2 wish-btn-${product.id}" onclick="toggleWishlist(this, ${product.id})" style="width: 45px; height: 40px;">
                                        <i class="${heartIcon} fa-heart text-danger"></i>
                                    </button>
                                    <button class="btn btn-light rounded-circle border p-2" style="width: 40px; height: 40px;"><i class="fa-solid fa-share-nodes text-muted"></i></button>
                                </div>
                            </div>
                        </div>`;
            $('#modal-loader-area').html(content);
        }
    });
}
</script>
@endpush
