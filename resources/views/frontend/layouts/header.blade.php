<nav class="navbar navbar-expand-lg sticky-top mb-5 navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 text-primary" href="{{ route('index') }}">
            <i class="fa-solid fa-bolt me-2"></i>E-SHOP
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('products.index') ? 'active fw-bold' : '' }}" href="{{ route('products.index') }}">Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 {{ request()->routeIs('index') ? 'active fw-bold' : '' }}" href="{{ route('index') }}">Home</a>
                </li>
                <li class="nav-item">
                    {{-- Order link-ti ekhane update kora hoyeche --}}
                    <a class="nav-link px-3 {{ request()->routeIs('order.index') ? 'active fw-bold' : '' }}" href="{{ route('order.index') }}">Orders</a>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <a href="{{ route('wishlist.index') }}" class="position-relative me-3 text-decoration-none">
                    <i class="fa-regular fa-heart fs-4 text-dark"></i>
                    <span class="position-absolute badge rounded-pill bg-danger count-badge">
                        {{ \App\Models\Wishlist::count() }}
                    </span>
                </a>

                <a href="{{ route('cart.index') }}" class="position-relative me-3 text-decoration-none">
                    <i class="fa-solid fa-cart-shopping fs-4 text-dark"></i>
                    <span class="position-absolute badge rounded-pill bg-primary count-badge">
                        {{ count((array) session('cart')) }}
                    </span>
                </a>
            </div>
        </div>
    </div>
</nav>
