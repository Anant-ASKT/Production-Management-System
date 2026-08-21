@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')


@section('content')

@php
    $user = auth()->user();
@endphp


{{-- Welcome --}}

<div class="welcome-card mb-4">

    <h2>
        Welcome back, {{ $user->name }}
    </h2>

    <p>
        Manage your production, garments, inventory and system operations from one place.
    </p>

</div>


{{-- Statistics --}}

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
                Barcodes
            </div>

            <div class="stat-value">
                0
            </div>

        </div>

    </div>


    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card">

            <div class="stat-icon">

                <i class="bi bi-people"></i>

            </div>

            <div class="stat-label">
                System Users
            </div>

            <div class="stat-value">
                2
            </div>

        </div>

    </div>

</div>


{{-- Quick Access --}}

<h3 class="dashboard-section-title">
    Quick Access
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
                    Create and manage garment specifications.
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
                    View and manage all garment records.
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
                    Check garments available for sale.
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
                    Generate and print garment barcodes.
                </div>

            </div>

        </a>

    </div>


    <div class="col-12 col-md-6 col-xl-4">

        <a
            href="javascript:void(0)"
            class="quick-card">

            <div class="quick-icon">

                <i class="bi bi-people"></i>

            </div>

            <div>

                <div class="quick-title">
                    User Management
                </div>

                <div class="quick-description">
                    Manage system users and their roles.
                </div>

            </div>

        </a>

    </div>


    <div class="col-12 col-md-6 col-xl-4">

        <a
            href="javascript:void(0)"
            class="quick-card">

            <div class="quick-icon">

                <i class="bi bi-shield-lock"></i>

            </div>

            <div>

                <div class="quick-title">
                    Module Access
                </div>

                <div class="quick-description">
                    Control user access to application modules.
                </div>

            </div>

        </a>

    </div>

</div>

@endsection