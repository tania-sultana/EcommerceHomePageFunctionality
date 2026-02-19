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
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('index') }}",
            type: "GET",
            dataType: "json",
            success: function (response) {
                $('#main-loader').hide();
                let products = response.data;
                let html = '';

                products.forEach(function(product) {

                    html += `
                        <div class="col-md-3 mb-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="p-3 text-center">
                                    <img src="${product.thumbnail}" class="img-fluid" object-fit: contain;" style="height: 180px;">
                                </div>
                                <div class="bg-light card-body d-flex flex-column">
                                    <h6 class="fw-bold">${product.name}</h6>
                                    <p class="text-muted small">${product.short_description.substring(0, 40)}... <a href="#">see more</a> </p>
                                    <p class="text-muted small">${product.short_description.substring(0, 40)}... <a href="#">see more</a> </p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="text-primary fw-bold">৳ ${product.price}</span>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3">Add to cart</button>
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
