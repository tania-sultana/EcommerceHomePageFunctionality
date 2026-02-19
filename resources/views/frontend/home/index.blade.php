@extends('frontend.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row g-4" id="product-grid-container">
            <div class="col-12 text-center py-5" id="main-loader">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Loading our catalog...</p>
            </div>
        </div>
    </div>

    {{-- ------------product details modal----- --}}
    <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-hidden="true">
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
        $(document).ready(function() {
           
            $.ajax({
                url: "{{ route('index') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    $('#main-loader').hide();
                    let products = response.data;
                    let html = '';

                    products.forEach(function(product) {
                        let displayDescription = '';
                        let rawDescription = product.short_description || '';

                        if (rawDescription.length > 40) {
                            displayDescription = rawDescription.substring(0, 40) + '...';
                        } else {
                            displayDescription = rawDescription;
                        }

                        html += `
                        <div class="col-6 col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden premium-card p-2 pb-3"
                                 onclick="showProductDetails(${product.id})"
                                 style="cursor: pointer;">

                                <div class="position-relative w-100 overflow-hidden" style="height: 200px;">
                                    <button class="wishlist-btn-modern shadow-sm"
                                            onclick="event.stopPropagation(); toggleWishlist(this, ${product.id})">
                                        <i class="fa-regular fa-heart text-danger"></i>
                                    </button>

                                    <img src="${product.thumbnail}" class="w-100 h-100 p-2"
                                        style="object-fit: contain; transition: transform 0.4s;"
                                        alt="img" >
                                </div>

                                <div class="card-body d-flex flex-column p-3">
                                    <h5 class="fw-bold mb-1 text-dark">${product.name}</h5>
                                    <p class="text-muted mb-3 flex-grow-1">
                                        ${displayDescription}
                                        <span class="text-primary fw-bold" style="font-size: 13px;">see more</span>
                                    </p>

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
            });
        });

        // Product Details Modal Logic
        function showProductDetails(id) {

            $('#productDetailsModal').modal('show');
            $('#modal-loader-area').html(
                '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>');

            $.ajax({
                url: "/product/" + id,
                type: "GET",
                success: function(res) {
                    let product = res.data;

                    let content = `
                        <div class="row g-0">
                            <div class="col-md-5 d-flex align-items-center justify-content-center p-4">
                                <img src="${product.thumbnail}" class="img-fluid" style="max-height: 350px; object-fit: contain;">
                            </div>
                            <div class="col-md-7 p-4 p-lg-5 position-relative">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>

                                <h2 class="fw-bold mb-2 mt-3 text-dark">${product.name}</h2>
                                <div class="text-warning mb-2">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                                    <span class="text-muted small ms-2">(4.5 Rating)</span>
                                </div>

                                <h3 class="text-primary fw-bold mb-3">৳ ${product.price}</h3>
                                <p class="text-muted mb-4 small" style="line-height: 1.6;">${product.short_description || 'Details not available'}</p>

                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <input type="number" class="form-control rounded-pill text-center shadow-none border" value="1" min="1" style="width: 85px; height: 40px;">
                                    <button class="btn btn-primary flex-grow-1 rounded-pill py-2 fw-bold shadow-sm" style="height: 40px;">
                                        <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
                                    </button>
                                </div>

                                <div class="d-flex gap-2 border-top pt-4">
                                    <button class="btn btn-light rounded-circle border p-2" style="width: 45px; height: 40px;"><i class="fa-regular fa-heart text-danger"></i></button>
                                    <button class="btn btn-light rounded-circle border p-2" style="width: 40px; height: 40px;"><i class="fa-solid fa-share-nodes text-muted"></i></button>
                                </div>
                            </div>
                        </div>`;
                    $('#modal-loader-area').html(content);
                },
                error: function() {
                    $('#modal-loader-area').html(
                        '<div class="p-5 text-center text-danger fw-bold">Failed to fetch product information.</div>'
                        );
                }
            });
        }
    </script>
@endpush
