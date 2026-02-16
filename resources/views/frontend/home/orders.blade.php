@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h4 class="fw-bold mb-0">My Recent Orders</h4>
            <a href="{{ route('index') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">Back to Store</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-3">Invoice</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Placed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="fw-bold ps-3">#{{ $order->invoice_no }}</td>
                        <td class="fw-bold text-primary">৳{{ number_format($order->total_amount, 0) }}</td>
                        <td>
                            @php
                                $badgeClass = match($order->status) {
                                    'pending' => 'bg-warning text-dark',
                                    'delivered' => 'bg-success',
                                    'cancelled' => 'bg-danger text-white',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $badgeClass }} px-3 py-2">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <p class="text-muted mb-0">No orders placed yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
