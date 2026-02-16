@extends('layouts.app')

@section('content')
<div class="mb-4 text-center">
    <h2>Products</h2>
</div>

<table class="table table-bordered bg-white shadow-sm table-hover text-center align-middle">
    <thead class="table-light">
        <tr>
            <th>Thumbnail</th>
            <th>Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            <tr>
                <td>
                    <img src="{{ $product->thumbnail }}" alt="Thumbnail"
                         class="rounded-circle product-thumb"
                         style="width:50px; height:50px; object-fit:cover;">
                </td>
                <td>{{ $product->name }}</td>
                <td>{{ number_format($product->price, 2) }}</td>
               <td class="text-center align-middle">
    <div class="d-flex justify-content-center align-items-center" style="gap:0.4rem;">
        <!-- Show -->
        <a href="{{ route('products.show', $product) }}"
           class="btn btn-info btn-sm d-flex align-items-center justify-content-center"
           title="View"
           style="width:36px; height:36px; padding:0; border-radius:50%;">
            <i class="fa-solid fa-eye"></i>
        </a>

        <!-- Edit -->
        <a href="{{ route('products.edit', $product) }}"
           class="btn btn-warning btn-sm d-flex align-items-center justify-content-center"
           title="Edit"
           style="width:36px; height:36px; padding:0; border-radius:50%;">
            <i class="fa-solid fa-pen"></i>
        </a>

        <!-- Delete -->
        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline deleteForm mb-0">
            @csrf
            @method('DELETE')
            <button type="button"
                    class="btn btn-danger btn-sm d-flex align-items-center justify-content-center deleteBtn"
                    title="Delete"
                    style="width:36px; height:36px; padding:0; border-radius:50%;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</td>


            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">No products found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.deleteBtn');
        deleteButtons.forEach(function(btn){
            btn.addEventListener('click', function(){
                const form = btn.closest('.deleteForm');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

