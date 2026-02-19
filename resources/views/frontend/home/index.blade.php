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
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        let seeMoreLink = '';
                        let rawDescription = product.short_description;

                        if (rawDescription.length > 40) {
                            displayDescription = rawDescription.substring(0, 40) + '...';
                            seeMoreLink =
                                `<a href="#" class="text-primary text-decoration-none fw-bold" style="font-size: 13px;">see more</a>`;
                        } else {
                            displayDescription = rawDescription;
                            seeMoreLink = '';
                        }

                        html += `
                        <div class="col-6 col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden premium-card p-2 pb-3">

                                <div class="position-relative w-100 overflow-hidden" style="height: 200px;">
                                    <button class="wishlist-btn-modern shadow-sm" onclick="toggleWishlist(this, ${product.id})">
                                        <i class="fa-regular fa-heart text-danger"></i>
                                    </button>

                                    <img src="${product.thumbnail}" class="w-100 h-100 p-2"
                                        style="object-fit: contain; transition: transform 0.4s;"
                                        alt="img" >
                                </div>

                                <div class="card-body d-flex flex-column p-3">
                                    <h5 class="fw-bold mb-1 text-dark">${product.name}</h5>
                                    <p class="text-muted mb-3 flex-grow-1">
                                        ${displayDescription ?? 'N/A'} ${seeMoreLink}

                                    </p>

                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="text-primary fw-bolder fs-5">৳ ${product.price}</span>
                                        <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 shadow-sm fw-bold">Add to cart</button>
                                    </div>
                                </div>
                            </div>
        </div>
                    `;
                    });
                    $('#product-grid-container').html(html);
                }
            });
        });
    </script>
@endpush
