@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">

                <i class="bi bi-box-seam me-2"></i>

                Stock Status

            </h4>

            <div class="text-muted">

                Published Product → Specification → Barcode → Vendor Stock

            </div>

        </div>

    </div>


    {{-- =========================================================
         SUMMARY
    ========================================================== --}}

    <div class="row g-3 mb-4">


        {{-- TOTAL PRODUCTS --}}

        <div class="col-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="small text-muted">

                        Total Barcodes

                    </div>

                    <div class="fs-4 fw-bold">

                        {{ number_format($summary['total_products']) }}

                    </div>

                </div>

            </div>

        </div>


        {{-- RECEIVED --}}

        <div class="col-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="small text-muted">

                        Received Qty

                    </div>

                    <div class="fs-4 fw-bold">

                        {{ number_format($summary['quantity_received'], 2) }}

                    </div>

                </div>

            </div>

        </div>


        {{-- SENT --}}

        <div class="col-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="small text-muted">

                        Sent Qty

                    </div>

                    <div class="fs-4 fw-bold">

                        {{ number_format($summary['send_qty'], 2) }}

                    </div>

                </div>

            </div>

        </div>


        {{-- AVAILABLE --}}

        <div class="col-6 col-lg-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="small text-muted">

                        Available Qty

                    </div>

                    <div class="fs-4 fw-bold text-success">

                        {{ number_format($summary['available_qty'], 2) }}

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- =========================================================
         STOCK STATUS REPORT
    ========================================================== --}}

    <div class="card border-0 shadow-sm">


        {{-- HEADER --}}

        <div
            class="card-header bg-white
                   d-flex justify-content-between
                   align-items-center"
        >

            <strong>

                <i class="bi bi-table me-2"></i>

                Stock Status Report

            </strong>


            <span class="small text-muted">

                {{ number_format($report->total()) }} records

            </span>

        </div>



        {{-- TABLE --}}

        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="table table-hover
                           align-middle
                           mb-0"
                >

                    <thead class="table-light">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                SKU
                            </th>

                            <th>
                                Barcode
                            </th>

                            <th>
                                Product
                            </th>

                            <th>
                                Type
                            </th>

                            <th>
                                Composition
                            </th>

                            <th>
                                Gender
                            </th>

                            <th>
                                Warehouse
                            </th>

                            <th class="text-end">
                                Received
                            </th>

                            <th class="text-end">
                                Sent
                            </th>

                            <th class="text-end">
                                Available
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        @forelse($report as $index => $row)

                            @php

                                $available =
                                    (float) ($row->available_qty ?? 0);

                                $status =
                                    $available > 0
                                        ? 'In Stock'
                                        : 'Out of Stock';

                            @endphp


                            <tr>


                                {{-- NUMBER --}}

                                <td>

                                    {{ $report->firstItem() + $index }}

                                </td>


                                {{-- SKU --}}

                                <td>

                                    <strong>

                                        {{ $row->sku ?: '-' }}

                                    </strong>

                                    @if(!empty($row->sku_supplier))

                                        <div class="small text-muted">

                                            {{ $row->sku_supplier }}

                                        </div>

                                    @endif

                                </td>


                                {{-- BARCODE --}}

                                <td>

                                    <span class="font-monospace">

                                        {{ $row->barcode ?: '-' }}

                                    </span>

                                </td>


                                {{-- PRODUCT --}}

                                <td>

                                    {{ $row->item_name_name
                                        ?: $row->category_name
                                        ?: '-' }}

                                </td>


                                {{-- TYPE --}}

                                <td>

                                    {{ $row->item_type_name ?: '-' }}

                                </td>


                                {{-- COMPOSITION --}}

                                <td>

                                    {{ $row->composition_name ?: '-' }}

                                </td>


                                {{-- GENDER --}}

                                <td>

                                    {{ $row->gender_name ?: '-' }}

                                </td>


                                {{-- WAREHOUSE --}}

                                <td>

                                    @if($row->warehouse_id)

                                        <strong>

                                            {{ $row->warehouse_id }}

                                        </strong>

                                        @if($row->warehouse_location)

                                            <div class="small text-muted">

                                                {{ $row->warehouse_location }}

                                            </div>

                                        @endif

                                    @else

                                        <span class="text-muted">

                                            No Stock

                                        </span>

                                    @endif

                                </td>


                                {{-- RECEIVED --}}

                                <td class="text-end">

                                    {{ number_format(
                                        (float) ($row->quantity_received ?? 0),
                                        2
                                    ) }}

                                </td>


                                {{-- SENT --}}

                                <td class="text-end">

                                    {{ number_format(
                                        (float) ($row->send_qty ?? 0),
                                        2
                                    ) }}

                                </td>


                                {{-- AVAILABLE --}}

                                <td class="text-end fw-bold">

                                    {{ number_format(
                                        $available,
                                        2
                                    ) }}

                                </td>


                                {{-- STATUS --}}

                                <td>

                                    @if($available > 0)

                                        <span class="badge bg-success">

                                            <i class="bi bi-check-circle me-1"></i>

                                            In Stock

                                        </span>

                                    @else

                                        <span class="badge bg-danger">

                                            <i class="bi bi-x-circle me-1"></i>

                                            Out of Stock

                                        </span>

                                    @endif

                                </td>


                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="12"
                                    class="text-center
                                           text-muted
                                           py-5"
                                >

                                    <i
                                        class="bi bi-inbox fs-2
                                               d-block mb-2"
                                    ></i>

                                    No stock records found.

                                </td>

                            </tr>

                        @endforelse


                    </tbody>

                </table>

            </div>

        </div>



        {{-- PAGINATION --}}

        @if($report->hasPages())

            <div class="card-footer bg-white">

                {{ $report->links() }}

            </div>

        @endif


    </div>

</div>

@endsection