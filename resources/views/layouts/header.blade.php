<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="{{ route('products.index') }}">
            <i class="bi bi-box-seam me-2"></i> Product Management
        </a>

        <!-- Toggler for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link btn rounded shadow-sm d-flex align-items-center me-2 px-4"
                       href="{{ route('index') }}"
                       style="background-color: #E3F2FD; color: #0d6efd;">
                         Home
                    </a>
                </li>

                 <!-- Products Button -->
                <li class="nav-item">
                    <a class="nav-link btn rounded shadow-sm d-flex align-items-center px-3"
                       href="{{ route('products.index') }}"
                       style="background-color: #E3F2FD; color: #0d6efd;">
                         Products
                    </a>
                </li>

                <!-- Add Product Button -->
                <li class="nav-item ms-2">
                    <a class="nav-link btn rounded shadow-sm d-flex align-items-center"
                       href="{{ route('products.create') }}"
                       style="background-color: #0d6efd; color: #ffffff;">
                         Add Product
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
