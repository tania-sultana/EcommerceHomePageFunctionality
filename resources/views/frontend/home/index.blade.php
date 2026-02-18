@extends('frontend.layouts.app')
@section('content')
    <div class="row g-4 py-4" id="product-grid-container">
        <div class="col-12 text-center py-5" id="main-loader">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Ecommerce Shop Loading...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

    </script>
@endpush
