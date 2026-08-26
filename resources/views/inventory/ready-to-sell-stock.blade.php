@extends('layouts.app')

@section('content')



<style>
/* =========================================================
   READY TO SELL STOCK
   ========================================================= */

:root {
    --rts-primary: #2b5288;
    --rts-primary-dark: #1e3f6d;
    --rts-orange: #ff8800;
    --rts-success: #198754;
    --rts-danger: #dc3545;
    --rts-border: #dfe5ec;
    --rts-bg: #f5f7fa;
    --rts-text: #263238;
    --rts-muted: #6b7280;
}

/* Page */
.ready-stock-page {
    width: 100%;
    max-width: 100%;
    padding: 18px;
    background: var(--rts-bg);
    box-sizing: border-box;
}

/* Main card */
.ready-stock-card {
    width: 100%;
    background: #fff;
    border: 1px solid var(--rts-border);
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

/* =========================================================
   HEADER
   ========================================================= */

.ready-stock-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 18px 22px;
    background: linear-gradient(
        135deg,
        var(--rts-primary),
        var(--rts-primary-dark)
    );
    color: #fff;
}

.ready-stock-title-area {
    min-width: 0;
}

.ready-stock-title {
    margin: 0;
    font-size: 21px;
    font-weight: 700;
    line-height: 1.3;
}

.ready-stock-subtitle {
    margin: 5px 0 0;
    font-size: 13px;
    opacity: 0.9;
}

/* =========================================================
   BODY
   ========================================================= */

.ready-stock-body {
    padding: 22px;
}

/* Section */
.rts-section {
    margin-bottom: 22px;
}

.rts-section:last-child {
    margin-bottom: 0;
}

.rts-section-title {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--rts-border);
    color: var(--rts-text);
    font-size: 16px;
    font-weight: 700;
}

.rts-section-title i {
    color: var(--rts-primary);
    font-size: 18px;
}

/* =========================================================
   BARCODE SEARCH
   ========================================================= */

.barcode-search-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 10px;
    align-items: end;
}

.rts-field {
    min-width: 0;
}

.rts-label {
    display: block;
    margin-bottom: 6px;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
}

.rts-input,
.rts-select {
    width: 100%;
    height: 42px;
    padding: 8px 12px;
    border: 1px solid #cfd6df;
    border-radius: 7px;
    background: #fff;
    color: #1f2937;
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
    transition: all .15s ease;
}

.rts-input:focus,
.rts-select:focus {
    border-color: var(--rts-primary);
    box-shadow: 0 0 0 3px rgba(43, 82, 136, 0.10);
}

.barcode-search-btn {
    height: 42px;
    min-width: 105px;
    padding: 0 18px;
    border: 0;
    border-radius: 7px;
    background: var(--rts-primary);
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s;
}

.barcode-search-btn:hover {
    background: var(--rts-primary-dark);
}

.barcode-search-btn:active {
    transform: translateY(1px);
}

/* =========================================================
   PRODUCT DETAILS
   ========================================================= */

.product-details-card {
    display: grid;
    grid-template-columns: 180px minmax(0, 1fr);
    gap: 22px;
    padding: 18px;
    border: 1px solid var(--rts-border);
    border-radius: 10px;
    background: #fafbfc;
}

/* Product image */
.product-image-wrapper {
    width: 180px;
    height: 180px;
    border: 1px solid var(--rts-border);
    border-radius: 9px;
    background: #fff;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: none;
}

.product-no-image {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 7px;
    width: 100%;
    height: 100%;
    color: #9ca3af;
    text-align: center;
}

.product-no-image i {
    font-size: 40px;
}

.product-no-image span {
    font-size: 12px;
}

/* Product information */
.product-info {
    min-width: 0;
}

.product-info-title {
    margin-bottom: 13px;
    color: var(--rts-text);
    font-size: 15px;
    font-weight: 700;
}

.product-info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px 22px;
}

.product-info-item {
    min-width: 0;
    display: grid;
    grid-template-columns: 125px minmax(0, 1fr);
    gap: 8px;
    align-items: center;
    min-height: 30px;
}

.product-info-item .info-label {
    color: #6b7280;
    font-size: 12px;
    font-weight: 600;
}

.product-info-item .info-value {
    min-width: 0;
    color: #1f2937;
    font-size: 13px;
    font-weight: 600;
    overflow-wrap: anywhere;
}

.product-info-item.full-width {
    grid-column: 1 / -1;
}

/* =========================================================
   STOCK ASSIGNMENT
   ========================================================= */

.stock-assignment-card {
    padding: 18px;
    border: 1px solid var(--rts-border);
    border-radius: 10px;
    background: #fff;
}

.assignment-grid {
    display: grid;
    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr)
        minmax(0, 1fr);
    gap: 14px;
}

/* Quantity */
.quantity-wrapper {
    margin-top: 16px;
    max-width: 220px;
}

.quantity-control {
    display: grid;
    grid-template-columns: 42px minmax(0, 1fr) 42px;
    height: 42px;
}

.quantity-btn {
    border: 1px solid #cfd6df;
    background: #f8fafc;
    color: #334155;
    font-size: 17px;
    cursor: pointer;
}

.quantity-btn:first-child {
    border-radius: 7px 0 0 7px;
}

.quantity-btn:last-child {
    border-radius: 0 7px 7px 0;
}

.quantity-btn:hover {
    background: #eef2f7;
}

.quantity-input {
    width: 100%;
    min-width: 0;
    border: 1px solid #cfd6df;
    border-left: 0;
    border-right: 0;
    text-align: center;
    font-weight: 600;
    outline: none;
}

/* Add button */
.assignment-action {
    display: flex;
    justify-content: flex-end;
    align-items: flex-end;
    margin-top: 18px;
}

.btn-add-stock {
    height: 42px;
    padding: 0 20px;
    border: 0;
    border-radius: 7px;
    background: var(--rts-success);
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.btn-add-stock:hover {
    background: #157347;
}

/* =========================================================
   STOCK LIST
   ========================================================= */

.stock-list-wrapper {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border: 1px solid var(--rts-border);
    border-radius: 9px;
}

.stock-list-table {
    width: 100%;
    min-width: 780px;
    border-collapse: collapse;
}

.stock-list-table thead th {
    padding: 11px 12px;
    background: #f3f5f8;
    border-bottom: 1px solid var(--rts-border);
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    text-align: left;
    white-space: nowrap;
}

.stock-list-table tbody td {
    padding: 11px 12px;
    border-bottom: 1px solid #edf0f3;
    color: #374151;
    font-size: 13px;
    vertical-align: middle;
}

.stock-list-table tbody tr:last-child td {
    border-bottom: 0;
}

.stock-list-table tbody tr:hover {
    background: #fafbfc;
}

/* Empty state */
.stock-list-empty {
    padding: 35px 20px;
    text-align: center;
    color: #9ca3af;
}

.stock-list-empty i {
    display: block;
    margin-bottom: 8px;
    font-size: 32px;
}

.stock-list-empty span {
    font-size: 13px;
}

/* =========================================================
   BADGES
   ========================================================= */

.rts-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 8px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 700;
}

.rts-badge-qty {
    background: #e8f5e9;
    color: #18743a;
}

.rts-master-add-btn {
    width: 42px;
    min-width: 42px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    flex-shrink: 0;
}

.rts-master-add-btn i {
    font-size: 16px;
}

/* =========================================================
   READY TO SELL - MASTER MODAL
========================================================= */

.rts-master-modal {
    border: 0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.20);
}

.rts-master-modal .modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
}

.rts-master-modal .modal-title {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
}

.rts-master-modal .modal-body {
    padding: 20px;
}

.rts-master-modal .form-label {
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
}

.rts-master-modal .form-control {
    min-height: 40px;
    border-radius: 7px;
    border: 1px solid #cbd5e1;
    font-size: 14px;
}

.rts-master-modal textarea.form-control {
    min-height: 90px;
    resize: vertical;
}

.rts-master-modal .form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
}

.rts-master-modal .modal-footer {
    padding: 14px 20px;
    border-top: 1px solid #e5e7eb;
}

.rts-generated-box-preview {
    margin-top: 15px;
    padding: 14px;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    background: #f8fafc;
}

.rts-generated-box-preview .small {
    margin-bottom: 4px;
}

.rts-generated-box-preview strong {
    display: block;
    font-size: 20px;
    color: #2563eb;
    word-break: break-word;
}

@media (max-width: 576px) {

    .rts-master-modal .modal-body {
        padding: 15px;
    }

    .rts-master-modal .modal-footer {
        padding: 12px 15px;
    }

    .rts-master-modal .modal-footer .btn {
        flex: 1;
    }

}

/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 991.98px) {

    .ready-stock-page {
        padding: 12px;
    }

    .ready-stock-body {
        padding: 16px;
    }

    .product-details-card {
        grid-template-columns: 150px minmax(0, 1fr);
        gap: 16px;
    }

    .product-image-wrapper {
        width: 150px;
        height: 150px;
    }

    .product-info-grid {
        grid-template-columns: 1fr;
    }

    .product-info-item {
        grid-template-columns: 115px minmax(0, 1fr);
    }

    .assignment-grid {
        grid-template-columns: 1fr 1fr;
    }

    .assignment-grid .rts-field:last-child {
        grid-column: 1 / -1;
    }

    .assignment-action {
        justify-content: stretch;
    }

    .btn-add-stock {
        width: 100%;
    }
}

@media (max-width: 767.98px) {

    .ready-stock-page {
        padding: 8px;
    }

    .ready-stock-header {
        padding: 15px;
    }

    .ready-stock-title {
        font-size: 18px;
    }

    .ready-stock-subtitle {
        font-size: 12px;
    }

    .ready-stock-body {
        padding: 12px;
    }

    .barcode-search-row {
        grid-template-columns: 1fr;
    }

    .barcode-search-btn {
        width: 100%;
    }

    .product-details-card {
        grid-template-columns: 1fr;
        padding: 14px;
    }

    .product-image-wrapper {
        width: 140px;
        height: 140px;
        margin: 0 auto;
    }

    .product-info-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .product-info-item {
        grid-template-columns: 110px minmax(0, 1fr);
    }

    .assignment-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .assignment-grid .rts-field:last-child {
        grid-column: auto;
    }

    .quantity-wrapper {
        width: 100%;
        max-width: none;
    }

    .assignment-action {
        margin-top: 14px;
    }

    .rts-section-title {
        font-size: 15px;
    }
}

@media (max-width: 480px) {

    .ready-stock-title {
        font-size: 17px;
    }

    .ready-stock-header {
        padding: 13px;
    }

    .ready-stock-body {
        padding: 10px;
    }

    .product-details-card {
        padding: 12px;
    }

    .product-image-wrapper {
        width: 120px;
        height: 120px;
    }

    .product-info-item {
        grid-template-columns: 100px minmax(0, 1fr);
    }

    .product-info-item .info-label {
        font-size: 11px;
    }

    .product-info-item .info-value {
        font-size: 12px;
    }

    .rts-input,
    .rts-select,
    .barcode-search-btn {
        height: 40px;
    }
}
</style>


