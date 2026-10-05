
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Physical Shop Order Details</h3>
            <p class="text-muted mb-0">
                Order: {{ $header->orderno }}
            </p>
        </div>

        <a href="{{ route('inventory.physical-shop-orders.orders.index') }}"
           class="btn btn-outline-secondary">
            Back to Orders
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <strong>Order Number</strong>
                    <div>{{ $header->orderno }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Shop</strong>
                    <div>{{ $header->shop_name ?? 'N/A' }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Order Date</strong>
                    <div>{{ $header->orderdate ?? 'N/A' }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Status</strong>
                    <div>{{ $header->status ?? 'N/A' }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Approval Status</strong>
                    <div>{{ $header->approve_status ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

   
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>All Garments in This Order</strong>
        <span class="badge bg-primary">
            {{ $items->count() }} garment rows
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Barcode</th>
                    <th>SKU</th>
                    <th>Item Type</th>
                    <th>Garment Name</th>
                    <th>Gender</th>
                    <th>Composition</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Box / Remarks</th>
                </tr>
            </thead>

            <tbody>
                @forelse($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>
                            @if(!empty($item->img_path))
                                <img
                                    src="{{ asset(ltrim($item->img_path, '/')) }}"
                                    alt="Garment"
                                    style="width:70px;height:80px;object-fit:contain;"
                                    onerror="this.style.display='none';"
                                >
                            @else
                                <span class="text-muted">No image</span>
                            @endif
                        </td>

                        <td>{{ $item->barcode ?? '-' }}</td>
                        <td>{{ $item->sku ?? '-' }}</td>
                        <td>{{ $item->item_type_name ?? '-' }}</td>
                        <td>{{ $item->item_name ?? '-' }}</td>
                        <td>{{ $item->gender_name ?? '-' }}</td>
                        <td>{{ $item->composition_name ?? '-' }}</td>
                        <td>{{ $item->size_name ?? '-' }}</td>
                        <td>{{ $item->qty ?? 0 }}</td>
                        <td>{{ $item->remarks ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-4">
                            No garment details found for this order.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if($items->isNotEmpty())
                <tfoot class="table-light">
                    <tr>
                        <th colspan="9" class="text-end">Total Quantity</th>
                        <th>{{ $items->sum('qty') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
</div>
@endsection
