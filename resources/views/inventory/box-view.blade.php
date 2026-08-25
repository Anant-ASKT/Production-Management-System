<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Box Stock -
        {{ $box->boxno }}
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            background:
                #f4f7fb;

            font-family:
                Arial,
                sans-serif;

            color:
                #172033;

        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .page-header {

            background:
                linear-gradient(
                    135deg,
                    #0f172a,
                    #1e3a5f
                );

            color:
                #fff;

            padding:
                20px 16px;

            border-radius:
                0 0 22px 22px;

            box-shadow:
                0 6px 20px
                rgba(
                    0,
                    0,
                    0,
                    .12
                );

        }


        .page-title {

            font-size:
                22px;

            font-weight:
                700;

            margin:
                0;

        }


        .box-number {

            font-size:
                16px;

            margin-top:
                6px;

            opacity:
                .9;

        }


        /*
        |--------------------------------------------------------------------------
        | INFO CARD
        |--------------------------------------------------------------------------
        */

        .info-card {

            background:
                #fff;

            margin:
                15px;

            padding:
                16px;

            border-radius:
                16px;

            box-shadow:
                0 4px 16px
                rgba(
                    0,
                    0,
                    0,
                    .06
                );

        }


        .info-row {

            display:
                flex;

            justify-content:
                space-between;

            gap:
                15px;

            padding:
                8px 0;

            border-bottom:
                1px dashed
                #e2e8f0;

        }


        .info-row:last-child {

            border-bottom:
                none;

        }


        .info-label {

            color:
                #64748b;

            font-size:
                13px;

        }


        .info-value {

            font-weight:
                600;

            text-align:
                right;

            font-size:
                14px;

        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT SECTION
        |--------------------------------------------------------------------------
        */

        .product-section {

            padding:
                0 15px 25px;

        }


        .section-header {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

            margin-bottom:
                12px;

        }


        .section-title {

            font-size:
                18px;

            font-weight:
                700;

            margin:
                0;

        }


        .count-badge {

            background:
                #2563eb;

            color:
                #fff;

            padding:
                5px 11px;

            border-radius:
                30px;

            font-size:
                12px;

            font-weight:
                700;

        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT CARD
        |--------------------------------------------------------------------------
        */

        .product-card {

            background:
                #fff;

            border-radius:
                16px;

            padding:
                15px;

            margin-bottom:
                12px;

            box-shadow:
                0 4px 16px
                rgba(
                    0,
                    0,
                    0,
                    .06
                );

        }


        .product-top {

            display:
                flex;

            justify-content:
                space-between;

            gap:
                10px;

        }


        .product-name {

            font-size:
                16px;

            font-weight:
                700;

            line-height:
                1.3;

        }


        .qty-badge {

            background:
                #dcfce7;

            color:
                #166534;

            padding:
                5px 10px;

            border-radius:
                30px;

            font-size:
                12px;

            font-weight:
                700;

            white-space:
                nowrap;

        }


        .product-details {

            margin-top:
                12px;

        }


        .detail-row {

            display:
                flex;

            justify-content:
                space-between;

            gap:
                12px;

            padding:
                6px 0;

            border-bottom:
                1px solid
                #f1f5f9;

        }


        .detail-label {

            color:
                #64748b;

            font-size:
                12px;

        }


        .detail-value {

            font-size:
                13px;

            font-weight:
                600;

            text-align:
                right;

            word-break:
                break-word;

        }


        .barcode {

            color:
                #2563eb;

            font-weight:
                700;

        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY
        |--------------------------------------------------------------------------
        */

        .empty-box {

            background:
                #fff;

            padding:
                30px 20px;

            text-align:
                center;

            border-radius:
                16px;

            color:
                #64748b;

        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        .page-footer {

            text-align:
                center;

            color:
                #94a3b8;

            font-size:
                12px;

            padding:
                15px;

        }

        /* =========================================================
   PRODUCT CARD
========================================================= */

.product-card {

    position: relative;

    display: grid;

    grid-template-columns:
        90px
        minmax(0, 1fr);

    gap: 14px;

    background: #ffffff;

    border-radius: 18px;

    padding: 14px;

    margin-bottom: 14px;

    box-shadow:
        0 5px 18px
        rgba(15, 23, 42, .07);

    border:
        1px solid #e8edf4;

}


/* =========================================================
   PRODUCT NUMBER
========================================================= */

.product-number {

    position: absolute;

    top: 10px;

    left: 10px;

    z-index: 2;

    min-width: 25px;

    height: 25px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #2563eb;

    color: #ffffff;

    border-radius: 50%;

    font-size: 11px;

    font-weight: 700;

}


/* =========================================================
   IMAGE
========================================================= */

.product-image-wrapper {

    width: 90px;

    height: 110px;

    border-radius: 12px;

    overflow: hidden;

    background: #f1f5f9;

    display: flex;

    align-items: center;

    justify-content: center;

}


.product-image {

    width: 100%;

    height: 100%;

    object-fit: cover;

    cursor: pointer;

    transition:
        transform .2s ease;

}


.product-image:hover {

    transform:
        scale(1.04);

}


.no-product-image {

    width: 100%;

    height: 100%;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    color: #94a3b8;

    font-size: 11px;

}


.no-product-image i {

    font-size: 28px;

    margin-bottom: 5px;

}


/* =========================================================
   CONTENT
========================================================= */

.product-content {

    min-width: 0;

}


.product-title {

    font-size: 17px;

    font-weight: 700;

    line-height: 1.3;

    color: #0f172a;

    padding-right: 4px;

}


.product-sku {

    margin-top: 3px;

    font-size: 12px;

    color: #64748b;

    font-weight: 600;

}


/* =========================================================
   INFO GRID
========================================================= */

.product-info-grid {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 8px;

    margin-top: 11px;

}


.product-info {

    min-width: 0;

    background: #f8fafc;

    border-radius: 9px;

    padding: 7px 8px;

}


.product-info span {

    display: block;

    font-size: 10px;

    color: #64748b;

    margin-bottom: 2px;

}


.product-info strong {

    display: block;

    font-size: 12px;

    color: #172033;

    overflow-wrap: anywhere;

}


/* =========================================================
   BARCODE
========================================================= */

.product-info:first-child strong {

    color: #2563eb;

}


/* =========================================================
   BOTTOM
========================================================= */

.product-bottom {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-top: 10px;

    padding-top: 9px;

    border-top:
        1px solid #e5e7eb;

    font-size: 12px;

    color: #64748b;

}


.qty-badge {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-width: 36px;

    padding: 5px 10px;

    border-radius: 20px;

    background: #dcfce7;

    color: #166534;

    font-size: 12px;

    font-weight: 700;

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 576px) {

    .product-card {

        grid-template-columns:
            75px
            minmax(0, 1fr);

        gap: 11px;

        padding: 11px;

        border-radius: 15px;

    }


    .product-image-wrapper {

        width: 75px;

        height: 95px;

        border-radius: 10px;

    }


    .product-title {

        font-size: 15px;

    }


    .product-info-grid {

        grid-template-columns:
            1fr;

        gap: 6px;

    }


    .product-info {

        padding: 6px 7px;

    }


    .product-info span {

        font-size: 9px;

    }


    .product-info strong {

        font-size: 11px;

    }

}


/* =========================================================
   VERY SMALL PHONES
========================================================= */

@media (max-width: 360px) {

    .product-card {

        grid-template-columns:
            62px
            minmax(0, 1fr);

    }


    .product-image-wrapper {

        width: 62px;

        height: 82px;

    }


    .product-title {

        font-size: 14px;

    }

}

    </style>

</head>


<body>


<!-- =========================================================
     HEADER
========================================================= -->

<div class="page-header">

    <div class="page-title">

        Box Stock Details

    </div>


    <div class="box-number">

        Box No:
        <strong>
            {{ $box->boxno }}
        </strong>

    </div>

</div>



<!-- =========================================================
     BOX INFORMATION
========================================================= -->

<div class="info-card">


    <div class="info-row d-none">

        <span class="info-label">
            Company ID
        </span>

        <span class="info-value">
            {{ $box->companyid }}
        </span>

    </div>


    <div class="info-row d-none">

        <span class="info-label">
            Sub Company ID
        </span>

        <span class="info-value">
            {{ $box->subcompanyid }}
        </span>

    </div>


    <div class="info-row d-none">

        <span class="info-label">
            Project ID
        </span>

        <span class="info-value">
            {{ $box->projectid }}
        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Warehouse
        </span>

        <span class="info-value">

            {{ $warehouse->warehousename ?? '-' }}

        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Location
        </span>

        <span class="info-value">

            {{ $location->locationname ?? '-' }}

        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Total Products
        </span>

        <span class="info-value">

            {{ $stock->count() }}

        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Total Quantity
        </span>

        <span class="info-value">

            {{ $totalQuantity }}

        </span>

    </div>


</div>



<!-- =========================================================
     PRODUCTS
========================================================= -->

<div class="product-section">


    <div class="section-header">

        <h2 class="section-title">

            Products in Box

        </h2>


        <span class="count-badge">

            {{ $stock->count() }}

        </span>

    </div>



    @forelse(
    $stock as $index => $product
)

<div class="product-card">

    <div class="product-number">
        {{ $index + 1 }}
    </div>


    <!-- PRODUCT IMAGE -->

    <div class="product-image-wrapper">

        @if(!empty($product->image_url))

            <img
                src="{{ $product->image_url }}"
                alt="{{ $product->item_name_text ?? 'Product' }}"
                class="product-image"
                loading="lazy"
                onclick="openProductImage(this.src)"
            >

        @else

            <div class="no-product-image">

                <i class="bi bi-image"></i>

                <span>
                    No Image
                </span>

            </div>

        @endif

    </div>


    <!-- PRODUCT INFORMATION -->

    <div class="product-content">


        <div class="product-title">

            {{ $product->item_name_text ?? 'Product' }}

        </div>


        @if(!empty($product->sku))

            <div class="product-sku">

                SKU:
                {{ $product->sku }}

            </div>

        @endif


        <div class="product-info-grid">


            <!-- BARCODE -->

            <div class="product-info">

                <span>
                    Barcode
                </span>

                <strong>
                    {{ $product->barcode }}
                </strong>

            </div>


            <!-- ITEM TYPE -->

            <div class="product-info">

                <span>
                    Item Type
                </span>

                <strong>
                    {{ $product->item_type_text ?? '-' }}
                </strong>

            </div>


            <!-- DESIGNER -->

            <div class="product-info">

                <span>
                    Designer
                </span>

                <strong>
                    {{ $product->designer_name_text ?? '-' }}
                </strong>

            </div>


            <!-- COLOUR -->

            <div class="product-info">

                <span>
                    Colour
                </span>

                <strong>
                    {{ $product->colour_text ?? '-' }}
                </strong>

            </div>


            <!-- SIZE -->

            <div class="product-info">

                <span>
                    Size
                </span>

                <strong>
                    {{ $product->size_text ?? '-' }}
                </strong>

            </div>


            <!-- GENDER -->

            <div class="product-info">

                <span>
                    Gender
                </span>

                <strong>
                    {{ $product->gender_text ?? '-' }}
                </strong>

            </div>


        </div>


        <!-- QUANTITY -->

        <div class="product-bottom">

            <span>
                Quantity
            </span>

            <strong class="qty-badge">

                {{ $product->quantity_received ?? 0 }}

            </strong>

        </div>


    </div>

</div>


@empty

<div class="empty-box">

    <div class="empty-icon">
        📦
    </div>

    <h5>
        No Products Found
    </h5>

    <p>
        There are currently no products
        assigned to this box.
    </p>

</div>

@endforelse


</div>



<div class="page-footer">

    Ready to Sell Stock

</div>

<div
    class="modal fade"
    id="productImageModal"
    tabindex="-1"
>

    <div
        class="modal-dialog modal-dialog-centered"
    >

        <div class="modal-content">

            <div class="modal-body p-2 text-center">

                <img
                    id="largeProductImage"
                    src=""
                    alt="Product"
                    style="
                        width:100%;
                        max-height:80vh;
                        object-fit:contain;
                        border-radius:12px;
                    "
                >

            </div>

        </div>

    </div>

</div>


<script>

function openProductImage(imageUrl)
{

    const image =
        document.getElementById(
            'largeProductImage'
        );


    if (!image) {
        return;
    }


    image.src =
        imageUrl;


    if (
        typeof bootstrap !==
        'undefined'
    ) {

        const modal =
            bootstrap.Modal.getOrCreateInstance(
                document.getElementById(
                    'productImageModal'
                )
            );


        modal.show();

    }

}

</script>


</body>

</html>