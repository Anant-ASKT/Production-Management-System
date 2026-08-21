@extends('layouts.app')

@section('content')

<style>

    /* =========================================================
       PAGE
    ========================================================= */

    .fy-page {
        background: #f4f6f9;
        min-height: 100vh;
        padding: 20px 0 50px;
    }

    .fy-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .fy-main-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0,0,0,.08);
        overflow: hidden;
    }

    /* =========================================================
       HEADER
    ========================================================= */

    .fy-page-header {
        background: linear-gradient(
            135deg,
            #0f172a,
            #2563eb
        );
        color: #fff;
        padding: 22px 24px;
    }

    .fy-page-header h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
    }

    .fy-page-header p {
        margin: 6px 0 0;
        opacity: .85;
        font-size: 14px;
    }

    .fy-body {
        padding: 24px;
    }

    /* =========================================================
       SECTION
    ========================================================= */

    .fy-section {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        margin-bottom: 22px;
        overflow: hidden;
    }

    .fy-section-title {
        background: #0f172a;
        color: #fff;
        padding: 13px 18px;
        font-size: 16px;
        font-weight: 600;
    }

    .fy-section-body {
        padding: 20px;
    }

    /* =========================================================
       FORM
    ========================================================= */

    .fy-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 7px;
    }

    .fy-required {
        color: #dc2626;
    }

    .fy-input,
    .fy-select,
    .fy-textarea {
        width: 100%;
        border: 1px solid #d7dee8;
        border-radius: 9px;
        padding: 11px 13px;
        font-size: 14px;
        color: #1e293b;
        background: #fff;
        outline: none;
        transition: .2s;
    }

    .fy-input:focus,
    .fy-select:focus,
    .fy-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.10);
    }

    .fy-textarea {
        min-height: 95px;
        resize: vertical;
    }

    .fy-row {
        margin-bottom: 16px;
    }

    /* =========================================================
       PURCHASE TYPE
    ========================================================= */

    .fy-type-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
    }

    .fy-type-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .fy-type-options {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .fy-type-option {
        position: relative;
        flex: 1;
        min-width: 150px;
    }

    .fy-type-option input {
        display: none;
    }

    .fy-type-option label {
        display: block;
        text-align: center;
        padding: 13px 15px;
        border: 2px solid #dbe3ec;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        font-weight: 600;
        color: #334155;
        transition: .2s;
    }

    .fy-type-option input:checked + label {
        border-color: #2563eb;
        background: #eff6ff;
        color: #2563eb;
    }

    /* =========================================================
       IMAGE SECTION
    ========================================================= */

    .fy-image-section {
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 18px;
        background: #fafcff;
        margin-bottom: 22px;
    }

    .fy-image-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 14px;
    }

    .fy-image-part {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        background: #fff;
        height: 100%;
    }

    .fy-upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        min-height: 145px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: .2s;
    }

    .fy-upload-box:hover {
        border-color: #2563eb;
        background: #f8fbff;
    }

    .fy-upload-icon {
        font-size: 35px;
        color: #2563eb;
        margin-bottom: 8px;
    }

    .fy-upload-text {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
    }

    .fy-upload-sub {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    .fy-file-input {
        display: none;
    }

    .fy-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .fy-preview-item {
        width: 78px;
        height: 78px;
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dbe3ec;
        background: #fff;
    }

    .fy-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* =========================================================
       SELECT2
    ========================================================= */

    .market-shop-select2 .select2-container {
        width: 100% !important;
    }

    .market-shop-select2
    .select2-selection--single {
        height: 46px !important;
        min-height: 46px !important;
        border: 1px solid #d5dce5 !important;
        border-radius: 9px !important;
        display: flex !important;
        align-items: center !important;
        background: #fff !important;
    }

    .market-shop-select2
    .select2-selection--single
    .select2-selection__rendered {
        line-height: 44px !important;
        height: 44px !important;
        padding-left: 13px !important;
        padding-right: 40px !important;
        font-size: 14px !important;
        color: #334155 !important;
    }

    .market-shop-select2
    .select2-selection--single
    .select2-selection__placeholder {
        color: #94a3b8 !important;
    }

    .market-shop-select2
    .select2-selection--single
    .select2-selection__arrow {
        height: 44px !important;
        top: 1px !important;
        right: 8px !important;
    }

    .select2-dropdown {
        z-index: 99999 !important;
    }

    /* =========================================================
       MATERIAL CARD
    ========================================================= */

    .fabric-card {
        border: 1px solid #dce4ed;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 20px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(15,23,42,.05);
    }

    .fabric-card-header {
        background: #198754;
        color: #fff;
        padding: 13px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .fabric-card-title {
        font-size: 16px;
        font-weight: 700;
    }

    .material-number {
        margin-left: 5px;
    }

    .delete-fabric-btn {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 7px;
        background: #dc3545;
        color: #fff;
        cursor: pointer;
    }

    .delete-fabric-btn:hover {
        background: #bb2d3b;
    }

    .fabric-card-body {
        padding: 20px;
    }

    /* =========================================================
       PKU
    ========================================================= */

    .pku-box {
        background: #f8fafc;
        border: 1px solid #dbe3ec;
        border-radius: 10px;
        padding: 13px;
        margin-bottom: 18px;
    }

    .pku-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
    }

    .pku-input {
        background: #f1f5f9 !important;
        font-weight: 700;
        color: #2563eb !important;
    }

    /* =========================================================
       ADD BUTTON
    ========================================================= */

    .add-fabric-wrapper {
        text-align: center;
        margin: 15px 0 25px;
    }

    .add-fabric-btn {
        border: none;
        background: #fbbf24;
        color: #111827;
        padding: 12px 22px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .add-fabric-btn:hover {
        background: #f59e0b;
    }

    /* =========================================================
       SAVE
    ========================================================= */

    .fy-save-area {
        background: #fff;
        border-top: 1px solid #e2e8f0;
        padding: 15px;
        position: sticky;
        bottom: 0;
        z-index: 50;
    }

    .fy-save-btn {
        width: 100%;
        border: none;
        background: #2563eb;
        color: #fff;
        padding: 14px 20px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
    }

    .fy-save-btn:hover {
        background: #1d4ed8;
    }

    .fy-save-btn:disabled {
        opacity: .7;
        cursor: not-allowed;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 767px) {

        .fy-page {
            padding: 10px 0 30px;
        }

        .fy-container {
            padding: 0 8px;
        }

        .fy-page-header {
            padding: 16px;
        }

        .fy-page-header h3 {
            font-size: 19px;
        }

        .fy-body {
            padding: 10px;
        }

        .fy-section-body {
            padding: 14px;
        }

        .fabric-card-body {
            padding: 14px;
        }

        .fy-type-option {
            min-width: 100%;
        }

        .fy-image-part {
            margin-bottom: 12px;
        }

        .fy-save-area {
            padding: 10px;
        }
    }

</style>


<div class="fy-page">

    <div class="fy-container">

        <div class="fy-main-card">

            {{-- =====================================================
                 HEADER
            ====================================================== --}}

            <div class="fy-page-header">

                <h3>
                    <i class="bi bi-cart-check me-2"></i>
                    Fabric-Yarn Buying Application
                </h3>

                <p>
                    Enter shop details and fabric/yarn purchase information.
                </p>

            </div>


            <div class="fy-body">

                {{-- =====================================================
                     PURCHASE INFORMATION
                ====================================================== --}}

                <div class="fy-section">

                    <div class="fy-section-title">
                        <i class="bi bi-calendar3 me-2"></i>
                        Purchase Information
                    </div>

                    <div class="fy-section-body">

                        <div class="row">

                            {{-- PURCHASE DATE --}}

                            <div class="col-md-6">

                                <div class="fy-row">

                                    <label class="fy-label">
                                        Purchase Date
                                        <span class="fy-required">*</span>
                                    </label>

                                    <input
                                        type="date"
                                        id="purchase_date"
                                        name="purchase_date"
                                        class="fy-input"
                                        value="{{ date('Y-m-d') }}"
                                    >

                                </div>

                            </div>


                            {{-- PURCHASE TYPE --}}

                            <div class="col-md-6">

                                <div class="fy-type-box">

                                    <div class="fy-type-title">

                                        Select Purchase Type
                                        <span class="fy-required">*</span>

                                    </div>

                                    <div class="fy-type-options">

                                        <div class="fy-type-option">

                                            <input
                                                type="radio"
                                                id="type_fabric"
                                                name="purchase_type"
                                                value="Fabric"
                                                checked
                                            >

                                            <label for="type_fabric">

                                                <i class="bi bi-layers me-1"></i>
                                                Fabric

                                            </label>

                                        </div>


                                        <div class="fy-type-option">

                                            <input
                                                type="radio"
                                                id="type_yarn"
                                                name="purchase_type"
                                                value="Yarn"
                                            >

                                            <label for="type_yarn">

                                                <i class="bi bi-bezier2 me-1"></i>
                                                Yarn

                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                     SHOP DETAILS
                ====================================================== --}}

                <div class="fy-section">

                    <div class="fy-section-title">

                        <i class="bi bi-shop me-2"></i>
                        Shop Details

                    </div>


                    <div class="fy-section-body">


                        {{-- =================================================
                             SHOP IMAGES
                        ================================================== --}}

                        <div class="fy-image-section">

                            <div class="fy-image-title">

                                <i class="bi bi-images me-2"></i>
                                Shop Images

                                <span class="fy-required">*</span>

                            </div>


                            <div class="row g-3">


                                {{-- VISITING CARD --}}

                                <div class="col-md-6">

                                    <div class="fy-image-part">

                                        <div class="fy-label">
                                            Visiting Card
                                        </div>


                                        <label
                                            class="fy-upload-box"
                                            for="visiting_card"
                                        >

                                            <div class="fy-upload-icon">

                                                <i class="bi bi-person-vcard"></i>

                                            </div>

                                            <div class="fy-upload-text">
                                                Upload Visiting Card
                                            </div>

                                            <div class="fy-upload-sub">
                                                Camera • Gallery • Desktop
                                            </div>

                                        </label>


                                        {{-- IMPORTANT:
                                             Controller expects shop_card_photos[] --}}

                                        <input
                                            type="file"
                                            id="visiting_card"
                                            name="shop_card_photos[]"
                                            class="fy-file-input"
                                            accept="image/*"
                                            multiple
                                        >


                                        <div
                                            id="visitingCardPreview"
                                            class="fy-preview"
                                        ></div>

                                    </div>

                                </div>


                                {{-- SHOP PHOTOS --}}

                                <div class="col-md-6">

                                    <div class="fy-image-part">

                                        <div class="fy-label">
                                            Shop Photos
                                        </div>


                                        <label
                                            class="fy-upload-box"
                                            for="shop_photos"
                                        >

                                            <div class="fy-upload-icon">

                                                <i class="bi bi-shop-window"></i>

                                            </div>

                                            <div class="fy-upload-text">
                                                Take Shop Photos
                                            </div>

                                            <div class="fy-upload-sub">
                                                Camera • Gallery • Desktop
                                            </div>

                                        </label>


                                        <input
                                            type="file"
                                            id="shop_photos"
                                            name="shop_photos[]"
                                            class="fy-file-input"
                                            accept="image/*"
                                            multiple
                                        >


                                        <div
                                            id="shopPhotosPreview"
                                            class="fy-preview"
                                        ></div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                             MARKET / SHOP
                        ================================================== --}}

                        <div class="row">


                            {{-- MARKET --}}

                            <div class="col-md-6 mb-3">

                                <label class="fy-label">

                                    Market Name
                                    <span class="fy-required">*</span>

                                </label>


                                <div class="market-shop-select2">

                                    <select
                                        id="market_name"
                                        class="form-select"
                                        style="width:100%;"
                                    >

                                        <option value="">
                                            Select or type Market
                                        </option>

                                    </select>

                                </div>


                                <input
                                    type="hidden"
                                    id="market_id"
                                    name="market_id"
                                >

                                <input
                                    type="hidden"
                                    id="market_text"
                                    name="market_text"
                                >

                            </div>


                            {{-- SHOP --}}

                            <div class="col-md-6 mb-3">

                                <label class="fy-label">

                                    Shop Name
                                    <span class="fy-required">*</span>

                                </label>


                                <div class="market-shop-select2">

                                    <select
                                        id="shop_name"
                                        class="form-select"
                                        style="width:100%;"
                                    >

                                        <option value="">
                                            Select or type Shop
                                        </option>

                                    </select>

                                </div>


                                <input
                                    type="hidden"
                                    id="shop_id"
                                    name="shop_id"
                                >

                                <input
                                    type="hidden"
                                    id="shop_text"
                                    name="shop_text"
                                >

                            </div>


                            {{-- CONTACT NUMBER --}}

                            <div class="col-md-6">

                                <div class="fy-row">

                                    <label class="fy-label">

                                        Contact Number
                                        <span class="fy-required">*</span>

                                    </label>

                                    <input
                                        type="text"
                                        id="mobileno"
                                        name="contact_no"
                                        class="fy-input"
                                        placeholder="Enter contact number"
                                    >

                                </div>

                            </div>


                            {{-- CONTACT PERSON --}}

                            <div class="col-md-6">

                                <div class="fy-row">

                                    <label class="fy-label">

                                        Contact Person
                                        <span class="fy-required">*</span>

                                    </label>

                                    <input
                                        type="text"
                                        id="contact_name"
                                        name="contact_person"
                                        class="fy-input"
                                        placeholder="Enter contact person"
                                    >

                                </div>

                            </div>


                            {{-- PLACE --}}

                            <div class="col-md-6">

                                <div class="fy-row">

                                    <label class="fy-label">

                                        Place
                                        <span class="fy-required">*</span>

                                    </label>

                                    <input
                                        type="text"
                                        id="place"
                                        name="place_name"
                                        class="fy-input"
                                        placeholder="Enter place"
                                    >

                                </div>

                            </div>


                            {{-- EMAIL --}}

                            <div class="col-md-6">

                                <div class="fy-row">

                                    <label class="fy-label">
                                        Email
                                    </label>

                                    <input
                                        type="email"
                                        id="emailid"
                                        name="email"
                                        class="fy-input"
                                        placeholder="Enter email address"
                                    >

                                </div>

                            </div>


                            {{-- ADDRESS --}}

                            <div class="col-12">

                                <div class="fy-row">

                                    <label class="fy-label">

                                        Address
                                        <span class="fy-required">*</span>

                                    </label>

                                    <textarea
                                        id="address"
                                        name="address"
                                        class="fy-textarea"
                                        placeholder="Enter complete shop address"
                                    ></textarea>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                     FABRIC / YARN
                ====================================================== --}}

                <div class="fy-section">

                    <div class="fy-section-title">

                        <i class="bi bi-box-seam me-2"></i>

                        <span id="materialSectionTitle">
                            Fabric Details
                        </span>

                    </div>


                    <div class="fy-section-body">

                        {{-- IMPORTANT:
                             JavaScript inserts Fabric-1 here --}}

                        <div id="fabricContainer"></div>


                        <div class="add-fabric-wrapper">

                            <button
                                type="button"
                                id="addFabricBtn"
                                class="add-fabric-btn"
                            >

                                <i class="bi bi-plus-circle me-1"></i>

                                Add More

                                <span id="addMaterialText">
                                    Fabric
                                </span>

                            </button>

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                     SAVE
                ====================================================== --}}

                <div class="fy-save-area">

                    <button
                        type="button"
                        id="saveFabricYarnBtn"
                        class="fy-save-btn"
                    >

                        <i class="bi bi-save me-2"></i>

                        Save Complete Purchase

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