<div class="ready-stock-page">

    <div class="ready-stock-card">

        {{-- =====================================================
             HEADER
             ===================================================== --}}
        <div class="ready-stock-header">

            <div class="ready-stock-title-area">

                <h1 class="ready-stock-title">
                    <i class="bi bi-box-seam me-2"></i>
                    Ready to Sell Stock
                </h1>

                <p class="ready-stock-subtitle">
                    Scan or enter product barcode
                </p>

            </div>

        </div>


        <div class="ready-stock-body">


            {{-- =================================================
                 BARCODE SEARCH
                 ================================================= --}}
            <div class="rts-section">

                <div class="rts-section-title">

                    <i class="bi bi-upc-scan"></i>

                    <span>
                        Product Barcode
                    </span>

                </div>


                <div class="barcode-search-row">

                    <div class="rts-field">

                        <label
                            for="rtsBarcode"
                            class="rts-label">

                            Barcode

                        </label>

                        <input
                            type="text"
                            id="rtsBarcode"
                            class="rts-input"
                            autocomplete="off"
                            placeholder="Scan barcode / Type barcode">

                    </div>


                    <button
                        type="button"
                        id="btnSearchBarcode"
                        class="barcode-search-btn">

                        <i class="bi bi-search me-1"></i>

                        Search

                    </button>

                </div>

            </div>



            {{-- =================================================
                 PRODUCT DETAILS
                 ================================================= --}}
            <div class="rts-section">

                <div class="rts-section-title">

                    <i class="bi bi-info-circle"></i>

                    <span>
                        Product Details
                    </span>

                </div>


                <div
                    class="product-details-card"
                    id="rtsProductDetails">


                    {{-- PRODUCT IMAGE --}}
                    <div class="product-image-wrapper">

                        <img
                            id="rtsProductImage"
                            src=""
                            alt="Product Image">

                        <div
                            id="rtsNoProductImage"
                            class="product-no-image">

                            <i class="bi bi-image"></i>

                            <span>
                                Product Image
                            </span>

                        </div>

                    </div>


                    {{-- PRODUCT INFORMATION --}}
                    <div class="product-info">

                        <div class="product-info-title">
                            Product Information
                        </div>


                        <div class="product-info-grid">


                            <div class="product-info-item">

                                <span class="info-label">
                                    Product Name
                                </span>

                                <strong
                                    id="rtsProductName"
                                    class="info-value">
                                    -
                                </strong>

                            </div>


                            <div class="product-info-item">

                                <span class="info-label">
                                    Barcode
                                </span>

                                <strong
                                    id="rtsProductBarcode"
                                    class="info-value">
                                    -
                                </strong>

                            </div>


                            <div class="product-info-item">

                                <span class="info-label">
                                    SKU
                                </span>

                                <strong
                                    id="rtsProductSku"
                                    class="info-value">
                                    -
                                </strong>

                            </div>


                            <div class="product-info-item">

                                <span class="info-label">
                                    Designer
                                </span>

                                <strong
                                    id="rtsProductDesigner"
                                    class="info-value">
                                    -
                                </strong>

                            </div>


                            <div class="product-info-item">

                                <span class="info-label">
                                    Composition
                                </span>

                                <strong
                                    id="rtsProductComposition"
                                    class="info-value">
                                    -
                                </strong>

                            </div>


                            <div class="product-info-item">

                                <span class="info-label">
                                    Colour
                                </span>

                                <strong
                                    id="rtsProductColour"
                                    class="info-value">
                                    -
                                </strong>

                            </div>


                            <div class="product-info-item">

                                <span class="info-label">
                                    Size
                                </span>

                                <strong
                                    id="rtsProductSize"
                                    class="info-value">
                                    -
                                </strong>

                            </div>


                            <div class="product-info-item">

                                <span class="info-label">
                                    Gender
                                </span>

                                <strong
                                    id="rtsProductGender"
                                    class="info-value">
                                    -
                                </strong>

                            </div>


                            <div class="product-info-item full-width">

                                <span class="info-label">
                                    Manufacturing
                                </span>

                                <strong
                                    id="rtsProductManufacturing"
                                    class="info-value">
                                    -
                                </strong>

                            </div>


                        </div>

                    </div>

                </div>

            </div>



            {{-- =================================================
                 STOCK ASSIGNMENT
                 ================================================= --}}
            <div class="rts-section">

                <div class="rts-section-title">

                    <i class="bi bi-box-arrow-in-down"></i>

                    <span>
                        Stock Assignment
                    </span>

                </div>


                <div class="row g-3">

                    {{-- WAREHOUSE --}}
                    <div class="col-12 col-md-4">

                        <label class="form-label">
                            Warehouse
                        </label>

                        <div class="d-flex gap-2">

                            <select
                                id="rtsWarehouse"
                                class="form-select"
                                disabled>

                                <option value="">
                                    Select Warehouse
                                </option>

                            </select>


                            <button
                                type="button"
                                id="btnAddRtsWarehouse"
                                class="btn btn-primary rts-master-add-btn"
                                title="Add Warehouse">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>


                    {{-- LOCATION --}}
                    <div class="col-12 col-md-4">

                        <label class="form-label">
                            Location
                        </label>

                        <div class="d-flex gap-2">

                            <select
                                id="rtsLocation"
                                class="form-select"
                                disabled>

                                <option value="">
                                    Select Location
                                </option>

                            </select>


                            <button
                                type="button"
                                id="btnAddRtsLocation"
                                class="btn btn-primary rts-master-add-btn"
                                title="Add Location">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>


                    {{-- BOX --}}
                    <div class="col-12 col-md-4">

                        <label class="form-label">
                            Box No.
                        </label>

                        <div class="d-flex gap-2">

                        <select
                            id="rtsBoxNo"
                            class="form-select"
                            disabled>

                            <option value="">
                                Select Box No.
                            </option>

                        </select>


                        <!-- DOWNLOAD BOX QR -->

                        <button
                            type="button"
                            id="btnDownloadBoxQr"
                            class="btn btn-outline-success"
                            title="Download Box QR"
                            style="display:none;">

                            <i class="bi bi-qr-code"></i>

                        </button>


                        <!-- CREATE NEW BOX -->

                        <button
                            type="button"
                            id="btnAddRtsBox"
                            class="btn btn-primary rts-master-add-btn"
                            title="Add Box">

                            <i class="bi bi-plus-lg"></i>

                        </button>

                    </div>

                    </div>

                </div>

            </div>



            {{-- =================================================
                 QUANTITY + ADD TO STOCK LIST
                 ================================================= --}}
            <div class="rts-section">

                <div class="rts-section-title">

                    <i class="bi bi-plus-circle"></i>

                    <span>
                        Add Stock
                    </span>

                </div>

                <div class="row g-3 align-items-end">

                    {{-- QUANTITY --}}
                    <div class="col-12 col-md-4">

                        <label
                            for="rtsQuantity"
                            class="form-label">
                            Quantity
                        </label>

                        <div class="input-group">

                            <button
                                type="button"
                                id="btnRtsQtyMinus"
                                class="btn btn-outline-secondary"
                                aria-label="Decrease quantity">
                                <i class="bi bi-dash-lg"></i>
                            </button>

                            <input
                                type="number"
                                id="rtsQuantity"
                                class="form-control text-center"
                                min="1"
                                step="1"
                                value="1">

                            <button
                                type="button"
                                id="btnRtsQtyPlus"
                                class="btn btn-outline-secondary"
                                aria-label="Increase quantity">
                                <i class="bi bi-plus-lg"></i>
                            </button>

                        </div>

                    </div>


                    {{-- ADD BUTTON --}}
                    <div class="col-12 col-md-8 text-md-end">

                        <button
                            type="button"
                            id="btnAddToStockList"
                            class="btn btn-primary rts-add-stock-btn">

                            <i class="bi bi-plus-circle me-1"></i>
                            Add to Stock List

                        </button>

                    </div>

                </div>

            </div>



            {{-- =================================================
                 STOCK LIST
                 ================================================= --}}
            <div class="rts-section">

                <div class="rts-section-title">

                    <i class="bi bi-list-check"></i>

                    <span>
                        Stock List
                    </span>

                </div>


                <div class="stock-list-wrapper">

                    <table class="stock-list-table">

                        <thead>

                            <tr>

                                <th>
                                    S.No.
                                </th>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Barcode
                                </th>

                                <th>
                                    SKU
                                </th>

                                <th>
                                    Warehouse
                                </th>

                                <th>
                                    Location
                                </th>

                                <th>
                                    Box No.
                                </th>

                                <th>
                                    Qty
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody id="readyStockListBody">

                            <tr id="readyStockEmptyRow">

                                <td
                                    colspan="9"
                                    class="stock-list-empty">

                                    <i class="bi bi-inbox"></i>

                                    <span>
                                        No products added to stock list.
                                    </span>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">

                    <button
                        type="button"
                        id="btnClearReadyStock"
                        class="btn btn-outline-secondary">

                        <i class="bi bi-x-circle me-1"></i>
                        Clear List

                    </button>


                    <button
                        type="button"
                        id="btnSaveReadyStock"
                        class="btn btn-success">

                        <i class="bi bi-check2-circle me-1"></i>
                        Save Stock

                    </button>

                </div>

            </div>


        </div>

    </div>

</div>

{{-- =========================================================
     WAREHOUSE MASTER MODAL
========================================================= --}}

