@extends('layouts.app')
@section('content')
<div class="container-fluid py-3"><div class="d-flex justify-content-between align-items-center mb-3"><h3>Confirmed Physical Shop Orders</h3><a class="btn btn-outline-secondary" href="{{ route('inventory.physical-shop-orders.index') }}">Back to Baskets</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Order No.</th><th>Basket</th><th>Confirmed At</th><th></th></tr></thead><tbody>@forelse($orders as $order)<tr><td>{{ $order->orderno ?? '' }}</td><td>{{ $order->orderno ?? 'N/A' }}</td><td>{{ $order->orderdate ?? 'N/A' }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('inventory.physical-shop-orders.orders.show', $order->id) }}">View</a></td></tr>@empty<tr><td colspan="4" class="text-center py-4 text-muted">No confirmed orders yet.</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $orders->links() }}</div></div></div>
@endsection