@push('scripts')

<script>

$(document).ready(function () {

    console.log('Fabric-Yarn Buying JS started');


    /* =========================================================
       ELEMENTS
    ========================================================= */

    const marketSelect = $('#market_name');
    const shopSelect   = $('#shop_name');


    /* =========================================================
       SELECT2
    ========================================================= */

    marketSelect.select2({

        width: '100%',

        placeholder: 'Select or type Market',

        allowClear: true,

        tags: true,

        createTag: function (params) {

            const term =
                $.trim(params.term);

            if (term === '') {
                return null;
            }

            return {
                id: 'new:' + term,
                text: term,
                newTag: true
            };

        }

    });


    shopSelect.select2({

        width: '100%',

        placeholder: 'Select or type Shop',

        allowClear: true,

        tags: true,

        createTag: function (params) {

            const term =
                $.trim(params.term);

            if (term === '') {
                return null;
            }

            return {
                id: 'new:' + term,
                text: term,
                newTag: true
            };

        }

    });


    /* =========================================================
       LOAD MARKETS
    ========================================================= */

    loadMarkets();


    function loadMarkets() {

        $.ajax({

            url:
                "{{ route('inventory.fabric-yarn-buying.markets') }}",

            type: 'GET',

            dataType: 'json',

            success: function (response) {

                console.log(
                    'MARKETS:',
                    response
                );

                marketSelect.empty();

                marketSelect.append(
                    new Option(
                        'Select or type Market',
                        '',
                        false,
                        false
                    )
                );


                if (
                    response.success &&
                    Array.isArray(response.data)
                ) {

                    response.data.forEach(
                        function (market) {

                            marketSelect.append(
                                new Option(
                                    market.market_name,
                                    market.id,
                                    false,
                                    false
                                )
                            );

                        }
                    );

                }


                marketSelect
                    .val('')
                    .trigger('change.select2');

            },

            error: function (xhr) {

                console.error(
                    'MARKET LOAD ERROR:',
                    xhr.responseText
                );

                Swal.fire(
                    'Error',
                    'Unable to load markets.',
                    'error'
                );

            }

        });

    }


    /* =========================================================
       MARKET CHANGE
    ========================================================= */

    marketSelect.on(
        'change',
        function () {

            const selected =
                marketSelect.select2('data')[0];


            $('#market_id').val('');
            $('#market_text').val('');


            clearShop();


            if (
                !selected ||
                !selected.id
            ) {
                return;
            }


            const selectedId =
                String(selected.id);


            /* NEW MARKET */

            if (
                selectedId.startsWith('new:')
            ) {

                const marketName =
                    selectedId
                        .substring(4)
                        .trim();

                $('#market_text')
                    .val(marketName);

                return;

            }


            /* EXISTING MARKET */

            $('#market_id')
                .val(selected.id);

            $('#market_text')
                .val(selected.text);


            loadShops(
                selected.id
            );

        }
    );


    /* =========================================================
       LOAD SHOPS
    ========================================================= */

    function loadShops(marketId) {

        if (!marketId) {
            return;
        }


        shopSelect.empty();

        shopSelect.append(
            new Option(
                'Loading shops...',
                '',
                false,
                false
            )
        );

        shopSelect
            .val('')
            .trigger('change.select2');


        $.ajax({

            url:
                "{{ route('inventory.fabric-yarn-buying.shops') }}",

            type: 'GET',

            data: {
                market_id: marketId
            },

            dataType: 'json',

            success: function (response) {

                console.log(
                    'SHOPS:',
                    response
                );


                shopSelect.empty();

                shopSelect.append(
                    new Option(
                        'Select or type Shop',
                        '',
                        false,
                        false
                    )
                );


                if (
                    response.success &&
                    Array.isArray(response.data)
                ) {

                    response.data.forEach(
                        function (shop) {

                            shopSelect.append(
                                new Option(
                                    shop.shop_name,
                                    shop.id,
                                    false,
                                    false
                                )
                            );

                        }
                    );

                }


                shopSelect
                    .val('')
                    .trigger('change.select2');

            },

            error: function (xhr) {

                console.error(
                    'SHOP LOAD ERROR:',
                    xhr.responseText
                );


                shopSelect.empty();

                shopSelect.append(
                    new Option(
                        'Unable to load shops',
                        '',
                        false,
                        false
                    )
                );

                shopSelect
                    .val('')
                    .trigger('change.select2');

            }

        });

    }


    /* =========================================================
       CLEAR SHOP
    ========================================================= */

    function clearShop() {

        shopSelect.empty();

        shopSelect.append(
            new Option(
                'Select or type Shop',
                '',
                false,
                false
            )
        );

        shopSelect
            .val('')
            .trigger('change.select2');


        $('#shop_id').val('');
        $('#shop_text').val('');


        /*
         * Clear shop fields when market changes.
         */

        $('#mobileno').val('');
        $('#contact_name').val('');
        $('#emailid').val('');
        $('#place').val('');
        $('#address').val('');

    }


    /* =========================================================
       SHOP CHANGE
    ========================================================= */

    shopSelect.on(
        'change',
        function () {

            const selected =
                shopSelect.select2('data')[0];


            $('#shop_id').val('');
            $('#shop_text').val('');


            if (
                !selected ||
                !selected.id
            ) {
                return;
            }


            const selectedId =
                String(selected.id);


            /* NEW SHOP */

            if (
                selectedId.startsWith('new:')
            ) {

                const shopName =
                    selectedId
                        .substring(4)
                        .trim();

                $('#shop_text')
                    .val(shopName);

                return;

            }


            /* EXISTING SHOP */

            $('#shop_id')
                .val(selected.id);

            $('#shop_text')
                .val(selected.text);


            loadShopDetails(
                selected.id
            );

        }
    );


    /* =========================================================
       SHOP DETAILS
    ========================================================= */

    function loadShopDetails(shopId) {

        if (!shopId) {
            return;
        }


        $.ajax({

            url:
                "{{ route('inventory.fabric-yarn-buying.shop-details') }}",

            type: 'GET',

            data: {
                shop_id: shopId
            },

            dataType: 'json',

            success: function (response) {

                console.log(
                    'SHOP DETAILS:',
                    response
                );


                if (
                    !response.success ||
                    !response.data
                ) {
                    return;
                }


                const shop =
                    response.data;


                $('#mobileno')
                    .val(shop.mobileno || '');

                $('#contact_name')
                    .val(shop.contact_name || '');

                $('#emailid')
                    .val(shop.emailid || '');

                $('#place')
                    .val(shop.place || '');

                $('#address')
                    .val(shop.address || '');

            },

            error: function (xhr) {

                console.error(
                    'SHOP DETAILS ERROR:',
                    xhr.responseText
                );

            }

        });

    }


    /* =========================================================
       PURCHASE TYPE
    ========================================================= */

    function getPurchaseType() {

        const selected =
            $('input[name="purchase_type"]:checked');

        return selected.length
            ? selected.val()
            : 'Fabric';

    }


    function getMaterialName() {

        return getPurchaseType() === 'Yarn'
            ? 'Yarn'
            : 'Fabric';

    }


    /* =========================================================
       GENERATE RANDOM PKU
    ========================================================= */

    function generatePKU(type) {

        const number =
            Math.floor(
                1000 +
                Math.random() * 9000
            );


        if (type === 'Yarn') {

            return 'YRN' + number;

        }


        return 'FAB' + number;

    }


    /* =========================================================
       CREATE MATERIAL SECTION
    ========================================================= */

    let materialCounter = 0;


    function createMaterialSection() {

        materialCounter++;


        const index =
            materialCounter - 1;


        const materialName =
            getMaterialName();


        const pku =
            generatePKU(
                getPurchaseType()
            );


        const card =
            document.createElement('div');


        card.className =
            'fabric-card';


        card.dataset.index =
            index;


        card.innerHTML = `

            <div class="fabric-card-header">

                <div class="fabric-card-title">

                    ${materialName}

                    <span class="material-number">
                        ${materialCounter}
                    </span>

                </div>


                <button
                    type="button"
                    class="delete-fabric-btn"
                    title="Delete ${materialName}"
                >

                    <i class="bi bi-trash"></i>

                </button>

            </div>


            <div class="fabric-card-body">


                <!-- =========================================
                     MATERIAL IMAGE
                ========================================== -->

                <div class="fy-image-section">

                    <div class="fy-image-title">

                        <i class="bi bi-images me-2"></i>

                        ${materialName} Photos

                        <span class="fy-required">*</span>

                    </div>


                    <label
                        class="fy-upload-box"
                        for="material_images_${index}"
                    >

                        <div class="fy-upload-icon">

                            <i class="bi bi-image"></i>

                        </div>


                        <div class="fy-upload-text">

                            Take ${materialName} Photos

                        </div>


                        <div class="fy-upload-sub">

                            Camera • Gallery • Desktop

                        </div>

                    </label>


                    <!-- IMPORTANT:
                         Controller expects:
                         fabric_photos_0[]
                         fabric_photos_1[]
                         fabric_photos_2[]
                    -->

                    <input
                        type="file"
                        id="material_images_${index}"
                        name="fabric_photos_${index}[]"
                        class="fy-file-input material-image-input"
                        accept="image/*"
                        multiple
                    >


                    <div
                        class="fy-preview material-preview"
                    ></div>

                </div>


                <!-- =========================================
                     PKU
                ========================================== -->

                <div class="pku-box">

                    <div class="pku-label">

                        PKU Number
                        <span class="fy-required">*</span>

                    </div>


                    <input
                        type="text"
                        name="pku_number[${index}]"
                        class="fy-input pku-input"
                        value="${pku}"
                        readonly
                    >

                </div>


                <!-- =========================================
                     BASIC DETAILS
                ========================================== -->

                <div class="row">


                    {{-- MATERIAL NAME --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">

                                ${materialName} Name

                                <span class="fy-required">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                name="fabric_name[${index}]"
                                class="fy-input"
                                placeholder="Enter ${materialName.toLowerCase()} name"
                            >

                        </div>

                    </div>


                    {{-- COMPOSITION --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">

                                Composition

                                <span class="fy-required">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                name="composition[${index}]"
                                class="fy-input"
                                placeholder="Enter composition"
                            >

                        </div>

                    </div>


                    {{-- WIDTH --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">
                                Width
                            </label>


                            <input
                                type="text"
                                name="fabric_width[${index}]"
                                class="fy-input"
                                placeholder="Enter width"
                            >

                        </div>

                    </div>


                    {{-- MOQ --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">
                                MOQ
                            </label>


                            <input
                                type="text"
                                name="minimum_order_qty[${index}]"
                                class="fy-input"
                                placeholder="Enter MOQ"
                            >

                        </div>

                    </div>


                    {{-- PRICE PER METER --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">
                                Price Per Meter
                            </label>


                            <input
                                type="number"
                                step="0.01"
                                name="price_per_meter[${index}]"
                                class="fy-input"
                                placeholder="Enter price per meter"
                            >

                        </div>

                    </div>


                    {{-- PRICE PER ROLL --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">
                                Price Per Roll
                            </label>


                            <input
                                type="number"
                                step="0.01"
                                name="price_per_roll[${index}]"
                                class="fy-input"
                                placeholder="Enter price per roll"
                            >

                        </div>

                    </div>


                    {{-- SKU --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">
                                SKU Number
                            </label>


                            <input
                                type="text"
                                name="sku_number[${index}]"
                                class="fy-input"
                                placeholder="Enter SKU number"
                            >

                        </div>

                    </div>


                    {{-- SAMPLE PURCHASE --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">
                                Sample Purchase
                            </label>


                            <select
                                name="sample_purchase[${index}]"
                                class="fy-select"
                            >

                                <option value="Yes">
                                    Yes
                                </option>

                                <option value="No">
                                    No
                                </option>

                            </select>

                        </div>

                    </div>


                    {{-- PHYSICAL NUMBER --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">
                                Physical Number
                            </label>


                            <input
                                type="text"
                                name="physical_number[${index}]"
                                class="fy-input"
                                placeholder="Enter physical number"
                            >

                        </div>

                    </div>


                    {{-- PHYSICAL BOX NUMBER --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">
                                Physical Box Number
                            </label>


                            <input
                                type="text"
                                name="physical_boxno[${index}]"
                                class="fy-input"
                                placeholder="Enter physical box number"
                            >

                        </div>

                    </div>


                    {{-- PHYSICAL LOCATION --}}

                    <div class="col-md-6">

                        <div class="fy-row">

                            <label class="fy-label">
                                Physical Location
                            </label>


                            <input
                                type="text"
                                name="physical_location[${index}]"
                                class="fy-input"
                                placeholder="Enter physical location"
                            >

                        </div>

                    </div>


                    {{-- COMMENTS --}}

                    <div class="col-12">

                        <div class="fy-row mb-0">

                            <label class="fy-label">
                                Comments
                            </label>


                            <textarea
                                name="comments[${index}]"
                                class="fy-textarea"
                                placeholder="Write any remarks..."
                            ></textarea>

                        </div>

                    </div>

                </div>

            </div>

        `;


        $('#fabricContainer')
            .append(card);


        /* =====================================================
           IMAGE PREVIEW
        ===================================================== */

        const fileInput =
            card.querySelector(
                '.material-image-input'
            );


        const preview =
            card.querySelector(
                '.material-preview'
            );


        fileInput.addEventListener(
            'change',
            function () {

                preview.innerHTML = '';


                Array
                    .from(this.files)
                    .forEach(
                        function (file) {

                            if (
                                !file.type.startsWith(
                                    'image/'
                                )
                            ) {
                                return;
                            }


                            const reader =
                                new FileReader();


                            reader.onload =
                                function (e) {

                                    const item =
                                        document.createElement(
                                            'div'
                                        );


                                    item.className =
                                        'fy-preview-item';


                                    item.innerHTML = `

                                        <img
                                            src="${e.target.result}"
                                            alt="Preview"
                                        >

                                    `;


                                    preview.appendChild(
                                        item
                                    );

                                };


                            reader.readAsDataURL(
                                file
                            );

                        }
                    );

            }
        );


        /* =====================================================
           DELETE MATERIAL
        ===================================================== */

        card
            .querySelector(
                '.delete-fabric-btn'
            )
            .addEventListener(
                'click',
                function () {

                    const cards =
                        document.querySelectorAll(
                            '#fabricContainer .fabric-card'
                        );


                    if (
                        cards.length <= 1
                    ) {

                        Swal.fire(
                            'Required',
                            'At least one Fabric/Yarn section is required.',
                            'warning'
                        );

                        return;

                    }


                    card.remove();


                    resequenceMaterials();

                }
            );

    }


    /* =========================================================
       RESEQUENCE MATERIALS
    ========================================================= */

    function resequenceMaterials() {

        const cards =
            document.querySelectorAll(
                '#fabricContainer .fabric-card'
            );


        cards.forEach(
            function (card, index) {

                const number =
                    index + 1;


                card.dataset.index =
                    index;


                const numberElement =
                    card.querySelector(
                        '.material-number'
                    );


                if (numberElement) {

                    numberElement.textContent =
                        number;

                }


                /*
                 * IMPORTANT:
                 *
                 * We must rename all fields
                 * because controller reads arrays
                 * using continuous indexes.
                 */

                const fields =
                    card.querySelectorAll(
                        'input, select, textarea'
                    );


                fields.forEach(
                    function (field) {

                        if (!field.name) {
                            return;
                        }


                        field.name =
                            field.name.replace(
                                /\[(\d+)\]/,
                                '[' + index + ']'
                            );


                        /*
                         * Fabric image input has
                         * a different naming pattern.
                         */

                        if (
                            field.classList.contains(
                                'material-image-input'
                            )
                        ) {

                            field.name =
                                'fabric_photos_' +
                                index +
                                '[]';

                        }

                    }
                );


                const imageInput =
                    card.querySelector(
                        '.material-image-input'
                    );


                if (imageInput) {

                    imageInput.id =
                        'material_images_' +
                        index;


                    const label =
                        card.querySelector(
                            'label[for]'
                        );


                    if (label) {

                        label.setAttribute(
                            'for',
                            'material_images_' +
                            index
                        );

                    }

                }

            }
        );


        materialCounter =
            cards.length;

    }


    /* =========================================================
       PURCHASE TYPE CHANGE
    ========================================================= */

    $('input[name="purchase_type"]')
        .on(
            'change',
            function () {

                const type =
                    getPurchaseType();


                const materialName =
                    getMaterialName();


                $('#materialSectionTitle')
                    .text(
                        materialName +
                        ' Details'
                    );


                $('#addMaterialText')
                    .text(
                        materialName
                    );


                /*
                 * Existing sections should also
                 * change their heading.
                 */

                $('#fabricContainer .fabric-card')
                    .each(
                        function (index) {

                            const title =
                                $(this)
                                    .find(
                                        '.fabric-card-title'
                                    );


                            title
                                .contents()
                                .first()[0]
                                .textContent =
                                materialName + ' ';


                            /*
                             * Generate a new PKU
                             * for every existing section
                             */

                            $(this)
                                .find('.pku-input')
                                .val(
                                    generatePKU(type)
                                );


                            /*
                             * Update image text.
                             */

                            $(this)
                                .find('.fy-image-title')
                                .html(
                                    '<i class="bi bi-images me-2"></i>' +
                                    materialName +
                                    ' Photos ' +
                                    '<span class="fy-required">*</span>'
                                );


                            $(this)
                                .find('.fy-upload-text')
                                .text(
                                    'Take ' +
                                    materialName +
                                    ' Photos'
                                );

                        }
                    );

            }
        );


    /* =========================================================
       ADD MORE
    ========================================================= */

    $('#addFabricBtn')
        .on(
            'click',
            function () {

                createMaterialSection();

            }
        );


    /* =========================================================
       VISITING CARD PREVIEW
    ========================================================= */

    $('#visiting_card')
        .on(
            'change',
            function () {

                const preview =
                    $('#visitingCardPreview');


                preview.empty();


                Array
                    .from(this.files)
                    .forEach(
                        function (file) {

                            if (
                                !file.type.startsWith(
                                    'image/'
                                )
                            ) {
                                return;
                            }


                            const reader =
                                new FileReader();


                            reader.onload =
                                function (e) {

                                    preview.append(`

                                        <div class="fy-preview-item">

                                            <img
                                                src="${e.target.result}"
                                                alt="Visiting Card"
                                            >

                                        </div>

                                    `);

                                };


                            reader.readAsDataURL(
                                file
                            );

                        }
                    );

            }
        );


    /* =========================================================
       SHOP PHOTOS PREVIEW
    ========================================================= */

    $('#shop_photos')
        .on(
            'change',
            function () {

                const preview =
                    $('#shopPhotosPreview');


                preview.empty();


                Array
                    .from(this.files)
                    .forEach(
                        function (file) {

                            if (
                                !file.type.startsWith(
                                    'image/'
                                )
                            ) {
                                return;
                            }


                            const reader =
                                new FileReader();


                            reader.onload =
                                function (e) {

                                    preview.append(`

                                        <div class="fy-preview-item">

                                            <img
                                                src="${e.target.result}"
                                                alt="Shop Photo"
                                            >

                                        </div>

                                    `);

                                };


                            reader.readAsDataURL(
                                file
                            );

                        }
                    );

            }
        );


    /* =========================================================
       CREATE FIRST MATERIAL
    ========================================================= */

    createMaterialSection();


    /* =========================================================
       SAVE
    ========================================================= */

    $('#saveFabricYarnBtn')
        .on(
            'click',
            function () {

                saveFabricYarn();

            }
        );


    /* =========================================================
       SAVE FUNCTION
    ========================================================= */

    function saveFabricYarn() {


        /* =====================================================
           BASIC DATA
        ===================================================== */

        const purchaseDate =
            $('#purchase_date')
                .val()
                .trim();


        const purchaseType =
            getPurchaseType();


        const marketText =
            $('#market_text')
                .val()
                .trim();


        const shopText =
            $('#shop_text')
                .val()
                .trim();


        const contactNo =
            $('#mobileno')
                .val()
                .trim();


        const contactPerson =
            $('#contact_name')
                .val()
                .trim();


        const place =
            $('#place')
                .val()
                .trim();


        const email =
            $('#emailid')
                .val()
                .trim();


        const address =
            $('#address')
                .val()
                .trim();


        /* =====================================================
           VALIDATION
        ===================================================== */

        if (!purchaseDate) {

            Swal.fire(
                'Required',
                'Please select Purchase Date.',
                'warning'
            );

            return;

        }


        if (!marketText) {

            Swal.fire(
                'Required',
                'Please select or enter Market Name.',
                'warning'
            );

            return;

        }


        if (!shopText) {

            Swal.fire(
                'Required',
                'Please select or enter Shop Name.',
                'warning'
            );

            return;

        }


        if (!contactNo) {

            Swal.fire(
                'Required',
                'Please enter Contact Number.',
                'warning'
            );

            return;

        }


        if (!contactPerson) {

            Swal.fire(
                'Required',
                'Please enter Contact Person.',
                'warning'
            );

            return;

        }


        if (!place) {

            Swal.fire(
                'Required',
                'Please enter Place.',
                'warning'
            );

            return;

        }


        if (
            email &&
            !/^[^\s@]+@[^\s@]+\.[^\s@]+$/
                .test(email)
        ) {

            Swal.fire(
                'Invalid Email',
                'Please enter a valid email address.',
                'warning'
            );

            return;

        }


        if (!address) {

            Swal.fire(
                'Required',
                'Please enter Address.',
                'warning'
            );

            return;

        }


        /* =====================================================
           SHOP IMAGE VALIDATION
        ===================================================== */

        const visitingCardFiles =
            document.getElementById(
                'visiting_card'
            ).files.length;


        const shopPhotoFiles =
            document.getElementById(
                'shop_photos'
            ).files.length;


        if (
            visitingCardFiles === 0 &&
            shopPhotoFiles === 0
        ) {

            Swal.fire(
                'Shop Image Required',
                'Please upload at least one Visiting Card or Shop Photo.',
                'warning'
            );

            return;

        }


        /* =====================================================
           MATERIAL CARDS
        ===================================================== */

        const cards =
            document.querySelectorAll(
                '#fabricContainer .fabric-card'
            );


        if (cards.length === 0) {

            Swal.fire(
                'Required',
                'At least one Fabric/Yarn section is required.',
                'warning'
            );

            return;

        }


        /* =====================================================
           VALIDATE EACH MATERIAL
        ===================================================== */

        for (
            let i = 0;
            i < cards.length;
            i++
        ) {

            const card =
                cards[i];


            const materialName =
                card.querySelector(
                    'input[name^="fabric_name"]'
                );


            const composition =
                card.querySelector(
                    'input[name^="composition"]'
                );


            const pku =
                card.querySelector(
                    'input[name^="pku_number"]'
                );


            const imageInput =
                card.querySelector(
                    '.material-image-input'
                );


            if (
                !materialName ||
                !materialName.value.trim()
            ) {

                Swal.fire(
                    'Required',
                    `${getMaterialName()} ${i + 1}: Please enter ${getMaterialName()} Name.`,
                    'warning'
                );

                materialName?.focus();

                return;

            }


            if (
                !composition ||
                !composition.value.trim()
            ) {

                Swal.fire(
                    'Required',
                    `${getMaterialName()} ${i + 1}: Please enter Composition.`,
                    'warning'
                );

                composition?.focus();

                return;

            }


            if (
                !pku ||
                !pku.value.trim()
            ) {

                Swal.fire(
                    'Required',
                    `${getMaterialName()} ${i + 1}: PKU Number is required.`,
                    'warning'
                );

                return;

            }


            /*
             * Every Fabric/Yarn section
             * must have at least one image.
             */

            if (
                !imageInput ||
                imageInput.files.length === 0
            ) {

                Swal.fire(
                    'Image Required',
                    `${getMaterialName()} ${i + 1}: Please upload at least one image.`,
                    'warning'
                );

                return;

            }

        }


        /* =====================================================
           FORM DATA
        ===================================================== */

        const formData =
            new FormData();


        formData.append(
            'purchase_type',
            purchaseType
        );


        formData.append(
            'purchase_date',
            purchaseDate
        );


        formData.append(
            'market_id',
            $('#market_id').val() || ''
        );


        formData.append(
            'market_text',
            marketText
        );


        formData.append(
            'shop_id',
            $('#shop_id').val() || ''
        );


        formData.append(
            'shop_text',
            shopText
        );


        formData.append(
            'contact_no',
            contactNo
        );


        formData.append(
            'contact_person',
            contactPerson
        );


        formData.append(
            'place_name',
            place
        );


        formData.append(
            'email',
            email
        );


        formData.append(
            'address',
            address
        );


        /* =====================================================
           SHOP PHOTOS
        ===================================================== */

        Array
            .from(
                document.getElementById(
                    'shop_photos'
                ).files
            )
            .forEach(
                function (file) {

                    formData.append(
                        'shop_photos[]',
                        file
                    );

                }
            );


        /* =====================================================
           VISITING CARD
        ===================================================== */

        Array
            .from(
                document.getElementById(
                    'visiting_card'
                ).files
            )
            .forEach(
                function (file) {

                    formData.append(
                        'shop_card_photos[]',
                        file
                    );

                }
            );


        /* =====================================================
           MATERIAL DATA
        ===================================================== */

        cards.forEach(
            function (card) {

                const fields =
                    card.querySelectorAll(
                        'input:not([type="file"]), select, textarea'
                    );


                fields.forEach(
                    function (field) {

                        if (!field.name) {
                            return;
                        }


                        formData.append(
                            field.name,
                            field.value
                        );

                    }
                );


                /* MATERIAL IMAGES */

                const imageInput =
                    card.querySelector(
                        '.material-image-input'
                    );


                if (imageInput) {

                    Array
                        .from(
                            imageInput.files
                        )
                        .forEach(
                            function (file) {

                                formData.append(
                                    imageInput.name,
                                    file
                                );

                            }
                        );

                }

            }
        );


        /* =====================================================
           CSRF
        ===================================================== */

        formData.append(
            '_token',
            "{{ csrf_token() }}"
        );


        /* =====================================================
           BUTTON
        ===================================================== */

        const saveButton =
            $('#saveFabricYarnBtn');


        saveButton
            .prop(
                'disabled',
                true
            )
            .html(
                '<i class="bi bi-hourglass-split me-2"></i> Saving...'
            );


        /* =====================================================
           AJAX SAVE
        ===================================================== */

        fetch(
            "{{ route('inventory.fabric-yarn-buying.save') }}",
            {
                method: 'POST',

                body: formData,

                headers: {

                    'X-Requested-With':
                        'XMLHttpRequest',

                    'Accept':
                        'application/json'

                }

            }
        )
        .then(
            async function (response) {

                let result;


                const contentType =
                    response.headers
                        .get('content-type') ||
                    '';


                if (
                    contentType.includes(
                        'application/json'
                    )
                ) {

                    result =
                        await response.json();

                } else {

                    const text =
                        await response.text();

                    console.error(
                        'NON JSON RESPONSE:',
                        text
                    );

                    throw new Error(
                        'Server returned HTTP ' +
                        response.status
                    );

                }


                if (
                    !response.ok ||
                    !result.success
                ) {

                    throw new Error(
                        result.message ||
                        'Unable to save purchase.'
                    );

                }


                Swal.fire({

                    icon: 'success',

                    title: 'Saved Successfully',

                    text:
                        result.message ||
                        'Purchase saved successfully.'

                });


                /*
                 * Optional reset after successful save.
                 */

                // location.reload();

            }
        )
        .catch(
            function (error) {

                console.error(
                    'SAVE ERROR:',
                    error
                );


                Swal.fire(
                    'Save Failed',
                    error.message ||
                    'Unable to save purchase.',
                    'error'
                );

            }
        )
        .finally(
            function () {

                saveButton
                    .prop(
                        'disabled',
                        false
                    )
                    .html(
                        '<i class="bi bi-save me-2"></i> Save Complete Purchase'
                    );

            }
        );

    }

});

</script>

@endpush

@endsection