@extends('layouts.supplier')

@section('title', 'Supplier Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-5">
        <h3 class="mb-3">Welcome to your Supplier Dashboard, {{ auth()->guard('supplier')->user()->name ?? 'Supplier' }}</h3>
        <p class="text-muted">This is your personalized portal. Manage your products here.</p>
    </div>
</div>
@endsection
