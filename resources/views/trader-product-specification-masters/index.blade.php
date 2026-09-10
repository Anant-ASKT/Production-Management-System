@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">
                Show All Products Master of Traders
            </h2>

            <div class="text-muted">
                Trader product specifications
            </div>
        </div>

        <a href="{{ route('trader-specifications.index') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-lg"></i>

            Add Trader Product

        </a>

    </div>


    <div class="card">

        <div class="card-body">

            <div class="row mb-4">

                <div class="col-md-6">

                    <input type="text"
                           id="traderProductSearch"
                           class="form-control"
                           placeholder="Search trader, product, SKU, bill...">

                </div>

                <div class="col-md-2">

                    <button type="button"
                            id="refreshTraderProducts"
                            class="btn btn-outline-secondary">

                        <i class="bi bi-arrow-clockwise"></i>

                        Refresh

                    </button>

                </div>

            </div>


            <div id="traderProductsContainer">

                <div class="text-center py-5 text-muted">

                    No trader products loaded yet.

                </div>

            </div>

        </div>

    </div>

</div>

@endsection