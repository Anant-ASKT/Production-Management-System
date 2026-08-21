@extends('layouts.app')

@section('title', 'User Dashboard')

@section('page-title', 'Dashboard')


@section('content')

@php
    $user = auth()->user();
@endphp


<div class="welcome-card mb-4">

    <h2>
        Welcome back, {{ $user->name }}
    </h2>

    <p>
        Here are the production modules available to you.
    </p>

</div>


<div class="row g-3">


    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card">

            <div class="stat-icon">
                <i class="bi bi-grid-3x3-gap"></i>
            </div>

            <div class="stat-label">
                Total Garments
            </div>

            <div class="stat-value">
                0
            </div>

        </div>

    </div>


    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card">

            <div class="stat-icon">
                <i class="bi bi-box-seam"></i>
            </div>

            <div class="stat-label">
                Ready to Sell
            </div>

            <div class="stat-value">
                0
            </div>

        </div>

    </div>


    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card">

            <div class="stat-icon">
                <i class="bi bi-upc-scan"></i>
            </div>

            <div class="stat-label">
                Barcode Items
            </div>

            <div class="stat-value">
                0
            </div>

        </div>

    </div>


    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card">

            <div class="stat-icon">
                <i class="bi bi-person-check"></i>
            </div>

            <div class="stat-label">
                Access Level
            </div>

            <div class="stat-value"
                 style="font-size:18px;">

                {{ ucfirst($user->role) }}

            </div>

        </div>

    </div>

</div>


<h3 class="dashboard-section-title">
    My Modules
</h3>


<div class="row g-3">


    <div class="col-12 col-md-6 col-xl-4">

        <a
            href="javascript:void(0)"
            class="quick-card">

            <div class="quick-icon">
                <i class="bi bi-rulers"></i>
            </div>

            <div>

                <div class="quick-title">
                    Design Specification
                </div>

                <div class="quick-description">
                    Manage assigned garment specifications.
                </div>

            </div>

        </a>

    </div>


    <div class="col-12 col-md-6 col-xl-4">

        <a
            href="javascript:void(0)"
            class="quick-card">

            <div class="quick-icon">
                <i class="bi bi-grid-3x3-gap"></i>
            </div>

            <div>

                <div class="quick-title">
                    View All Garments
                </div>

                <div class="quick-description">
                    View available garment records.
                </div>

            </div>

        </a>

    </div>


    <div class="col-12 col-md-6 col-xl-4">

        <a
            href="javascript:void(0)"
            class="quick-card">

            <div class="quick-icon">
                <i class="bi bi-box-seam"></i>
            </div>

            <div>

                <div class="quick-title">
                    Ready to Sell Stock
                </div>

                <div class="quick-description">
                    View available stock.
                </div>

            </div>

        </a>

    </div>


    <div class="col-12 col-md-6 col-xl-4">

        <a
            href="javascript:void(0)"
            class="quick-card">

            <div class="quick-icon">
                <i class="bi bi-upc-scan"></i>
            </div>

            <div>

                <div class="quick-title">
                    Print Barcode
                </div>

                <div class="quick-description">
                    Generate and print assigned barcodes.
                </div>

            </div>

        </a>

    </div>

</div>

@endsection