<div
    class="modal fade"
    id="rtsWarehouseMasterModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content rts-master-modal">

            {{-- HEADER --}}
            <div class="modal-header">

                <div>
                    <h5 class="modal-title">
                        <i class="bi bi-building me-2"></i>
                        Warehouse Master
                    </h5>

                    <small class="text-muted">
                        Add new warehouse
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            {{-- BODY --}}
            <div class="modal-body">

                <form
                    id="rtsWarehouseMasterForm"
                    autocomplete="off">

                    <div class="row g-3">

                        {{-- WAREHOUSE NAME --}}
                        <div class="col-12 col-md-6">

                            <label
                                for="rtsMasterWarehouseName"
                                class="form-label">

                                Warehouse Name
                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                id="rtsMasterWarehouseName"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter warehouse name">

                        </div>


                        {{-- TYPE --}}
                        <div class="col-12 col-md-6">

                            <label
                                for="rtsMasterWarehouseType"
                                class="form-label">

                                Type
                                <span class="text-danger">*</span>

                            </label>

                            <select
                                id="rtsMasterWarehouseType"
                                class="form-select">

                                <option value="">
                                    Select Type
                                </option>

                                <option value="warehouse">
                                    Warehouse
                                </option>

                                <option value="department">
                                    Department
                                </option>

                            </select>

                        </div>


                        {{-- COUNTRY --}}
                        <div class="col-12 col-md-4">

                            <label
                                for="rtsMasterCountry"
                                class="form-label">

                                Country

                            </label>

                            <input
                                type="text"
                                id="rtsMasterCountry"
                                class="form-control"
                                value="India"
                                readonly>

                        </div>


                        {{-- STATE --}}
                        <div class="col-12 col-md-4">

                            <label
                                for="rtsMasterState"
                                class="form-label">

                                State
                                <span class="text-danger">*</span>

                            </label>

                            <select
                                id="rtsMasterState"
                                class="form-select">

                                <option value="">
                                    Loading states...
                                </option>

                            </select>

                        </div>


                        {{-- DISTRICT --}}
                        <div class="col-12 col-md-4">

                            <label
                                for="rtsMasterDistrict"
                                class="form-label">

                                District

                            </label>

                            <input
                                type="text"
                                id="rtsMasterDistrict"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter district">

                        </div>


                        {{-- CONTACT PERSON --}}
                        <div class="col-12 col-md-6">

                            <label
                                for="rtsMasterContactPerson"
                                class="form-label">

                                Contact Person

                            </label>

                            <input
                                type="text"
                                id="rtsMasterContactPerson"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter contact person">

                        </div>


                        {{-- PHONE --}}
                        <div class="col-12 col-md-6">

                            <label
                                for="rtsMasterPhone"
                                class="form-label">

                                Phone Number

                            </label>

                            <input
                                type="text"
                                id="rtsMasterPhone"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter phone number">

                        </div>


                        {{-- EMAIL --}}
                        <div class="col-12 col-md-6">

                            <label
                                for="rtsMasterEmail"
                                class="form-label">

                                Email Address

                            </label>

                            <input
                                type="email"
                                id="rtsMasterEmail"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter email address">

                        </div>


                        {{-- PRIMARY ADDRESS --}}
                        <div class="col-12">

                            <label
                                for="rtsMasterPrimaryAddress"
                                class="form-label">

                                Primary Address

                            </label>

                            <textarea
                                id="rtsMasterPrimaryAddress"
                                class="form-control"
                                rows="3"
                                placeholder="Enter primary address"></textarea>

                        </div>

                    </div>

                </form>

            </div>


            {{-- FOOTER --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    Cancel

                </button>


                <button
                    type="button"
                    id="btnSaveRtsWarehouse"
                    class="btn btn-primary">

                    <i class="bi bi-check-lg me-1"></i>

                    Save Warehouse

                </button>

            </div>

        </div>

    </div>

</div>

{{-- =========================================================
     LOCATION MASTER MODAL
========================================================= --}}

<div
    class="modal fade"
    id="rtsLocationMasterModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content rts-master-modal">

            {{-- HEADER --}}
            <div class="modal-header">

                <div>

                    <h5 class="modal-title">

                        <i class="bi bi-geo-alt me-2"></i>

                        Location Master

                    </h5>

                    <small class="text-muted">
                        Add new location
                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            {{-- BODY --}}
            <div class="modal-body">

                <form
                    id="rtsLocationMasterForm"
                    autocomplete="off">

                    <div class="row g-3">

                        {{-- LOCATION NAME --}}
                        <div class="col-12 col-md-6">

                            <label
                                for="rtsMasterLocationName"
                                class="form-label">

                                Location Name
                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                id="rtsMasterLocationName"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter location name">

                        </div>


                        {{-- WAREHOUSE --}}
                        <div class="col-12 col-md-6">

                            <label
                                class="form-label">

                                Warehouse

                            </label>

                            <input
                                type="text"
                                id="rtsMasterLocationWarehouseName"
                                class="form-control"
                                readonly>

                            <input
                                type="hidden"
                                id="rtsMasterLocationWarehouseId">

                        </div>


                        {{-- STATE --}}
                        <div class="col-12 col-md-6">

                            <label
                                class="form-label">

                                State

                            </label>

                            <input
                                type="text"
                                id="rtsMasterLocationState"
                                class="form-control"
                                readonly>

                            <input
                                type="hidden"
                                id="rtsMasterLocationStateId">

                        </div>


                        {{-- WAREHOUSE SECTION --}}
                        <div class="col-12 col-md-6">

                            <label
                                for="rtsMasterWarehouseSection"
                                class="form-label">

                                Warehouse Section

                            </label>

                            <input
                                type="text"
                                id="rtsMasterWarehouseSection"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter section">

                        </div>


                        {{-- FLOOR --}}
                        <div class="col-12 col-md-4">

                            <label
                                for="rtsMasterFloorNumber"
                                class="form-label">

                                Floor Number

                            </label>

                            <input
                                type="text"
                                id="rtsMasterFloorNumber"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter floor">

                        </div>


                        {{-- STACK --}}
                        <div class="col-12 col-md-4">

                            <label
                                for="rtsMasterStackNo"
                                class="form-label">

                                Stack No.

                            </label>

                            <input
                                type="text"
                                id="rtsMasterStackNo"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter stack">

                        </div>


                        {{-- RACK --}}
                        <div class="col-12 col-md-4">

                            <label
                                for="rtsMasterRackNo"
                                class="form-label">

                                Rack Number

                            </label>

                            <input
                                type="text"
                                id="rtsMasterRackNo"
                                class="form-control"
                                maxlength="500"
                                placeholder="Enter rack">

                        </div>

                    </div>

                </form>

            </div>


            {{-- FOOTER --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    Cancel

                </button>


                <button
                    type="button"
                    id="btnSaveRtsLocation"
                    class="btn btn-primary">

                    <i class="bi bi-check-lg me-1"></i>

                    Save Location

                </button>

            </div>

        </div>

    </div>

</div>

{{-- =========================================================
     BOX MASTER MODAL
========================================================= --}}

<div
    class="modal fade"
    id="rtsBoxMasterModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-md">

        <div class="modal-content rts-master-modal">

            {{-- HEADER --}}
            <div class="modal-header">

                <div>

                    <h5 class="modal-title">

                        <i class="bi bi-box-seam me-2"></i>

                        Box Master

                    </h5>

                    <small class="text-muted">
                        Create a new box
                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            {{-- BODY --}}
            <div class="modal-body">

                <form
                    id="rtsBoxMasterForm"
                    autocomplete="off">

                    {{-- WAREHOUSE --}}
                    <div class="mb-3">

                        <label class="form-label">

                            Warehouse

                        </label>

                        <input
                            type="text"
                            id="rtsMasterBoxWarehouseName"
                            class="form-control"
                            readonly>

                        <input
                            type="hidden"
                            id="rtsMasterBoxWarehouseId">

                    </div>


                    {{-- LOCATION --}}
                    <div class="mb-3">

                        <label class="form-label">

                            Location

                        </label>

                        <input
                            type="text"
                            id="rtsMasterBoxLocationName"
                            class="form-control"
                            readonly>

                        <input
                            type="hidden"
                            id="rtsMasterBoxLocationId">

                    </div>


                    {{-- EXISTING TITLE --}}
                    <div class="mb-3">

                        <label
                            for="rtsExistingBoxTitle"
                            class="form-label">

                            Existing Box Title

                        </label>

                        <select
                            id="rtsExistingBoxTitle"
                            class="form-select">

                            <option value="">
                                Select Existing Title
                            </option>

                        </select>

                        <small class="text-muted">
                            Select an existing title or enter a new title below.
                        </small>

                    </div>


                    {{-- NEW TITLE --}}
                    <div class="mb-3">

                        <label
                            for="rtsNewBoxTitle"
                            class="form-label">

                            New Box Title

                        </label>

                        <input
                            type="text"
                            id="rtsNewBoxTitle"
                            class="form-control"
                            maxlength="200"
                            placeholder="Enter new box title">

                    </div>


                    {{-- GENERATED BOX NUMBER --}}
                    <div class="rts-generated-box-preview">

                        <div class="small text-muted">
                            Box No.
                        </div>

                        <strong
                            id="rtsGeneratedBoxNumber">
                            -
                        </strong>

                    </div>

                </form>

            </div>


            {{-- FOOTER --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    Cancel

                </button>


                <button
                    type="button"
                    id="btnSaveRtsBox"
                    class="btn btn-primary">

                    <i class="bi bi-check-lg me-1"></i>

                    Save Box

                </button>

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const warehouseSelect =
            document.getElementById(
                'rtsWarehouse'
            );

        const locationSelect =
            document.getElementById(
                'rtsLocation'
            );

        const boxSelect =
            document.getElementById(
                'rtsBoxNo'
            );
        const downloadBoxQrButton =
            document.getElementById(
                'btnDownloadBoxQr'
            );

        const qtyInput =
            document.getElementById(
                'rtsQuantity'
            );

        const rtsBarcodeInput =
            document.getElementById(
                'rtsBarcode'
            );

        const rtsBarcodeSearchButton =
            document.getElementById(
                'btnSearchBarcode'
            );

        const addStockButton =
            document.getElementById(
                'btnAddToStockList'
            );


        /*
        |--------------------------------------------------------------------------
        | PRODUCT STORAGE
        |--------------------------------------------------------------------------
        */

        window.readyStockProduct = null;

        /*| TEMPORARY STOCK LIST
        |--------------------------------------------------------------------------
        */

        window.readyStockList = [];


        /*
|--------------------------------------------------------------------------
| SAVE READY TO SELL STOCK
|--------------------------------------------------------------------------
*/

const saveReadyStockButton =
    document.getElementById(
        'btnSaveReadyStock'
    );


if (saveReadyStockButton) {

    saveReadyStockButton.addEventListener(
        'click',
        async function () {


            /*
            |--------------------------------------------------------------------------
            | CHECK LIST
            |--------------------------------------------------------------------------
            */

            if (
                !Array.isArray(
                    window.readyStockList
                ) ||
                window.readyStockList.length === 0
            ) {

                showRtsAlert(
                    'warning',
                    'Stock List Empty',
                    'Please add at least one product to the stock list.'
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | CHECK BOXES
            |--------------------------------------------------------------------------
            */

            const uniqueBoxes = [];


            window.readyStockList.forEach(
                function (item) {

                    const alreadyExists =
                        uniqueBoxes.some(
                            function (box) {

                                return String(
                                    box.box_id
                                ) === String(
                                    item.box_id
                                );

                            }
                        );


                    if (!alreadyExists) {

                        uniqueBoxes.push({

                            box_id:
                                item.box_id,

                            box_no:
                                item.box_no

                        });

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | ASK USER ABOUT CLOSING BOX
            |--------------------------------------------------------------------------
            */

            let closeBox =
                false;


            if (
                typeof Swal !==
                'undefined'
            ) {

                const result =
                    await Swal.fire({

                        icon:
                            'question',

                        title:
                            'Close Box?',

                        html:
                            `
                            <div style="text-align:left;">
                                <p>
                                    Stock is ready to be saved.
                                </p>

                                <p class="mb-2">
                                    Do you want to close the selected box
                                    after saving?
                                </p>

                                <strong>
                                    ${uniqueBoxes
                                        .map(
                                            function (box) {
                                                return escapeHtml(
                                                    box.box_no
                                                );
                                            }
                                        )
                                        .join('<br>')}
                                </strong>
                            </div>
                            `,

                        showCancelButton:
                            true,

                        confirmButtonText:
                            'Yes, Save & Close Box',

                        cancelButtonText:
                            'Save & Keep Box Open',

                        reverseButtons:
                            true

                    });


                /*
                |--------------------------------------------------------------------------
                | CANCEL ENTIRE SAVE
                |--------------------------------------------------------------------------
                */

                if (
                    result.isDismissed &&
                    result.dismiss ===
                    Swal.DismissReason.cancel
                ) {

                    /*
                    | Cancel button means:
                    | Save stock but keep box open.
                    */

                    closeBox =
                        false;

                } else if (
                    result.isConfirmed
                ) {

                    closeBox =
                        true;

                } else {

                    return;

                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | FALLBACK
                |--------------------------------------------------------------------------
                */

                closeBox =
                    confirm(
                        'Do you want to close the selected box after saving?'
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | DISABLE BUTTON
            |--------------------------------------------------------------------------
            */

            saveReadyStockButton.disabled =
                true;


            const oldButtonHtml =
                saveReadyStockButton.innerHTML;


            saveReadyStockButton.innerHTML = `

                <span
                    class="spinner-border spinner-border-sm me-1"
                    role="status">
                </span>

                Saving Stock...

            `;


            try {


                /*
                |--------------------------------------------------------------------------
                | CSRF
                |--------------------------------------------------------------------------
                */

                const csrfToken =
                    document
                        .querySelector(
                            'meta[name="csrf-token"]'
                        )
                        ?.getAttribute(
                            'content'
                        );


                /*
                |--------------------------------------------------------------------------
                | SEND TO LARAVEL
                |--------------------------------------------------------------------------
                */

                const response =
                    await fetch(
                        "{{ route('inventory.ready-to-sell-stock.save') }}",
                        {

                            method:
                                'POST',

                            headers: {

                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    csrfToken || ''

                            },

                            body:
                                JSON.stringify({

                                    items:
                                        window.readyStockList,

                                    close_box:
                                        closeBox

                                })

                        }
                    );


                const result =
                    await response.json();


                /*
                |--------------------------------------------------------------------------
                | ERROR
                |--------------------------------------------------------------------------
                */

                if (
                    !response.ok ||
                    !result.success
                ) {

                    throw new Error(
                        result.message ||
                        'Unable to save stock.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | SUCCESS
                |--------------------------------------------------------------------------
                */

                if (
                    typeof Swal !==
                    'undefined'
                ) {

                    await Swal.fire({

                        icon:
                            'success',

                        title:
                            'Stock Saved',

                        text:
                            result.message ||
                            'Stock saved successfully.',

                        confirmButtonText:
                            'OK'

                    });

                } else {

                    alert(
                        result.message ||
                        'Stock saved successfully.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CLEAR TEMPORARY LIST
                |--------------------------------------------------------------------------
                */

                window.readyStockList =
                    [];


                renderReadyStockList();


                /*
                |--------------------------------------------------------------------------
                | RESET PRODUCT
                |--------------------------------------------------------------------------
                */

                clearReadyStockProduct();


                /*
                |--------------------------------------------------------------------------
                | RESET ASSIGNMENT
                |--------------------------------------------------------------------------
                */

                if (warehouseSelect) {

                    warehouseSelect.value =
                        '';

                }


                if (locationSelect) {

                    resetSelect(
                        locationSelect,
                        'Select Location'
                    );

                    locationSelect.disabled =
                        true;

                }


                if (boxSelect) {

                    resetSelect(
                        boxSelect,
                        'Select Box No.'
                    );

                    boxSelect.disabled =
                        true;

                }


                if (qtyInput) {

                    qtyInput.value =
                        1;

                }


                /*
                |--------------------------------------------------------------------------
                | FOCUS BARCODE
                |--------------------------------------------------------------------------
                */

                if (rtsBarcodeInput) {

                    rtsBarcodeInput.value =
                        '';

                    rtsBarcodeInput.focus();

                }


            } catch (error) {


                console.error(
                    'Save Stock Error:',
                    error
                );


                showRtsAlert(
                    'error',
                    'Save Failed',
                    error.message ||
                    'Unable to save stock.'
                );


            } finally {


                /*
                |--------------------------------------------------------------------------
                | RESTORE BUTTON
                |--------------------------------------------------------------------------
                */

                saveReadyStockButton.disabled =
                    false;

                saveReadyStockButton.innerHTML =
                    oldButtonHtml;

            }

        }
    );

}


        
        /*
        |--------------------------------------------------------------------------
        | RESET SELECT
        |--------------------------------------------------------------------------
        */

        function resetSelect(
            selectElement,
            text
        ) {

            if (!selectElement) {
                return;
            }

            selectElement.innerHTML = '';


            const option =
                document.createElement(
                    'option'
                );


            option.value = '';

            option.textContent = text;


            selectElement.appendChild(
                option
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SWEET ALERT
        |--------------------------------------------------------------------------
        */

        function showRtsAlert(
            icon,
            title,
            text
        ) {

            if (
                typeof Swal !==
                'undefined'
            ) {

                return Swal.fire({

                    icon: icon,

                    title: title,

                    text: text

                });

            }


            alert(
                title + '\n' + text
            );

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD WAREHOUSES
        |--------------------------------------------------------------------------
        */

        function loadWarehouses() {

            if (!warehouseSelect) {
                return;
            }


            resetSelect(
                warehouseSelect,
                'Loading warehouses...'
            );


            warehouseSelect.disabled = true;


            fetch(
                "{{ route('inventory.ready-to-sell-stock.warehouses') }}",
                {
                    method: 'GET',

                    headers: {

                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'

                    }

                }
            )

            .then(
                function (response) {

                    if (!response.ok) {

                        throw new Error(
                            'Unable to load warehouses.'
                        );

                    }


                    return response.json();

                }
            )

            .then(
                function (result) {

                    resetSelect(
                        warehouseSelect,
                        'Select Warehouse'
                    );


                    if (
                        !result.success ||
                        !Array.isArray(result.data)
                    ) {

                        throw new Error(
                            'Warehouse data is invalid.'
                        );

                    }


                    if (
                        result.data.length === 0
                    ) {

                        const option =
                            document.createElement(
                                'option'
                            );


                        option.value = '';

                        option.textContent =
                            'No warehouse found';


                        warehouseSelect.appendChild(
                            option
                        );


                        return;

                    }


                    result.data.forEach(
                        function (warehouse) {

                            const option =
                                document.createElement(
                                    'option'
                                );


                            option.value =
                                warehouse.id;


                            option.textContent =
                                warehouse.warehousename ||
                                'Unnamed Warehouse';


                            warehouseSelect.appendChild(
                                option
                            );

                        }
                    );


                    warehouseSelect.disabled =
                        false;

                }
            )

            .catch(
                function (error) {

                    console.error(
                        'Warehouse Error:',
                        error
                    );


                    resetSelect(
                        warehouseSelect,
                        'Unable to load warehouse'
                    );


                    showRtsAlert(
                        'error',
                        'Warehouse Error',
                        'Unable to load warehouse list.'
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | WAREHOUSE CHANGE
        |--------------------------------------------------------------------------
        */

        if (warehouseSelect) {

            warehouseSelect.addEventListener(
                'change',
                function () {

                    const warehouseId =
                        this.value;


                    resetSelect(
                        locationSelect,
                        'Select Location'
                    );


                    resetSelect(
                        boxSelect,
                        'Select Box No.'
                    );


                    locationSelect.disabled =
                        true;

                    boxSelect.disabled =
                        true;


                    if (!warehouseId) {
                        return;
                    }


                    loadLocations(
                        warehouseId
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD LOCATIONS
        |--------------------------------------------------------------------------
        */

        function loadLocations(
            warehouseId
        ) {

            resetSelect(
                locationSelect,
                'Loading locations...'
            );


            locationSelect.disabled = true;


            const url =
                "{{ route('inventory.ready-to-sell-stock.locations') }}" +
                '?warehouse_id=' +
                encodeURIComponent(
                    warehouseId
                );


            fetch(
                url,
                {
                    method: 'GET',

                    headers: {

                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'

                    }

                }
            )

            .then(
                function (response) {

                    if (!response.ok) {

                        throw new Error(
                            'Unable to load locations.'
                        );

                    }


                    return response.json();

                }
            )

            .then(
                function (result) {

                    resetSelect(
                        locationSelect,
                        'Select Location'
                    );


                    if (
                        !result.success ||
                        !Array.isArray(result.data)
                    ) {

                        throw new Error(
                            'Location data is invalid.'
                        );

                    }


                    if (
                        result.data.length === 0
                    ) {

                        const option =
                            document.createElement(
                                'option'
                            );


                        option.value = '';

                        option.textContent =
                            'No location found';


                        locationSelect.appendChild(
                            option
                        );


                        return;

                    }


                    result.data.forEach(
                        function (location) {

                            const option =
                                document.createElement(
                                    'option'
                                );


                            option.value =
                                location.id;


                            let locationText =
                                location.locationname ||
                                'Unnamed Location';


                            const extra = [];


                            if (
                                location.warehousesection
                            ) {

                                extra.push(
                                    location.warehousesection
                                );

                            }


                            if (
                                location.floornumber
                            ) {

                                extra.push(
                                    'Floor ' +
                                    location.floornumber
                                );

                            }


                            if (
                                location.racknumber
                            ) {

                                extra.push(
                                    'Rack ' +
                                    location.racknumber
                                );

                            }


                            if (
                                extra.length
                            ) {

                                locationText +=
                                    ' (' +
                                    extra.join(
                                        ' / '
                                    ) +
                                    ')';

                            }


                            option.textContent =
                                locationText;


                            locationSelect.appendChild(
                                option
                            );

                        }
                    );


                    locationSelect.disabled =
                        false;

                }
            )

            .catch(
                function (error) {

                    console.error(
                        'Location Error:',
                        error
                    );


                    resetSelect(
                        locationSelect,
                        'Unable to load locations'
                    );


                    showRtsAlert(
                        'error',
                        'Location Error',
                        'Unable to load location list.'
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | LOCATION CHANGE
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| LOCATION CHANGE
|--------------------------------------------------------------------------
*/

if (locationSelect) {

    locationSelect.addEventListener(
        'change',
        function () {

            const locationId =
                this.value;


            resetSelect(
                boxSelect,
                'Select Box No.'
            );


            boxSelect.disabled = true;


            if (!locationId) {
                return;
            }


            loadBoxes(
                warehouseSelect.value,
                locationId
            );

        }
    );

}


        /*
|--------------------------------------------------------------------------
| LOAD BOXES
|--------------------------------------------------------------------------
*/

async function loadBoxes(
    warehouseId,
    locationId,
    selectedBoxSno = null
) {

    /*
    |--------------------------------------------------------------------------
    | CHECK WAREHOUSE
    |--------------------------------------------------------------------------
    */

    if (!warehouseId) {

        resetSelect(
            boxSelect,
            'Select Box No.'
        );

        boxSelect.disabled = true;

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK LOCATION
    |--------------------------------------------------------------------------
    */

    if (!locationId) {

        resetSelect(
            boxSelect,
            'Select Box No.'
        );

        boxSelect.disabled = true;

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | LOADING
    |--------------------------------------------------------------------------
    */

    resetSelect(
        boxSelect,
        'Loading boxes...'
    );

    boxSelect.disabled = true;


    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    */

    const url =
        "{{ route('inventory.ready-to-sell-stock.boxes') }}" +
        '?warehouse_id=' +
        encodeURIComponent(
            warehouseId
        ) +
        '&location_id=' +
        encodeURIComponent(
            locationId
        );


    try {

        /*
        |--------------------------------------------------------------------------
        | FETCH
        |--------------------------------------------------------------------------
        */

        const response =
            await fetch(
                url,
                {
                    method: 'GET',

                    headers: {

                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'

                    }
                }
            );


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        const result =
            await response.json();


        if (!response.ok) {

            throw new Error(
                result.message ||
                'Unable to load boxes.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | RESET DROPDOWN
        |--------------------------------------------------------------------------
        */

        resetSelect(
            boxSelect,
            'Select Box No.'
        );


        /*
        |--------------------------------------------------------------------------
        | CHECK SUCCESS
        |--------------------------------------------------------------------------
        */

        if (!result.success) {

            throw new Error(
                result.message ||
                'Unable to load boxes.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | NO BOX FOUND
        |--------------------------------------------------------------------------
        */

        if (
            !Array.isArray(result.data) ||
            result.data.length === 0
        ) {

            const option =
                document.createElement(
                    'option'
                );

            option.value = '';

            option.textContent =
                'No available box found';

            boxSelect.appendChild(
                option
            );

            boxSelect.disabled = true;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ADD BOXES
        |--------------------------------------------------------------------------
        */

        result.data.forEach(
            function (box) {

                const option =
                    document.createElement(
                        'option'
                    );


                /*
                |--------------------------------------------------------------------------
                | VALUE = tbl_boxes.sno
                |--------------------------------------------------------------------------
                */

                option.value =
                   
                        box.id;
                   


                /*
                |--------------------------------------------------------------------------
                | DISPLAY BOX NUMBER
                |--------------------------------------------------------------------------
                */

                option.textContent =
                    box.boxno ||
                    '-';


                /*
                |--------------------------------------------------------------------------
                | STORE BOX INFORMATION
                |--------------------------------------------------------------------------
                */

                option.dataset.boxId =
                    box.id || '';

                option.dataset.boxNo =
                    box.boxno || '';
                option.dataset.qrCode =
                    box.qr_code || box.boxno || '';

                option.dataset.warehouseId =
                    box.warehouseid || '';

                option.dataset.locationId =
                    box.location || '';

                option.dataset.floorNo =
                    box.FloorNo || '';

                option.dataset.stackNo =
                    box.StackNo || '';

                option.dataset.rackNo =
                    box.RackNo || '';


                /*
                |--------------------------------------------------------------------------
                | ADD OPTION
                |--------------------------------------------------------------------------
                */

                boxSelect.appendChild(
                    option
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ENABLE BOX DROPDOWN
        |--------------------------------------------------------------------------
        */

        boxSelect.disabled = false;


        /*
        |--------------------------------------------------------------------------
        | AUTOMATICALLY SELECT NEWLY CREATED BOX
        |--------------------------------------------------------------------------
        */

        if (
            selectedBoxSno !== null &&
            selectedBoxSno !== undefined &&
            selectedBoxSno !== ''
        ) {

            const newBoxSno =
                String(
                    selectedBoxSno
                );


            const newBoxOption =
                Array.from(
                    boxSelect.options
                ).find(
                    function (option) {

                        return String(
                            option.value
                        ) === newBoxSno;

                    }
                );


            if (newBoxOption) {

                boxSelect.value =
                    newBoxSno;


                /*
                |--------------------------------------------------------------------------
                | TRIGGER CHANGE
                |--------------------------------------------------------------------------
                */

                boxSelect.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true
                        }
                    )
                );

            }

        }

    } catch (error) {

        console.error(
            'Box Loading Error:',
            error
        );


        resetSelect(
            boxSelect,
            'Unable to load boxes'
        );


        boxSelect.disabled = true;


        showRtsAlert(
            'error',
            'Box Error',
            error.message ||
            'Unable to load box list.'
        );

    }

}

/*
|--------------------------------------------------------------------------
| DOWNLOAD BOX QR
|--------------------------------------------------------------------------
*/

function downloadBoxQr(
    qrValue,
    boxNo
) {

    if (!qrValue) {

        showRtsAlert(
            'warning',
            'QR Not Available',
            'QR code is not available for this box.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | TEMPORARY QR CONTAINER
    |--------------------------------------------------------------------------
    */

    const qrContainer =
        document.createElement(
            'div'
        );

    qrContainer.style.position =
        'fixed';

    qrContainer.style.left =
        '-99999px';

    qrContainer.style.top =
        '-99999px';

    document.body.appendChild(
        qrContainer
    );


    /*
    |--------------------------------------------------------------------------
    | GENERATE QR
    |--------------------------------------------------------------------------
    */

    new QRCode(
        qrContainer,
        {
            text: qrValue,

            width: 400,

            height: 400,

            correctLevel:
                QRCode.CorrectLevel.H
        }
    );


    /*
    |--------------------------------------------------------------------------
    | WAIT FOR QR IMAGE
    |--------------------------------------------------------------------------
    */

    setTimeout(
        function () {

            const canvas =
                qrContainer.querySelector(
                    'canvas'
                );

            const image =
                qrContainer.querySelector(
                    'img'
                );


            let downloadUrl = '';


            if (canvas) {

                downloadUrl =
                    canvas.toDataURL(
                        'image/png'
                    );

            } else if (image) {

                downloadUrl =
                    image.src;

            }


            if (!downloadUrl) {

                document.body.removeChild(
                    qrContainer
                );

                showRtsAlert(
                    'error',
                    'QR Error',
                    'Unable to generate QR code.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | DOWNLOAD
            |--------------------------------------------------------------------------
            */

            const link =
                document.createElement(
                    'a'
                );

            link.href =
                downloadUrl;

            link.download =
                'BOX_QR_' +
                boxNo
                    .replace(
                        /[^a-zA-Z0-9_-]/g,
                        '_'
                    ) +
                '.png';


            document.body.appendChild(
                link
            );

            link.click();

            document.body.removeChild(
                link
            );


            /*
            |--------------------------------------------------------------------------
            | REMOVE TEMP QR
            |--------------------------------------------------------------------------
            */

            document.body.removeChild(
                qrContainer
            );

        },
        300
    );
}

/*
|--------------------------------------------------------------------------
| BOX CHANGE
|--------------------------------------------------------------------------
*/

if (boxSelect) {

    boxSelect.addEventListener(
        'change',
        function () {

            const selectedOption =
                this.options[
                    this.selectedIndex
                ];


            if (
                !selectedOption ||
                !this.value
            ) {

                if (
                    downloadBoxQrButton
                ) {
                    downloadBoxQrButton.style.display =
                        'none';
                }

                return;
            }


            const qrCode =
                selectedOption.dataset.qrCode ||
                selectedOption.dataset.boxNo ||
                '';


            /*
            |--------------------------------------------------------------------------
            | SHOW QR BUTTON
            |--------------------------------------------------------------------------
            */

            if (
                downloadBoxQrButton &&
                qrCode
            ) {

                downloadBoxQrButton.style.display =
                    'inline-flex';

                downloadBoxQrButton.dataset.qrCode =
                    qrCode;

                downloadBoxQrButton.dataset.boxNo =
                    selectedOption.dataset.boxNo ||
                    '';

            }

        }
    );

}


/*
|--------------------------------------------------------------------------
| DOWNLOAD QR BUTTON CLICK
|--------------------------------------------------------------------------
*/

if (downloadBoxQrButton) {

    downloadBoxQrButton.addEventListener(
        'click',
        function () {

            const qrCode =
                this.dataset.qrCode ||
                '';

            const boxNo =
                this.dataset.boxNo ||
                'BOX';


            downloadBoxQr(
                qrCode,
                boxNo
            );

        }
    );

}
      


        /*
        |--------------------------------------------------------------------------
        | QUANTITY MINUS
        |--------------------------------------------------------------------------
        */

        if (
            document.getElementById(
                'btnRtsQtyMinus'
            )
        ) {

            document
                .getElementById(
                    'btnRtsQtyMinus'
                )
                .addEventListener(
                    'click',
                    function () {

                        let qty =
                            parseInt(
                                qtyInput.value,
                                10
                            ) || 1;


                        qty--;


                        if (qty < 1) {
                            qty = 1;
                        }


                        qtyInput.value =
                            qty;

                    }
                );

        }


        /*
        |--------------------------------------------------------------------------
        | QUANTITY PLUS
        |--------------------------------------------------------------------------
        */

        if (
            document.getElementById(
                'btnRtsQtyPlus'
            )
        ) {

            document
                .getElementById(
                    'btnRtsQtyPlus'
                )
                .addEventListener(
                    'click',
                    function () {

                        let qty =
                            parseInt(
                                qtyInput.value,
                                10
                            ) || 1;


                        qty++;


                        qtyInput.value =
                            qty;

                    }
                );

        }


        /*
|--------------------------------------------------------------------------
| WAREHOUSE MASTER MODAL
|--------------------------------------------------------------------------
*/

const btnAddRtsWarehouse =
    document.getElementById(
        'btnAddRtsWarehouse'
    );


const btnSaveRtsWarehouse =
    document.getElementById(
        'btnSaveRtsWarehouse'
    );




/*
|--------------------------------------------------------------------------
| OPEN WAREHOUSE MASTER
|--------------------------------------------------------------------------
*/

if (btnAddRtsWarehouse) {

    btnAddRtsWarehouse.addEventListener(
        'click',
        function () {

            const modalElement =
                document.getElementById(
                    'rtsWarehouseMasterModal'
                );


            if (!modalElement) {

                console.error(
                    'rtsWarehouseMasterModal not found.'
                );

                return;

            }


            if (
                typeof bootstrap ===
                'undefined'
            ) {

                console.error(
                    'Bootstrap is not loaded.'
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | RESET FORM
            |--------------------------------------------------------------------------
            */

            const form =
                document.getElementById(
                    'rtsWarehouseMasterForm'
                );


            if (form) {

                form.reset();

            }


            /*
            |--------------------------------------------------------------------------
            | COUNTRY
            |--------------------------------------------------------------------------
            */

            const country =
                document.getElementById(
                    'rtsMasterCountry'
                );


            if (country) {

                country.value =
                    'India';

            }


            /*
            |--------------------------------------------------------------------------
            | LOAD STATES
            |--------------------------------------------------------------------------
            */

            loadWarehouseStates();


            /*
            |--------------------------------------------------------------------------
            | SHOW MODAL
            |--------------------------------------------------------------------------
            */

            const modal =
                bootstrap.Modal
                    .getOrCreateInstance(
                        modalElement
                    );


            modal.show();

        }
    );

}

        /*
        |--------------------------------------------------------------------------
        | SAVE WAREHOUSE
        |--------------------------------------------------------------------------
        */
        

if (btnSaveRtsWarehouse) {

    btnSaveRtsWarehouse.addEventListener(
        'click',
        async function () {

            /*
            |--------------------------------------------------------------------------
            | GET FIELDS
            |--------------------------------------------------------------------------
            */

            const warehouseName =
                document
                    .getElementById(
                        'rtsMasterWarehouseName'
                    )
                    .value
                    .trim();


            const warehouseType =
                document
                    .getElementById(
                        'rtsMasterWarehouseType'
                    )
                    .value
                    .trim();


            const stateSelect =
                document.getElementById(
                    'rtsMasterState'
                );


            const stateId =
                stateSelect
                    ? stateSelect.value
                    : '';


            const selectedState =
                stateSelect &&
                stateSelect.selectedIndex >= 0
                    ? stateSelect.options[
                        stateSelect.selectedIndex
                    ]
                    : null;


            const stateName =
                selectedState
                    ? (
                        selectedState.dataset.state ||
                        selectedState.textContent ||
                        ''
                    ).trim()
                    : '';


            /*
            |--------------------------------------------------------------------------
            | VALIDATION - WAREHOUSE NAME
            |--------------------------------------------------------------------------
            */

            if (!warehouseName) {

                await Swal.fire({

                    icon: 'warning',

                    title: 'Warehouse Name Required',

                    text:
                        'Please enter Warehouse Name.'

                });


                document
                    .getElementById(
                        'rtsMasterWarehouseName'
                    )
                    .focus();

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATION - TYPE
            |--------------------------------------------------------------------------
            */

            if (!warehouseType) {

                await Swal.fire({

                    icon: 'warning',

                    title: 'Type Required',

                    text:
                        'Please select Warehouse Type.'

                });


                document
                    .getElementById(
                        'rtsMasterWarehouseType'
                    )
                    .focus();

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATION - STATE
            |--------------------------------------------------------------------------
            */

            if (!stateId) {

                await Swal.fire({

                    icon: 'warning',

                    title: 'State Required',

                    text:
                        'Please select State.'

                });


                if (stateSelect) {
                    stateSelect.focus();
                }

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | FORM DATA
            |--------------------------------------------------------------------------
            */

            const formData =
                new FormData();


            formData.append(
                'warehousename',
                warehouseName
            );


            formData.append(
                'type',
                warehouseType
            );


            formData.append(
                'country',
                document
                    .getElementById(
                        'rtsMasterCountry'
                    )
                    .value
                    .trim()
            );


            formData.append(
                'state_id',
                stateId
            );


            formData.append(
                'state',
                stateName
            );


            formData.append(
                'district',
                document
                    .getElementById(
                        'rtsMasterDistrict'
                    )
                    .value
                    .trim()
            );


            formData.append(
                'contactperson',
                document
                    .getElementById(
                        'rtsMasterContactPerson'
                    )
                    .value
                    .trim()
            );


            formData.append(
                'phonenumber',
                document
                    .getElementById(
                        'rtsMasterPhone'
                    )
                    .value
                    .trim()
            );


            formData.append(
                'emailaddress',
                document
                    .getElementById(
                        'rtsMasterEmail'
                    )
                    .value
                    .trim()
            );


            formData.append(
                'primaryaddress',
                document
                    .getElementById(
                        'rtsMasterPrimaryAddress'
                    )
                    .value
                    .trim()
            );


            /*
            |--------------------------------------------------------------------------
            | SAVE BUTTON LOADING
            |--------------------------------------------------------------------------
            */

            const originalHTML =
                btnSaveRtsWarehouse.innerHTML;


            btnSaveRtsWarehouse.disabled =
                true;


            btnSaveRtsWarehouse.innerHTML =
                `
                <span
                    class="spinner-border spinner-border-sm me-1"
                    role="status">
                </span>
                Saving...
                `;


            try {

                /*
                |--------------------------------------------------------------------------
                | CSRF
                |--------------------------------------------------------------------------
                */

                const csrfTokenElement =
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    );


                const csrfToken =
                    csrfTokenElement
                        ? csrfTokenElement.getAttribute(
                            'content'
                        )
                        : '';


                if (!csrfToken) {

                    throw new Error(
                        'CSRF token not found.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | SAVE TO LARAVEL
                |--------------------------------------------------------------------------
                */

                const response =
                    await fetch(
                        "{{ route('inventory.ready-to-sell-stock.warehouse.store') }}",
                        {

                            method: 'POST',

                            headers: {

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    csrfToken

                            },

                            body:
                                formData

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | READ RESPONSE
                |--------------------------------------------------------------------------
                */

                const result =
                    await response.json();


                /*
                |--------------------------------------------------------------------------
                | LARAVEL ERROR
                |--------------------------------------------------------------------------
                */

                if (!response.ok) {

                    /*
                    |--------------------------------------------------------------------------
                    | Validation errors
                    |--------------------------------------------------------------------------
                    */

                    if (
                        result.errors
                    ) {

                        const firstError =
                            Object.values(
                                result.errors
                            )[0];


                        throw new Error(
                            Array.isArray(firstError)
                                ? firstError[0]
                                : String(firstError)
                        );

                    }


                    throw new Error(
                        result.message ||
                        'Unable to save warehouse.'
                    );

                }


                if (
                    !result.success
                ) {

                    throw new Error(
                        result.message ||
                        'Unable to save warehouse.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CLOSE MODAL
                |--------------------------------------------------------------------------
                */

                const modalElement =
                    document.getElementById(
                        'rtsWarehouseMasterModal'
                    );


                if (modalElement) {

                    const modal =
                        bootstrap.Modal
                            .getOrCreateInstance(
                                modalElement
                            );


                    modal.hide();

                }


                /*
                |--------------------------------------------------------------------------
                | RESET FORM
                |--------------------------------------------------------------------------
                */

                const form =
                    document.getElementById(
                        'rtsWarehouseMasterForm'
                    );


                if (form) {

                    form.reset();

                }


                /*
                |--------------------------------------------------------------------------
                | SET INDIA AGAIN
                |--------------------------------------------------------------------------
                */

                const country =
                    document.getElementById(
                        'rtsMasterCountry'
                    );


                if (country) {

                    country.value =
                        'India';

                }


                /*
                |--------------------------------------------------------------------------
                | SUCCESS MESSAGE
                |--------------------------------------------------------------------------
                */

                await Swal.fire({

                    icon: 'success',

                    title:
                        'Warehouse Created',

                    text:
                        result.message ||
                        'Warehouse created successfully.',

                    confirmButtonText:
                        'OK'

                });


                /*
                |--------------------------------------------------------------------------
                | RELOAD WAREHOUSE DROPDOWN
                |--------------------------------------------------------------------------
                */

                await loadWarehouses();


                /*
                |--------------------------------------------------------------------------
                | SELECT NEW WAREHOUSE
                |--------------------------------------------------------------------------
                */

                if (
                    result.data &&
                    result.data.sno &&
                    warehouseSelect
                ) {

                    warehouseSelect.value =
                        result.data.sno;


                    /*
                    |--------------------------------------------------------------------------
                    | Trigger location loading
                    |--------------------------------------------------------------------------
                    */

                    warehouseSelect.dispatchEvent(
                        new Event(
                            'change'
                        )
                    );

                }

            } catch (error) {

                console.error(
                    'Warehouse Save Error:',
                    error
                );


                await Swal.fire({

                    icon: 'error',

                    title:
                        'Warehouse Save Failed',

                    text:
                        error.message ||
                        'Unable to save warehouse.'

                });

            } finally {

                /*
                |--------------------------------------------------------------------------
                | RESTORE BUTTON
                |--------------------------------------------------------------------------
                */

                btnSaveRtsWarehouse.disabled =
                    false;


                btnSaveRtsWarehouse.innerHTML =
                    originalHTML;

            }

        }
    );

}

/*
|--------------------------------------------------------------------------
| LOCATION MASTER
|--------------------------------------------------------------------------
*/

const btnAddRtsLocation =
    document.getElementById(
        'btnAddRtsLocation'
    );


const btnSaveRtsLocation =
    document.getElementById(
        'btnSaveRtsLocation'
    );


/*
|--------------------------------------------------------------------------
| OPEN LOCATION MODAL
|--------------------------------------------------------------------------
*/

if (btnAddRtsLocation) {

    btnAddRtsLocation.addEventListener(
        'click',
        function () {

            /*
            |--------------------------------------------------------------------------
            | WAREHOUSE MUST BE SELECTED
            |--------------------------------------------------------------------------
            */

            if (
                !warehouseSelect ||
                !warehouseSelect.value
            ) {

                Swal.fire({

                    icon: 'warning',

                    title:
                        'Select Warehouse',

                    text:
                        'Please select a warehouse first.'

                });

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | SELECTED WAREHOUSE
            |--------------------------------------------------------------------------
            */

            const selectedWarehouse =
                warehouseSelect.options[
                    warehouseSelect.selectedIndex
                ];


            const warehouseId =
                warehouseSelect.value;


            const warehouseName =
                selectedWarehouse
                    ? selectedWarehouse.textContent.trim()
                    : '';


            const warehouseState =
                selectedWarehouse
                    ? (
                        selectedWarehouse.dataset.state ||
                        ''
                    )
                    : '';


            const warehouseStateId =
                selectedWarehouse
                    ? (
                        selectedWarehouse.dataset.stateId ||
                        ''
                    )
                    : '';


            /*
            |--------------------------------------------------------------------------
            | RESET FORM
            |--------------------------------------------------------------------------
            */

            const form =
                document.getElementById(
                    'rtsLocationMasterForm'
                );


            if (form) {

                form.reset();

            }


            /*
            |--------------------------------------------------------------------------
            | SET WAREHOUSE
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'rtsMasterLocationWarehouseId'
                )
                .value =
                    warehouseId;


            document
                .getElementById(
                    'rtsMasterLocationWarehouseName'
                )
                .value =
                    warehouseName;


            /*
            |--------------------------------------------------------------------------
            | SET STATE
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'rtsMasterLocationState'
                )
                .value =
                    warehouseState;


            document
                .getElementById(
                    'rtsMasterLocationStateId'
                )
                .value =
                    warehouseStateId;


            /*
            |--------------------------------------------------------------------------
            | SHOW MODAL
            |--------------------------------------------------------------------------
            */

            const modalElement =
                document.getElementById(
                    'rtsLocationMasterModal'
                );


            if (!modalElement) {

                console.error(
                    'rtsLocationMasterModal not found.'
                );

                return;

            }


            const modal =
                bootstrap.Modal
                    .getOrCreateInstance(
                        modalElement
                    );


            modal.show();

        }
    );

}

/*
|--------------------------------------------------------------------------
| SAVE LOCATION
|--------------------------------------------------------------------------
*/

if (btnSaveRtsLocation) {

    btnSaveRtsLocation.addEventListener(
        'click',
        async function () {

            const locationName =
                document
                    .getElementById(
                        'rtsMasterLocationName'
                    )
                    .value
                    .trim();


            const warehouseId =
                document
                    .getElementById(
                        'rtsMasterLocationWarehouseId'
                    )
                    .value
                    .trim();


            const warehouseName =
                document
                    .getElementById(
                        'rtsMasterLocationWarehouseName'
                    )
                    .value
                    .trim();


            const stateId =
                document
                    .getElementById(
                        'rtsMasterLocationStateId'
                    )
                    .value
                    .trim();


            const stateName =
                document
                    .getElementById(
                        'rtsMasterLocationState'
                    )
                    .value
                    .trim();


            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            if (!locationName) {

                await Swal.fire({

                    icon: 'warning',

                    title:
                        'Location Name Required',

                    text:
                        'Please enter Location Name.'

                });


                document
                    .getElementById(
                        'rtsMasterLocationName'
                    )
                    .focus();

                return;

            }


            if (!warehouseId) {

                await Swal.fire({

                    icon: 'warning',

                    title:
                        'Warehouse Required',

                    text:
                        'Please select a warehouse first.'

                });

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | FORM DATA
            |--------------------------------------------------------------------------
            */

            const formData =
                new FormData();


            formData.append(
                'locationname',
                locationName
            );


            formData.append(
                'warehouse_id',
                warehouseId
            );


            formData.append(
                'warehousename',
                warehouseName
            );


            formData.append(
                'state_id',
                stateId
            );


            formData.append(
                'state',
                stateName
            );


            formData.append(
                'warehousesection',
                document
                    .getElementById(
                        'rtsMasterWarehouseSection'
                    )
                    .value
                    .trim()
            );


            formData.append(
                'floornumber',
                document
                    .getElementById(
                        'rtsMasterFloorNumber'
                    )
                    .value
                    .trim()
            );


            formData.append(
                'stackno',
                document
                    .getElementById(
                        'rtsMasterStackNo'
                    )
                    .value
                    .trim()
            );


            formData.append(
                'racknumber',
                document
                    .getElementById(
                        'rtsMasterRackNo'
                    )
                    .value
                    .trim()
            );


            /*
            |--------------------------------------------------------------------------
            | BUTTON LOADING
            |--------------------------------------------------------------------------
            */

            const originalHTML =
                btnSaveRtsLocation.innerHTML;


            btnSaveRtsLocation.disabled =
                true;


            btnSaveRtsLocation.innerHTML =
                `
                <span
                    class="spinner-border spinner-border-sm me-1">
                </span>
                Saving...
                `;


            try {

                const csrfElement =
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    );


                const csrfToken =
                    csrfElement
                        ? csrfElement.getAttribute(
                            'content'
                        )
                        : '';


                if (!csrfToken) {

                    throw new Error(
                        'CSRF token not found.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | SAVE
                |--------------------------------------------------------------------------
                */

                const response =
                    await fetch(
                        "{{ route('inventory.ready-to-sell-stock.location.store') }}",
                        {

                            method: 'POST',

                            headers: {

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    csrfToken

                            },

                            body:
                                formData

                        }
                    );


                const result =
                    await response.json();


                if (!response.ok) {

                    if (result.errors) {

                        const firstError =
                            Object.values(
                                result.errors
                            )[0];


                        throw new Error(
                            Array.isArray(
                                firstError
                            )
                                ? firstError[0]
                                : String(firstError)
                        );

                    }


                    throw new Error(
                        result.message ||
                        'Unable to save location.'
                    );

                }


                if (!result.success) {

                    throw new Error(
                        result.message ||
                        'Unable to save location.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CLOSE MODAL
                |--------------------------------------------------------------------------
                */

                const modalElement =
                    document.getElementById(
                        'rtsLocationMasterModal'
                    );


                const modal =
                    bootstrap.Modal
                        .getOrCreateInstance(
                            modalElement
                        );


                modal.hide();


                /*
                |--------------------------------------------------------------------------
                | RESET
                |--------------------------------------------------------------------------
                */

                const form =
                    document.getElementById(
                        'rtsLocationMasterForm'
                    );


                if (form) {

                    form.reset();

                }


                /*
                |--------------------------------------------------------------------------
                | SUCCESS
                |--------------------------------------------------------------------------
                */

                await Swal.fire({

                    icon: 'success',

                    title:
                        'Location Created',

                    text:
                        result.message ||
                        'Location created successfully.',

                    confirmButtonText:
                        'OK'

                });


                /*
                |--------------------------------------------------------------------------
                | RELOAD LOCATIONS
                |--------------------------------------------------------------------------
                */

                if (
                    warehouseSelect &&
                    warehouseSelect.value
                ) {

                    await loadLocations(
                        warehouseSelect.value
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | SELECT NEW LOCATION
                |--------------------------------------------------------------------------
                */

                if (
                    result.data &&
                    result.data.sno &&
                    locationSelect
                ) {

                    locationSelect.value =
                        result.data.sno;


                    locationSelect.dispatchEvent(
                        new Event(
                            'change'
                        )
                    );

                }

            } catch (error) {

                console.error(
                    'Location Save Error:',
                    error
                );


                await Swal.fire({

                    icon: 'error',

                    title:
                        'Location Save Failed',

                    text:
                        error.message ||
                        'Unable to save location.'

                });

            } finally {

                btnSaveRtsLocation.disabled =
                    false;


                btnSaveRtsLocation.innerHTML =
                    originalHTML;

            }

        }
    );

}

/*
|--------------------------------------------------------------------------
| BOX MASTER
|--------------------------------------------------------------------------
*/

const btnAddRtsBox =
    document.getElementById(
        'btnAddRtsBox'
    );


const btnSaveRtsBox =
    document.getElementById(
        'btnSaveRtsBox'
    );


const rtsExistingBoxTitle =
    document.getElementById(
        'rtsExistingBoxTitle'
    );


const rtsNewBoxTitle =
    document.getElementById(
        'rtsNewBoxTitle'
    );


const rtsGeneratedBoxNumber =
    document.getElementById(
        'rtsGeneratedBoxNumber'
    );


/*
|--------------------------------------------------------------------------
| OPEN BOX MASTER
|--------------------------------------------------------------------------
*/

if (btnAddRtsBox) {

    btnAddRtsBox.addEventListener(
        'click',
        async function () {

            /*
            |--------------------------------------------------------------------------
            | WAREHOUSE
            |--------------------------------------------------------------------------
            */

            if (
                !warehouseSelect ||
                !warehouseSelect.value
            ) {

                await Swal.fire({

                    icon: 'warning',

                    title:
                        'Select Warehouse',

                    text:
                        'Please select a warehouse first.'

                });

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            if (
                !locationSelect ||
                !locationSelect.value
            ) {

                await Swal.fire({

                    icon: 'warning',

                    title:
                        'Select Location',

                    text:
                        'Please select a location first.'

                });

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | SELECTED WAREHOUSE
            |--------------------------------------------------------------------------
            */

            const warehouseOption =
                warehouseSelect.options[
                    warehouseSelect.selectedIndex
                ];


            const warehouseId =
                warehouseSelect.value;


            const warehouseName =
                warehouseOption
                    ? warehouseOption.textContent.trim()
                    : '';


            /*
            |--------------------------------------------------------------------------
            | SELECTED LOCATION
            |--------------------------------------------------------------------------
            */

            const locationOption =
                locationSelect.options[
                    locationSelect.selectedIndex
                ];


            const locationId =
                locationSelect.value;


            const locationName =
                locationOption
                    ? locationOption.textContent.trim()
                    : '';


            /*
            |--------------------------------------------------------------------------
            | RESET
            |--------------------------------------------------------------------------
            */

            const form =
                document.getElementById(
                    'rtsBoxMasterForm'
                );


            if (form) {

                form.reset();

            }


            /*
            |--------------------------------------------------------------------------
            | SET WAREHOUSE
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'rtsMasterBoxWarehouseId'
                )
                .value =
                    warehouseId;


            document
                .getElementById(
                    'rtsMasterBoxWarehouseName'
                )
                .value =
                    warehouseName;


            /*
            |--------------------------------------------------------------------------
            | SET LOCATION
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'rtsMasterBoxLocationId'
                )
                .value =
                    locationId;


            document
                .getElementById(
                    'rtsMasterBoxLocationName'
                )
                .value =
                    locationName;


            /*
            |--------------------------------------------------------------------------
            | LOAD EXISTING BOX TITLES
            |--------------------------------------------------------------------------
            */

            rtsExistingBoxTitle.innerHTML =
                '<option value="">Loading titles...</option>';


            try {

                const response =
                    await fetch(
                        "{{ route('inventory.ready-to-sell-stock.box-titles') }}" +
                        '?warehouse_id=' +
                        encodeURIComponent(
                            warehouseId
                        ) +
                        '&location_id=' +
                        encodeURIComponent(
                            locationId
                        ),
                        {

                            method: 'GET',

                            headers: {

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'

                            }

                        }
                    );


                const result =
                    await response.json();


                if (!response.ok) {

                    throw new Error(
                        result.message ||
                        'Unable to load box titles.'
                    );

                }


                rtsExistingBoxTitle.innerHTML =
                    '<option value="">Select Existing Title</option>';


                if (
                    result.success &&
                    Array.isArray(
                        result.data
                    )
                ) {

                  
                       result.data.forEach(
    function (item) {

        const option =
            document.createElement(
                'option'
            );


        /*
        |--------------------------------------------------------------------------
        | BOX TITLE
        |--------------------------------------------------------------------------
        */

        option.value =
            item.box_title;


        option.textContent =
            item.box_title;


        /*
        |--------------------------------------------------------------------------
        | NEXT ID_MAX
        |--------------------------------------------------------------------------
        */

        option.dataset.nextIdMax =
            item.next_id_max || 1;


        /*
        |--------------------------------------------------------------------------
        | ADD OPTION
        |--------------------------------------------------------------------------
        */

        rtsExistingBoxTitle.appendChild(
            option
        );

    }
);
                }

            } catch (error) {

                console.error(
                    'Box Title Error:',
                    error
                );


                rtsExistingBoxTitle.innerHTML =
                    '<option value="">Unable to load titles</option>';


                await Swal.fire({

                    icon: 'error',

                    title:
                        'Box Title Error',

                    text:
                        error.message

                });

            }


            /*
            |--------------------------------------------------------------------------
            | RESET PREVIEW
            |--------------------------------------------------------------------------
            */

            rtsGeneratedBoxNumber.textContent =
                '-';


            /*
            |--------------------------------------------------------------------------
            | SHOW MODAL
            |--------------------------------------------------------------------------
            */

            const modalElement =
                document.getElementById(
                    'rtsBoxMasterModal'
                );


            const modal =
                bootstrap.Modal
                    .getOrCreateInstance(
                        modalElement
                    );


            modal.show();

        }
    );

}

/*
|--------------------------------------------------------------------------
| EXISTING TITLE SELECTED
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| EXISTING TITLE SELECTED
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| EXISTING TITLE SELECTED
|--------------------------------------------------------------------------
*/

if (rtsExistingBoxTitle) {

    rtsExistingBoxTitle.addEventListener(
        'change',
        function () {

            const selectedOption =
                this.options[
                    this.selectedIndex
                ];


            const selectedTitle =
                this.value.trim();


            /*
            |--------------------------------------------------------------------------
            | CLEAR NEW TITLE
            |--------------------------------------------------------------------------
            */

            if (rtsNewBoxTitle) {
                rtsNewBoxTitle.value = '';
            }


            /*
            |--------------------------------------------------------------------------
            | NOTHING SELECTED
            |--------------------------------------------------------------------------
            */

            if (!selectedTitle) {

                rtsGeneratedBoxNumber.textContent =
                    '-';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | GET NEXT ID_MAX
            |--------------------------------------------------------------------------
            */

            const nextIdMax =
                parseInt(
                    selectedOption.dataset.nextIdMax,
                    10
                ) || 1;


            /*
            |--------------------------------------------------------------------------
            | GENERATE BOX NUMBER
            |--------------------------------------------------------------------------
            */

            const boxNumber =
                selectedTitle +
                '-' +
                nextIdMax;


            /*
            |--------------------------------------------------------------------------
            | SHOW PREVIEW
            |--------------------------------------------------------------------------
            */

            rtsGeneratedBoxNumber.textContent =
                boxNumber;

        }
    );

}

/*
|--------------------------------------------------------------------------
| NEW TITLE ENTERED
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| NEW TITLE ENTERED
|--------------------------------------------------------------------------
*/

if (rtsNewBoxTitle) {

    rtsNewBoxTitle.addEventListener(
        'input',
        function () {

            const newTitle =
                this.value.trim();


            /*
            |--------------------------------------------------------------------------
            | CLEAR EXISTING TITLE
            |--------------------------------------------------------------------------
            */

            if (newTitle) {
                rtsExistingBoxTitle.value = '';
            }


            /*
            |--------------------------------------------------------------------------
            | EMPTY
            |--------------------------------------------------------------------------
            */

            if (!newTitle) {

                rtsGeneratedBoxNumber.textContent =
                    '-';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | NEW TITLE STARTS FROM 1
            |--------------------------------------------------------------------------
            */

            rtsGeneratedBoxNumber.textContent =
                newTitle +
                '-1';

        }
    );

}

/*
|--------------------------------------------------------------------------
| SAVE BOX
|--------------------------------------------------------------------------
*/

if (btnSaveRtsBox) {

    btnSaveRtsBox.addEventListener(
        'click',
        async function () {

            const warehouseId =
                document
                    .getElementById(
                        'rtsMasterBoxWarehouseId'
                    )
                    .value
                    .trim();


            const locationId =
                document
                    .getElementById(
                        'rtsMasterBoxLocationId'
                    )
                    .value
                    .trim();


            /*
            |--------------------------------------------------------------------------
            | TITLE
            |--------------------------------------------------------------------------
            */

            const existingTitle =
                rtsExistingBoxTitle.value.trim();


            const newTitle =
                rtsNewBoxTitle.value.trim();


            const boxTitle =
                newTitle ||
                existingTitle;


            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            if (!warehouseId) {

                await Swal.fire({

                    icon: 'warning',

                    title:
                        'Warehouse Required',

                    text:
                        'Please select Warehouse.'

                });

                return;

            }


            if (!locationId) {

                await Swal.fire({

                    icon: 'warning',

                    title:
                        'Location Required',

                    text:
                        'Please select Location.'

                });

                return;

            }


            if (!boxTitle) {

                await Swal.fire({

                    icon: 'warning',

                    title:
                        'Box Title Required',

                    text:
                        'Please select an existing Box Title or enter a new Box Title.'

                });


                if (newTitle === '') {

                    rtsNewBoxTitle.focus();

                }

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | FORM DATA
            |--------------------------------------------------------------------------
            */

            const formData =
                new FormData();


            formData.append(
                'warehouse_id',
                warehouseId
            );


            formData.append(
                'location_id',
                locationId
            );


            formData.append(
                'box_title',
                boxTitle
            );


            /*
            |--------------------------------------------------------------------------
            | BUTTON LOADING
            |--------------------------------------------------------------------------
            */

            const originalHTML =
                btnSaveRtsBox.innerHTML;


            btnSaveRtsBox.disabled =
                true;


            btnSaveRtsBox.innerHTML =
                `
                <span
                    class="spinner-border spinner-border-sm me-1">
                </span>
                Saving...
                `;


            try {

                const csrfElement =
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    );


                const csrfToken =
                    csrfElement
                        ? csrfElement.getAttribute(
                            'content'
                        )
                        : '';


                if (!csrfToken) {

                    throw new Error(
                        'CSRF token not found.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | SAVE
                |--------------------------------------------------------------------------
                */

                const response =
                    await fetch(
                        "{{ route('inventory.ready-to-sell-stock.box.store') }}",
                        {

                            method: 'POST',

                            headers: {

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    csrfToken

                            },

                            body:
                                formData

                        }
                    );


                const result =
                    await response.json();


                if (!response.ok) {

                    if (result.errors) {

                        const firstError =
                            Object.values(
                                result.errors
                            )[0];


                        throw new Error(
                            Array.isArray(
                                firstError
                            )
                                ? firstError[0]
                                : String(firstError)
                        );

                    }


                    throw new Error(
                        result.message ||
                        'Unable to save box.'
                    );

                }


                if (!result.success) {

                    throw new Error(
                        result.message ||
                        'Unable to save box.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CLOSE MODAL
                |--------------------------------------------------------------------------
                */

                const modalElement =
                    document.getElementById(
                        'rtsBoxMasterModal'
                    );


                const modal =
                    bootstrap.Modal
                        .getOrCreateInstance(
                            modalElement
                        );


                modal.hide();


                /*
                |--------------------------------------------------------------------------
                | SUCCESS
                |--------------------------------------------------------------------------
                */

                await Swal.fire({

                    icon: 'success',

                    title:
                        'Box Created',

                    text:
                        result.message ||
                        'Box created successfully.',

                    confirmButtonText:
                        'OK'

                });


                /*
                |--------------------------------------------------------------------------
                | RELOAD BOX LIST
                |--------------------------------------------------------------------------
                */

                /*
            |--------------------------------------------------------------------------
            | RELOAD BOX LIST
            |--------------------------------------------------------------------------
            */

            if (
                warehouseSelect &&
                warehouseSelect.value &&
                locationSelect &&
                locationSelect.value
            ) {

                 await loadBoxes(
                    warehouseSelect.value,
                    locationSelect.value,
                    result.data
                        ? result.data.sno
                        : null
                );

            }


            } catch (error) {

                console.error(
                    'Box Save Error:',
                    error
                );


                await Swal.fire({

                    icon: 'error',

                    title:
                        'Box Save Failed',

                    text:
                        error.message ||
                        'Unable to save box.'

                });

            } finally {

                btnSaveRtsBox.disabled =
                    false;


                btnSaveRtsBox.innerHTML =
                    originalHTML;

            }

        }
    );

}



        /*
        |--------------------------------------------------------------------------
        | BARCODE SEARCH
        |--------------------------------------------------------------------------
        */

        function searchProductByBarcode() {

            const barcode =
                rtsBarcodeInput.value.trim();


            /*
            |--------------------------------------------------------------------------
            | Validation
            |--------------------------------------------------------------------------
            */

            if (!barcode) {

                showRtsAlert(
                    'warning',
                    'Barcode Required',
                    'Please scan or enter a barcode.'
                );


                rtsBarcodeInput.focus();

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Save Original Button
            |--------------------------------------------------------------------------
            */

            const originalButtonHTML =
                rtsBarcodeSearchButton.innerHTML;


            /*
            |--------------------------------------------------------------------------
            | Loading
            |--------------------------------------------------------------------------
            */

            rtsBarcodeSearchButton.disabled =
                true;


            rtsBarcodeSearchButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span> Searching...';


            /*
            |--------------------------------------------------------------------------
            | Clear Previous Product
            |--------------------------------------------------------------------------
            */

            clearReadyStockProduct();


            /*
            |--------------------------------------------------------------------------
            | API URL
            |--------------------------------------------------------------------------
            */

            const url =
                "{{ route('design-specifications.find-by-barcode') }}" +
                '?barcode=' +
                encodeURIComponent(
                    barcode
                );


            /*
            |--------------------------------------------------------------------------
            | FETCH
            |--------------------------------------------------------------------------
            */

            fetch(
                url,
                {
                    method: 'GET',

                    headers: {

                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'

                    }

                }
            )

            .then(
                function (response) {

                    return response
                        .json()
                        .then(
                            function (data) {

                                return {

                                    ok:
                                        response.ok,

                                    data:
                                        data

                                };

                            }
                        );

                }
            )

            .then(
                function (result) {

                    if (
                        !result.ok ||
                        !result.data.success
                    ) {

                        throw new Error(

                            result.data.message ||
                            'Product not found.'

                        );

                    }


                    const product =
                        result.data.data;


                    displayReadyStockProduct(
                        product
                    );

                }
            )

            .catch(
                function (error) {

                    console.error(
                        'Barcode Search Error:',
                        error
                    );


                    clearReadyStockProduct();


                    showRtsAlert(
                        'error',
                        'Product Not Found',
                        error.message ||
                        'No product found for this barcode.'
                    );


                    rtsBarcodeInput.focus();

                }
            )

            .finally(
                function () {

                    rtsBarcodeSearchButton.disabled =
                        false;


                    rtsBarcodeSearchButton.innerHTML =
                        originalButtonHTML;

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH BUTTON
        |--------------------------------------------------------------------------
        */

        if (
            rtsBarcodeSearchButton
        ) {

            rtsBarcodeSearchButton.addEventListener(
                'click',
                function () {

                    searchProductByBarcode();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | BARCODE SCANNER ENTER
        |--------------------------------------------------------------------------
        |
        | ONLY ONE ENTER HANDLER
        |--------------------------------------------------------------------------
        */

        if (
            rtsBarcodeInput
        ) {

            rtsBarcodeInput.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key ===
                        'Enter'
                    ) {

                        event.preventDefault();

                        searchProductByBarcode();

                    }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | DISPLAY PRODUCT
        |--------------------------------------------------------------------------
        */

        function displayReadyStockProduct(
            product
        ) {

            console.log(
                'Product Found:',
                product
            );


            setReadyStockValue(
                'rtsProductName',
                product.item_name_text
            );


            setReadyStockValue(
                'rtsProductBarcode',
                product.barcode
            );


            setReadyStockValue(
                'rtsProductSku',
                product.sku
            );


            setReadyStockValue(
                'rtsProductDesigner',
                product.designer_name_text
            );


            setReadyStockValue(
                'rtsProductComposition',
                product.composition_text
            );


            setReadyStockValue(
                'rtsProductColour',
                product.colour_text
            );


            setReadyStockValue(
                'rtsProductSize',
                product.size_text
            );


            setReadyStockValue(
                'rtsProductGender',
                product.gender_text
            );


            setReadyStockValue(
                'rtsProductManufacturing',
                product.manufacturing_process_text
            );


            /*
            |--------------------------------------------------------------------------
            | IMAGE
            |--------------------------------------------------------------------------
            */

            const image =
                document.getElementById(
                    'rtsProductImage'
                );

            const noImage =
                document.getElementById(
                    'rtsNoProductImage'
                );


            if (
                product.image_url &&
                image
            ) {

                image.src =
                    product.image_url;


                image.style.display =
                    'block';


                if (noImage) {

                    noImage.style.display =
                        'none';

                }


                image.onerror =
                    function () {

                        image.style.display =
                            'none';


                        if (noImage) {

                            noImage.style.display =
                                'flex';

                        }

                    };

            } else {

                if (image) {

                    image.removeAttribute(
                        'src'
                    );


                    image.style.display =
                        'none';

                }


                if (noImage) {

                    noImage.style.display =
                        'flex';

                }

            }


            /*
            |--------------------------------------------------------------------------
            | STORE PRODUCT
            |--------------------------------------------------------------------------
            */

            window.readyStockProduct =
                product;


            /*
            |--------------------------------------------------------------------------
            | ENABLE ASSIGNMENT
            |--------------------------------------------------------------------------
            */

            enableStockAssignment();


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            showRtsAlert(
                'success',
                'Product Found',
                product.item_name_text ||
                'Product loaded successfully.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SET PRODUCT VALUE
        |--------------------------------------------------------------------------
        */

        function setReadyStockValue(
            elementId,
            value
        ) {

            const element =
                document.getElementById(
                    elementId
                );


            if (!element) {
                return;
            }


            const finalValue =
                value === null ||
                value === undefined ||
                String(value).trim() === ''
                    ? '-'
                    : String(value);


            element.textContent =
                finalValue;

        }


        /*
        |--------------------------------------------------------------------------
        | CLEAR PRODUCT
        |--------------------------------------------------------------------------
        */

        function clearReadyStockProduct() {

            const fields = [

                'rtsProductName',

                'rtsProductBarcode',

                'rtsProductSku',

                'rtsProductDesigner',

                'rtsProductComposition',

                'rtsProductColour',

                'rtsProductSize',

                'rtsProductGender',

                'rtsProductManufacturing'

            ];


            fields.forEach(
                function (id) {

                    setReadyStockValue(
                        id,
                        '-'
                    );

                }
            );


            const image =
                document.getElementById(
                    'rtsProductImage'
                );


            const noImage =
                document.getElementById(
                    'rtsNoProductImage'
                );


            if (image) {

                image.removeAttribute(
                    'src'
                );


                image.style.display =
                    'none';

            }


            if (noImage) {

                noImage.style.display =
                    'flex';

            }


            window.readyStockProduct =
                null;


            disableStockAssignment();

        }


        /*
        |--------------------------------------------------------------------------
        | DISABLE ASSIGNMENT
        |--------------------------------------------------------------------------
        */

        function disableStockAssignment() {

            if (warehouseSelect) {

                warehouseSelect.disabled =
                    true;

            }


            if (locationSelect) {

                locationSelect.disabled =
                    true;

            }


            if (boxSelect) {

                boxSelect.disabled =
                    true;

            }


            if (addStockButton) {

                addStockButton.disabled =
                    true;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | ENABLE ASSIGNMENT
        |--------------------------------------------------------------------------
        */

        function enableStockAssignment() {

            if (warehouseSelect) {

                warehouseSelect.disabled =
                    false;

            }


            /*
            |--------------------------------------------------------------------------
            | Location and Box stay disabled
            | until warehouse/location selected.
            |--------------------------------------------------------------------------
            */

            if (locationSelect) {

                locationSelect.disabled =
                    true;

            }


            if (boxSelect) {

                boxSelect.disabled =
                    true;

            }


            if (addStockButton) {

                addStockButton.disabled =
                    false;

            }

        }

        /*
|--------------------------------------------------------------------------
| ADD TO STOCK LIST
|--------------------------------------------------------------------------
*/

if (addStockButton) {

    addStockButton.addEventListener(
        'click',
        function () {

            /*
            |--------------------------------------------------------------------------
            | PRODUCT VALIDATION
            |--------------------------------------------------------------------------
            */

            if (!window.readyStockProduct) {

                showRtsAlert(
                    'warning',
                    'Product Required',
                    'Please scan or search a product barcode first.'
                );

                if (rtsBarcodeInput) {
                    rtsBarcodeInput.focus();
                }

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | WAREHOUSE
            |--------------------------------------------------------------------------
            */

            const warehouseId =
                warehouseSelect
                    ? warehouseSelect.value
                    : '';


            const warehouseName =
                warehouseSelect &&
                warehouseSelect.selectedIndex >= 0
                    ? warehouseSelect
                        .options[
                            warehouseSelect.selectedIndex
                        ]
                        .textContent
                        .trim()
                    : '';


            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            const locationId =
                locationSelect
                    ? locationSelect.value
                    : '';


            const locationName =
                locationSelect &&
                locationSelect.selectedIndex >= 0
                    ? locationSelect
                        .options[
                            locationSelect.selectedIndex
                        ]
                        .textContent
                        .trim()
                    : '';


            /*
            |--------------------------------------------------------------------------
            | BOX
            |--------------------------------------------------------------------------
            */

            const boxId =
                boxSelect
                    ? boxSelect.value
                    : '';


            const boxNo =
                boxSelect &&
                boxSelect.selectedIndex >= 0
                    ? boxSelect
                        .options[
                            boxSelect.selectedIndex
                        ]
                        .textContent
                        .trim()
                    : '';


            /*
            |--------------------------------------------------------------------------
            | QUANTITY
            |--------------------------------------------------------------------------
            */

            const quantity =
                qtyInput
                    ? parseInt(
                        qtyInput.value,
                        10
                    )
                    : 0;


            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            if (!warehouseId) {

                showRtsAlert(
                    'warning',
                    'Warehouse Required',
                    'Please select Warehouse.'
                );

                if (warehouseSelect) {
                    warehouseSelect.focus();
                }

                return;
            }


            if (!locationId) {

                showRtsAlert(
                    'warning',
                    'Location Required',
                    'Please select Location.'
                );

                if (locationSelect) {
                    locationSelect.focus();
                }

                return;
            }


            if (!boxId) {

                showRtsAlert(
                    'warning',
                    'Box Required',
                    'Please select Box No.'
                );

                if (boxSelect) {
                    boxSelect.focus();
                }

                return;
            }


            if (!quantity || quantity < 1) {

                showRtsAlert(
                    'warning',
                    'Quantity Required',
                    'Please enter a valid quantity.'
                );

                if (qtyInput) {
                    qtyInput.focus();
                }

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | PRODUCT
            |--------------------------------------------------------------------------
            */

            const product =
                window.readyStockProduct;


            const barcode =
                String(
                    product.barcode || ''
                ).trim();


            const sku =
                String(
                    product.sku || ''
                ).trim();


            const productName =
                String(
                    product.item_name_text || ''
                ).trim();


            if (!barcode) {

                showRtsAlert(
                    'warning',
                    'Barcode Missing',
                    'Product barcode is missing.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CHECK DUPLICATE
            |--------------------------------------------------------------------------
            |
            | Same Product + Warehouse + Location + Box
            | = Increase Quantity
            |
            */

            const duplicateIndex =
                window.readyStockList.findIndex(
                    function (item) {

                        return (

                            String(
                                item.barcode
                            ) === barcode &&

                            String(
                                item.warehouse_id
                            ) === String(
                                warehouseId
                            ) &&

                            String(
                                item.location_id
                            ) === String(
                                locationId
                            ) &&

                            String(
                                item.box_id
                            ) === String(
                                boxId
                            )

                        );

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | DUPLICATE
            |--------------------------------------------------------------------------
            */

            if (duplicateIndex !== -1) {

                window.readyStockList[
                    duplicateIndex
                ].quantity += quantity;

            } else {

                /*
                |--------------------------------------------------------------------------
                | ADD NEW ITEM
                |--------------------------------------------------------------------------
                */

                window.readyStockList.push({

                    product_id:
                        product.id ||
                        product.sno ||
                        product.specification_id ||
                        '',

                    product_name:
                        productName || '-',

                    barcode:
                        barcode,

                    sku:
                        sku || '-',

                    warehouse_id:
                        warehouseId,

                    warehouse_name:
                        warehouseName,

                    location_id:
                        locationId,

                    location_name:
                        locationName,

                    box_id:
                        boxId,

                    box_no:
                        boxNo,

                    quantity:
                        quantity

                });

            }


            /*
            |--------------------------------------------------------------------------
            | REFRESH STOCK LIST
            |--------------------------------------------------------------------------
            */

            renderReadyStockList();


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            showRtsAlert(
                'success',
                'Added Successfully',
                'Product has been added to the stock list.'
            );
            


            /*
            |--------------------------------------------------------------------------
            | RESET QUANTITY
            |--------------------------------------------------------------------------
            */

            if (qtyInput) {
                qtyInput.value = 1;
            }

            /*
        |--------------------------------------------------------------------------
        | CLEAR PRODUCT DETAILS
        |--------------------------------------------------------------------------
        */

        clearReadyStockProduct();


    /*
    |--------------------------------------------------------------------------
    | CLEAR BARCODE INPUT
    |--------------------------------------------------------------------------
    */

    if (rtsBarcodeInput) {
        rtsBarcodeInput.value = '';
    }


    /*
    |--------------------------------------------------------------------------
    | FOCUS BARCODE INPUT
    |--------------------------------------------------------------------------
    */

        if (rtsBarcodeInput) {

            setTimeout(function () {

                rtsBarcodeInput.focus();

            }, 100);

        }

        }
    );

}


/*
|--------------------------------------------------------------------------
| REMOVE STOCK LIST ITEM
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'click',
    function (event) {

        const button =
            event.target.closest(
                '.btnRemoveReadyStock'
            );


        if (!button) {
            return;
        }


        const index =
            parseInt(
                button.dataset.index,
                10
            );


        if (
            Number.isNaN(index) ||
            !window.readyStockList[index]
        ) {
            return;
        }


        if (
            typeof Swal === 'undefined'
        ) {

            window.readyStockList.splice(
                index,
                1
            );

            renderReadyStockList();

            return;
        }


        Swal.fire({

            icon: 'question',

            title: 'Remove Product?',

            text:
                'Do you want to remove this product from the stock list?',

            showCancelButton: true,

            confirmButtonText:
                'Yes, Remove',

            cancelButtonText:
                'Cancel'

        }).then(
            function (result) {

                if (
                    result.isConfirmed
                ) {

                    window.readyStockList.splice(
                        index,
                        1
                    );

                    renderReadyStockList();

                }

            }
        );

    }
);


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
|
| IMPORTANT:
| This function is INSIDE DOMContentLoaded.
|
*/

function escapeHtml(value) {

    return String(
        value ?? ''
    )
    .replace(
        /&/g,
        '&amp;'
    )
    .replace(
        /</g,
        '&lt;'
    )
    .replace(
        />/g,
        '&gt;'
    )
    .replace(
        /"/g,
        '&quot;'
    )
    .replace(
        /'/g,
        '&#039;'
    );

}


/*
|--------------------------------------------------------------------------
| RENDER STOCK LIST
|--------------------------------------------------------------------------
*/

function renderReadyStockList() {

    const tbody =
        document.getElementById(
            'readyStockListBody'
        );


    if (!tbody) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CLEAR
    |--------------------------------------------------------------------------
    */

    tbody.innerHTML = '';


    /*
    |--------------------------------------------------------------------------
    | EMPTY
    |--------------------------------------------------------------------------
    */

    if (
        !Array.isArray(
            window.readyStockList
        ) ||
        window.readyStockList.length === 0
    ) {

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="9"
                    class="text-center text-muted py-4">

                    <i
                        class="bi bi-inbox"
                        style="font-size:28px;">
                    </i>

                    <div class="mt-2">
                        No products added to stock list.
                    </div>

                </td>

            </tr>

        `;

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE ROWS
    |--------------------------------------------------------------------------
    */

    window.readyStockList.forEach(
        function (item, index) {

            const row =
                document.createElement(
                    'tr'
                );


            row.innerHTML = `

                <td>
                    ${index + 1}
                </td>


                <td>
                    <strong>
                        ${escapeHtml(
                            item.product_name
                        )}
                    </strong>
                </td>


                <td>
                    <span class="text-primary">
                        ${escapeHtml(
                            item.barcode
                        )}
                    </span>
                </td>


                <td>
                    ${escapeHtml(
                        item.sku
                    )}
                </td>


                <td>
                    ${escapeHtml(
                        item.warehouse_name
                    )}
                </td>


                <td>
                    ${escapeHtml(
                        item.location_name
                    )}
                </td>


                <td>
                    ${escapeHtml(
                        item.box_no
                    )}
                </td>


                <td>
                    <span
                        class="badge bg-primary">

                        ${item.quantity}

                    </span>
                </td>


                <td>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger btnRemoveReadyStock"
                        data-index="${index}"
                        title="Remove">

                        <i class="bi bi-trash"></i>

                    </button>

                </td>

            `;


            tbody.appendChild(
                row
            );

        }
    );

}


/*
|--------------------------------------------------------------------------
| INITIAL STOCK LIST STATE
|--------------------------------------------------------------------------
*/

window.readyStockList = [];

renderReadyStockList();

clearReadyStockProduct();


        /*
        |--------------------------------------------------------------------------
        | LOAD WAREHOUSES
        |--------------------------------------------------------------------------
        */

        loadWarehouses();


        /*
        |--------------------------------------------------------------------------
        | FOCUS BARCODE
        |--------------------------------------------------------------------------
        */

        if (rtsBarcodeInput) {

            rtsBarcodeInput.focus();

        }

    function loadWarehouseStates() {

    const stateSelect =
        document.getElementById(
            'rtsMasterState'
        );


    if (!stateSelect) {
        return;
    }


    stateSelect.innerHTML =
        '<option value="">Loading states...</option>';


    stateSelect.disabled = true;


    fetch(
        "{{ route('inventory.ready-to-sell-stock.states') }}",
        {
            method: 'GET',

            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    )

    .then(function (response) {

        if (!response.ok) {
            throw new Error(
                'Unable to load states.'
            );
        }

        return response.json();

    })

    .then(function (result) {

        stateSelect.innerHTML =
            '<option value="">Select State</option>';


        if (
            !result.success ||
            !Array.isArray(result.data)
        ) {

            throw new Error(
                'State list could not be loaded.'
            );

        }


        result.data.forEach(
            function (state) {

                const option =
                    document.createElement(
                        'option'
                    );


                /*
                |--------------------------------------------------------------
                | IMPORTANT
                |--------------------------------------------------------------
                */

                option.value =
                    state.state_id;


                option.textContent =
                    state.state;


                option.dataset.state =
                    state.state || '';


                option.dataset.stateCode =
                    state.state_code || '';


                stateSelect.appendChild(
                    option
                );

            }
        );


        stateSelect.disabled = false;

    })

    .catch(function (error) {

        console.error(
            'State Loading Error:',
            error
        );


        stateSelect.innerHTML =
            '<option value="">Unable to load states</option>';


        stateSelect.disabled = true;


        Swal.fire({
            icon: 'error',
            title: 'State Error',
            text:
                error.message ||
                'Unable to load state list.'
        });

    });

}

    }


 



);
</script>

@endsection