@extends('layouts.app')

@section('title', 'Design Specification Master')

@section('content')

<div class="container-fluid py-3 design-spec-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="page-header mb-4">

        <div class="page-title-area">

            <div class="page-title-icon">
                <i class="bi bi-palette2"></i>
            </div>

            <div>
                <h3 class="page-title mb-1">
                    Design Specification Master
                </h3>

                <p class="page-subtitle mb-0">
                    Create and manage garment design specifications
                </p>
            </div>

        </div>


        <div class="page-actions">

            <button
                type="button"
                class="btn btn-primary"
                id="btnNewSpecification">

                <i class="bi bi-plus-lg me-1"></i>

                New Specification

            </button>


            <button
                type="button"
                class="btn btn-outline-primary"
                id="btnShowAllSpecifications">

                <i class="bi bi-list-ul me-1"></i>

                Show All Specifications

            </button>

            <button
                type="button"
                class="btn btn-outline-success"
                id="btnUseUploadedImage">

                <i class="bi bi-images me-1"></i>

                Use Uploaded Image

            </button>
            <button
                type="button"
                class="btn btn-outline-info"
                id="btnUseSupplierdImage">

                <i class="bi bi-images me-1"></i>

                 Uploaded Supplier Raw Data

            </button>

        </div>

    </div>


    {{-- =========================================================
         COMPANY / PROJECT CONTEXT
    ========================================================== --}}
    {{-- =========================================================
     COMPANY / PROJECT CONTEXT
========================================================= --}}
<div class="context-card mb-4">

    {{-- Company --}}
    <div class="context-item d-none">

        <div class="context-icon">
            <i class="bi bi-building"></i>
        </div>

        <div class="context-content">

            <span class="context-label">
                Company
            </span>

            <strong class="context-value">
                {{ $companyName }}
            </strong>

        </div>

    </div>


    {{-- Sub Company --}}
    <div class="context-item d-none">

        <div class="context-icon">
            <i class="bi bi-diagram-3"></i>
        </div>

        <div class="context-content">

            <span class="context-label">
                Sub Company
            </span>

            <strong class="context-value">
                {{ $subCompanyName }}
            </strong>

        </div>

    </div>


    {{-- Project --}}
    <div class="context-item d-none">

        <div class="context-icon">
            <i class="bi bi-folder"></i>
        </div>

        <div class="context-content">

            <span class="context-label">
                Project
            </span>

            <strong class="context-value">
                {{ $projectName }}
            </strong>

        </div>

        

    </div>

    {{-- Supplier --}}
    <div class="context-item">

    <div class="context-icon">
        <i class="bi bi-shop"></i>
    </div>

    <div class="context-content">

        <span class="context-label">
            Supplier
        </span>

        <strong
            class="context-value"
            id="supplierContextName"
        >
           {{ $projectName }}
        </strong>

        <input
            type="hidden"
            id="supplierContextId"
            name="supplier_id"
            value=""
        >

    </div>

</div>

</div>


    {{-- =========================================================
         NEW DESIGN SPECIFICATION FORM
    ========================================================== --}}
    <div
        class="card design-section-card mb-4"
        id="newSpecificationSection">


        {{-- Section Header --}}
        <div class="section-header">

            <div class="section-title-area">

                <div class="section-icon">
                    <i class="bi bi-plus-circle"></i>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">

                <h5
                    class="section-title mb-1"
                    id="specificationFormTitle">
                    New Design Specification
                </h5>

                <span
                    id="editBarcodeBadge"
                    class="edit-barcode-badge"
                    style="display:none;">

                    <i class="bi bi-upc-scan me-1"></i>

                    Barcode:
                    <strong id="editBarcodeText"></strong>

                </span>

            </div>

            <p
                class="section-subtitle mb-0"
                id="specificationFormSubtitle">
                Enter garment design specification details
            </p>

            </div>


            <button
                type="button"
                class="btn btn-sm btn-light"
                id="btnClearSpecification">

                <i class="bi bi-arrow-counterclockwise me-1"></i>

                Clear

            </button>

        </div>

        <input
            type="hidden"
            id="editSpecificationId"
            value="">

        <input
            type="hidden"
            id="editSpecificationBarcode"
            value="">


        <div class="section-body">


            {{-- =================================================
                 DESIGN INFORMATION
            ================================================== --}}
            <div class="inner-card">

                <div class="inner-card-header">

                    <div class="inner-icon">
                        <i class="bi bi-info-circle"></i>
                    </div>

                    <div>

                        <h6 class="mb-1">
                            Design Information
                        </h6>

                        <small>
                            Basic garment specification
                        </small>

                    </div>

                </div>

                 {{-- =================================================
                 IMAGE PREVIEW
            ================================================== --}}
            <div
                class="selected-images-section mt-4"
                id="selectedImagesSection"
                style="display:none;">

                <div class="selected-images-title">

                    <i class="bi bi-images me-1"></i>

                    Selected Images

                </div>

                <div
                    id="selectedImagePreview"
                    class="selected-image-grid">
                </div>

            </div>

            <!-- =========================================================
     SELECTED SUPPLIER PRODUCT DETAILS
========================================================= -->
<div style="display:flex; gap:20px; align-items:flex-start;">
    
    <!-- YOUR EXISTING MAIN IMAGE SECTION -->
    
    <div id="supplierProductInfo"
         style="
            flex:1;
            padding:15px;
            border:1px solid #dee2e6;
            border-radius:8px;
            background:#fff;
         ">
    </div>

</div>


                <div class="form-grid-2">

                     {{-- Image Upload --}}
                    <div class="form-group">

                        <label class="form-label">
                            Design Images
                        </label>

                        <input
                            type="file"
                            class="form-control"
                            id="filenew"
                            name="design_images[]"
                            accept=".jpg,.jpeg,.png,.webp"
                            multiple>

                        <small class="form-help">
                            You can select multiple design images.
                        </small>

                    </div>

                    <!-- =========================================================
                            DESIGN SUB IMAGES
                        ========================================================= -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <strong>Design Sub Images</strong>
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-12">

                                        <label class="form-label fw-bold">
                                            Add Sub Images
                                        </label>

                                        <input
                                            type="file"
                                            class="form-control"
                                            id="sub_images"
                                            name="sub_images[]"
                                            accept="image/*"
                                            multiple
                                        >

                                        <small class="text-muted">
                                            You can select multiple images.
                                        </small>

                                    </div>
                                </div>

                                <!-- Preview -->
                               <div class="row mt-3" id="subImagesPreview"></div>

                            </div>
                        </div>


                  

                    
                    {{-- Item Name --}}
                    <div class="form-group">

                        <label class="form-label">
                            Item Name
                            <span class="required">*</span>
                        </label>

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="item_name"
                                name="item_name">

                                <option value="">
                                    Select Item Name
                                </option>

                                @foreach($itemNames as $itemName)

                                    <option value="{{ $itemName->id }}">
                                        {{ $itemName->itemname }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Item Name">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>




                    {{-- Item Type --}}
                    <div class="form-group">

                        <label class="form-label">
                            Item Type
                            <span class="required">*</span>
                        </label>

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="item_type"
                                name="item_type">

                                <option value="">
                                    Select Item Type
                                </option>

                                @foreach($itemTypes as $itemType)

                                    <option value="{{ $itemType->id }}">
                                        {{ $itemType->itemtype }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Item Type">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>

                      {{-- Designer --}}
                    <div class="form-group">

                        <label class="form-label">
                            Designer
                            <span class="required">*</span>
                        </label>

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="designer_name"
                                name="designer_name">

                                <option value="">
                                    Select Designer
                                </option>

                                @foreach($designers as $designer)

                                    <option value="{{ $designer->id }}">
                                        {{ $designer->designername }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Designer">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>



                    {{-- Gender --}}
                    <div class="form-group">

                        <label class="form-label">
                            Gender
                            <span class="required">*</span>
                        </label>

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="gender_type"
                                name="gender">

                                <option value="">
                                    Select Gender
                                </option>

                                @foreach($genders as $gender)

                                    <option value="{{ $gender->id }}">
                                        {{ $gender->name }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Gender">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>





                    {{-- Composition --}}
                    <div class="form-group">

                        <label class="form-label">
                            Composition
                            <span class="required">*</span>
                        </label>

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="composition"
                                name="composition">

                                <option value="">
                                    Select Composition
                                </option>

                                @foreach($compositions as $composition)

                                    <option value="{{ $composition->id }}">
                                        {{ $composition->composition_details }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Composition">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>



                    {{-- Colour --}}
                    <div class="form-group">

                        <label class="form-label">
                            Colour
                            <span class="required">*</span>
                        </label>

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="colour"
                                name="colour">

                                <option value="">
                                    Select Colour
                                </option>

                                @foreach($colours as $colour)

                                    <option value="{{ $colour->id }}">
                                        {{ $colour->colourname }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Colour">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>



                    {{-- Size --}}
                    <div class="form-group">

                        <label class="form-label">
                            Size
                            <span class="required">*</span>
                        </label>

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="sizes"
                                name="sizes">

                                <option value="">
                                    Select Size
                                </option>

                                @foreach($sizes as $size)

                                    <option value="{{ $size->id }}">
                                        {{ $size->size }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Size">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>



                    {{-- Embellishment --}}
                    <div class="form-group">

                        <label class="form-label">
                            Embellishment
                        </label>

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="embellishment"
                                name="embellishment">

                                <option value="">
                                    Select Embellishment
                                </option>

                                @foreach($embellishments as $embellishment)

                                    <option value="{{ $embellishment->id }}">
                                        {{ $embellishment->embellishmentname }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Embellishment">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>



                    {{-- Manufacturing Process --}}
                    <div class="form-group">

                        <label class="form-label">
                            Manufacturing Process
                            <span class="required">*</span>
                        </label>
                        
                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="manufacturing_process"
                                name="manufacturing_process">

                                <option value="">
                                    Select Manufacturing Process
                                </option>

                                @foreach(
                                    $manufacturingProcesses
                                    as $process
                                )

                                    <option value="{{ $process->id }}">
                                        {{ $process->manufacturing_process }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Manufacturing Process">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>



                    {{-- Craftsman --}}
                    <div class="form-group">

                        <label class="form-label">
                            Craftsman
                        </label>

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="mcraftsman"
                                name="craftsman">

                                <option value="">
                                    Select Craftsman
                                </option>

                                @foreach($craftsmen as $craftsman)

                                    <option
                                        value="{{ $craftsman->id }}"
                                        data-code="{{ $craftsman->code }}">

                                        {{ $craftsman->name }}

                                        @if($craftsman->code)
                                            ({{ $craftsman->code }})
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Craftsman">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>



                    {{-- Manufacture --}}
                    <div class="form-group">

                        <label class="form-label">
                            Manufacture
                             <span class="required">*</span>
                        </label>
                       
                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="cmbmanufacture"
                                name="manufecture">

                                <option value="">
                                    Select Manufacture
                                </option>

                                @foreach($manufactures as $manufacture)

                                    <option value="{{ $manufacture->id }}">
                                        {{ $manufacture->name }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Manufacture">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>



                    {{-- Client --}}
                    <div class="form-group">

                        <label class="form-label">
                            Collection
                             <span class="required">*</span>
                        </label>
                       

                        <div class="input-with-add">

                            <select
                                class="form-select select2-master"
                                id="cmbclient"
                                name="client">

                                <option value="">
                                    Select Collection
                                </option>

                                @foreach($clients as $client)

                                    <option value="{{ $client->id }}">
                                        {{ $client->name }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="button"
                                class="btn-add-master"
                                title="Add Client">

                                <i class="bi bi-plus-lg"></i>

                            </button>

                        </div>

                    </div>

                    <div class="form-field">
                        <label for="sku" class="form-label">
                            SKU
                            <span class="text-muted">(Optional)</span>
                        </label>

                        <input
                            type="text"
                            id="sku"
                            name="sku"
                            class="form-control"
                            placeholder="Enter SKU if available"
                            autocomplete="off"
                        >

                        <small class="text-muted">
                            Leave blank if SKU is not available.
                        </small>
                    </div>

                    <div class="form-field">
                        <label for="price" class="form-label">
                            Product Price
                            <span class="text-muted">(Optional)</span>
                        </label>

                        <input
                            type="number"
                            id="price"
                            name="price"
                            class="form-control"
                            min-value='0'
                            placeholder="Enter Price if available"
                            autocomplete="off"
                        >

                        
                    </div>

                    <div class="form-field">
                        <label for="saleprice" class="form-label">
                            Sale Price
                            <span class="text-muted">(Optional)</span>
                        </label>

                        <input
                            type="number"
                            id="saleprice"
                            name="saleprice"
                            class="form-control"
                            min-value='0'
                            placeholder="Enter Sale Price if available"
                            autocomplete="off"
                        >

                        
                    </div>

                    <div class="form-field">
                        <label for="minprice" class="form-label">
                            Sale Price
                            <span class="text-muted">(Optional)</span>
                        </label>

                        <input
                            type="number"
                            id="minprice"
                            name="minprice"
                            class="form-control"
                            min-value='0'
                            placeholder="Enter Min Price if available"
                            autocomplete="off"
                        >

                        
                    </div>



                    {{-- Client Reference --}}
                    <div class="form-group">

                        <label class="form-label">
                            Client Reference No. & Description
                        </label>

                        <textarea
                            id="txt_clientreference"
                            name="clientreference"
                            class="form-control"
                            rows="2"
                            placeholder="Enter client reference or description..."></textarea>

                    </div>



                   

                    {{-- =========================================================
     AI GENERATED PRODUCT CONTENT
========================================================= --}}
<div class="ai-content-card mt-4 d-none">

    <div class="ai-content-header">

        <div class="ai-content-title">

            <div class="ai-icon">
                <i class="bi bi-stars"></i>
            </div>

            <div>
                <h6 class="mb-1">
                    AI Generated Product Content
                </h6>

                <small>
                    Content is automatically generated from the selected design image.
                </small>
            </div>

        </div>

        <div class="ai-status">
            <span class="ai-status-dot"></span>
            AI Ready
        </div>

    </div>


        <div class="ai-content-body">

            {{-- Product Name --}}
            <div class="ai-field ai-field-full">

                <label for="txt_productName">
                    <i class="bi bi-type"></i>
                    Product Name
                </label>

                <input
                    type="text"
                    id="txt_productName"
                    class="form-control"
                    placeholder="AI generated product name">

            </div>


            {{-- Product Description --}}
            <div class="ai-field ai-field-full">

                <label for="txt_productDescription">
                    <i class="bi bi-card-text"></i>
                    Product Description
                </label>

                <textarea
                    id="txt_productDescription"
                    class="form-control"
                    rows="4"
                    placeholder="AI generated product description"></textarea>

            </div>


            {{-- Meta Title --}}
            <div class="ai-field">

                <label for="txt_metaTitle">
                    <i class="bi bi-heading"></i>
                    Meta Title
                </label>

                <input
                    type="text"
                    id="txt_metaTitle"
                    class="form-control"
                    placeholder="AI generated meta title">

            </div>


            {{-- Meta Keywords --}}
            <div class="ai-field">

                <label for="txt_metaKeywords">
                    <i class="bi bi-tags"></i>
                    Meta Keywords
                </label>

                <input
                    type="text"
                    id="txt_metaKeywords"
                    class="form-control"
                    placeholder="AI generated keywords">

            </div>


            {{-- Meta Description --}}
            <div class="ai-field ai-field-full">

                <label for="txt_metaDescription">
                    <i class="bi bi-file-text"></i>
                    Meta Description
                </label>

                <textarea
                    id="txt_metaDescription"
                    class="form-control"
                    rows="3"
                    placeholder="AI generated meta description"></textarea>

            </div>


            {{-- Product Tags --}}
            <div class="ai-field ai-field-full">

                <label for="txt_productTags">
                    <i class="bi bi-bookmark"></i>
                    Product Tags
                </label>

                <input
                    type="text"
                    id="txt_productTags"
                    class="form-control"
                    placeholder="AI generated product tags">

            </div>


            {{-- Image Alt Text --}}
            <div class="ai-field ai-field-full">

                <label for="txt_image_alt_text">
                    <i class="bi bi-image-alt"></i>
                    Image Alt Text
                </label>

                <input
                    type="text"
                    id="txt_image_alt_text"
                    class="form-control"
                    placeholder="AI generated image alt text">

            </div>

        </div>

</div>

                </div>

            </div>


           


            {{-- =================================================
                 FORM ACTIONS
            ================================================== --}}
            <div class="form-actions mt-4">

                <button id="generateBtn" class="d-none">⚡ Generate Content</button>

                <button
                    type="button"
                    class="btn btn-light"
                    id="btnCancelSpecification">

                    <i class="bi bi-x-lg me-1"></i>

                    Clear

                </button>


                <button
                    type="button"
                    class="btn btn-primary"
                    id="btnSaveSpecification">

                    <i class="bi bi-check-lg me-1"></i>

                    Save Specification

                </button>

            </div>

        </div>

    </div>



    {{-- =========================================================
         ALL SPECIFICATIONS SECTION
    ========================================================== --}}
    <div
        class="card design-section-card"
        id="allSpecificationsSection"
        style="display:none;">


        {{-- Header --}}
        <div class="section-header">

            <div class="section-title-area">

                <div class="section-icon">
                    <i class="bi bi-list-ul"></i>
                </div>

                <div>

                    <h5 class="section-title mb-1">
                        All Design Specifications
                    </h5>

                    <p class="section-subtitle mb-0">
                        Existing specifications for the current project
                    </p>

                </div>

            </div>


            <div class="list-header-actions">

                <div class="search-box">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        id="specificationSearch"
                        class="form-control"
                        placeholder="Search specification...">

                </div>


                <button
                    type="button"
                    class="btn btn-light"
                    id="btnRefreshSpecifications">

                    <i class="bi bi-arrow-clockwise me-1"></i>

                    Refresh

                </button>

            </div>

        </div>


        {{-- Loading --}}
        <div
            id="specificationLoading"
            class="specification-loading"
            style="display:none;">

            <div class="spinner-border text-primary">
            </div>

            <span>
                Loading specifications...
            </span>

        </div>


        {{-- Empty --}}
        <div
            id="specificationEmpty"
            class="empty-state"
            style="display:none;">

            <div class="empty-icon">

                <i class="bi bi-inbox"></i>

            </div>

            <h6>
                No design specifications found
            </h6>

            <p>
                Create your first garment design specification.
            </p>

        </div>


        {{-- Cards --}}
        <div
            id="specificationCards"
            class="specification-grid">

        </div>

        {{-- =================================================
             PAGINATION
        ================================================== --}}
        <div
            id="specificationPagination"
            class="specification-pagination"
            style="display:none;">

            <div class="pagination-info" id="paginationInfo"></div>

            <div class="pagination-controls">

                <div class="pagination-per-page">
                    <label for="specificationPerPage">Show</label>

                    <select
                        id="specificationPerPage"
                        class="form-select form-select-sm">

                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>

                    <span>records</span>
                </div>

                <nav aria-label="Specification pagination">
                    <ul
                        class="pagination pagination-sm mb-0"
                        id="specificationPaginationList">
                    </ul>
                </nav>

            </div>

        </div>


    </div>

</div>



{{-- =============================================================
     IMAGE VIEW MODAL
============================================================= --}}
<div
    class="modal fade"
    id="designImageModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content image-modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Design Image
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body image-modal-body">

                <img
                    id="largeDesignImage"
                    src=""
                    alt="Design Image"
                    class="large-design-image">

            </div>

        </div>

    </div>

</div>
{{-- =============================================================
     DESIGN SPECIFICATION VIEW MODAL
============================================================= --}}
<div
    class="modal fade"
    id="designSpecificationViewModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content specification-view-modal">

            {{-- Header --}}
            <div class="modal-header">

                <div class="d-flex align-items-center gap-3">

                    <div class="view-modal-icon">
                        <i class="bi bi-eye"></i>
                    </div>

                    <div>
                        <h5
                            class="modal-title mb-0"
                            id="viewSpecificationTitle">
                            Design Specification
                        </h5>

                        <small class="text-muted">
                            Complete specification details
                        </small>
                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            {{-- Body --}}
            <div class="modal-body">

                {{-- Product Top --}}
                <div class="view-product-top">

                    <div
                        id="viewSpecificationImage"
                        class="view-product-image">
                    </div>
                    <!-- =========================================================
                        DESIGN SUB IMAGES
                    ========================================================== -->
                    <div
                        class="view-section mt-4"
                        id="viewSubImagesSection"
                        style="display:none;"
                    >

                        <div class="view-section-header">

                            <i class="bi bi-images"></i>

                            <div>
                                <h6>Design Sub Images</h6>

                                <small>
                                    Additional images of this design
                                </small>
                            </div>

                        </div>

                        <div
                            id="viewSubImages"
                            class="view-sub-images-grid"
                        ></div>

                    </div>

                    <div class="view-product-summary">

                        <div class="view-barcode-row">

                            <span class="view-barcode-badge">
                                <i class="bi bi-upc-scan"></i>

                                <span id="viewBarcode">
                                    -
                                </span>
                            </span>

                            <span class="view-sku">
                                SKU:
                                <strong id="viewSku">
                                    -
                                </strong>
                            </span>

                        </div>

                        <h3
                            id="viewItemName"
                            class="view-product-name">
                            -
                        </h3>

                        <div
                            id="viewStatus"
                            class="mt-2">
                        </div>

                    </div>

                </div>


                {{-- Basic Information --}}
                <div class="view-section">

                    <div class="view-section-header">

                        <i class="bi bi-info-circle"></i>

                        <div>
                            <h6>Design Information</h6>
                            <small>
                                Basic garment specification
                            </small>
                        </div>

                    </div>


                    <div class="view-info-grid">

                        <div class="view-info-box">
                            <span>Designer</span>
                            <strong id="viewDesigner">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Item Type</span>
                            <strong id="viewItemType">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Gender</span>
                            <strong id="viewGender">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Item Name</span>
                            <strong id="viewItemName2">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Composition</span>
                            <strong id="viewComposition">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Colour</span>
                            <strong id="viewColour">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Size</span>
                            <strong id="viewSize">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Embellishment</span>
                            <strong id="viewEmbellishment">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Manufacturing Process</span>
                            <strong id="viewManufacturingProcess">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Craftsman</span>
                            <strong id="viewCraftsman">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Craftsman Code</span>
                            <strong id="viewCraftsmanCode">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Manufacture</span>
                            <strong id="viewManufacture">-</strong>
                        </div>

                        <div class="view-info-box">
                            <span>Client</span>
                            <strong id="viewClient">-</strong>
                        </div>

                        <div class="view-info-box view-info-box-wide">
                            <span>Client Reference / Description</span>
                            <strong id="viewClientReference">-</strong>
                        </div>

                    </div>

                </div>


                {{-- Company Context --}}
                <div class="view-section">

                    <div class="view-section-header">

                        <i class="bi bi-building"></i>

                        <div>
                            <h6>Company Context</h6>
                            <small>
                                Current company and project
                            </small>
                        </div>

                    </div>


                    <div class="view-context-grid">

                        <div class="view-context-box">

                            <span>Company</span>

                            <strong id="viewCompany">
                                -
                            </strong>

                        </div>


                        <div class="view-context-box">

                            <span>Sub Company</span>

                            <strong id="viewSubCompany">
                                -
                            </strong>

                        </div>


                        <div class="view-context-box">

                            <span>Project</span>

                            <strong id="viewProject">
                                -
                            </strong>

                        </div>

                    </div>

                </div>


                {{-- AI Information --}}
                <div
                    class="view-section"
                    id="viewAISection">

                    <div class="view-section-header">

                        <i class="bi bi-stars"></i>

                        <div>
                            <h6>AI Product Information</h6>

                            <small>
                                AI generated product content
                            </small>
                        </div>

                    </div>


                    <div class="view-ai-grid">

                        <div class="view-ai-box view-ai-wide">

                            <span>Product Name</span>

                            <div id="viewAIProductName">
                                -
                            </div>

                        </div>


                        <div class="view-ai-box view-ai-wide">

                            <span>Product Description</span>

                            <div id="viewAIProductDescription">
                                -
                            </div>

                        </div>


                        <div class="view-ai-box">

                            <span>Meta Title</span>

                            <div id="viewAIMetaTitle">
                                -
                            </div>

                        </div>


                        <div class="view-ai-box">

                            <span>Meta Keywords</span>

                            <div id="viewAIMetaKeywords">
                                -
                            </div>

                        </div>


                        <div class="view-ai-box view-ai-wide">

                            <span>Meta Description</span>

                            <div id="viewAIMetaDescription">
                                -
                            </div>

                        </div>


                        <div class="view-ai-box">

                            <span>Product Tags</span>

                            <div id="viewAIProductTags">
                                -
                            </div>

                        </div>


                        <div class="view-ai-box">

                            <span>Image Alt Text</span>

                            <div id="viewAIImageAltText">
                                -
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Footer --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    <i class="bi bi-x-lg me-1"></i>

                    Close

                </button>

            </div>

        </div>

    </div>

</div>

{{-- =============================================================
     BARCODE PRINT MODAL
============================================================= --}}
{{-- =========================================================
     BARCODE PRINT MODAL
========================================================= --}}

<div
    class="modal fade"
    id="barcodePrintModal"
    tabindex="-1"
    aria-hidden="true">

    <div
        class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content barcode-print-modal">


            {{-- =====================================================
                 HEADER
            ====================================================== --}}

            <div class="modal-header barcode-modal-header">

                <div>

                    <h5 class="modal-title mb-1">

                        <i class="bi bi-upc-scan me-2"></i>

                        Barcode Print

                    </h5>

                    <small>
                        Prepare barcode labels for printing
                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>



            {{-- =====================================================
                 BODY
            ====================================================== --}}

            <div class="modal-body">


                {{-- =================================================
                     SELECTED PRODUCTS
                ================================================== --}}

                <div class="barcode-selected-products-section">


                    {{-- SECTION HEADER --}}

                    <div class="barcode-section-title">

                        <div>

                            <i class="bi bi-box-seam me-2"></i>

                            <strong>
                                Selected Products
                            </strong>

                        </div>


                        <span
                            id="selectedBarcodeProductCount"
                            class="badge bg-primary">

                            0 Products

                        </span>

                    </div>



                    {{-- =================================================
                         DYNAMIC PRODUCT LIST

                         JavaScript will insert all selected
                         products here.
                    ================================================== --}}

                    <div
                        id="selectedBarcodeProductsContainer"
                        class="selected-barcode-products-container">


                        {{-- Initial message --}}

                        <div
                            class="selected-barcode-empty">

                            <i
                                class="bi bi-box-seam">
                            </i>

                            <div>
                                No products selected.
                            </div>

                        </div>


                    </div>


                </div>



                {{-- =================================================
                     BARCODE PREVIEW
                ================================================== --}}

                <div class="barcode-preview-section">


                    <div class="barcode-section-title">

                        <div>

                            <i class="bi bi-upc-scan me-2"></i>

                            <strong>
                                Barcode Preview
                            </strong>

                        </div>


                        <span
                            id="barcodePreviewCount"
                            class="badge bg-secondary">

                            0 Barcodes

                        </span>

                    </div>



                    <div
                        id="barcodePreviewBox"
                        class="barcode-preview-box">


                        {{--

                            Barcode preview will be generated
                            here by JavaScript.

                        --}}


                        <div
                            id="barcodePreviewEmpty"
                            class="barcode-preview-empty">

                            <i
                                class="bi bi-upc">
                            </i>

                            <span>
                                Barcode preview will appear here.
                            </span>

                        </div>


                    </div>


                </div>



                {{-- =================================================
                     PRINT SETTINGS
                ================================================== --}}

                <div class="barcode-print-settings">


                    <div class="barcode-section-title">

                        <div>

                            <i class="bi bi-printer me-2"></i>

                            <strong>
                                Print Settings
                            </strong>

                        </div>

                    </div>



                    <div class="row g-3">


                        {{-- =========================================
                             HOW MANY BARCODE
                        ========================================== --}}

                        <div class="col-12 col-md-6">


                            <label
                                for="txt_howmanybarcode"
                                class="barcode-input-label">

                                How many barcodes do you want to
                                print?

                            </label>


                            <input
                                type="number"
                                id="txt_howmanybarcode"
                                class="form-control"
                                min="1"
                                step="1"
                                value="0"
                                readonly>

                            <small class="text-muted">
                                Total quantity from all selected products.
                            </small>


                        </div>



                        {{-- =========================================
                             START BOX
                        ========================================== --}}

                        <div class="col-12 col-md-6">


                            <label
                                for="txt_startbox"
                                class="barcode-input-label">

                                Where do you want to start from
                                box to print barcode?

                            </label>


                            <input
                                type="number"
                                id="txt_startbox"
                                class="form-control"
                                min="1"
                                max="25"
                                step="1"
                                value="1">


                            <small
                                class="text-muted">

                                Page contains 25 barcode boxes.

                            </small>


                        </div>


                    </div>


                </div>



                {{-- =================================================
                     PRINT INFORMATION
                ================================================== --}}

                <div
                    class="barcode-print-summary"
                    id="barcodePrintSummary">


                    <div class="barcode-summary-item">

                        <span>
                            Selected Products
                        </span>

                        <strong
                            id="barcodeSummaryProducts">

                            0

                        </strong>

                    </div>



                    <div class="barcode-summary-item">

                        <span>
                            Total Barcodes
                        </span>

                        <strong
                            id="barcodeSummaryQuantity">

                            0

                        </strong>

                    </div>



                    <div class="barcode-summary-item">

                        <span>
                            Starting Box
                        </span>

                        <strong
                            id="barcodeSummaryStartBox">

                            1

                        </strong>

                    </div>


                </div>



                {{-- =================================================
                     HIDDEN DATA
                ================================================== --}}


                <input
                    type="hidden"
                    id="barcodePrintSpecificationId"
                    value="">


                <input
                    type="hidden"
                    id="barcodePrintValue"
                    value="">

                <input
                type="hidden"
                id="specificationProjectId"
                value="{{ $projectId }}">


            </div>



            {{-- =====================================================
                 FOOTER
            ====================================================== --}}

            <div
                class="modal-footer barcode-modal-footer">


                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    <i class="bi bi-x-lg me-1"></i>

                    Cancel

                </button>



                <button
                    type="button"
                    id="printbarcodefrom"
                    class="btn btn-primary">

                    <i class="bi bi-printer me-1"></i>

                    Generate Print Preview

                </button>


            </div>


        </div>

    </div>

</div>

<!-- ============================================================
     BARCODE SHEET PREVIEW MODAL
============================================================ -->
<div
    class="modal fade"
    id="barcodeSheetPreviewModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-fullscreen">

        <div class="modal-content">

            <div class="modal-header">

                <div>
                    <h5 class="modal-title">
                        <i class="bi bi-printer me-2"></i>
                        Barcode Print Preview
                    </h5>

                    <small class="text-muted">
                        A4 barcode sheet preview
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body barcode-preview-page-container">

                <div
                    id="barcodePdfContent_X"
                    class="barcode-print-sheet">
                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    Close

                </button>


                <button
                    type="button"
                    id="btnDownloadBarcodePdf"
                    class="btn btn-primary">

                    <i class="bi bi-file-earmark-pdf me-1"></i>

                    Download PDF

                </button>


                <button
                    type="button"
                    id="btnPrintBarcodePdf"
                    class="btn btn-success">

                    <i class="bi bi-printer me-1"></i>

                    Print

                </button>

            </div>

        </div>

    </div>

</div>

<!-- ============================================================
     SAVE / UPDATE CONFIRMATION MODAL
============================================================ -->

<div
    class="modal fade"
    id="saveSpecificationPreviewModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="savePreviewTitle">

                        <i class="bi bi-check2-square me-2"></i>

                        Confirm Design Specification

                    </h5>

                    <small class="text-muted">

                        Please check all details before final saving.

                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <!-- BODY -->
            <div class="modal-body">

                <!-- PRODUCT / BARCODE -->
                <div class="confirm-product-header">

                    <div
                        id="confirmSpecificationImage"
                        class="confirm-product-image">

                        <div class="confirm-no-image">
                            <i class="bi bi-image"></i>
                            <span>No Image</span>
                        </div>

                    </div>

                    


                    <div class="confirm-product-main">

                        <div class="confirm-item-title">

                            <span>
                                Item Name
                            </span>

                            <strong id="confirmItemName">
                                -
                            </strong>

                        </div>


                        <div class="confirm-barcode">
                            <span>
                                Barcode
                            </span>

                            <strong id="confirmBarcode">
                                -
                            </strong>

                            <div
                                id="confirmBarcodePreview"
                                class="mt-2 text-center"
                                style="display:none;"
                            >
                                <svg
                                    id="confirmBarcodeSvg"
                                    style="max-width:100%; height:auto;">
                                </svg>
                            </div>
                        </div>

                    </div>

                </div>


                <!-- DESIGN INFORMATION -->
                <div class="confirm-section">

                    <div class="confirm-section-title">

                        <i class="bi bi-info-circle"></i>

                        Design Information

                    </div>


                    <div class="confirm-grid">

                        <div class="confirm-field">
                            <span>Item Name</span>
                            <strong id="confirmItemName2">-</strong>
                        </div>

                        <div class="confirm-field">
                            <span>Item Type</span>
                            <strong id="confirmItemType">-</strong>
                        </div>

                        <div class="confirm-field">
                            <span>Designer</span>
                            <strong id="confirmDesigner">-</strong>
                        </div>

                        <div class="confirm-field">
                            <span>Gender</span>
                            <strong id="confirmGender">-</strong>
                        </div>

                        <div class="confirm-field">
                            <span>Composition</span>
                            <strong id="confirmComposition">-</strong>
                        </div>

                        <div class="confirm-field">
                            <span>Colour</span>
                            <strong id="confirmColour">-</strong>
                        </div>

                        <div class="confirm-field">
                            <span>Size</span>
                            <strong id="confirmSize">-</strong>
                        </div>

                        <div class="confirm-field">
                            <span>Embellishment</span>
                            <strong id="confirmEmbellishment">-</strong>
                        </div>

                        <div class="confirm-field">
                            <span>Manufacturing Process</span>
                            <strong id="confirmManufacturingProcess">
                                -
                            </strong>
                        </div>

                        <div class="confirm-field">
                            <span>Craftsman</span>
                            <strong id="confirmCraftsman">
                                -
                            </strong>
                        </div>

                        <div class="confirm-field">
                            <span>Craftsman Code</span>
                            <strong id="confirmCraftsmanCode">
                                -
                            </strong>
                        </div>

                        <div class="confirm-field">
                            <span>Manufacture</span>
                            <strong id="confirmManufacture">
                                -
                            </strong>
                        </div>

                        <div class="confirm-field">
                            <span>Client / Collection</span>
                            <strong id="confirmClient">
                                -
                            </strong>
                        </div>

                        <div class="confirm-field">
                            <span>SKU</span>
                            <strong id="confirmSku">
                                -
                            </strong>
                        </div>

                        <div class="confirm-field confirm-field-wide">
                            <span>Client Reference / Description</span>
                            <strong id="confirmClientReference">
                                -
                            </strong>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Supplier Stock Qty</div>
                            <div id="confirmSupplierStock" class="fw-semibold">-</div>
                        </div>

                    </div>

                </div>


                <!-- AI INFORMATION -->
                <div
                    class="confirm-section"
                    id="confirmAISection">

                    <div class="confirm-section-title">

                        <i class="bi bi-stars"></i>

                        AI Product Information

                    </div>


                    <div class="confirm-ai-grid">

                        <div class="confirm-ai-field">
                            <span>Product Name</span>
                            <div id="confirmAIProductName">-</div>
                        </div>

                        <div class="confirm-ai-field">
                            <span>Product Description</span>
                            <div id="confirmAIProductDescription">-</div>
                        </div>

                        <div class="confirm-ai-field">
                            <span>Meta Title</span>
                            <div id="confirmAIMetaTitle">-</div>
                        </div>

                        <div class="confirm-ai-field">
                            <span>Meta Keywords</span>
                            <div id="confirmAIMetaKeywords">-</div>
                        </div>

                        <div class="confirm-ai-field">
                            <span>Meta Description</span>
                            <div id="confirmAIMetaDescription">-</div>
                        </div>

                        <div class="confirm-ai-field">
                            <span>Product Tags</span>
                            <div id="confirmAIProductTags">-</div>
                        </div>

                        <div class="confirm-ai-field">
                            <span>Image Alt Text</span>
                            <div id="confirmAIImageAltText">-</div>
                        </div>

                    </div>

                </div>


                <!-- IMAGES -->
                <div class="confirm-section">

                    <div class="confirm-section-title">

                        <i class="bi bi-images"></i>

                        Design Images

                    </div>

                    <div
                        id="confirmImages"
                        class="confirm-images-grid">

                    </div>

                    <!-- =========================================================
                        DESIGN SUB IMAGES
                    ========================================================= -->
                    <div class="confirm-section mt-3">

                        <div class="confirm-section-title">
                            <i class="bi bi-images"></i>
                            Design Sub Images
                        </div>

                        <div
                            id="confirmSubImages"
                            class="confirm-images-grid">
                        </div>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    <i class="bi bi-arrow-left me-1"></i>

                    Back to Edit

                </button>


                <button
                    type="button"
                    id="btnFinalSaveSpecification"
                    class="btn btn-primary">

                    <i class="bi bi-check-lg me-1"></i>

                    Final Save

                </button>

            </div>

        </div>

    </div>

</div>

<!-- =========================================================
     MASTER MANAGEMENT MODAL
========================================================= -->

<div
    class="modal fade"
    id="masterManagementModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <div>
                    <h5
                        class="modal-title"
                        id="masterModalTitle"
                    >
                        Master
                    </h5>

                    <small class="text-muted">
                        Add new master or edit existing master
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <input
                    type="hidden"
                    id="masterModalType"
                >

                <input
                    type="hidden"
                    id="masterSelectedId"
                    value=""
                >


                <!-- NAME -->

                <div class="mb-3">

                    <label class="form-label">
                        Master Name
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="masterNameInput"
                        class="form-control"
                        placeholder="Enter master name"
                        autocomplete="off"
                    >

                </div>


                <!-- CODE -->
                
                <div
                    class="mb-3"
                    id="masterCodeWrapper"
                    style="display:none;"
                >

                    <label class="form-label">
                        Code
                    </label>

                    <input
                        type="text"
                        id="masterCodeInput"
                        class="form-control"
                        placeholder="Code will be generated automatically"
                        readonly
                    >

                </div>


                <!-- BUTTONS -->

                <div class="d-flex gap-2 mb-4">

                    <button
                        type="button"
                        class="btn btn-success"
                        id="btnMasterAdd"
                    >
                        <i class="bi bi-plus-lg me-1"></i>
                        Add New
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="btnMasterUpdate"
                        disabled
                    >
                        <i class="bi bi-pencil me-1"></i>
                        Update Selected
                    </button>

                    <button
                        type="button"
                        class="btn btn-light"
                        id="btnMasterClear"
                    >
                        Clear
                    </button>

                </div>


                <!-- LIST -->

                <div class="border rounded">

                    <div class="p-3 border-bottom bg-light">

                        <strong>
                            Existing
                            <span id="masterListTitle">
                                Masters
                            </span>
                        </strong>

                    </div>


                    <div
                        id="masterListLoading"
                        class="text-center p-4"
                        style="display:none;"
                    >

                        <div class="spinner-border text-primary">
                        </div>

                        <div class="mt-2">
                            Loading...
                        </div>

                    </div>


                    <div
                        id="masterListEmpty"
                        class="text-center text-muted p-4"
                        style="display:none;"
                    >
                        No records found.
                    </div>

                    <div class="mb-3">
    <div class="input-group">
        <span class="input-group-text">
            <i class="bi bi-search"></i>
        </span>

        <input
            type="text"
            id="masterSearchInput"
            class="form-control"
            placeholder="Search master..."
            autocomplete="off"
        >

        <button
            type="button"
            class="btn btn-outline-secondary"
            id="btnClearMasterSearch"
            title="Clear search"
        >
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</div>


                    <div
                        id="masterListContainer"
                        style="
                            max-height:350px;
                            overflow-y:auto;
                        "
                    >
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<!-- =========================================================
     USE UPLOADED IMAGE MODAL
========================================================= -->

<div
    class="modal fade"
    id="uploadedImageModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title">
                        <i class="bi bi-images me-2"></i>
                        Use Uploaded Image
                    </h5>

                    <small class="text-muted">
                        Select an uploaded image to use
                        in this design specification.
                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">

                <!-- SEARCH -->

                <div class="mb-3">

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="text"
                            id="uploadedImageSearch"
                            class="form-control"
                            placeholder="Search file name, garment name, garment type or user..."
                            autocomplete="off"
                        >

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            id="btnClearUploadedImageSearch">

                            <i class="bi bi-x-lg"></i>

                        </button>

                    </div>

                </div>


                <!-- LOADING -->

                <div
                    id="uploadedImageLoading"
                    class="text-center py-5"
                    style="display:none;"
                >

                    <div
                        class="spinner-border text-primary">
                    </div>

                    <div class="mt-2 text-muted">
                        Loading uploaded images...
                    </div>

                </div>


                <!-- EMPTY -->

                <div
                    id="uploadedImageEmpty"
                    class="text-center py-5 text-muted"
                    style="display:none;"
                >

                    <i
                        class="bi bi-image"
                        style="font-size:40px;">
                    </i>

                    <div class="mt-2">
                        No uploaded images found.
                    </div>

                </div>


                <!-- TABLE -->

                <div
                    class="table-responsive"
                    id="uploadedImageTableWrapper"
                >

                    <table
                        class="table table-hover align-middle mb-0"
                    >

                        <thead class="table-light">

                            <tr>

                                <th style="width:70px;">
                                    #
                                </th>

                                <th style="width:180px;">
                                    Image
                                </th>

                                <th>
                                    File Name
                                </th>

                                <th>
                                    Garment Name
                                </th>

                                <th>
                                    Garment Type
                                </th>

                                <th>
                                    User
                                </th>

                                <th style="width:100px;">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            id="uploadedImageList"
                        >
                        </tbody>

                    </table>

                </div>

            </div>


            <div class="modal-footer">

                <span
                    class="text-muted me-auto"
                    id="uploadedImageCount">
                </span>

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>

<!-- =========================================================
     SUPPLIER PRODUCT RAW DATA MODAL
========================================================= -->

<div
    class="modal fade"
    id="supplierProductModal"
    tabindex="-1"
    aria-labelledby="supplierProductModalLabel"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-xl modal-dialog-scrollable"
    >

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <h5
                    class="modal-title fw-bold"
                    id="supplierProductModalLabel"
                >
                    <i class="bi bi-box-seam me-2"></i>
                    Supplier Product Raw Data
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <!-- BODY -->

            <div class="modal-body">


                <!-- LOADING -->

                <div
                    id="supplierProductsLoading"
                    class="text-center py-4"
                    style="display:none;"
                >

                    <div
                        class="spinner-border text-primary"
                        role="status"
                    ></div>

                    <div class="mt-2 text-muted">
                        Loading supplier products...
                    </div>

                </div>


                <!-- TABLE -->

                <div
                    class="table-responsive"
                    id="supplierProductsTableWrapper"
                >

                    <table
                        class="table table-bordered table-hover align-middle"
                        id="supplierProductsTable"
                    >

                        <thead class="table-light">

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Supplier
                                </th>

                                <th>
                                    Item Type
                                </th>

                                <th>
                                    Gender
                                </th>

                                <th>
                                    Composition
                                </th>

                                <th>
                                    Colour
                                </th>

                                <th>
                                    Size
                                </th>

                                <th>
                                    Main Image
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            id="supplierProductsTableBody"
                        >

                        </tbody>

                    </table>

                </div>


                <!-- NO DATA -->

                <div
                    id="supplierProductsNoData"
                    class="text-center text-muted py-5"
                    style="display:none;"
                >

                    <i
                        class="bi bi-box-seam fs-1 d-block mb-2"
                    ></i>

                    No supplier products found.

                </div>


            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>

<div id="loader" style="display: none;">

    <img src="http://localhost/ai-product/loading.gif">

</div>



<style>

/* ============================================================
   PAGE
============================================================ */

.design-spec-page {
    max-width: 1600px;
    margin: 0 auto;
}


/* ============================================================
   HEADER
============================================================ */

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.page-title-area {
    display: flex;
    align-items: center;
    gap: 14px;
}

.page-title-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: #eef2ff;
    color: #4f46e5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
}

.page-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
}

.page-subtitle {
    font-size: 13px;
    color: #64748b;
}

.page-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}


/* ============================================================
   CONTEXT
============================================================ */

.context-card {
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
    overflow: hidden;
}

.context-item {
    flex: 0 0 auto;
    min-width: 180px;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-right: 1px solid #e5e7eb;
}

.context-item:last-child {
    border-right: 0;
}

.context-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
}

.context-label {
    display: block;
    font-size: 11px;
    color: #64748b;
    margin-bottom: 2px;
}

.context-item strong {
    color: #0f172a;
    font-size: 14px;
}


/* ============================================================
   CARD
============================================================ */

.design-section-card {
    border: 1px solid #e5e7eb !important;
    border-radius: 12px !important;
    box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
    overflow: hidden;
    background: #fff;
}


/* ============================================================
   SECTION HEADER
============================================================ */

.section-header {
    min-height: 64px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    border-bottom: 1px solid #e5e7eb;
    background: #fff;
}

.section-title-area {
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: #eef2ff;
    color: #4f46e5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.section-title {
    color: #0f172a;
    font-size: 15px;
    font-weight: 700;
}

.section-subtitle {
    color: #64748b;
    font-size: 11px;
}


/* ============================================================
   SECTION BODY
============================================================ */

.section-body {
    padding: 18px;
}


/* ============================================================
   INNER CARD
============================================================ */

.inner-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
}

.inner-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
}

.inner-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
}

.inner-card-header h6 {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
}

.inner-card-header small {
    font-size: 11px;
    color: #64748b;
}


/* ============================================================
   TWO COLUMN FORM
============================================================ */

.form-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px 20px;
    padding: 18px;
}

.form-group {
    min-width: 0;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
}

.required {
    color: #ef4444;
    margin-left: 2px;
}

.form-select,
.form-control {
    min-height: 40px;
    border-color: #d7dee8;
    border-radius: 7px;
    font-size: 13px;
}

.form-select:focus,
.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, .08);
}

.input-with-add {
    display: flex;
    width: 100%;
}

/* ============================================================
   SELECT2 WITH ADD BUTTON
============================================================ */

.input-with-add {
    display: flex;
    width: 100%;
    align-items: stretch;
}


/* Select2 takes remaining width */

.input-with-add .select2-container {
    flex: 1 1 auto;
    width: auto !important;
    min-width: 0;
}


/* Select2 selection */

.input-with-add .select2-container
.select2-selection--single {

    height: 40px;

    border: 1px solid #d7dee8;

    border-radius: 7px 0 0 7px;

    background: #fff;
}


/* Selected text */

.input-with-add
.select2-container
.select2-selection--single
.select2-selection__rendered {

    height: 38px;

    line-height: 38px;

    padding-left: 12px;

    padding-right: 30px;

    color: #334155;

    font-size: 13px;
}


/* Arrow */

.input-with-add
.select2-container
.select2-selection--single
.select2-selection__arrow {

    height: 38px;

    right: 7px;
}


/* Focus */

.input-with-add
.select2-container--focus
.select2-selection--single,

.input-with-add
.select2-container--open
.select2-selection--single {

    border-color: #4f46e5;

    box-shadow:
        0 0 0 3px
        rgba(79, 70, 229, .08);

    outline: none;
}


/* Keep + button correct */

.input-with-add .btn-add-master {

    flex: 0 0 40px;

    width: 40px;

    min-width: 40px;

    height: 40px;

    border: 1px solid #d7dee8;

    border-left: 0;

    border-radius: 0 7px 7px 0;

    background: #f8fafc;

    color: #2563eb;

}


/* Dropdown */

.select2-container .select2-dropdown {

    border-color: #d7dee8;

    border-radius: 7px;

    overflow: hidden;

    box-shadow:
        0 8px 25px
        rgba(15, 23, 42, .12);

}


/* Search box */

.select2-container
.select2-search--dropdown
.select2-search__field {

    border: 1px solid #d7dee8;

    border-radius: 6px;

    padding: 7px 9px;

    font-size: 13px;

}


/* Options */

.select2-container
.select2-results__option {

    padding: 8px 10px;

    font-size: 13px;

}


/* Hover */

.select2-container
.select2-results__option--highlighted {

    background: #4f46e5;

    color: #fff;

}


/* Selected option */

.select2-container
.select2-results__option[aria-selected="true"] {

    background: #eef2ff;

    color: #4338ca;

}


/* ============================================================
   ALL FORM LABELS BOLD
============================================================ */

.design-spec-page label.form-label {

    font-weight: 700;

}


/* AI labels also bold */

.design-spec-page .ai-field label {

    font-weight: 700;

}

.input-with-add .form-select {
    flex: 1;
    min-width: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.btn-add-master {
    width: 40px;
    min-width: 40px;
    border: 1px solid #d7dee8;
    border-left: 0;
    background: #f8fafc;
    color: #2563eb;
    border-radius: 0 7px 7px 0;
    cursor: pointer;
    transition: .15s ease;
}

.btn-add-master:hover {
    background: #eff6ff;
    color: #1d4ed8;
}

.form-help {
    display: block;
    margin-top: 5px;
    color: #64748b;
    font-size: 11px;
}


/* ============================================================
   SELECTED IMAGES
============================================================ */

.selected-images-section {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 15px;
    background: #fafbfc;
}

.selected-images-title {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 12px;
}

.selected-image-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 10px;
}

.selected-image-item {
    position: relative;
    height: 200px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
}

.selected-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-selected-image {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 25px;
    height: 25px;
    border: 0;
    border-radius: 50%;
    background: rgba(220, 38, 38, .9);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}


/* ============================================================
   FORM ACTIONS
============================================================ */

.form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
}


/* ============================================================
   LIST HEADER
============================================================ */

.list-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.search-box {
    position: relative;
    width: 250px;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    z-index: 2;
}

.search-box input {
    padding-left: 35px;
}


/* ============================================================
   SPECIFICATION GRID
============================================================ */

.specification-grid {
    padding: 18px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}


/* ============================================================
   SPECIFICATION CARD
============================================================ */

.specification-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
    transition: .18s ease;
    min-width: 0;
}

.specification-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 6px 20px rgba(15, 23, 42, .07);
    transform: translateY(-1px);
}


/* ============================================================
   PRODUCT IMAGE
============================================================ */

.specification-image-wrapper {
    position: relative;
    width: 100%;
    height: 190px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    overflow: hidden;
}

.specification-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    cursor: pointer;
    transition: transform .2s ease;
    padding: 8px;
}

.specification-image:hover {
    transform: scale(1.04);
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: #94a3b8;
    gap: 6px;
}

.no-image i {
    font-size: 38px;
}

.no-image span {
    font-size: 12px;
}


/* ============================================================
   CARD BODY
============================================================ */

.specification-card-body {
    padding: 14px;
}

.product-barcode {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px;
    border-radius: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-family: monospace;
    font-size: 11px;
    margin-bottom: 12px;
}

.product-sku {
    float: right;
    color: #64748b;
    font-size: 11px;
    padding-top: 5px;
}

.product-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 10px;
    clear: both;
}

.product-info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
}

.product-info-item {
    min-width: 0;
}

.product-info-label {
    display: block;
    font-size: 10px;
    color: #94a3b8;
    margin-bottom: 2px;
}

.product-info-value {
    display: block;
    color: #334155;
    font-size: 12px;
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}


/* ============================================================
   STATUS
============================================================ */

.product-status {
    margin-top: 12px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
}

.status-success {
    background: #dcfce7;
    color: #166534;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}


/* ============================================================
   CARD ACTIONS
============================================================ */

/* =========================================================
   SPECIFICATION ACTION BUTTONS
   View / Edit / Barcode
   ========================================================= */

.specification-actions {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 6px !important;
    width: 100% !important;
    margin-top: 10px;
    box-sizing: border-box;
}


/* All three buttons */
.specification-actions .specification-action-btn {
    width: 100% !important;
    min-width: 0 !important;
    max-width: none !important;

    height: 36px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    padding: 5px 6px !important;
    margin: 0 !important;

    font-size: 12px !important;
    line-height: 1 !important;

    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;

    box-sizing: border-box !important;
}


/* Icons */
.specification-actions .specification-action-btn i {
    margin-right: 5px;
    flex-shrink: 0;
}


/* Remove any old flex sizing */
.specification-actions > * {
    flex: none !important;
}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 576px) {

    .specification-actions {
        grid-template-columns:
            repeat(3, minmax(0, 1fr)) !important;

        gap: 5px !important;
    }

    .specification-actions
    .specification-action-btn {

        height: 34px !important;

        padding-left: 4px !important;
        padding-right: 4px !important;

        font-size: 11px !important;
    }

    .specification-actions
    .specification-action-btn i {

        margin-right: 3px;
    }

}


/* ============================================================
   LOADING
============================================================ */

.specification-loading {
    min-height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    color: #64748b;
    font-size: 13px;
}


/* ============================================================
   EMPTY
============================================================ */

.empty-state {
    min-height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: #64748b;
    text-align: center;
    padding: 30px;
}

.empty-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}

.empty-icon i {
    font-size: 32px;
    color: #94a3b8;
}

.empty-state h6 {
    color: #334155;
    font-weight: 700;
}

.empty-state p {
    font-size: 12px;
    margin-bottom: 0;
}


/* ============================================================
   IMAGE MODAL
============================================================ */

.image-modal-content {
    border: 0;
    border-radius: 12px;
    overflow: hidden;
}

.image-modal-body {
    background: #0f172a;
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.large-design-image {
    max-width: 100%;
    max-height: 75vh;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 6px;
}


/* ============================================================
   PAGINATION
============================================================ */

.specification-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 14px 18px;
    border-top: 1px solid #e5e7eb;
    background: #fff;
    flex-wrap: wrap;
}

.pagination-info {
    color: #64748b;
    font-size: 12px;
}

.pagination-controls {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 14px;
    flex-wrap: wrap;
}

.pagination-per-page {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #64748b;
    font-size: 12px;
}

.pagination-per-page .form-select {
    width: 75px;
    min-height: 34px;
}

.specification-pagination .pagination {
    gap: 3px;
}

.specification-pagination .page-link {
    border-radius: 6px !important;
    min-width: 34px;
    text-align: center;
    color: #475569;
    border-color: #dbe3ec;
}

.specification-pagination .page-item.active .page-link {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #fff;
}

.specification-pagination .page-item.disabled .page-link {
    color: #94a3b8;
    background: #f8fafc;
}

#loader {
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
    z-index:9999;
    display:flex;
    align-items:center;
    justify-content:center;
}


/* ============================================================
   AI CONTENT GENERATOR
============================================================ */

.ai-content-card {
    border: 1px solid #dbe4f0;
    border-radius: 12px;
    background: #ffffff;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
}


/* Header */

.ai-content-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;

    padding: 14px 18px;

    background: linear-gradient(
        135deg,
        #f8faff 0%,
        #f5f3ff 100%
    );

    border-bottom: 1px solid #e2e8f0;
}


.ai-content-title {
    display: flex;
    align-items: center;
    gap: 12px;
}


.ai-icon {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #eef2ff;
    color: #4f46e5;

    font-size: 19px;
}


.ai-content-header h6 {
    margin: 0;

    color: #172033;
    font-size: 14px;
    font-weight: 700;
}


.ai-content-header small {
    color: #64748b;
    font-size: 11px;
}


/* AI Ready */

.ai-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 5px 9px;

    border-radius: 20px;

    background: #ecfdf5;
    color: #047857;

    border: 1px solid #a7f3d0;

    font-size: 10px;
    font-weight: 600;

    white-space: nowrap;
}


.ai-status-dot {
    width: 7px;
    height: 7px;

    border-radius: 50%;

    background: #10b981;
}


/* Body */

.ai-content-body {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 16px 18px;

    padding: 18px;
}


/* Fields */

.ai-field {
    min-width: 0;
}


.ai-field-full {
    grid-column: 1 / -1;
}


.ai-field label {

    display: flex;
    align-items: center;

    gap: 6px;

    margin-bottom: 6px;

    color: #334155;

    font-size: 12px;
    font-weight: 600;
}


.ai-field label i {
    color: #6366f1;
    font-size: 13px;
}


.ai-field .form-control {

    width: 100%;

    min-height: 40px;

    border: 1px solid #d7dee8;

    border-radius: 7px;

    background: #fff;

    color: #334155;

    font-size: 13px;

    transition:
        border-color .15s ease,
        box-shadow .15s ease;
}


.ai-field textarea.form-control {

    resize: vertical;

    min-height: 80px;

}


.ai-field .form-control:focus {

    border-color: #6366f1;

    box-shadow:
        0 0 0 3px
        rgba(99, 102, 241, .08);

    outline: none;
}


/* AI generated value indication */

.ai-field .form-control:not(:placeholder-shown) {

    background: #fcfcff;

}

/* ============================================================
   SAVE / UPDATE CONFIRMATION
============================================================ */

.confirm-product-header {
    display: flex;
    gap: 20px;
    padding: 18px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    margin-bottom: 18px;
}

.confirm-product-image {
    width: 200px;
    height: 250px;
    min-width: 120px;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    border: 1px solid #dbe2ea;

    display: flex;
    align-items: center;
    justify-content: center;
}

.confirm-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.confirm-no-image {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    color: #94a3b8;
    font-size: 12px;
    gap: 5px;
}

.confirm-no-image i {
    font-size: 28px;
}

.confirm-product-main {
    flex: 1;
}

.confirm-item-title span,
.confirm-barcode span {
    display: block;
    color: #64748b;
    font-size: 12px;
    margin-bottom: 3px;
}

.confirm-item-title strong {
    display: block;
    color: #0f172a;
    font-size: 20px;
    margin-bottom: 15px;
}

.confirm-barcode {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 7px;
    background: #eef2ff;
}

.confirm-barcode strong {
    color: #4338ca;
    font-family: monospace;
    font-size: 14px;
}


/* SECTION */

.confirm-section {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 18px;
}

.confirm-section-title {
    display: flex;
    align-items: center;
    gap: 8px;

    padding: 12px 15px;

    background: #f8fafc;

    border-bottom: 1px solid #e2e8f0;

    font-size: 14px;
    font-weight: 700;

    color: #0f172a;
}

.confirm-section-title i {
    color: #2563eb;
}


/* DESIGN INFORMATION */

.confirm-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 1px;

    background: #e2e8f0;
}

.confirm-field {
    background: #fff;
    padding: 12px 14px;
}

.confirm-field span {
    display: block;

    font-size: 11px;

    color: #64748b;

    margin-bottom: 4px;
}

.confirm-field strong {
    display: block;

    font-size: 13px;

    color: #0f172a;

    word-break: break-word;
}

.confirm-field-wide {
    grid-column: 1 / -1;
}


/* AI */

.confirm-ai-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 12px;

    padding: 14px;
}

.confirm-ai-field {
    border: 1px solid #e2e8f0;

    border-radius: 8px;

    padding: 11px;

    background: #fff;
}

.confirm-ai-field span {
    display: block;

    color: #64748b;

    font-size: 11px;

    margin-bottom: 5px;

    font-weight: 600;
}

.confirm-ai-field div {
    color: #0f172a;

    font-size: 13px;

    line-height: 1.5;

    white-space: pre-wrap;

    word-break: break-word;
}


/* IMAGES */

.confirm-images-grid {
    display: grid;

    grid-template-columns:
        repeat(6, minmax(0, 1fr));

    gap: 10px;

    padding: 14px;
}

.confirm-image-item {
    height: 250px;

    border: 1px solid #e2e8f0;

    border-radius: 8px;

    overflow: hidden;

    background: #fff;
}

.confirm-image-item img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}


@media (max-width: 768px) {

    .confirm-product-header {
        flex-direction: column;
    }

    .confirm-grid {
        grid-template-columns: 1fr;
    }

    .confirm-field-wide {
        grid-column: auto;
    }

    .confirm-ai-grid {
        grid-template-columns: 1fr;
    }

    .confirm-images-grid {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }

}

/* =========================================================
   DESIGN SPECIFICATION VIEW MODAL
========================================================= */

.specification-view-modal {
    border: 0;
    border-radius: 18px;
    overflow: hidden;
}


.specification-view-modal .modal-header {
    padding: 18px 22px;
    border-bottom: 1px solid #e8edf5;
    background: #ffffff;
}


.view-modal-icon {
    width: 42px;
    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 12px;

    background: #eef4ff;
    color: #2563eb;

    font-size: 19px;
}


.specification-view-modal .modal-body {
    padding: 22px;
    background: #f7f9fc;
}


/* Product top */

.view-product-top {
    display: grid;

    grid-template-columns:
        minmax(180px, 280px)
        1fr;

    gap: 24px;

    margin-bottom: 20px;
}


.view-product-image {
    min-height: 240px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #e0e7f0;
    border-radius: 14px;

    background: #ffffff;

    overflow: hidden;
}


.view-product-image img {
    width: 100%;
    height: 260px;

    object-fit: contain;

    cursor: pointer;

    transition:
        transform .2s ease;
}


.view-product-image img:hover {
    transform: scale(1.03);
}


.view-product-summary {
    display: flex;
    flex-direction: column;
    justify-content: center;
}


.view-barcode-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}


.view-barcode-badge {
    display: inline-flex;

    align-items: center;
    gap: 6px;

    padding: 7px 10px;

    border: 1px solid #dce5f2;
    border-radius: 8px;

    background: #ffffff;

    font-size: 12px;
    font-weight: 600;

    color: #334155;
}


.view-sku {
    font-size: 13px;
    color: #64748b;
}


.view-product-name {
    margin-top: 18px;
    margin-bottom: 0;

    font-size: 25px;
    font-weight: 700;

    color: #0f172a;
}


/* Sections */

.view-section {
    margin-top: 18px;

    padding: 18px;

    border: 1px solid #e1e8f0;

    border-radius: 14px;

    background: #ffffff;
}


.view-section-header {
    display: flex;

    align-items: center;

    gap: 11px;

    margin-bottom: 18px;

    padding-bottom: 13px;

    border-bottom: 1px solid #edf1f6;
}


.view-section-header > i {
    width: 34px;
    height: 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #eef4ff;
    color: #2563eb;
}


.view-section-header h6 {
    margin: 0;

    font-size: 15px;
    font-weight: 700;

    color: #0f172a;
}


.view-section-header small {
    display: block;

    margin-top: 2px;

    color: #64748b;

    font-size: 11px;
}


/* Information grid */

.view-info-grid {
    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 12px;
}


.view-info-box {
    padding: 12px 14px;

    border: 1px solid #edf1f5;

    border-radius: 10px;

    background: #fafbfd;

    min-width: 0;
}


.view-info-box-wide {
    grid-column: span 2;
}


.view-info-box span,
.view-context-box span,
.view-ai-box span {
    display: block;

    margin-bottom: 5px;

    font-size: 11px;

    font-weight: 500;

    color: #64748b;
}


.view-info-box strong,
.view-context-box strong {
    display: block;

    font-size: 13px;

    font-weight: 600;

    color: #172033;

    word-break: break-word;
}


/* Context */

.view-context-grid {
    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 12px;
}


.view-context-box {
    padding: 14px;

    border-radius: 10px;

    background: #f8fafc;

    border: 1px solid #edf1f5;
}


/* AI */

.view-ai-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 12px;
}


.view-ai-box {
    padding: 14px;

    border: 1px solid #edf1f5;

    border-radius: 10px;

    background: #fafbfd;

    min-width: 0;
}


.view-ai-wide {
    grid-column: span 2;
}


.view-ai-box div {
    font-size: 13px;

    line-height: 1.65;

    color: #1e293b;

    white-space: pre-wrap;

    word-break: break-word;
}


/* Mobile */

@media (max-width: 991.98px) {

    .view-product-top {
        grid-template-columns: 1fr;
    }


    .view-info-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }


    .view-context-grid {
        grid-template-columns:
            1fr;
    }

}


@media (max-width: 575.98px) {

    .specification-view-modal .modal-body {
        padding: 14px;
    }


    .view-section {
        padding: 14px;
    }


    .view-info-grid,
    .view-ai-grid {
        grid-template-columns: 1fr;
    }


    .view-info-box-wide,
    .view-ai-wide {
        grid-column: auto;
    }


    .view-product-name {
        font-size: 20px;
    }


    .view-product-image {
        min-height: 200px;
    }


    .view-product-image img {
        height: 220px;
    }

}


/* ============================================================
   GENERATE BUTTON
============================================================ */

#generateBtn {

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    min-height: 38px;

    padding: 0 16px;

    border: 1px solid #6366f1;

    border-radius: 7px;

    background: #6366f1;

    color: #fff;

    font-size: 12px;

    font-weight: 600;

    cursor: pointer;

    transition: .15s ease;
}


#generateBtn:hover {

    background: #4f46e5;

    border-color: #4f46e5;

    transform: translateY(-1px);

}


#generateBtn:active {

    transform: translateY(0);

}


#generateBtn:disabled {

    opacity: .65;

    cursor: not-allowed;

    transform: none;
}


/* ============================================================
   MOBILE
============================================================ */

@media (max-width: 767.98px) {

    .ai-content-header {

        align-items: flex-start;

        flex-direction: column;

    }


    .ai-status {

        align-self: flex-start;

    }


    .ai-content-body {

        grid-template-columns: 1fr;

        gap: 14px;

        padding: 14px;

    }


    .ai-field-full {

        grid-column: auto;

    }

}

.edit-barcode-badge {
    display: inline-flex;
    align-items: center;

    padding: 5px 9px;

    border-radius: 7px;

    background: #eef4ff;
    border: 1px solid #cbdcf8;

    color: #2563eb;

    font-size: 11px;
    font-weight: 600;
}

.edit-barcode-badge strong {
    margin-left: 3px;
}


@media (max-width: 575.98px) {

    .ai-content-header {

        padding: 12px;

    }


    .ai-content-body {

        padding: 12px;

    }


    .ai-content-title {

        align-items: flex-start;

    }


    .ai-icon {

        flex: 0 0 36px;

    }

}


/* ============================================================
   RESPONSIVE
============================================================ */

@media (max-width: 1199.98px) {

    .specification-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .selected-image-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

}


@media (max-width: 991.98px) {

    .page-header {
        align-items: flex-start;
    }

    .context-item {
        min-width: 150px;
    }

    .search-box {
        width: 220px;
    }

}


@media (max-width: 767.98px) {

    .page-header {
        flex-direction: column;
    }

    .page-title-area {
        width: 100%;
    }

    .page-actions {
        width: 100%;
    }

    .page-actions .btn {
        flex: 1;
    }


    .context-card {
        display: grid;
        grid-template-columns: 1fr;
    }

    .context-item {
        border-right: 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .context-item:last-child {
        border-bottom: 0;
    }


    /* EXACTLY ONE COLUMN ON MOBILE */
    .form-grid-2 {
        grid-template-columns: 1fr;
    }


    .section-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .list-header-actions {
        width: 100%;
    }

    .search-box {
        width: 100%;
        flex: 1;
    }

    .specification-pagination {
        align-items: stretch;
        flex-direction: column;
    }

    .pagination-info {
        width: 100%;
        text-align: center;
    }

    .pagination-controls {
        width: 100%;
        justify-content: center;
    }


    .specification-grid {
        grid-template-columns: 1fr;
        padding: 12px;
    }


    .selected-image-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }


    .form-actions {
        flex-direction: column-reverse;
    }

    .form-actions .btn {
        width: 100%;
    }

}


@media (max-width: 575.98px) {

    .design-spec-page {
        padding-left: 8px;
        padding-right: 8px;
    }

    .page-title {
        font-size: 20px;
    }

    .page-subtitle {
        font-size: 11px;
    }

    .page-actions {
        flex-direction: column;
    }

    .page-actions .btn {
        width: 100%;
    }

    .section-body {
        padding: 10px;
    }

    .form-grid-2 {
        padding: 12px;
        gap: 14px;
    }

    .specification-image-wrapper {
        height: 210px;
    }

}

/* ============================================================
   BARCODE PRINT MODAL
============================================================ */

.barcode-print-modal {
    border: 0;
    border-radius: 16px;
    overflow: hidden;
}


.barcode-modal-header {
    background: linear-gradient(
        135deg,
        #0f172a,
        #2563eb
    );

    color: #fff;

    border-bottom: 0;
}


.barcode-modal-header .btn-close {
    filter: brightness(0) invert(1);
}


.barcode-modal-header h5 {
    font-weight: 700;
}


.barcode-modal-header small {
    opacity: .8;
}


/* ============================================================
   PRODUCT SECTION
============================================================ */

.barcode-product-section {
    display: grid;

    grid-template-columns:
        180px
        minmax(0, 1fr);

    gap: 20px;

    padding: 18px;

    background: #f8fafc;

    border: 1px solid #e2e8f0;

    border-radius: 12px;
}


.barcode-product-image-box {
    width: 180px;
    height: 180px;

    border-radius: 12px;

    overflow: hidden;

    background: #fff;

    border: 1px solid #e2e8f0;

    display: flex;

    align-items: center;

    justify-content: center;
}


.barcode-product-image-box img {
    width: 100%;
    height: 100%;

    object-fit: contain;

    display: none;
}


.barcode-no-image {
    width: 100%;
    height: 100%;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    color: #94a3b8;

    gap: 6px;
}


.barcode-no-image i {
    font-size: 36px;
}


.barcode-product-info {
    min-width: 0;
}


.barcode-info-title {
    font-size: 14px;

    font-weight: 700;

    color: #0f172a;

    margin-bottom: 14px;
}


.barcode-info-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 10px;
}


.barcode-info-item {
    background: #fff;

    border: 1px solid #e2e8f0;

    border-radius: 8px;

    padding: 10px;

    min-width: 0;
}


.barcode-info-item span {
    display: block;

    font-size: 11px;

    color: #64748b;

    margin-bottom: 3px;
}


.barcode-info-item strong {
    display: block;

    font-size: 13px;

    color: #0f172a;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/* ============================================================
   BARCODE NUMBER
============================================================ */

.barcode-number-box {
    margin-top: 12px;

    padding: 11px 14px;

    background: #fff;

    border: 1px solid #cbd5e1;

    border-radius: 8px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;
}


.barcode-number-box span {
    color: #64748b;

    font-size: 12px;

    font-weight: 600;
}


.barcode-number-box strong {
    color: #0f172a;

    font-family: monospace;

    font-size: 15px;

    letter-spacing: .5px;

    word-break: break-all;

    text-align: right;
}


/* ============================================================
   BARCODE PREVIEW
============================================================ */

.barcode-preview-section {
    margin-top: 18px;

    border: 1px solid #e2e8f0;

    border-radius: 12px;

    overflow: hidden;
}


.barcode-section-title {
    padding: 11px 14px;

    background: #f8fafc;

    border-bottom: 1px solid #e2e8f0;

    font-weight: 700;

    font-size: 13px;

    color: #0f172a;
}


.barcode-section-title i {
    margin-right: 6px;
}


.barcode-preview-box {
    min-height: 145px;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 20px;

    background: #fff;

    overflow-x: auto;
}


#singleBarcodePreview {
    max-width: 100%;

    height: auto;
}


/* ============================================================
   PRINT SETTINGS
============================================================ */

.barcode-print-settings {
    margin-top: 18px;

    border: 1px solid #e2e8f0;

    border-radius: 12px;

    overflow: hidden;
}


.barcode-input-label {
    display: block;

    font-size: 13px;

    font-weight: 700;

    color: #0f172a;

    margin-bottom: 7px;
}


#txt_howmanybarcode,
#txt_startbox {
    height: 44px;

    font-size: 14px;
}


.barcode-modal-footer {
    border-top: 1px solid #e2e8f0;

    background: #fff;

    padding: 14px 18px;
}


/* ============================================================
   MOBILE
============================================================ */

@media (max-width: 767.98px) {

    .barcode-product-section {
        grid-template-columns: 1fr;
    }


    .barcode-product-image-box {
        width: 150px;
        height: 150px;

        margin: 0 auto;
    }


    .barcode-info-grid {
        grid-template-columns: 1fr;
    }


    .barcode-number-box {
        flex-direction: column;

        align-items: flex-start;
    }


    .barcode-number-box strong {
        text-align: left;
    }


    .barcode-modal-footer {
        flex-direction: column-reverse;
    }


    .barcode-modal-footer button {
        width: 100%;
    }

}

/* ============================================================
   BARCODE A4 PREVIEW
============================================================ */

.barcode-preview-page-container {
    background: #e5e7eb;

    overflow: auto;

    padding: 30px;

    display: flex;

    justify-content: center;

    align-items: flex-start;
}


/*
|--------------------------------------------------------------------------
| A4 PAGE
|--------------------------------------------------------------------------
*/

/* ============================================================
   BARCODE PREVIEW WRAPPER
============================================================ */

.barcode-pages-container {

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 25px;

    width: 100%;

}


/* ============================================================
   A4 PAGE
============================================================ */



.barcode-a4-page {
    width: 210mm;
    height: 297mm;

    min-width: 210mm;
    min-height: 297mm;

    background: #fff;
    position: relative;
    box-sizing: border-box;

    /*
     * Physical barcode sheet
     * 3 columns × 8 rows = 24 boxes
     */
    padding: 15mm 8mm 10mm 8mm;

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    grid-template-rows:
        repeat(8, 1fr);

    column-gap: 8mm;

    /*
     * Keep row spacing controlled
     */
    row-gap: 2mm;

    flex-shrink: 0;
}


/* ============================================================
   BARCODE BOX
============================================================ */

.barcode-box {

    width: 100%;

    height: 100%;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: flex-start;

    text-align: center;

    box-sizing: border-box;

    overflow: hidden;

    padding-top: 2mm;

}


.barcode-item-name {

    font-size: 8px;

    line-height: 10px;

    font-weight: 700;

    white-space: nowrap;

    max-width: 100%;

    overflow: hidden;

    text-overflow: ellipsis;

}


.barcode-item-detail {
    font-size: 8px;
    line-height: 10px;
    font-weight: 700;
    white-space: nowrap;
    margin-bottom: 1mm;
}


.barcode-box svg {

    width: 48mm;

    max-width: 100%;

    height: 18mm;

    display: block;

}


/*
|--------------------------------------------------------------------------
| BARCODE BOX
|--------------------------------------------------------------------------
*/




/*
|--------------------------------------------------------------------------
| PRODUCT INFORMATION
|--------------------------------------------------------------------------
*/

.barcode-box .barcode-item-name {

    font-size: 8px;

    line-height: 10px;

    font-weight: 500;

    white-space: nowrap;

    max-width: 100%;

    overflow: hidden;

    text-overflow: ellipsis;

}





/*
|--------------------------------------------------------------------------
| SVG BARCODE
|--------------------------------------------------------------------------
*/

.barcode-box svg {

    width: 48mm;

    max-width: 100%;

    height: 18mm;

    display: block;

}


/*
|--------------------------------------------------------------------------
| RESPONSIVE SCREEN PREVIEW
|--------------------------------------------------------------------------
*/

@media (max-width: 900px) {

    .barcode-preview-page-container {

        justify-content: flex-start;

        overflow-x: auto;

    }

}


/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

@media print {

    body * {

        visibility: hidden !important;

    }


    #barcodePdfContent_X,

    #barcodePdfContent_X * {

        visibility: visible !important;

    }


    #barcodePdfContent_X {

        position: absolute;

        left: 0;

        top: 0;

        margin: 0;

        box-shadow: none;

    }

}

.focus-after-validation {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.20) !important;
}

/* =========================================================
   BARCODE SELECTION AREA
   ========================================================= */

   .barcode-selection-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    padding: 8px 0 2px;
    border-top: 1px solid #e5e7eb;
}

   /* Barcode quantity */
.barcode-qty-wrapper {
    width: 90px;
    flex: 0 0 90px;
}

.barcode-qty-label {
    display: block;
    margin: 0 0 5px 0;
    font-size: 11px;
    line-height: 14px;
    font-weight: 600;
    color: #64748b;
}

.barcode-qty-input {
    display: block;
    width: 100%;
    height: 34px;
    padding: 5px 8px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #fff;
    color: #1e293b;
    font-size: 13px;
    text-align: center;
    box-sizing: border-box;
}

.barcode-qty-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.10);
}


/* Checkbox area */
.barcode-check-wrapper {
    flex: 1;
    min-width: 0;
    display: flex !important;
    align-items: center !important;
}

.barcode-check-label {
    display: inline-flex !important;
    align-items: center !important;
    gap: 7px !important;

    width: auto !important;
    min-width: 0 !important;

    margin: 0 !important;
    padding: 0 !important;

    cursor: pointer;
    white-space: nowrap;

    font-size: 12px;
    line-height: 18px;
    font-weight: 600;
    color: #334155;
}


.barcode-select-checkbox {
    appearance: auto !important;
    -webkit-appearance: checkbox !important;

    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;

    position: static !important;

    width: 18px !important;
    min-width: 18px !important;
    max-width: 18px !important;

    height: 18px !important;
    min-height: 18px !important;
    max-height: 18px !important;

    margin: 0 !important;
    padding: 0 !important;

    flex: 0 0 18px !important;

    cursor: pointer;

    accent-color: #2563eb;
}

/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 576px) {

    .barcode-selection-row {
        gap: 10px;
    }

    .barcode-qty-wrapper {
        width: 80px;
        flex-basis: 80px;
    }

    .barcode-check-label {
        font-size: 11px;
    }

}


/* ============================================================
   PREVENT HORIZONTAL OVERFLOW
============================================================ */

.design-spec-page,
.design-spec-page * {
    max-width: 100%;
}

/* =========================================================
   SELECTED PRODUCTS CONTAINER
========================================================= */

.selected-barcode-products-container {
    padding: 12px;

    background: #f8fafc;

    border-left: 1px solid #dbe3ef;
    border-right: 1px solid #dbe3ef;
    border-bottom: 1px solid #dbe3ef;

    border-radius: 0 0 8px 8px;

    max-height: 420px;

    overflow-y: auto;
}


/* =========================================================
   EMPTY PRODUCT MESSAGE
========================================================= */

.selected-barcode-empty {

    min-height: 140px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 8px;

    color: #94a3b8;

    text-align: center;

}


.selected-barcode-empty i {

    font-size: 35px;

}


.selected-barcode-empty div {

    font-size: 13px;

}


/* =========================================================
   PRODUCT CARD
========================================================= */

.selected-barcode-product {

    display: grid;

    grid-template-columns:
        100px
        minmax(0, 1fr)
        85px;

    gap: 15px;

    align-items: center;

    padding: 12px;

    margin-bottom: 10px;

    background: #ffffff;

    border: 1px solid #dbe3ef;

    border-radius: 8px;

}


.selected-barcode-product:last-child {

    margin-bottom: 0;

}


/* =========================================================
   PRODUCT IMAGE
========================================================= */

.selected-barcode-product-image {

    width: 90px;

    height: 90px;

    object-fit: contain;

    display: block;

    margin: auto;

    background: #f8fafc;

    border: 1px solid #e2e8f0;

    border-radius: 7px;

}


.selected-barcode-no-image {

    width: 90px;

    height: 90px;

    display: flex;

    align-items: center;

    justify-content: center;

    margin: auto;

    background: #f8fafc;

    border: 1px solid #e2e8f0;

    border-radius: 7px;

    color: #94a3b8;

    font-size: 11px;

    text-align: center;

}


/* =========================================================
   PRODUCT INFORMATION
========================================================= */

.selected-barcode-product-info {

    min-width: 0;

}


.selected-barcode-product-name {

    margin-bottom: 8px;

    color: #0f172a;

    font-size: 15px;

    font-weight: 700;

    word-break: break-word;

}


.selected-barcode-info-grid {

    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 7px;

}


.selected-barcode-info-item {

    padding: 7px 8px;

    background: #f8fafc;

    border: 1px solid #e2e8f0;

    border-radius: 6px;

    min-width: 0;

}


.selected-barcode-info-label {

    display: block;

    margin-bottom: 2px;

    color: #64748b;

    font-size: 10px;

    font-weight: 600;

}


.selected-barcode-info-value {

    display: block;

    color: #0f172a;

    font-size: 12px;

    font-weight: 600;

    word-break: break-word;

}


/* =========================================================
   BARCODE VALUE
========================================================= */

.selected-barcode-code {

    margin-top: 7px;

    padding: 5px 8px;

    background: #ffffff;

    border: 1px dashed #cbd5e1;

    border-radius: 5px;

    color: #334155;

    font-size: 11px;

    word-break: break-all;

}


/* =========================================================
   QUANTITY
========================================================= */

.selected-barcode-qty {

    padding: 10px 6px;

    text-align: center;

    background: #eff6ff;

    border: 1px solid #bfdbfe;

    border-radius: 8px;

}


.selected-barcode-qty-label {

    display: block;

    margin-bottom: 4px;

    color: #475569;

    font-size: 10px;

    font-weight: 600;

}


.selected-barcode-qty-value {

    display: block;

    color: #1d4ed8;

    font-size: 23px;

    font-weight: 700;

}


/* =========================================================
   BARCODE PREVIEW
========================================================= */

.barcode-preview-box {

    min-height: 180px;

    padding: 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #f8fafc;

    border: 1px solid #dbe3ef;

    border-radius: 0 0 8px 8px;

    overflow-x: auto;

}


.barcode-preview-empty {

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 8px;

    color: #94a3b8;

    font-size: 13px;

}


.barcode-preview-empty i {

    font-size: 35px;

}


/* =========================================================
   PRINT SUMMARY
========================================================= */

.barcode-print-summary {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 10px;

    margin-top: 15px;

}


.barcode-summary-item {

    padding: 10px 12px;

    background: #f8fafc;

    border: 1px solid #dbe3ef;

    border-radius: 7px;

}


.barcode-summary-item span {

    display: block;

    margin-bottom: 3px;

    color: #64748b;

    font-size: 11px;

}


.barcode-summary-item strong {

    color: #0f172a;

    font-size: 16px;

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 768px) {

    .selected-barcode-product {

        grid-template-columns:
            75px
            minmax(0, 1fr);

        gap: 10px;

    }


    .selected-barcode-product-image,
    .selected-barcode-no-image {

        width: 70px;

        height: 70px;

    }


    .selected-barcode-qty {

        grid-column: 1 / -1;

        width: 100%;

    }


    .selected-barcode-info-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

    }


    .barcode-print-summary {

        grid-template-columns: 1fr;

    }

}


/* =========================================================
   DESIGN SUB IMAGES PREVIEW
   ========================================================= */

#subImagesPreview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.sub-image-preview-card {
    position: relative;
    width: 100%;
    min-width: 0;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    transition: all 0.2s ease;
}

.sub-image-preview-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 14px rgba(0, 0, 0, 0.12);
}

.sub-image-preview-image-wrapper {
    width: 100%;
    height: 170px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.sub-image-preview-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.sub-image-preview-info {
    padding: 9px 10px;
    background: #fff;
}

.sub-image-preview-name {
    font-size: 13px;
    color: #495057;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sub-image-remove {
    position: absolute;
    top: 8px;
    right: 8px;

    width: 30px;
    height: 30px;

    border: none;
    border-radius: 50%;

    background: rgba(220, 53, 69, 0.95);
    color: #fff;

    display: flex;
    align-items: center;
    justify-content: center;

    cursor: pointer;
    z-index: 5;

    font-size: 14px;
    line-height: 1;

    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.sub-image-remove:hover {
    background: #dc3545;
    transform: scale(1.05);
}

.sub-images-empty {
    width: 100%;
    padding: 25px;
    border: 1px dashed #ced4da;
    border-radius: 10px;
    text-align: center;
    color: #6c757d;
    background: #f8f9fa;
}

/* Tablet */
@media (max-width: 768px) {

    #subImagesPreview {
        grid-template-columns:
            repeat(auto-fill, minmax(130px, 1fr));

        gap: 12px;
    }

    .sub-image-preview-image-wrapper {
        height: 150px;
    }

}

/* Mobile */
@media (max-width: 480px) {

    #subImagesPreview {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 10px;
    }

    .sub-image-preview-image-wrapper {
        height: 135px;
    }

    .sub-image-preview-name {
        font-size: 12px;
    }

    .sub-image-remove {
        width: 27px;
        height: 27px;
        top: 6px;
        right: 6px;
    }

}


@media (max-width: 480px) {

    .selected-barcode-product-name {

        font-size: 13px;

    }


    .selected-barcode-info-value {

        font-size: 11px;

    }


    .selected-barcode-products-container {

        padding: 8px;

    }

}

/* =========================================================
   VIEW DESIGN SUB IMAGES
========================================================= */

.view-sub-images-grid {
    display: grid;
    grid-template-columns:
        repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
    margin-top: 15px;
}

.view-sub-image-card {
    position: relative;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
    transition: .2s ease;
}

.view-sub-image-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,.12);
}

.view-sub-image-wrapper {
    width: 100%;
    height: 180px;
    background: #f8f9fa;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;
}

.view-sub-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    cursor: pointer;
}

.view-sub-image-name {
    padding: 8px 10px;

    font-size: 12px;
    color: #6c757d;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

@media (max-width: 768px) {

    .view-sub-images-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 10px;
    }

    .view-sub-image-wrapper {
        height: 150px;
    }
}

@media (max-width: 420px) {

    .view-sub-images-grid {
        grid-template-columns: 1fr;
    }

    .view-sub-image-wrapper {
        height: 220px;
    }
}

/* =========================================================
   DESIGN SUB IMAGE PREVIEW
   ========================================================= */

#subImagesPreview {
    display: flex;
    flex-wrap: wrap;
}

.sub-image-preview-card {
    position: relative;
    width: 100%;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.sub-image-preview-wrapper {
    position: relative;
    width: 100%;
    height: 180px;
    background: #f5f6f8;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.sub-image-preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.sub-image-preview-name {
    padding: 8px 10px;
    font-size: 12px;
    color: #495057;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sub-image-existing-badge,
.sub-image-new-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    z-index: 5;
    padding: 3px 8px;
    border-radius: 5px;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
}

.sub-image-existing-badge {
    background: #198754;
}

.sub-image-new-badge {
    background: #0d6efd;
}

.sub-image-remove-btn {
    position: absolute;
    top: 7px;
    right: 7px;
    width: 30px;
    height: 30px;
    border: none;
    border-radius: 50%;
    background: #dc3545;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
}

.sub-image-remove-btn:hover {
    opacity: 0.85;
}

.sub-image-error {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    gap: 6px;
}

.sub-image-error i {
    font-size: 35px;
}

.sub-image-error span {
    font-size: 12px;
}

body {
    overflow-x: hidden;
}

/* ============================================================
   SELECTED SUPPLIER PRODUCT
============================================================ */

.selected-supplier-product-section {
    border: 1px solid #dbe4f0;
    border-radius: 12px;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
}

.selected-supplier-product-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 14px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.selected-supplier-product-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.selected-supplier-product-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.selected-supplier-product-header h6 {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: #172033;
}

.selected-supplier-product-header small {
    color: #64748b;
    font-size: 11px;
}

.selected-supplier-product-body {
    display: grid;
    grid-template-columns: 280px minmax(0, 1fr);
    gap: 20px;
    padding: 18px;
}

.selected-supplier-main-image-wrapper {
    min-width: 0;
}

.selected-supplier-main-image {
    width: 100%;
    height: 360px;
    border: 1px solid #dbe2ea;
    border-radius: 10px;
    background: #f8fafc;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.selected-supplier-main-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 8px;
}

.selected-supplier-no-image {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    color: #94a3b8;
    font-size: 12px;
}

.selected-supplier-no-image i {
    font-size: 40px;
}

.selected-supplier-details {
    min-width: 0;
}

.selected-supplier-details-title {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 10px;
}

.selected-supplier-details table {
    margin-bottom: 0;
}

.selected-supplier-details table td {
    padding: 9px 12px;
    vertical-align: top;
    font-size: 12px;
}

.selected-supplier-details table td:first-child {
    width: 190px;
    color: #64748b;
    font-weight: 600;
    background: #f8fafc;
}

.selected-supplier-details table td:last-child {
    color: #1e293b;
    font-weight: 500;
    word-break: break-word;
}

@media (max-width: 768px) {

    .selected-supplier-product-body {
        grid-template-columns: 1fr;
    }

    .selected-supplier-main-image {
        height: 300px;
    }

}


</style>
<script>

    let existingImages = [];
  
    let selectedBarcodeSpecifications = [];

    let existingSubImages = [];
    let selectedSupplierProduct = null;
    /*
    |--------------------------------------------------------------------------
    | GET COLLECTION VALUE
    |--------------------------------------------------------------------------
    | IMPORTANT:
    | In the Blade form, the field displayed with the label
    | "Collection" actually has id="cmbclient".
    | Therefore validation must use cmbclient.
    |--------------------------------------------------------------------------
    */

    function getCollectionField() {

        const ids = [
            /*
            | The form field shown as "Collection" actually uses
            | id="cmbclient" in the Blade code.
            */
            'cmbclient',

            'collection',
            'cmbcollection',
            'cmbCollection',
            'collection_id',
            'collectionId'
        ];

        for (const id of ids) {

            const element =
                document.getElementById(id);

            if (element) {
                return element;
            }

        }

        return null;
    }

    /* ============================================================
    GLOBAL ALERT -> SWEETALERT2
    ============================================================ */

    /*
    ================================================================
    GLOBAL ALERT -> SWEETALERT2
    ================================================================
    All existing alert(...) calls in this file continue to work,
    but are displayed through SweetAlert2.
    */

    /*
    |--------------------------------------------------------------------------
    | GLOBAL ALERT -> SWEETALERT2
    |--------------------------------------------------------------------------
    | Always override the browser's native alert().
    |--------------------------------------------------------------------------
    */

    window.showSpecificationAlert =
        function (
            message,
            icon = 'warning',
            title = 'Please check'
        ) {

            if (typeof Swal === 'undefined') {

                console.error(
                    'SweetAlert2 is not loaded:',
                    message
                );

                return Promise.resolve();

            }

            return Swal.fire({
                icon: icon,
                title: title,
                text: String(message || ''),
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb'
            });

        };

    window.alert =
        function (message) {

            return window.showSpecificationAlert(
                message
            );

        };

    /*
    |--------------------------------------------------------------------------
    | SWEETALERT2 + FOCUS FIELD AFTER OK
    |--------------------------------------------------------------------------
    | After the user clicks OK, focus the field that needs attention.
    | For Select2 fields, focus the Select2 selection box when available.
    |--------------------------------------------------------------------------
    */

    function showSpecificationAlertAndFocus(message, field) {

    return Swal.fire({
        icon: 'warning',
        title: 'Please check',
        text: String(message || ''),
        confirmButtonText: 'OK',
        confirmButtonColor: '#2563eb',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then(function () {

        if (!field) {
            return;
        }

        let element = field;

        /*
        |--------------------------------------------------------------------------
        | GET ACTUAL ELEMENT
        |--------------------------------------------------------------------------
        */

        if (typeof field === 'string') {

            element =
                document.getElementById(field) ||
                document.querySelector(field);

        }

        if (!element) {

            console.warn(
                'Focus field not found:',
                field
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | SELECT2 FIELD
        |--------------------------------------------------------------------------
        */

        if (
            typeof window.jQuery !== 'undefined' &&
            window.jQuery.fn &&
            window.jQuery.fn.select2 &&
            window.jQuery(element).hasClass(
                'select2-hidden-accessible'
            )
        ) {

            const $select =
                window.jQuery(element);

            const $select2Container =
                $select
                    .next('.select2');

            const $selection =
                $select2Container
                    .find('.select2-selection');


            /*
            |--------------------------------------------------------------
            | Scroll to the field
            |--------------------------------------------------------------
            */

            if ($select2Container.length) {

                $select2Container[0]
                    .scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

            }


            /*
            |--------------------------------------------------------------
            | Focus visible Select2 control
            |--------------------------------------------------------------
            */

            setTimeout(function () {

                if ($selection.length) {

                    $selection
                        .trigger('focus');

                    /*
                    | Add visual focus
                    */

                    $selection
                        .addClass(
                            'focus-after-validation'
                        );

                    setTimeout(function () {

                        $selection
                            .removeClass(
                                'focus-after-validation'
                            );

                    }, 2000);

                }

            }, 300);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | NORMAL INPUT / TEXTAREA
        |--------------------------------------------------------------------------
        */

        setTimeout(function () {

            try {

                element
                    .scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                element.focus({
                    preventScroll: true
                });

                /*
                | Select text if possible
                */

                if (
                    typeof element.select ===
                    'function' &&
                    (
                        element.tagName ===
                        'INPUT' ||
                        element.tagName ===
                        'TEXTAREA'
                    )
                ) {

                    element.select();

                }

            } catch (error) {

                console.warn(
                    'Could not focus field:',
                    error
                );

            }

        }, 300);

    });
    }


        /*
        * ============================================================
        * EXTRACT AI RESPONSE SECTION
        * ============================================================
        *
        * This function MUST be outside the DOMContentLoaded blocks
        * because the Generate button uses it.
        */

        function extractSection(text, startLabel, endLabel = '') {

            if (!text) {
                return '';
            }

            text = String(text);

            const startIndex =
                text.toLowerCase().indexOf(
                    startLabel.toLowerCase()
                );

            if (startIndex === -1) {
                return '';
            }

            let start =
                startIndex + startLabel.length;

            let end = text.length;

            if (endLabel) {

                const endIndex =
                    text.toLowerCase().indexOf(
                        endLabel.toLowerCase(),
                        start
                    );

                if (endIndex !== -1) {
                    end = endIndex;
                }
            }

            let value =
                text.substring(start, end);

            return value
                .replace(/^[:\-–—]\s*/, '')
                .trim();
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE / UPDATE DESIGN SPECIFICATION
        |--------------------------------------------------------------------------
        |
        | NEW PRODUCT:
        |   POST -> design-specifications.store
        |   Laravel generates a NEW barcode.
        |
        | EDIT PRODUCT:
        |   POST + _method=PUT -> design-specifications/{id}
        |   Laravel keeps the EXISTING barcode.
        |
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | SAVE / UPDATE BUTTON CLICK
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Check the element that was ACTUALLY clicked.
        |
        | Do NOT use document.getElementById() as the click test.
        | Otherwise every click on the page, including SweetAlert OK,
        | can execute this validation again.
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            function (event) {

                const saveButton =
                    event.target.closest(
                        '#btnSaveSpecification'
                    );

                if (!saveButton) {
                    return;
                }

                event.preventDefault();


                /*
                |--------------------------------------------------------------------------
                | EDIT INFORMATION
                |--------------------------------------------------------------------------
                */

                const editId =
                    document.getElementById(
                        'editSpecificationId'
                    )?.value.trim() || '';

                const existingBarcode =
                    document.getElementById(
                        'editSpecificationBarcode'
                    )?.value.trim() || '';

                const isEdit =
                    editId !== '';


                /*
                |--------------------------------------------------------------------------
                | GET VALUES
                |--------------------------------------------------------------------------
                */

                const itemName =
                    document.getElementById(
                        'item_name'
                    )?.value || '';

                const itemType =
                    document.getElementById(
                        'item_type'
                    )?.value || '';

                const designer =
                    document.getElementById(
                        'designer_name'
                    )?.value || '';

                const gender =
                    document.getElementById(
                        'gender_type'
                    )?.value || '';

                const composition =
                    document.getElementById(
                        'composition'
                    )?.value || '';

                const colour =
                    document.getElementById(
                        'colour'
                    )?.value || '';

                const sizes =
                    document.getElementById(
                        'sizes'
                    )?.value || '';

                const manufacturingProcess =
                    document.getElementById(
                        'manufacturing_process'
                    )?.value || '';

                const manufacture =
                    document.getElementById(
                        'cmbmanufacture'
                    )?.value || '';

                const collectionField =
                    getCollectionField();

                const collection =
                    collectionField
                        ? (collectionField.value || '')
                        : '';

                const imageInput =
                    document.getElementById(
                        'filenew'
                    );

                const hasNewImages =
                    !!(
                        imageInput &&
                        imageInput.files &&
                        imageInput.files.length > 0
                    );

                const hasExistingImages =
                    Array.isArray(existingImages) &&
                    existingImages.length > 0;

                const hasDesignImages =
                    hasNewImages ||
                    hasExistingImages;


                /*
                |--------------------------------------------------------------------------
                | REQUIRED VALIDATION
                |--------------------------------------------------------------------------
                */

                if (!itemName) {
                    showSpecificationAlertAndFocus('Please select Item Name.', 'item_name');
                    return;
                }

                if (!itemType) {
                    showSpecificationAlertAndFocus('Please select Item Type.', 'item_type');
                    return;
                }

                if (!designer) {
                    showSpecificationAlertAndFocus('Please select Designer.', 'designer_name');
                    return;
                }

                if (!gender) {
                    showSpecificationAlertAndFocus('Please select Gender.', 'gender_type');
                    return;
                }

                if (!composition) {
                    showSpecificationAlertAndFocus('Please select Composition.', 'composition');
                    return;
                }

                if (!colour) {
                    showSpecificationAlertAndFocus('Please select Colour.', 'colour');
                    return;
                }

                if (!sizes) {
                    showSpecificationAlertAndFocus('Please select Size.', 'sizes');
                    return;
                }

                if (!manufacturingProcess) {
                    showSpecificationAlertAndFocus(
                        'Please select Manufacturing Process.',
                        'manufacturing_process'
                    );
                    return;
                }

                if (!collectionField) {
                    showSpecificationAlert(
                        'Collection field is not found.'
                    );
                    return;
                }

                if (!collection) {
                    showSpecificationAlertAndFocus(
                        'Please select Collection.',
                        collectionField
                    );
                    return;
                }

                if (!manufacture) {
                    showSpecificationAlertAndFocus(
                        'Please select Manufacture.',
                        'cmbmanufacture'
                    );
                    return;
                }

                if (!hasDesignImages) {
                    showSpecificationAlertAndFocus(
                        'Please select at least one design image.',
                        'filenew'
                    );
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | SHOW CONFIRMATION
                |--------------------------------------------------------------------------
                */

                prepareSpecificationConfirmation(
                    isEdit
                );

            }
        );

        document.addEventListener(
            'click',
            async function (event) {

                const finalButton =
                    event.target.closest(
                        '#btnFinalSaveSpecification'
                    );

                if (!finalButton) {
                    return;
                }


                event.preventDefault();


                const isEdit =
                    finalButton.dataset.mode ===
                    'edit';


                /*
                |--------------------------------------------------------------------------
                | FINAL CONFIRMATION
                |--------------------------------------------------------------------------
                */

                

                const result =
                    await Swal.fire({

                        icon:
                            isEdit
                                ? 'question'
                                : 'info',

                        title:
                            isEdit
                                ? 'Update Specification?'
                                : 'Save Specification?',

                        text:
                            isEdit
                                ? 'Are you sure you want to update this specification? The existing barcode will remain unchanged.'
                                : 'Are you sure you want to save this new specification?',

                        showCancelButton:
                            true,

                        confirmButtonText:
                            isEdit
                                ? 'Yes, Update'
                                : 'Yes, Save',

                        cancelButtonText:
                            'Back',

                        confirmButtonColor:
                            isEdit
                                ? '#f59e0b'
                                : '#2563eb',

                        cancelButtonColor:
                            '#64748b'

                    });


                if (!result.isConfirmed) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | CLOSE PREVIEW
                |--------------------------------------------------------------------------
                */

                const modalElement =
                    document.getElementById(
                        'saveSpecificationPreviewModal'
                    );

                const modal =
                    bootstrap.Modal.getOrCreateInstance(
                        modalElement
                    );

                modal.hide();

                Swal.fire({

        title: isEdit
            ? 'Updating Specification...'
            : 'Saving Specification...',

        html: isEdit
            ? 'Please wait while the specification is being updated.'
            : 'Please wait while the specification is being saved.',

        allowOutsideClick: false,

        allowEscapeKey: false,

        showConfirmButton: false,

        didOpen: function () {

            Swal.showLoading();

        }

         

    });



            /*
            |--------------------------------------------------------------------------
            | NOW SAVE TO DATABASE
            |--------------------------------------------------------------------------
            */

            await submitSpecificationSave();

        }
    );

    async function submitSpecificationSave() {

            /*
            |--------------------------------------------------------------------------
            | GET SAVE / UPDATE BUTTON DIRECTLY
            |--------------------------------------------------------------------------
            | This function is called by the Final Save / Final Update
            | confirmation handler, so there is no click event argument here.
            |--------------------------------------------------------------------------
            */

            const saveButton =
                document.getElementById(
                    'btnSaveSpecification'
                );

            if (!saveButton) {
                throw new Error(
                    'Save / Update button not found.'
                );
            }

            console.log(
                'SAVE / UPDATE BUTTON CLICKED'
            );


            try {

                /*
                |--------------------------------------------------------------------------
                | CREATE EDIT FIELDS AUTOMATICALLY
                |--------------------------------------------------------------------------
                |
                | No HTML changes required.
                |
                */

                let editIdElement =
                    document.getElementById(
                        'editSpecificationId'
                    );


                let editBarcodeElement =
                    document.getElementById(
                        'editSpecificationBarcode'
                    );


                if (!editIdElement) {

                    editIdElement =
                        document.createElement(
                            'input'
                        );

                    editIdElement.type =
                        'hidden';

                    editIdElement.id =
                        'editSpecificationId';

                    editIdElement.value =
                        '';

                    document.body.appendChild(
                        editIdElement
                    );

                }


                if (!editBarcodeElement) {

                    editBarcodeElement =
                        document.createElement(
                            'input'
                        );

                    editBarcodeElement.type =
                        'hidden';

                    editBarcodeElement.id =
                        'editSpecificationBarcode';

                    editBarcodeElement.value =
                        '';

                    document.body.appendChild(
                        editBarcodeElement
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CHECK NEW OR EDIT
                |--------------------------------------------------------------------------
                */

                const editSpecificationId =
                    editIdElement.value.trim();


                const editBarcode =
                    editBarcodeElement.value.trim();


                const isEdit =
                    editSpecificationId !== '';


                console.log(
                    'IS EDIT:',
                    isEdit
                );


                console.log(
                    'EDIT ID:',
                    editSpecificationId
                );


                console.log(
                    'EXISTING BARCODE:',
                    editBarcode
                );


                /*
                |--------------------------------------------------------------------------
                | GET FORM VALUES
                |--------------------------------------------------------------------------
                */

                const itemName =
                    document.getElementById(
                        'item_name'
                    )?.value || '';


                const itemType =
                    document.getElementById(
                        'item_type'
                    )?.value || '';


                const designer =
                    document.getElementById(
                        'designer_name'
                    )?.value || '';


                const gender =
                    document.getElementById(
                        'gender_type'
                    )?.value || '';


                const composition =
                    document.getElementById(
                        'composition'
                    )?.value || '';


                const colour =
                    document.getElementById(
                        'colour'
                    )?.value || '';


                const sizes =
                    document.getElementById(
                        'sizes'
                    )?.value || '';


                const embellishment =
                    document.getElementById(
                        'embellishment'
                    )?.value || '';


                const manufacturingProcess =
                    document.getElementById(
                        'manufacturing_process'
                    )?.value || '';


                const craftsman =
                    document.getElementById(
                        'mcraftsman'
                    )?.value || '';


                const manufacture =
                    document.getElementById(
                        'cmbmanufacture'
                    )?.value || '';


                const client =
                    document.getElementById(
                        'cmbclient'
                    )?.value || '';


                const sku =
                    document.getElementById(
                        'sku'
                    )?.value.trim() || '';

                 const saleprice =
                    document.getElementById(
                        'saleprice'
                    )?.value.trim() || '';

                const price =
                    document.getElementById(
                        'price'
                    )?.value.trim() || '';

                const minprice =
                    document.getElementById(
                        'minprice'
                    )?.value.trim() || '';


                const clientReference =
                    document.getElementById(
                        'txt_clientreference'
                    )?.value.trim() || '';

                const collectionField =
                    getCollectionField();

                const collection =
                    collectionField
                        ? (collectionField.value || '')
                        : '';

                const imageInputForValidation =
                    document.getElementById(
                        'filenew'
                    );

                const hasNewImagesForValidation =
                    !!(
                        imageInputForValidation &&
                        imageInputForValidation.files &&
                        imageInputForValidation.files.length > 0
                    );

                const hasExistingImagesForValidation =
                    Array.isArray(existingImages) &&
                    existingImages.length > 0;

                const hasDesignImagesForValidation =
                    hasNewImagesForValidation ||
                    hasExistingImagesForValidation;


                /*
                |--------------------------------------------------------------------------
                | REQUIRED FIELD VALIDATION
                |--------------------------------------------------------------------------
                */

                if (!itemName) {

                    showSpecificationAlertAndFocus(
                        'Please select Item Name.',
                        'item_name'
                    );

                    return;
                }


                if (!itemType) {

                    showSpecificationAlertAndFocus(
                        'Please select Item Type.',
                        'item_type'
                    );

                    return;
                }


                if (!designer) {

                    showSpecificationAlertAndFocus(
                        'Please select Designer.',
                        'designer_name'
                    );

                    return;
                }


                if (!gender) {

                    showSpecificationAlertAndFocus(
                        'Please select Gender.',
                        'gender_type'
                    );

                    return;
                }


                if (!composition) {

                    showSpecificationAlertAndFocus(
                        'Please select Composition.',
                        'composition'
                    );

                    return;
                }


                if (!colour) {

                    showSpecificationAlertAndFocus(
                        'Please select Colour.',
                        'colour'
                    );

                    return;
                }


                if (!sizes) {

                    showSpecificationAlertAndFocus(
                        'Please select Size.',
                        'sizes'
                    );

                    return;
                }

                if (!manufacturingProcess) {

                    showSpecificationAlertAndFocus(
                        'Please select Manufacturing Process.',
                        'manufacturing_process'
                    );

                    return;
                }

                if (!collectionField) {

                    showSpecificationAlert(
                        'Collection field is not found.'
                    );

                    return;
                }

                if (!collection) {

                    showSpecificationAlertAndFocus(
                        'Please select Collection.',
                        collectionField
                    );

                    return;
                }

                if (!manufacture) {

                    showSpecificationAlertAndFocus(
                        'Please select Manufacture.',
                        'cmbmanufacture'
                    );

                    return;
                }

                if (!hasDesignImagesForValidation) {

                    showSpecificationAlertAndFocus(
                        'Please select at least one design image.',
                        'filenew'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | SHOW SAVING / UPDATING
                |--------------------------------------------------------------------------
                */

                const oldButtonHtml =
                    saveButton.innerHTML;


                // saveButton.disabled =
                //     true;


                saveButton.innerHTML =
                    isEdit

                        ? '<span class="spinner-border spinner-border-sm me-1"></span> Updating...'

                        : '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';


                /*
                |--------------------------------------------------------------------------
                | CREATE FORMDATA
                |--------------------------------------------------------------------------
                */

                const formData =
                    new FormData();


                formData.append(
                    '_token',
                    '{{ csrf_token() }}'
                );

                /*
                |--------------------------------------------------------------------------
                | SUPPLIER RAW PRODUCT DATA
                |--------------------------------------------------------------------------
                | These fields are optional.
                | If supplier raw product was selected, send the available values.
                |--------------------------------------------------------------------------
                */

                if (selectedSupplierProduct) {

                    const supplierProductId =
                        selectedSupplierProduct.product_id || '';

                    const supplierPersonId =
                        selectedSupplierProduct.login_supplier_id || '';

                    const supplierStock =
                        selectedSupplierProduct.stock !== null &&
                        selectedSupplierProduct.stock !== undefined &&
                        selectedSupplierProduct.stock !== ''
                            ? selectedSupplierProduct.stock
                            : '';

                    formData.append(
                        'supplier_product_id',
                        supplierProductId
                    );

                    formData.append(
                        'login_supplier_id',
                        supplierPersonId
                    );

                    formData.append(
                        'login_supplier_stock',
                        supplierStock
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | IF EDIT
                |--------------------------------------------------------------------------
                |
                | Laravel will receive:
                |
                | _method = PUT
                |
                | This is better than sending PUT directly with multipart FormData.
                |
                */

                /*
                |--------------------------------------------------------------------------
                | EDIT INFORMATION
                |--------------------------------------------------------------------------
                */

                const editId =
                    editSpecificationId;

                const existingBarcode =
                    editBarcode;


                if (isEdit) {

                    /*
                    | Tell Laravel this is a modification
                    */

                    formData.append(
                        'edit_id',
                        editId
                    );


                    /*
                    | VERY IMPORTANT:
                    | Keep the old barcode.
                    */

                    formData.append(
                        'existing_barcode',
                        existingBarcode
                    );

                }

                if (isEdit) {

                    formData.append(
                        '_method',
                        'PUT'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | DESIGN SPECIFICATION DATA
                |--------------------------------------------------------------------------
                */

                formData.append(
                    'item_name',
                    itemName
                );


                formData.append(
                    'item_type',
                    itemType
                );


                formData.append(
                    'designer_name',
                    designer
                );


                formData.append(
                    'gender',
                    gender
                );


                formData.append(
                    'composition',
                    composition
                );


                formData.append(
                    'colour',
                    colour
                );


                formData.append(
                    'sizes',
                    sizes
                );


                formData.append(
                    'embellishment',
                    embellishment
                );


                formData.append(
                    'manufacturing_process',
                    manufacturingProcess
                );


                formData.append(
                    'craftsman',
                    craftsman
                );


                formData.append(
                    'manufecture',
                    manufacture
                );


                formData.append(
                    'client',
                    client
                );


                formData.append(
                    'sku',
                    sku
                );

                formData.append(
                    'saleprice',
                    saleprice
                );

                formData.append(
                    'price',
                    price
                );

                formData.append(
                    'minprice',
                    minprice
                );


                formData.append(
                    'clientreference',
                    clientReference
                );


                /*
                |--------------------------------------------------------------------------
                | AI PRODUCT DATA
                |--------------------------------------------------------------------------
                */

                formData.append(
                    'AI_product_name',
                    document.getElementById(
                        'txt_productName'
                    )?.value || ''
                );


                formData.append(
                    'AI_product_description',
                    document.getElementById(
                        'txt_productDescription'
                    )?.value || ''
                );


                formData.append(
                    'AI_Metatitle',
                    document.getElementById(
                        'txt_metaTitle'
                    )?.value || ''
                );


                formData.append(
                    'AI_Metakeywards',
                    document.getElementById(
                        'txt_metaKeywords'
                    )?.value || ''
                );


                formData.append(
                    'AI_Metadescription',
                    document.getElementById(
                        'txt_metaDescription'
                    )?.value || ''
                );


                formData.append(
                    'AI_Producttag',
                    document.getElementById(
                        'txt_productTags'
                    )?.value || ''
                );


                formData.append(
                    'AI_Imagealttext',
                    document.getElementById(
                        'txt_image_alt_text'
                    )?.value || ''
                );


                /*
                |--------------------------------------------------------------------------
                | IMAGES
                |--------------------------------------------------------------------------
                */

                const imageInput =
                    document.getElementById(
                        'filenew'
                    );


                if (
                    imageInput &&
                    imageInput.files
                ) {

                    Array.from(
                        imageInput.files
                    ).forEach(
                        function (file) {

                            formData.append(
                                'design_images[]',
                                file
                            );

                        }
                    );

                }

                            
                 // =========================================================
                    // SUB IMAGES
                    // =========================================================

                    if (
                        Array.isArray(selectedSubImages) &&
                        selectedSubImages.length > 0
                    ) {

                        selectedSubImages.forEach(
                            function (file) {

                                formData.append(
                                    'sub_images[]',
                                    file,
                                    file.name
                                );

                            }
                        );

                    }


                    console.log(
                        'SUB IMAGES TO UPLOAD:',
                        selectedSubImages.length
                    );


                /*
                |--------------------------------------------------------------------------
                | DEBUG
                |--------------------------------------------------------------------------
                */

                console.log(
                    'Saving / Updating Design Specification...',
                    {
                        isEdit:
                            isEdit,

                        editId:
                            editSpecificationId,

                        existingBarcode:
                            editBarcode,

                        itemName:
                            itemName,

                        itemType:
                            itemType,

                        designer:
                            designer,

                        gender:
                            gender,

                        composition:
                            composition,

                        colour:
                            colour,

                        sizes:
                            sizes,

                        sku:
                            sku
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | URL
                |--------------------------------------------------------------------------
                */

                let requestUrl;


                if (isEdit) {

                    /*
                    | EDIT
                    |
                    | Example:
                    | /design-specifications/25
                    */

                requestUrl =
                        "{{ url('/admin/design-specifications') }}/" +
                        encodeURIComponent(
                            editSpecificationId
                        );
    

                } else {

                    /*
                    | NEW
                    */

                    requestUrl =
                        "{{ route('design-specifications.store') }}";

                }


                console.log(
                    'REQUEST URL:',
                    requestUrl
                );


                /*
                |--------------------------------------------------------------------------
                | SEND TO LARAVEL
                |--------------------------------------------------------------------------
                */

                const response =
                    await fetch(
                        requestUrl,
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
                    );


                /*
                |--------------------------------------------------------------------------
                | READ RESPONSE
                |--------------------------------------------------------------------------
                */

                const contentType =
                    response.headers.get(
                        'content-type'
                    ) || '';


                let result;


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
                        'Non JSON response:',
                        text
                    );


                    throw new Error(
                        'Server returned an unexpected response. HTTP ' +
                        response.status
                    );

                }


                console.log(
                    'SAVE / UPDATE RESPONSE:',
                    result
                );


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
                        (
                            isEdit
                                ? 'Unable to update specification.'
                                : 'Unable to save specification.'
                        )
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | SUCCESS MESSAGE
                |--------------------------------------------------------------------------
                */

                if (isEdit) {

                    alert(
                        'Specification updated successfully.\n\n' +
                        'Barcode: ' +
                        (
                            result.barcode ||
                            editBarcode ||
                            '-'
                        )
                    );

                } else {

                    alert(
                        'Specification saved successfully.\n\n' +
                        'Barcode: ' +
                        (
                            result.barcode ||
                            '-'
                        )
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | DEBUG
                |--------------------------------------------------------------------------
                */

                console.log(
                    'Product ID:',
                    result.product_id ||
                    editSpecificationId
                );


                console.log(
                    'Barcode:',
                    result.barcode ||
                    editBarcode
                );


                /*
                    |--------------------------------------------------------------------------
                    | CLEAR DESIGN SUB IMAGES
                    |--------------------------------------------------------------------------
                    */

                    selectedSubImages = [];

                    const subImagesInput =
                        document.getElementById('sub_images');

                    if (subImagesInput) {
                        subImagesInput.value = '';
                    }

                    const subImagesPreview =
                        document.getElementById('subImagesPreview');

                    if (subImagesPreview) {
                        subImagesPreview.innerHTML = '';
                    }

                    const confirmSubImages =
                        document.getElementById('confirmSubImages');

                    if (confirmSubImages) {
                        confirmSubImages.innerHTML = '';
                    }


                /*
                |--------------------------------------------------------------------------
                | CLEAR FORM
                |--------------------------------------------------------------------------
                */

                const inputElements =
                    document.querySelectorAll(
                        '#newSpecificationSection input, ' +
                        '#newSpecificationSection textarea'
                    );


                inputElements.forEach(
                    function (element) {

                        if (
                            element.type ===
                            'file'
                        ) {

                            element.value =
                                '';

                        } else {

                            element.value =
                                '';

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | CLEAR SELECT2
                |--------------------------------------------------------------------------
                */

                if (
                    typeof jQuery !==
                        'undefined' &&
                    jQuery.fn.select2
                ) {

                    jQuery(
                        '#newSpecificationSection select'
                    )
                    .val(null)
                    .trigger('change');

                } else {

                    document
                        .querySelectorAll(
                            '#newSpecificationSection select'
                        )
                        .forEach(
                            function (select) {

                                select.value =
                                    '';

                            }
                        );

                }


                /*
                |--------------------------------------------------------------------------
                | CLEAR EDIT MODE
                |--------------------------------------------------------------------------
                */

                editIdElement.value =
                    '';

                editBarcodeElement.value =
                    '';


                /*
                |--------------------------------------------------------------------------
                | RESET EDIT UI
                |--------------------------------------------------------------------------
                */

                const editBarcodeBadge =
                    document.getElementById(
                        'editBarcodeBadge'
                    );


                if (editBarcodeBadge) {

                    editBarcodeBadge.style.display =
                        'none';

                }


                const editBarcodeText =
                    document.getElementById(
                        'editBarcodeText'
                    );


                if (editBarcodeText) {

                    editBarcodeText.textContent =
                        '';

                }


                const specificationFormTitle =
                    document.getElementById(
                        'specificationFormTitle'
                    );


                if (specificationFormTitle) {

                    specificationFormTitle.textContent =
                        'New Design Specification';

                }


                const specificationFormSubtitle =
                    document.getElementById(
                        'specificationFormSubtitle'
                    );


                if (specificationFormSubtitle) {

                    specificationFormSubtitle.textContent =
                        'Enter garment design specification details';

                }


                /*
                |--------------------------------------------------------------------------
                | RESET BUTTON
                |--------------------------------------------------------------------------
                */

                saveButton.innerHTML =
                    '<i class="bi bi-check-lg me-1"></i> Save Specification';


                /*
                |--------------------------------------------------------------------------
                | HIDE SELECTED IMAGES
                |--------------------------------------------------------------------------
                */

                const selectedImagesSection =
                    document.getElementById(
                        'selectedImagesSection'
                    );


                const selectedImagePreview =
                    document.getElementById(
                        'selectedImagePreview'
                    );


                if (
                    selectedImagePreview
                ) {

                    selectedImagePreview.innerHTML =
                        '';

                }


                if (
                    selectedImagesSection
                ) {

                    selectedImagesSection.style.display =
                        'none';

                }


                /*
                |--------------------------------------------------------------------------
                | REFRESH PRODUCT LIST
                |--------------------------------------------------------------------------
                */

                if (
                    typeof loadSpecifications ===
                    'function'
                ) {

                    loadSpecifications(1);

                }


            } catch (error) {

                console.error(
                    'SAVE / UPDATE SPECIFICATION ERROR:',
                    error
                );

                alert(
                    'Save / Update Error:\n\n' +
                    error.message
                );

            }

        }


    function prepareSpecificationConfirmation(
        isEdit
    ) {

        /*
        |--------------------------------------------------------------------------
        | SELECT TEXT HELPER
        |--------------------------------------------------------------------------
        */

        function getSelectValue(id) {
            const select = document.getElementById(id);

            if (!select) {
                return '0';
            }

            return select.value || '0';
        }

        function getSelectText(id) {

            const select =
                document.getElementById(id);

            if (!select) {
                return '-';
            }

            const option =
                select.options[
                    select.selectedIndex
                ];

            if (!option) {
                return '-';
            }

            return (
                option.textContent || ''
            ).trim() || '-';
        }


        /*
        |--------------------------------------------------------------------------
        | GET VALUES
        |--------------------------------------------------------------------------
        */

        const itemName =
            getSelectText('item_name');

        const itemType =
            getSelectText('item_type');

        const designer =
            getSelectText('designer_name');

        const gender =
            getSelectText('gender_type');

        const composition =
            getSelectText('composition');

        const colour =
            getSelectText('colour');

        const size =
            getSelectText('sizes');

        const embellishment =
            getSelectText('embellishment');

        const manufacturingProcess =
            getSelectText(
                'manufacturing_process'
            );

        const craftsman =
            getSelectText(
                'mcraftsman'
            );

        const manufacture =
            getSelectText(
                'cmbmanufacture'
            );

        const client =
            getSelectText(
                'cmbclient'
            );

        /*
        |--------------------------------------------------------------------------
        | BARCODE VALUES
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Use SELECT VALUE/ID, not display text.
        |--------------------------------------------------------------------------
        */

        const barcodeItemName =
            getSelectValue('item_name');

        const barcodeItemType =
            getSelectValue('item_type');

        const barcodeDesigner =
            getSelectValue('designer_name');

        const barcodeColour =
            getSelectValue('colour');

        const barcodeSize =
            getSelectValue('sizes');

        const barcodeClient =
            getSelectValue('cmbclient');

        const projectId =
    document.getElementById(
        'specificationProjectId'
    )?.value || '0';
    const generatedBarcode =
    [
        projectId,
        barcodeItemName,
        barcodeItemType,
        barcodeDesigner,
        barcodeColour,
        barcodeSize,
        barcodeClient
    ].join('');

        /*
        |--------------------------------------------------------------------------
        | CRAFTSMAN CODE
        |--------------------------------------------------------------------------
        */

        const craftsmanSelect =
            document.getElementById(
                'mcraftsman'
            );

        let craftsmanCode = '-';

        if (
            craftsmanSelect &&
            craftsmanSelect.selectedIndex >= 0
        ) {

            const selectedOption =
                craftsmanSelect.options[
                    craftsmanSelect.selectedIndex
                ];

            craftsmanCode =
                selectedOption?.dataset?.code ||
                '-';

        }


        /*
        |--------------------------------------------------------------------------
        | TEXT FIELDS
        |--------------------------------------------------------------------------
        */

        const sku =
            document.getElementById(
                'sku'
            )?.value.trim() || '-';

        const clientReference =
            document.getElementById(
                'txt_clientreference'
            )?.value.trim() || '-';

        const supplierStock =
            selectedSupplierProduct &&
            selectedSupplierProduct.stock !== null &&
            selectedSupplierProduct.stock !== undefined &&
            selectedSupplierProduct.stock !== ''
                ? selectedSupplierProduct.stock
                : '-';


        /*
        |--------------------------------------------------------------------------
        | AI
        |--------------------------------------------------------------------------
        */

        const aiProductName =
            document.getElementById(
                'txt_productName'
            )?.value.trim() || '';

        const aiProductDescription =
            document.getElementById(
                'txt_productDescription'
            )?.value.trim() || '';

        const aiMetaTitle =
            document.getElementById(
                'txt_metaTitle'
            )?.value.trim() || '';

        const aiMetaKeywords =
            document.getElementById(
                'txt_metaKeywords'
            )?.value.trim() || '';

        const aiMetaDescription =
            document.getElementById(
                'txt_metaDescription'
            )?.value.trim() || '';

        const aiProductTags =
            document.getElementById(
                'txt_productTags'
            )?.value.trim() || '';

        const aiImageAltText =
            document.getElementById(
                'txt_image_alt_text'
            )?.value.trim() || '';


        /*
        |--------------------------------------------------------------------------
        | SET BASIC INFORMATION
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'confirmItemName'
        ).textContent =
            itemName;

        document.getElementById(
            'confirmItemName2'
        ).textContent =
            itemName;

        document.getElementById(
            'confirmItemType'
        ).textContent =
            itemType;

        document.getElementById(
            'confirmDesigner'
        ).textContent =
            designer;

        document.getElementById(
            'confirmGender'
        ).textContent =
            gender;

        document.getElementById(
            'confirmComposition'
        ).textContent =
            composition;

        document.getElementById(
            'confirmColour'
        ).textContent =
            colour;

        document.getElementById(
            'confirmSize'
        ).textContent =
            size;

        document.getElementById(
            'confirmEmbellishment'
        ).textContent =
            embellishment;

        document.getElementById(
            'confirmManufacturingProcess'
        ).textContent =
            manufacturingProcess;

        document.getElementById(
            'confirmCraftsman'
        ).textContent =
            craftsman;

        document.getElementById(
            'confirmCraftsmanCode'
        ).textContent =
            craftsmanCode;

        document.getElementById(
            'confirmManufacture'
        ).textContent =
            manufacture;

        document.getElementById(
            'confirmClient'
        ).textContent =
            client;

        document.getElementById(
            'confirmSku'
        ).textContent =
            sku;

        document.getElementById(
            'confirmClientReference'
        ).textContent =
            clientReference;

        const confirmSupplierStock =
            document.getElementById(
                'confirmSupplierStock'
            );

        if (confirmSupplierStock) {
            confirmSupplierStock.textContent =
                supplierStock;
        }


        /*
        |--------------------------------------------------------------------------
        | BARCODE
        |--------------------------------------------------------------------------
        */

        const barcodeElement =
            document.getElementById(
                'confirmBarcode'
            );

        if (isEdit) {

    const existingBarcode =
        document.getElementById(
            'editSpecificationBarcode'
        )?.value || '-';

    barcodeElement.textContent =
        existingBarcode;

    renderConfirmationBarcode(
        existingBarcode
    );

} else {

    barcodeElement.textContent =
        generatedBarcode;

    renderConfirmationBarcode(
        generatedBarcode
    );

}


        /*
        |--------------------------------------------------------------------------
        | AI DATA
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'confirmAIProductName'
        ).textContent =
            aiProductName || '-';

        document.getElementById(
            'confirmAIProductDescription'
        ).textContent =
            aiProductDescription || '-';

        document.getElementById(
            'confirmAIMetaTitle'
        ).textContent =
            aiMetaTitle || '-';

        document.getElementById(
            'confirmAIMetaKeywords'
        ).textContent =
            aiMetaKeywords || '-';

        document.getElementById(
            'confirmAIMetaDescription'
        ).textContent =
            aiMetaDescription || '-';

        document.getElementById(
            'confirmAIProductTags'
        ).textContent =
            aiProductTags || '-';

        document.getElementById(
            'confirmAIImageAltText'
        ).textContent =
            aiImageAltText || '-';


        /*
        |--------------------------------------------------------------------------
        | HIDE AI SECTION IF EMPTY
        |--------------------------------------------------------------------------
        */

        const aiSection =
            document.getElementById(
                'confirmAISection'
            );

        const hasAI =
            aiProductName ||
            aiProductDescription ||
            aiMetaTitle ||
            aiMetaKeywords ||
            aiMetaDescription ||
            aiProductTags ||
            aiImageAltText;

        if (aiSection) {

            aiSection.style.display =
                hasAI ? '' : 'none';

        }


        /*
        |--------------------------------------------------------------------------
        | IMAGES
        |--------------------------------------------------------------------------
        */

        window.renderConfirmationImages();


        window.renderConfirmationSubImages();



        /*
        |--------------------------------------------------------------------------
        | PRODUCT IMAGE
        |--------------------------------------------------------------------------
        */

        window.renderConfirmationMainImage();


        /*
        |--------------------------------------------------------------------------
        | CHANGE TITLE
        |--------------------------------------------------------------------------
        */

        const title =
            document.getElementById(
                'savePreviewTitle'
            );

        const finalButton =
            document.getElementById(
                'btnFinalSaveSpecification'
            );


        if (isEdit) {

            title.innerHTML =
                '<i class="bi bi-pencil-square me-2"></i>' +
                'Confirm Update Specification';

            finalButton.innerHTML =
                '<i class="bi bi-pencil-square me-1"></i>' +
                'Final Update';

            finalButton.className =
                'btn btn-warning';

        } else {

            title.innerHTML =
                '<i class="bi bi-check2-square me-2"></i>' +
                'Confirm New Specification';

            finalButton.innerHTML =
                '<i class="bi bi-check-lg me-1"></i>' +
                'Final Save';

            finalButton.className =
                'btn btn-primary';

        }


        /*
        |--------------------------------------------------------------------------
        | STORE CURRENT MODE
        |--------------------------------------------------------------------------
        */

        finalButton.dataset.mode =
            isEdit
                ? 'edit'
                : 'new';


        /*
        |--------------------------------------------------------------------------
        | OPEN MODAL
        |--------------------------------------------------------------------------
        */

        const modalElement =
            document.getElementById(
                'saveSpecificationPreviewModal'
            );

        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

        modal.show();

    }

    /* ============================================================
   BARCODE PRINT - MULTI PRODUCT VERSION
   ============================================================ */

let barcodePrintSpecification = null;
window.selectedBarcodeSpecifications = [];

/* ============================================================
   HTML ESCAPE
   ============================================================ */
function escapeHtml(value) {
    if (value === null || value === undefined) {
        return '';
    }

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/* ============================================================
   GET SELECTED BARCODE PRODUCTS
   ============================================================ */
function getSelectedBarcodeProducts() {

    const products = [];

    document.querySelectorAll(
        '.barcode-select-checkbox:checked'
    ).forEach(function (checkbox) {

        const card =
            checkbox.closest('.specification-card');

        const qtyInput = card
            ? card.querySelector('.barcode-qty-input')
            : null;

        const qty = qtyInput
            ? parseInt(qtyInput.value, 10) || 0
            : 0;

        products.push({
            specificationId: String(
                checkbox.dataset.id || ''
            ).trim(),

            barcode: String(
                checkbox.dataset.barcode || ''
            ).trim(),

            itemName: String(
                checkbox.dataset.itemname || '-'
            ).trim(),

            composition: String(
                checkbox.dataset.composition || '-'
            ).trim(),

            colour: String(
                checkbox.dataset.colour || '-'
            ).trim(),

            size: String(
                checkbox.dataset.size || '-'
            ).trim(),

            image: String(
                checkbox.dataset.image || ''
            ).trim(),

            quantity: qty
        });
    });

    return products;
}

/* ============================================================
   CALCULATE TOTAL BARCODE QUANTITY
   ============================================================ */
function calculateSelectedBarcodeTotal(products) {

    return (products || []).reduce(
        function (total, product) {

            return total + (
                parseInt(product.quantity, 10) || 0
            );

        },
        0
    );
}

/* ============================================================
   UPDATE BARCODE TOTAL
   ============================================================ */
function updateBarcodePrintTotal() {

    const products =
        getSelectedBarcodeProducts();

    const total =
        calculateSelectedBarcodeTotal(products);

    const totalInput =
        document.getElementById('txt_howmanybarcode');

    if (totalInput) {
        totalInput.value = total;
    }

    const totalDisplay =
        document.getElementById('barcodeTotalCount');

    if (totalDisplay) {
        totalDisplay.textContent = total;
    }

    const summaryQuantity =
        document.getElementById('barcodeSummaryQuantity');

    if (summaryQuantity) {
        summaryQuantity.textContent = total;
    }

    const previewCount =
        document.getElementById('barcodePreviewCount');

    if (previewCount) {
        previewCount.textContent =
            total + (total === 1 ? ' Barcode' : ' Barcodes');
    }

    return total;
}

/* ============================================================
   RENDER BARCODE PREVIEW FOR EACH SELECTED PRODUCT
   ============================================================ */
function renderSelectedBarcodePreview() {

    const container =
        document.getElementById('barcodePreviewProductsContainer');

    if (!container) {
        return;
    }

    container.innerHTML = '';

    const products =
        window.selectedBarcodeSpecifications || [];

    if (!products.length) {

        container.innerHTML = `
            <div class="barcode-preview-empty">
                <i class="bi bi-upc-scan"></i>
                <div>Barcode preview will appear here.</div>
            </div>
        `;

        return;
    }

    products.forEach(function (product) {

        const previewId =
            'barcode_preview_' +
            String(product.specificationId || Math.random())
                .replace(/[^a-zA-Z0-9_-]/g, '_');

        const item = document.createElement('div');
        item.className = 'barcode-preview-product-item';

        item.innerHTML = `
            <div class="barcode-preview-product-info">
                <strong>${escapeHtml(product.itemName || '-')}</strong>
                <span>${escapeHtml(product.barcode || '-')}</span>
            </div>

            <div class="barcode-preview-svg-wrap">
                <svg id="${previewId}"></svg>
            </div>

            <div class="barcode-preview-product-qty">
                ${product.quantity || 0} ×
            </div>
        `;

        container.appendChild(item);

        const svg =
            document.getElementById(previewId);

        if (
            svg &&
            product.barcode &&
            typeof JsBarcode === 'function'
        ) {

            try {
                JsBarcode(
                    svg,
                    product.barcode,
                    {
                        format: 'CODE128',
                        width: 1.6,
                        height: 48,
                        displayValue: true,
                        fontSize: 12,
                        textMargin: 3,
                        margin: 0
                    }
                );
            } catch (error) {
                console.error(
                    'Barcode preview error:',
                    error
                );
            }
        }
    });
}

/* ============================================================
   RENDER SELECTED PRODUCTS IN INPUT MODAL
   ============================================================ */
function renderSelectedBarcodePrintList() {

    const container =
        document.getElementById(
            'selectedBarcodeProductsContainer'
        );

    if (!container) {
        console.error(
            'selectedBarcodeProductsContainer not found.'
        );
        return;
    }

    container.innerHTML = '';

    const products =
        window.selectedBarcodeSpecifications || [];

    if (!products.length) {

        container.innerHTML = `
            <div class="selected-barcode-empty">
                <i class="bi bi-box-seam"></i>
                <div>No products selected.</div>
            </div>
        `;

        return;
    }

    products.forEach(function (item) {

        const imageHtml = item.image
            ? `
                <img
                    src="${escapeHtml(item.image)}"
                    class="selected-barcode-product-image"
                    alt="${escapeHtml(item.itemName)}"
                    onerror="
                        this.style.display='none';
                        this.nextElementSibling.style.display='flex';
                    "
                >

                <div
                    class="selected-barcode-no-image"
                    style="display:none;"
                >
                    No Image
                </div>
            `
            : `
                <div class="selected-barcode-no-image">
                    No Image
                </div>
            `;

        const html = `
            <div class="selected-barcode-product">

                <div class="selected-barcode-image-wrap">
                    ${imageHtml}
                </div>

                <div class="selected-barcode-product-info">

                    <div class="selected-barcode-product-name">
                        ${escapeHtml(item.itemName || '-')}
                    </div>

                    <div class="selected-barcode-info-grid">

                        <div class="selected-barcode-info-item">
                            <span class="selected-barcode-info-label">
                                Composition
                            </span>
                            <span class="selected-barcode-info-value">
                                ${escapeHtml(item.composition || '-')}
                            </span>
                        </div>

                        <div class="selected-barcode-info-item">
                            <span class="selected-barcode-info-label">
                                Colour
                            </span>
                            <span class="selected-barcode-info-value">
                                ${escapeHtml(item.colour || '-')}
                            </span>
                        </div>

                        <div class="selected-barcode-info-item">
                            <span class="selected-barcode-info-label">
                                Size
                            </span>
                            <span class="selected-barcode-info-value">
                                ${escapeHtml(item.size || '-')}
                            </span>
                        </div>

                        <div class="selected-barcode-info-item">
                            <span class="selected-barcode-info-label">
                                Barcode
                            </span>
                            <span class="selected-barcode-info-value">
                                ${escapeHtml(item.barcode || '-')}
                            </span>
                        </div>

                    </div>
                </div>

                <div class="selected-barcode-qty">
                    <span class="selected-barcode-qty-label">
                        Print Qty
                    </span>
                    <span class="selected-barcode-qty-value">
                        ${item.quantity || 0}
                    </span>
                </div>

            </div>
        `;

        container.insertAdjacentHTML(
            'beforeend',
            html
        );
    });
}

/* ============================================================
   BARCODE BUTTON CLICK

   IMPORTANT:
   - Uses the checkboxes on the product cards.
   - Does NOT use allSpecifications.
   - Does NOT open a single-product barcode modal.
   ============================================================ */
document.addEventListener(
    'click',
    function (event) {

        const barcodeButton =
            event.target.closest(
                '.btn-barcode-spec'
            );

        if (!barcodeButton) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        const checkedBoxes =
            Array.from(
                document.querySelectorAll(
                    '.barcode-select-checkbox:checked'
                )
            );

        if (!checkedBoxes.length) {

            Swal.fire({
                icon: 'warning',
                title: 'Please check',
                text:
                    'Please select at least one product for barcode printing.',
                confirmButtonText: 'OK'
            });

            return;
        }

        const products = [];

        for (
            let i = 0;
            i < checkedBoxes.length;
            i++
        ) {

            const checkbox =
                checkedBoxes[i];

            const card =
                checkbox.closest('.specification-card');

            const qtyInput = card
                ? card.querySelector('.barcode-qty-input')
                : null;

            const qty = qtyInput
                ? parseInt(qtyInput.value, 10)
                : NaN;

            const itemName =
                String(
                    checkbox.dataset.itemname || '-'
                ).trim();

            const barcode =
                String(
                    checkbox.dataset.barcode || ''
                ).trim();

            /* --------------------------------------------------------
               QUANTITY VALIDATION
               -------------------------------------------------------- */
            if (
                !qtyInput ||
                !Number.isInteger(qty) ||
                qty < 1
            ) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Please check',
                    text:
                        'Please enter Barcode Qty for ' +
                        itemName +
                        '.',
                    confirmButtonText: 'OK'
                }).then(function () {

                    if (qtyInput) {
                        qtyInput.focus();
                        qtyInput.select();
                    }
                });

                return;
            }

            /* --------------------------------------------------------
               BARCODE VALIDATION
               -------------------------------------------------------- */
            if (!barcode) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Barcode Not Available',
                    text:
                        'Barcode is not available for ' +
                        itemName +
                        '.',
                    confirmButtonText: 'OK'
                });

                return;
            }

            products.push({
                specificationId:
                    String(
                        checkbox.dataset.id || ''
                    ).trim(),

                barcode: barcode,

                itemName: itemName,

                composition:
                    String(
                        checkbox.dataset.composition || '-'
                    ).trim(),

                colour:
                    String(
                        checkbox.dataset.colour || '-'
                    ).trim(),

                size:
                    String(
                        checkbox.dataset.size || '-'
                    ).trim(),

                image:
                    String(
                        checkbox.dataset.image || ''
                    ).trim(),

                quantity: qty
            });
        }

        window.selectedBarcodeSpecifications =
            products;

        const totalBarcodeCount =
            calculateSelectedBarcodeTotal(products);

        /* ------------------------------------------------------------
           UPDATE TOTAL
           ------------------------------------------------------------ */
        const totalInput =
            document.getElementById(
                'txt_howmanybarcode'
            );

        if (totalInput) {
            totalInput.value =
                totalBarcodeCount;
        }

        const startBox =
            document.getElementById(
                'txt_startbox'
            );

        if (startBox) {
            startBox.value = '1';
        }

        /* ------------------------------------------------------------
           UPDATE COUNTS
           ------------------------------------------------------------ */
        const productCount =
            products.length;

        const productCountElement =
            document.getElementById(
                'selectedBarcodeProductCount'
            );

        if (productCountElement) {
            productCountElement.textContent =
                productCount +
                (
                    productCount === 1
                        ? ' Product'
                        : ' Products'
                );
        }

        const previewCount =
            document.getElementById(
                'barcodePreviewCount'
            );

        if (previewCount) {
            previewCount.textContent =
                totalBarcodeCount +
                (
                    totalBarcodeCount === 1
                        ? ' Barcode'
                        : ' Barcodes'
                );
        }

        const summaryProducts =
            document.getElementById(
                'barcodeSummaryProducts'
            );

        if (summaryProducts) {
            summaryProducts.textContent =
                productCount;
        }

        const summaryQuantity =
            document.getElementById(
                'barcodeSummaryQuantity'
            );

        if (summaryQuantity) {
            summaryQuantity.textContent =
                totalBarcodeCount;
        }

        const summaryStartBox =
            document.getElementById(
                'barcodeSummaryStartBox'
            );

        if (summaryStartBox) {
            summaryStartBox.textContent = '1';
        }

        renderSelectedBarcodePrintList();
        renderSelectedBarcodePreview();

        /* ------------------------------------------------------------
           SHOW MODAL
           ------------------------------------------------------------ */
        const modalElement =
            document.getElementById(
                'barcodePrintModal'
            );

        if (!modalElement) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text:
                    'Barcode print modal was not found.',
                confirmButtonText: 'OK'
            });

            return;
        }

        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

        modal.show();
    }
);

/* ============================================================
   UPDATE MODAL TOTAL WHEN QUANTITY CHANGES
   ============================================================ */
document.addEventListener(
    'input',
    function (event) {

        if (
            !event.target.classList.contains(
                'barcode-qty-input'
            )
        ) {
            return;
        }

        const selectedCheckbox =
            event.target
                .closest('.specification-card')
                ?.querySelector(
                    '.barcode-select-checkbox:checked'
                );

        if (!selectedCheckbox) {
            return;
        }

        const product =
            window.selectedBarcodeSpecifications
                .find(function (item) {
                    return String(item.specificationId) ===
                        String(selectedCheckbox.dataset.id);
                });

        if (product) {

            const qty =
                parseInt(
                    event.target.value,
                    10
                ) || 0;

            product.quantity = qty;

            const total =
                calculateSelectedBarcodeTotal(
                    window.selectedBarcodeSpecifications
                );

            const totalInput =
                document.getElementById(
                    'txt_howmanybarcode'
                );

            if (totalInput) {
                totalInput.value = total;
            }

            const previewCount =
                document.getElementById(
                    'barcodePreviewCount'
                );

            if (previewCount) {
                previewCount.textContent =
                    total +
                    (total === 1 ? ' Barcode' : ' Barcodes');
            }

            const summaryQuantity =
                document.getElementById(
                    'barcodeSummaryQuantity'
                );

            if (summaryQuantity) {
                summaryQuantity.textContent = total;
            }

            renderSelectedBarcodePrintList();
            renderSelectedBarcodePreview();
        }
    }
);

/* ============================================================
   SINGLE BARCODE PREVIEW - kept for compatibility
   ============================================================ */
function renderSingleBarcode(barcode) {

    const svg =
        document.getElementById(
            'singleBarcodePreview'
        );

    if (!svg) {
        return;
    }

    svg.innerHTML = '';

    if (
        !barcode ||
        typeof JsBarcode !== 'function'
    ) {
        return;
    }

    JsBarcode(
        svg,
        barcode,
        {
            format: 'CODE128',
            width: 2,
            height: 70,
            displayValue: true,
            fontSize: 17,
            textMargin: 5,
            margin: 0
        }
    );
}

/* ============================================================
   GENERATE MULTI PRODUCT BARCODE SHEET

   Example:
   Product A qty = 1
   Product B qty = 3

   Output:
   Box 1 -> Product A
   Box 2 -> Product B
   Box 3 -> Product B
   Box 4 -> Product B
   ============================================================ */
function generateBarcodeSheetPreview(
    products,
    starting
) {

    const container =
        document.getElementById(
            'barcodePdfContent_X'
        );

    if (!container) {

        console.error(
            'barcodePdfContent_X not found.'
        );

        return false;
    }

    container.innerHTML = '';

    const BOXES_PER_PAGE = 24;

    let start =
        parseInt(starting, 10);

    if (
        !Number.isInteger(start) ||
        start < 1 ||
        start > BOXES_PER_PAGE
    ) {
        start = 1;
    }

    /* ------------------------------------------------------------
       EXPAND PRODUCTS BY QUANTITY
       ------------------------------------------------------------ */
    const barcodeItems = [];

    (products || []).forEach(function (product) {

        const quantity =
            parseInt(product.quantity, 10) || 0;

        for (
            let i = 0;
            i < quantity;
            i++
        ) {
            barcodeItems.push(product);
        }
    });

    if (!barcodeItems.length) {

        return false;
    }

    /* ------------------------------------------------------------
       PAGE CALCULATION
       ------------------------------------------------------------ */
    let itemIndex = 0;
    let firstPageStart = start;

    while (
        itemIndex < barcodeItems.length
    ) {

        const page =
            document.createElement('div');

        page.className =
            'barcode-a4-page';

        /* --------------------------------------------------------
           25 BOXES ON EVERY PAGE
           -------------------------------------------------------- */
        for (
            let position = 1;
            position <= BOXES_PER_PAGE;
            position++
        ) {

            const box =
                document.createElement('div');

            box.className =
                'barcode-box';

            /* Empty box before starting position */
            if (
                itemIndex >= barcodeItems.length ||
                position < firstPageStart
            ) {
                page.appendChild(box);
                continue;
            }

            const product =
                barcodeItems[itemIndex];

            /* ----------------------------------------------------
               PRODUCT NAME
               ---------------------------------------------------- */
            const itemNameElement =
                document.createElement('div');

            itemNameElement.className =
                'barcode-item-name';

            itemNameElement.textContent =
                product.itemName || '';

            box.appendChild(
                itemNameElement
            );

            /* ----------------------------------------------------
               COMPOSITION + COLOUR
               ---------------------------------------------------- */
            const detailElement =
                document.createElement('div');

            detailElement.className =
                'barcode-item-detail';

            detailElement.textContent = [
                product.composition,
                product.colour
            ]
                .filter(function (value) {
                    return value && value !== '-';
                })
                .join(' , ');

            box.appendChild(
                detailElement
            );

            /* ----------------------------------------------------
               BARCODE SVG
               ---------------------------------------------------- */
            const svg =
                document.createElementNS(
                    'http://www.w3.org/2000/svg',
                    'svg'
                );

            box.appendChild(svg);

            if (
                product.barcode &&
                typeof JsBarcode === 'function'
            ) {

                try {

                    JsBarcode(
                        svg,
                        product.barcode,
                        {
                            format: 'CODE128',
                            width: 2,
                            height: 45,
                            displayValue: true,
                            fontSize: 12,
                            textMargin: 2,
                            margin: 0
                        }
                    );

                } catch (error) {

                    console.error(
                        'JsBarcode error:',
                        error
                    );
                }
            }

            /* ----------------------------------------------------
               SIZE
               ---------------------------------------------------- */
            const sizeElement =
                document.createElement('div');

            sizeElement.className =
                'barcode-item-detail';

            sizeElement.textContent =
                product.size || '';

            box.appendChild(
                sizeElement
            );

            /* ----------------------------------------------------
               NEXT BARCODE
               ---------------------------------------------------- */
            itemIndex++;

            page.appendChild(box);
        }

        container.appendChild(page);

        /* --------------------------------------------------------
           NEXT PAGE ALWAYS STARTS AT BOX 1
           -------------------------------------------------------- */
        firstPageStart = 1;
    }

    return true;
}

/* ============================================================
   GENERATE PRINT PREVIEW BUTTON
   ============================================================ */
document.addEventListener(
    'click',
    function (event) {

        const button =
            event.target.closest(
                '#printbarcodefrom'
            );

        if (!button) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        const products =
            window.selectedBarcodeSpecifications || [];

        /* --------------------------------------------------------
           MUST HAVE SELECTED PRODUCTS
           -------------------------------------------------------- */
        if (!products.length) {

            Swal.fire({
                icon: 'warning',
                title: 'Please check',
                text:
                    'Please select at least one product for barcode printing.',
                confirmButtonText: 'OK'
            });

            return;
        }

        /* --------------------------------------------------------
           VALIDATE EVERY SELECTED PRODUCT AGAIN
           -------------------------------------------------------- */
        for (
            let i = 0;
            i < products.length;
            i++
        ) {

            const product =
                products[i];

            const quantity =
                parseInt(product.quantity, 10);

            if (
                !product.barcode
            ) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Barcode Not Available',
                    text:
                        'Barcode is not available for ' +
                        (product.itemName || 'selected product') +
                        '.',
                    confirmButtonText: 'OK'
                });

                return;
            }

            if (
                !Number.isInteger(quantity) ||
                quantity < 1
            ) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Please check',
                    text:
                        'Please enter Barcode Qty for ' +
                        (product.itemName || 'selected product') +
                        '.',
                    confirmButtonText: 'OK'
                });

                return;
            }
        }

        /* --------------------------------------------------------
           TOTAL BARCODE COUNT
           -------------------------------------------------------- */
        const total =
            calculateSelectedBarcodeTotal(products);

        const totalInput =
            document.getElementById(
                'txt_howmanybarcode'
            );

        if (totalInput) {
            totalInput.value = total;
        }

        if (total < 1) {

            Swal.fire({
                icon: 'warning',
                title: 'Please check',
                text:
                    'Barcode quantity must be greater than 0.',
                confirmButtonText: 'OK'
            });

            return;
        }

        /* --------------------------------------------------------
           STARTING BOX
           -------------------------------------------------------- */
        const startInput =
            document.getElementById(
                'txt_startbox'
            );

        const starting =
            parseInt(
                startInput?.value || '1',
                10
            );

        if (
            !Number.isInteger(starting) ||
            starting < 1 ||
            starting > 25
        ) {

            Swal.fire({
                icon: 'warning',
                title: 'Please check',
                text:
                    'Starting box must be between 1 and 25.',
                confirmButtonText: 'OK'
            }).then(function () {

                if (startInput) {
                    startInput.focus();
                    startInput.select();
                }
            });

            return;
        }

        /* --------------------------------------------------------
           CREATE SHEET
           -------------------------------------------------------- */
        const generated =
            generateBarcodeSheetPreview(
                products,
                starting
            );

        if (!generated) {

            Swal.fire({
                icon: 'error',
                title: 'Unable to generate preview',
                text:
                    'No barcode labels could be generated.',
                confirmButtonText: 'OK'
            });

            return;
        }

        /* --------------------------------------------------------
           CLOSE INPUT MODAL
           -------------------------------------------------------- */
        const inputModalElement =
            document.getElementById(
                'barcodePrintModal'
            );

        if (inputModalElement) {

            const inputModal =
                bootstrap.Modal.getInstance(
                    inputModalElement
                );

            if (inputModal) {
                inputModal.hide();
            }
        }

        /* --------------------------------------------------------
           OPEN BARCODE SHEET PREVIEW MODAL
           -------------------------------------------------------- */
        const previewModalElement =
            document.getElementById(
                'barcodeSheetPreviewModal'
            );

        if (!previewModalElement) {

            Swal.fire({
                icon: 'error',
                title: 'Preview Modal Not Found',
                text:
                    'barcodeSheetPreviewModal was not found.',
                confirmButtonText: 'OK'
            });

            return;
        }

        const previewModal =
            bootstrap.Modal.getOrCreateInstance(
                previewModalElement
            );

        previewModal.show();
    }
);


    document.addEventListener('DOMContentLoaded', function () {

        const generateBtn =
            document.getElementById('generateBtn');

        if (!generateBtn) {
            return;
        }

        generateBtn.addEventListener('click', function () {

            const previewContainer =
        document.getElementById('selectedImagePreview');

        if (!previewContainer) {
            alert('Image preview area not found');
            return;
        }

            const img =
                previewContainer.querySelector('img');

            if (!img || !img.src) {
                alert('Upload image first');
                return;
            }

            const loader =
                document.getElementById('loader');

            if (loader) {
                loader.style.display = 'flex';
            }


            fetch('/ocforms/vision-api-call.php', {

                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/x-www-form-urlencoded'
                },

                body:
                    new URLSearchParams({
                        image_url: img.src
                    })

            })

            .then(function (res) {

                if (!res.ok) {

                    throw new Error(
                        'API returned HTTP ' +
                        res.status
                    );

                }

                return res.json();

            })

            .then(function (data) {

                console.log(
                    'Generate API Response:',
                    data
                );


                if (
                    !data ||
                    !data.choices ||
                    !data.choices.length ||
                    !data.choices[0].message
                ) {

                    throw new Error(
                        'Invalid API response'
                    );

                }


                const response =
                    data.choices[0].message.content;


                console.log(
                    'AI Response:',
                    response
                );


                /*
                * ----------------------------------------------------
                * Existing generated fields
                * ----------------------------------------------------
                */

                const name =
                    extractSection(
                        response,
                        'Meta Tag Title'
                    );


                const desc =
                    extractSection(
                        response,
                        'Product Description',
                        'Product Tags'
                    );


                const tags =
                    extractSection(
                        response,
                        'Product Tags'
                    );


                const metaDesc =
                    extractSection(
                        response,
                        'Meta Tag Description'
                    );


                const keywords =
                    extractSection(
                        response,
                        'Meta Tag Keywords'
                    );


                let altText =
                    extractSection(
                        response,
                        'Image Alt Text'
                    );


                if (!altText) {

                    altText =
                        name + ' ' + keywords;

                }


                /*
                * ----------------------------------------------------
                * Fill generated fields
                * ----------------------------------------------------
                */

                const productName =
                    document.getElementById(
                        'txt_productName'
                    );

                if (productName) {
                    productName.value = name;
                }


                const productDescription =
                    document.getElementById(
                        'txt_productDescription'
                    );

                if (productDescription) {
                    productDescription.value = desc;
                }


                const metaTitle =
                    document.getElementById(
                        'txt_metaTitle'
                    );

                if (metaTitle) {
                    metaTitle.value = name;
                }


                const metaDescription =
                    document.getElementById(
                        'txt_metaDescription'
                    );

                if (metaDescription) {
                    metaDescription.value = metaDesc;
                }


                const metaKeywords =
                    document.getElementById(
                        'txt_metaKeywords'
                    );

                if (metaKeywords) {
                    metaKeywords.value = keywords;
                }


                const productTags =
                    document.getElementById(
                        'txt_productTags'
                    );

                if (productTags) {
                    productTags.value = tags;
                }


                const imageAltText =
                    document.getElementById(
                        'txt_image_alt_text'
                    );

                if (imageAltText) {
                    imageAltText.value = altText;
                }


                console.log(
                    'Generated fields filled successfully.'
                );

            })

            .catch(function (error) {

                console.error(
                    'Generate Error:',
                    error
                );

                alert(
                    '❌ Generate Error\n\n' +
                    error.message
                );

            })

            .finally(function () {

        if (loader) {
            loader.style.display = 'none';
        }


        generateBtn.disabled = false;

        generateBtn.innerHTML =
            '<i class="bi bi-stars"></i> Generate Content';

        });

            });

        });

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                if (typeof jQuery !== 'undefined' &&
                typeof jQuery.fn.select2 !== 'undefined') {

                $('.select2-master').select2({

                    width: '100%',

                    placeholder: 'Select an option',

                    allowClear: true,

                    minimumResultsForSearch: 0

                });

            }



                /* =====================================================
                ELEMENTS
                ====================================================== */

                const newSection =
                    document.getElementById(
                        'newSpecificationSection'
                    );


                const allSection =
                    document.getElementById(
                        'allSpecificationsSection'
                    );


                const btnNew =
                    document.getElementById(
                        'btnNewSpecification'
                    );


                const btnShowAll =
                    document.getElementById(
                        'btnShowAllSpecifications'
                    );


                const btnClear =
                    document.getElementById(
                        'btnClearSpecification'
                    );


                const btnCancel =
                    document.getElementById(
                        'btnCancelSpecification'
                    );


                const btnRefresh =
                    document.getElementById(
                        'btnRefreshSpecifications'
                    );


                const search =
                    document.getElementById(
                        'specificationSearch'
                    );


                const cardsContainer =
                    document.getElementById(
                        'specificationCards'
                    );


                const loading =
                    document.getElementById(
                        'specificationLoading'
                    );


                const empty =
                    document.getElementById(
                        'specificationEmpty'
                    );


                const imageInput =
                    document.getElementById(
                        'filenew'
                    );


                const selectedImagePreview =
                    document.getElementById(
                        'selectedImagePreview'
                    );


                const selectedImagesSection =
                    document.getElementById(
                        'selectedImagesSection'
                    );


                let selectedFiles = [];

                /* =========================================================
   USE UPLOADED IMAGE
    ========================================================= */

    const btnUseUploadedImage =
        document.getElementById(
            'btnUseUploadedImage'
        );

     const btnUseSupplierdImage =
        document.getElementById(
            'btnUseSupplierdImage'
        );


    const uploadedImageSearch =
        document.getElementById(
            'uploadedImageSearch'
        );


    const uploadedImageList =
    document.getElementById(
        'uploadedImageList'
    );


    const uploadedImageLoading =
        document.getElementById(
            'uploadedImageLoading'
        );


    const uploadedImageEmpty =
        document.getElementById(
            'uploadedImageEmpty'
        );


    const uploadedImageTableWrapper =
        document.getElementById(
            'uploadedImageTableWrapper'
        );


    const uploadedImageCount =
        document.getElementById(
            'uploadedImageCount'
        );


    const btnClearUploadedImageSearch =
        document.getElementById(
            'btnClearUploadedImageSearch'
        );


    const uploadedImageModalElement =
        document.getElementById(
            'uploadedImageModal'
        );


    let uploadedImages = [];


    let uploadedImageModal = null;


    /*
    |--------------------------------------------------------------------------
    | Bootstrap Modal
    |--------------------------------------------------------------------------
    */

    function getUploadedImageModal() {

        if (
            typeof bootstrap === 'undefined' ||
            !bootstrap.Modal
        ) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text:
                    'Bootstrap JavaScript is not loaded.'
            });

            return null;

        }


        if (!uploadedImageModal) {

            uploadedImageModal =
                bootstrap.Modal.getOrCreateInstance(
                    uploadedImageModalElement
                );

        }


        return uploadedImageModal;

    }


    /*
    |--------------------------------------------------------------------------
    | Load Uploaded Images
    |--------------------------------------------------------------------------
    */

    async function loadUploadedImages() {

        uploadedImageLoading.style.display =
            'block';

        uploadedImageEmpty.style.display =
            'none';

        uploadedImageTableWrapper.style.display =
            'none';


        uploadedImageList.innerHTML =
            '';


        try {

            const response =
                await fetch(
                    "{{ route('design-specifications.uploaded-images') }}",
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


            if (
                !response.ok ||
                !result.success
            ) {

                throw new Error(
                    result.message ||
                    'Unable to load uploaded images.'
                );

            }


            uploadedImages =
                result.data || [];


            renderUploadedImages();


        } catch (error) {

            console.error(
                'UPLOADED IMAGE ERROR:',
                error
            );


            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });


        } finally {

            uploadedImageLoading.style.display =
                'none';

        }

    }


/*
|--------------------------------------------------------------------------
| Render Uploaded Images
|--------------------------------------------------------------------------
*/

function renderUploadedImages() {

    uploadedImageList.innerHTML =
        '';


    const search =
        String(
            uploadedImageSearch.value || ''
        )
        .trim()
        .toLowerCase();


    const filtered =
        uploadedImages.filter(
            function (row) {

                return (

                    String(
                        row.download_filename || ''
                    )
                    .toLowerCase()
                    .includes(search)

                    ||

                    String(
                        row.garment_name || ''
                    )
                    .toLowerCase()
                    .includes(search)

                    ||

                    String(
                        row.garment_type || ''
                    )
                    .toLowerCase()
                    .includes(search)

                    ||

                    String(
                        row.user_name || ''
                    )
                    .toLowerCase()
                    .includes(search)

                );

            }
        );


    uploadedImageCount.textContent =
        filtered.length +
        ' image' +
        (
            filtered.length === 1
                ? ''
                : 's'
        );


    if (!filtered.length) {

        uploadedImageEmpty.style.display =
            'block';

        uploadedImageTableWrapper.style.display =
            'none';

        return;

    }


    uploadedImageEmpty.style.display =
        'none';

    uploadedImageTableWrapper.style.display =
        'block';


    filtered.forEach(
        function (row, index) {

            const tr =
                document.createElement(
                    'tr'
                );


            tr.innerHTML = `

                <td>
                    ${index + 1}
                </td>


                <td>

                    <img
                        src="${escapeHtml(row.image_url)}"
                        class="uploaded-image-thumb"
                        alt="Uploaded Image"
                        style="
                            width:110px;
                            height:110px;
                            object-fit:cover;
                            border-radius:8px;
                            border:1px solid #dee2e6;
                        "
                        onerror="
                            this.style.display='none';
                        "
                    >

                </td>


                <td>

                    <div class="fw-semibold">
                        ${escapeHtml(
                            row.download_filename || '-'
                        )}
                    </div>

                    <small class="text-muted">
                        ID: ${row.sno}
                    </small>

                </td>


                <td>
                    ${escapeHtml(
                        row.garment_name || '-'
                    )}
                </td>


                <td>
                    ${escapeHtml(
                        row.garment_type || '-'
                    )}
                </td>


                <td>
                    ${escapeHtml(
                        row.user_name || '-'
                    )}
                </td>


                <td>

                    <button
                        type="button"
                        class="btn btn-success btn-sm btn-select-uploaded-image"
                        data-id="${row.sno}"
                    >

                        <i class="bi bi-check-lg me-1"></i>

                        Select

                    </button>

                </td>

            `;


            uploadedImageList.appendChild(
                tr
            );

        }
    );

}


/*
|--------------------------------------------------------------------------
| Open Modal
|--------------------------------------------------------------------------
*/

if (btnUseUploadedImage) {

    btnUseUploadedImage.addEventListener(
        'click',
        function () {

            const modal =
                getUploadedImageModal();


            if (!modal) {
                return;
            }


            uploadedImageSearch.value =
                '';


            uploadedImages =
                [];


            modal.show();


            loadUploadedImages();

        }
    );

}


if (btnUseSupplierdImage) {

    btnUseSupplierdImage.addEventListener(
        'click',
        function () {

              const modalElement =
            document.getElementById(
                'supplierProductModal'
            );

        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

            modal.show();


            /*
            |--------------------------------------------------------------------------
            | LOAD SUPPLIER PRODUCTS
            |--------------------------------------------------------------------------
            */

            loadSupplierProducts();

        }
    );

}

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

if (uploadedImageSearch) {

    uploadedImageSearch.addEventListener(
        'input',
        function () {

            renderUploadedImages();

        }
    );

}


/*
|--------------------------------------------------------------------------
| Clear Search
|--------------------------------------------------------------------------
*/

if (btnClearUploadedImageSearch) {

    btnClearUploadedImageSearch.addEventListener(
        'click',
        function () {

            uploadedImageSearch.value =
                '';

            renderUploadedImages();

            uploadedImageSearch.focus();

        }
    );

}


/*
|--------------------------------------------------------------------------
| Select Uploaded Image
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'click',
    async function (event) {

        const button =
            event.target.closest(
                '.btn-select-uploaded-image'
            );


        if (!button) {
            return;
        }


        const imageId =
            String(
                button.dataset.id || ''
            );


        if (!imageId) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text:
                    'Uploaded image ID is missing.'
            });

            return;

        }


        const row =
            uploadedImages.find(
                function (item) {

                    return String(
                        item.sno
                    ) === imageId;

                }
            );


        if (!row) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text:
                    'Uploaded image could not be found.'
            });

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Show loading on button
        |--------------------------------------------------------------------------
        */

        const oldHtml =
            button.innerHTML;


        button.disabled =
            true;


        button.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-1">
            </span>
            Loading...
        `;


        try {

            /*
            |--------------------------------------------------------------------------
            | Download image as Blob
            |--------------------------------------------------------------------------
            */

            const response =
                await fetch(
                    row.image_url
                );


            if (!response.ok) {

                throw new Error(
                    'Unable to download selected image.'
                );

            }


            const blob =
                await response.blob();


            /*
            |--------------------------------------------------------------------------
            | Get file name
            |--------------------------------------------------------------------------
            */

            let fileName =
                row.download_filename ||
                row.main_image ||
                'uploaded-image.webp';


            /*
            |--------------------------------------------------------------------------
            | Make sure extension exists
            |--------------------------------------------------------------------------
            */

            if (
                !/\.(jpg|jpeg|png|webp)$/i
                    .test(fileName)
            ) {

                let extension =
                    'webp';


                if (
                    blob.type ===
                    'image/jpeg'
                ) {

                    extension =
                        'jpg';

                } else if (
                    blob.type ===
                    'image/png'
                ) {

                    extension =
                        'png';

                }


                fileName +=
                    '.' + extension;

            }


            /*
            |--------------------------------------------------------------------------
            | Create File object
            |--------------------------------------------------------------------------
            */

            const file =
                new File(
                    [blob],
                    fileName,
                    {
                        type:
                            blob.type ||
                            'image/webp',

                        lastModified:
                            Date.now()
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Add to selectedFiles
            |--------------------------------------------------------------------------
            */

            selectedFiles = [
                file
            ];


            /*
            |--------------------------------------------------------------------------
            | Put File into existing input
            |--------------------------------------------------------------------------
            */

            if (imageInput) {

                const dataTransfer =
                    new DataTransfer();


                dataTransfer.items.add(
                    file
                );


                imageInput.files =
                    dataTransfer.files;

            }


            /*
            |--------------------------------------------------------------------------
            | Use existing preview system
            |--------------------------------------------------------------------------
            */

            renderSelectedImages();


            /*
            |--------------------------------------------------------------------------
            | Close modal
            |--------------------------------------------------------------------------
            */

            const modal =
                getUploadedImageModal();


            if (modal) {

                modal.hide();

            }

            // Show New Specification after selecting uploaded image
            const newSection =
                document.getElementById('newSpecificationSection');

            const allSection =
                document.getElementById('allSpecificationsSection');

            if (newSection) {
                newSection.style.display = '';
            }

            if (allSection) {
                allSection.style.display = 'none';
            }


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            Swal.fire({
                icon: 'success',
                title: 'Image Selected',
                text:
                    'Uploaded image has been attached successfully.',
                timer: 1400,
                showConfirmButton: false
            });


        } catch (error) {

            console.error(
                'SELECT UPLOADED IMAGE ERROR:',
                error
            );


            Swal.fire({
                icon: 'error',
                title: 'Unable to Select Image',
                text:
                    error.message
            });


        } finally {

            button.disabled =
                false;

            button.innerHTML =
                oldHtml;

        }

    }
);

document.addEventListener(
    'click',
    async function (event) {

        const button =
            event.target.closest(
                '.btn-select-supplier-product'
            );


        /*
        |--------------------------------------------------------------------------
        | NOT SUPPLIER PRODUCT BUTTON
        |--------------------------------------------------------------------------
        */

        if (!button) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | GET PRODUCT FROM CLICKED BUTTON
        |--------------------------------------------------------------------------
        */

        const product =
            $(button).data('product');

        const productId =
        button.getAttribute('data-product-id') || '';

        const loginSupplierId =
            button.getAttribute('data-product-loginsupplerid') || '';

        console.log('Selected Product ID:', productId);
        console.log('Selected Login Supplier ID:', loginSupplierId);


        /*
        |--------------------------------------------------------------------------
        | CHECK PRODUCT
        |--------------------------------------------------------------------------
        */

        if (!product) {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Supplier product data not found.'
            });

            return;
        }

        // Show New Specification after selecting supplier product
const newSection =
    document.getElementById('newSpecificationSection');

const allSection =
    document.getElementById('allSpecificationsSection');

if (newSection) {
    newSection.style.display = '';
}

if (allSection) {
    allSection.style.display = 'none';
}


        /*
        |--------------------------------------------------------------------------
        | DEBUG
        |--------------------------------------------------------------------------
        */

        console.log(
            'Selected Supplier Product:',
            product
        );


        /*
        |--------------------------------------------------------------------------
        | SELECT SUPPLIER PRODUCT
        |--------------------------------------------------------------------------
        */

        selectSupplierProduct(
            product,
            productId,
            loginSupplierId
        );

    }
);

                /*
                |--------------------------------------------------------------------------
                | Existing images of edited specification
                |--------------------------------------------------------------------------
                */
             

                /*
                * Pagination state
                *
                * The API returns Laravel pagination data:
                * data, current_page, last_page, per_page, total, from, to.
                */
                let allSpecifications = [];
                let currentPage = 1;
                let currentPerPage = 20;
                let currentSearch = '';
                let searchTimer = null;

                const pagination =
                    document.getElementById(
                        'specificationPagination'
                    );

                const paginationInfo =
                    document.getElementById(
                        'paginationInfo'
                    );

                const paginationList =
                    document.getElementById(
                        'specificationPaginationList'
                    );

                const perPageSelect =
                    document.getElementById(
                        'specificationPerPage'
                    );



                /* =====================================================
                SHOW NEW FORM
                ====================================================== */

                function showNewSpecification() {

                    newSection.style.display = '';

                    allSection.style.display = 'none';

                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                }



                /* =====================================================
                SHOW ALL
                ====================================================== */

                function showAllSpecificationSection() {

                    newSection.style.display = 'none';

                    allSection.style.display = '';

                    loadSpecifications();

                }



                /* =====================================================
                BUTTON EVENTS
                ====================================================== */

                if (btnNew) {

                    btnNew.addEventListener(
                        'click',
                        function () {

                            showNewSpecification();

                        }
                    );

                }


                if (btnShowAll) {

                    btnShowAll.addEventListener(
                        'click',
                        function () {

                            showAllSpecificationSection();

                        }
                    );

                }


                if (btnClear) {

                    btnClear.addEventListener(
                        'click',
                        resetForm
                    );

                }


                if (btnCancel) {

                    btnCancel.addEventListener(
                        'click',
                        resetForm
                    );

                }


                if (btnRefresh) {

                    btnRefresh.addEventListener(
                        'click',
                        loadSpecifications
                    );

                }



                /* =====================================================
                IMAGE FILE SELECTION
                ====================================================== */

                if (imageInput) {

        imageInput.addEventListener(
            'change',
            function () {

                const files =
                    Array.from(
                        this.files || []
                    );


                /*
                * No image selected
                */
                if (!files.length) {

                    selectedFiles = [];

                    renderSelectedImages();

                    return;
                }


                /*
                * Store selected files
                */
                selectedFiles = files;


                /*
                * Render image previews
                */
                renderSelectedImages();


                /*
                * Automatically run
                * Generate Content
                */
                const generateBtn =
                    document.getElementById(
                        'generateBtn'
                    );


                if (generateBtn) {

                    /*
                    * Wait until FileReader has
                    * created the preview <img>
                    */
                    setTimeout(function () {

                        //generateBtn.click();

                    }, 300);

                }

            }
        );

        }



                /* =====================================================
                RENDER SELECTED IMAGES
                ====================================================== */

            /*
                |--------------------------------------------------------------------------
                | RENDER EXISTING + NEW IMAGES
                |--------------------------------------------------------------------------
                */

                function renderSelectedImages() {

                    if (!selectedImagePreview) {
                        return;
                    }

                    selectedImagePreview.innerHTML = '';


                    /*
                    |--------------------------------------------------------------------------
                    | EXISTING DATABASE IMAGES
                    |--------------------------------------------------------------------------
                    */

                    existingImages.forEach(
                        function (imageUrl, index) {

                            const item =
                                document.createElement('div');

                            item.className =
                                'selected-image-item existing-image-item';


                            item.innerHTML = `
                                <img
                                    src="${escapeHtml(imageUrl)}"
                                    alt="Existing Image"
                                    onerror="this.style.display='none';"
                                >

                                <span class="existing-image-badge">
                                    Existing
                                </span>
                            `;


                            selectedImagePreview.appendChild(
                                item
                            );

                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | NEW SELECTED IMAGES
                    |--------------------------------------------------------------------------
                    */

                    selectedFiles.forEach(
                        function (file, index) {

                            const reader =
                                new FileReader();


                            reader.onload =
                                function (event) {

                                    const item =
                                        document.createElement(
                                            'div'
                                        );


                                    item.className =
                                        'selected-image-item new-image-item';


                                    item.innerHTML = `
                                        <img
                                            src="${event.target.result}"
                                            alt="New Image"
                                        >

                                        <button
                                            type="button"
                                            class="remove-selected-image"
                                            data-index="${index}">

                                            <i class="bi bi-x"></i>

                                        </button>

                                        <span class="new-image-badge">
                                            New
                                        </span>
                                    `;


                                    selectedImagePreview
                                        .appendChild(item);


                                    const removeButton =
                                        item.querySelector(
                                            '.remove-selected-image'
                                        );


                                    if (removeButton) {

                                        removeButton.addEventListener(
                                            'click',
                                            function () {

                                                const removeIndex =
                                                    Number(
                                                        this.dataset.index
                                                    );


                                                selectedFiles.splice(
                                                    removeIndex,
                                                    1
                                                );


                                                renderSelectedImages();

                                            }
                                        );

                                    }

                                };


                            reader.readAsDataURL(file);

                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SHOW / HIDE SECTION
                    |--------------------------------------------------------------------------
                    */

                    if (
                        existingImages.length > 0 ||
                        selectedFiles.length > 0
                    ) {

                        selectedImagesSection.style.display =
                            '';

                    } else {

                        selectedImagesSection.style.display =
                            'none';

                    }

                }



                /* =====================================================
                RESET FORM
                ====================================================== */

                    /*
        |--------------------------------------------------------------------------
        | RESET FORM
        |--------------------------------------------------------------------------
        */

        function resetForm() {

            /*
            |--------------------------------------------------------------------------
            | CLEAR SELECTS
            |--------------------------------------------------------------------------
            */

            const selects =
                newSection.querySelectorAll(
                    'select'
                );


            selects.forEach(
                function (select) {

                    if (
                        typeof jQuery !==
                            'undefined' &&
                        jQuery.fn.select2 &&
                        jQuery(select).hasClass(
                            'select2-hidden-accessible'
                        )
                    ) {

                        jQuery(select)
                            .val(null)
                            .trigger('change');

                    } else {

                        select.value =
                            '';

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR TEXTAREAS
            |--------------------------------------------------------------------------
            */

            const textareas =
                newSection.querySelectorAll(
                    'textarea'
                );


            textareas.forEach(
                function (textarea) {

                    textarea.value =
                        '';

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR INPUTS
            |--------------------------------------------------------------------------
            */

            const inputs =
                newSection.querySelectorAll(
                    'input'
                );


            inputs.forEach(
                function (input) {

                    if (
                        input.type ===
                        'file'
                    ) {

                        input.value =
                            '';

                    } else {

                        input.value =
                            '';

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR SELECTED FILES
            |--------------------------------------------------------------------------
            */

            selectedFiles = [];


            if (imageInput) {

                imageInput.value =
                    '';

            }


            renderSelectedImages();


            /*
            |--------------------------------------------------------------------------
            | CLEAR EDIT MODE
            |--------------------------------------------------------------------------
            */

            const editIdElement =
                document.getElementById(
                    'editSpecificationId'
                );


            if (editIdElement) {

                editIdElement.value =
                    '';

            }


            const editBarcodeElement =
                document.getElementById(
                    'editSpecificationBarcode'
                );


            if (editBarcodeElement) {

                editBarcodeElement.value =
                    '';

            }


            /*
            |--------------------------------------------------------------------------
            | RESET FORM TITLE
            |--------------------------------------------------------------------------
            */

            const formTitle =
                document.getElementById(
                    'specificationFormTitle'
                );


            if (formTitle) {

                formTitle.textContent =
                    'New Design Specification';

            }


            const formSubtitle =
                document.getElementById(
                    'specificationFormSubtitle'
                );


            if (formSubtitle) {

                formSubtitle.textContent =
                    'Enter garment design specification details';

            }


            /*
            |--------------------------------------------------------------------------
            | HIDE BARCODE
            |--------------------------------------------------------------------------
            */

            const barcodeBadge =
                document.getElementById(
                    'editBarcodeBadge'
                );


            if (barcodeBadge) {

                barcodeBadge.style.display =
                    'none';

            }


            const barcodeText =
                document.getElementById(
                    'editBarcodeText'
                );


            if (barcodeText) {

                barcodeText.textContent =
                    '';

            }


            /*
            |--------------------------------------------------------------------------
            | RESET SAVE BUTTON
            |--------------------------------------------------------------------------
            */

            const saveButton =
                document.getElementById(
                    'btnSaveSpecification'
                );


            if (saveButton) {

                saveButton.innerHTML =
                    '<i class="bi bi-check-lg me-1"></i> Save Specification';

            }


            /*
            |--------------------------------------------------------------------------
            | SHOW NEW FORM
            |--------------------------------------------------------------------------
            */

            showNewSpecification();

        }



                /* =====================================================
                LOAD SPECIFICATIONS
                ====================================================== */

                function loadSpecifications(
                    page = 1
                ) {

                    if (!cardsContainer) {
                        return;
                    }

                    currentPage = page;

                    cardsContainer.innerHTML = '';

                    empty.style.display = 'none';
                    loading.style.display = 'flex';

                    if (pagination) {
                        pagination.style.display = 'none';
                    }

                    const params = new URLSearchParams();

                    params.set('page', currentPage);
                    params.set('per_page', currentPerPage);

                    if (currentSearch) {
                        params.set('search', currentSearch);
                    }

                    fetch(
                        "{{ route('design-specifications.data') }}?" +
                        params.toString(),
                        {
                            method: 'GET',

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'application/json'
                            }
                        }
                    )
                    .then(
                        function (response) {

                            if (!response.ok) {

                                throw new Error(
                                    'Unable to load specifications.'
                                );

                            }

                            return response.json();

                        }
                    )
                    .then(
                        function (responseData) {

                            /*
                            * Support BOTH response formats:
                            *
                            * 1. Laravel paginate():
                            * {
                            *   data: [...],
                            *   current_page: 1,
                            *   last_page: 10,
                            *   per_page: 20,
                            *   total: 200,
                            *   from: 1,
                            *   to: 20
                            * }
                            *
                            * 2. Old API:
                            * [...]
                            */
                            let data = [];
                            let meta = null;

                            if (Array.isArray(responseData)) {

                                data = responseData;

                                meta = {
                                    current_page: 1,
                                    last_page: 1,
                                    per_page: data.length || currentPerPage,
                                    total: data.length,
                                    from: data.length ? 1 : 0,
                                    to: data.length
                                };

                            } else if (
                                responseData &&
                                Array.isArray(responseData.data)
                            ) {

                                data = responseData.data;

                                meta = responseData;

                            }

                            allSpecifications = data;

                            loading.style.display = 'none';

                            renderSpecifications(
                                allSpecifications,
                                meta
                            );

                            renderPagination(meta);

                        }
                    )
                    .catch(
                        function (error) {

                            console.error(error);

                            loading.style.display = 'none';

                            if (pagination) {
                                pagination.style.display = 'none';
                            }

                            cardsContainer.innerHTML = `

                                <div
                                    class="empty-state"
                                    style="grid-column:1/-1;">

                                    <div class="empty-icon">

                                        <i class="bi bi-exclamation-triangle"></i>

                                    </div>

                                    <h6>
                                        Unable to load specifications
                                    </h6>

                                    <p>
                                        Please try again.
                                    </p>

                                </div>

                            `;

                        }
                    );

                }


                /* =====================================================
                RENDER PAGINATION
                ====================================================== */

                function renderPagination(meta) {

                    if (
                        !pagination ||
                        !paginationList ||
                        !paginationInfo
                    ) {
                        return;
                    }

                    paginationList.innerHTML = '';

                    if (
                        !meta ||
                        !meta.total ||
                        Number(meta.last_page || 1) <= 1
                    ) {

                        pagination.style.display = 'none';

                        return;
                    }

                    const current =
                        Number(meta.current_page || 1);

                    const last =
                        Number(meta.last_page || 1);

                    const from =
                        Number(meta.from || 0);

                    const to =
                        Number(meta.to || 0);

                    const total =
                        Number(meta.total || 0);

                    paginationInfo.textContent =
                        `Showing ${from} to ${to} of ${total} records`;

                    pagination.style.display = 'flex';


                    function addPage(
                        label,
                        page,
                        disabled = false,
                        active = false
                    ) {

                        const li =
                            document.createElement('li');

                        li.className =
                            'page-item' +
                            (disabled ? ' disabled' : '') +
                            (active ? ' active' : '');

                        const button =
                            document.createElement('button');

                        button.type = 'button';

                        button.className =
                            'page-link';

                        button.textContent = label;

                        button.disabled =
                            disabled;

                        if (!disabled && !active) {

                            button.addEventListener(
                                'click',
                                function () {
                                    loadSpecifications(page);
                                }
                            );

                        }

                        li.appendChild(button);

                        paginationList.appendChild(li);

                    }


                    /*
                    * Previous
                    */
                    addPage(
                        '‹',
                        current - 1,
                        current <= 1
                    );


                    /*
                    * Page numbers.
                    *
                    * Show first page, nearby pages and last page.
                    */
                    let pages = [];

                    if (last <= 7) {

                        for (
                            let i = 1;
                            i <= last;
                            i++
                        ) {
                            pages.push(i);
                        }

                    } else {

                        pages.push(1);

                        if (current > 4) {
                            pages.push('...');
                        }

                        const start =
                            Math.max(2, current - 2);

                        const end =
                            Math.min(last - 1, current + 2);

                        for (
                            let i = start;
                            i <= end;
                            i++
                        ) {
                            pages.push(i);
                        }

                        if (current < last - 3) {
                            pages.push('...');
                        }

                        pages.push(last);

                    }


                    pages.forEach(
                        function (page) {

                            if (page === '...') {

                                const li =
                                    document.createElement('li');

                                li.className =
                                    'page-item disabled';

                                const span =
                                    document.createElement('span');

                                span.className =
                                    'page-link';

                                span.textContent =
                                    '...';

                                li.appendChild(span);

                                paginationList.appendChild(li);

                                return;
                            }

                            addPage(
                                page,
                                page,
                                false,
                                page === current
                            );

                        }
                    );


                    /*
                    * Next
                    */
                    addPage(
                        '›',
                        current + 1,
                        current >= last
                    );

                }


                /* =====================================================
                RENDER SPECIFICATION CARDS
                ====================================================== */

                function renderSpecifications(
                    specifications,
                    meta = null
                ) {

                    cardsContainer.innerHTML =
                        '';


                    if (!specifications.length) {

                        empty.style.display =
                            'flex';

                        return;

                    }


                    empty.style.display =
                        'none';


                    specifications.forEach(
                        function (specification, index) {

                            const rowNumber =
                                meta &&
                                Number(meta.from || 0)
                                    ? Number(meta.from) + index
                                    : index + 1;

                            cardsContainer.appendChild(
                                createSpecificationCard(
                                    specification,
                                    rowNumber
                                )
                            );

                        }
                    );

                }



                /* =====================================================
                CREATE SPECIFICATION CARD
                ====================================================== */

                function createSpecificationCard(
                    specification,
                    index
                ) {

                    const card =
                        document.createElement(
                            'div'
                        );


                    card.className =
                        'specification-card';


                    const imageUrl =
                        getSpecificationImage(
                            specification
                        );


                    const barcode =
                        specification.barcode ||
                        '-';


                    const sku =
                        specification.sku ||
                        '-';


                    const designer =
                        specification.designer_name_text ||
                        specification.designer_name ||
                        '-';


                    const itemType =
                        specification.item_type_text ||
                        specification.item_type ||
                        '-';


                    const gender =
                        specification.gender_text ||
                        specification.gender ||
                        '-';


                    const itemName =
                            specification.item_name_text ||
                            specification.item_name ||
                            '-';


                    const composition =
                            specification.composition_text ||
                            specification.composition ||
                            '-';


                    const colour =
                            specification.colour_text ||
                            specification.colour ||
                            '-';


                    const size =
                            specification.size_text ||
                            specification.sizes ||
                            '-';


                    const status =
                        specification.status ||
                        'Pending';


                    const statusClass =
                        String(status).toLowerCase() === 'done'
                            ? 'status-success'
                            : 'status-pending';


                    let imageHtml = '';


                    if (imageUrl) {

                        imageHtml = `

                            <div
                                class="specification-image-wrapper">

                                <img
                                    src="${escapeHtml(imageUrl)}"
                                    class="specification-image specification-image-click"
                                    data-image="${escapeHtml(imageUrl)}"
                                    alt="Design Image"
                                    loading="lazy"
                                    onerror="this.parentElement.innerHTML =
                                        '<div class=&quot;no-image&quot;><i class=&quot;bi bi-image&quot;></i><span>Image unavailable</span></div>';"
                                >

                            </div>

                        `;

                    } else {

                        imageHtml = `

                            <div
                                class="specification-image-wrapper">

                                <div class="no-image">

                                    <i class="bi bi-image"></i>

                                    <span>
                                        No Image
                                    </span>

                                </div>

                            </div>

                        `;

                    }


                    card.innerHTML = `

                        ${imageHtml}


                        <div class="specification-card-body">

                            <div>

                                <span class="product-barcode">

                                    <i class="bi bi-upc-scan"></i>

                                    ${escapeHtml(barcode)}

                                </span>


                                <span class="product-sku">

                                    SKU:
                                    ${escapeHtml(sku)}

                                </span>

                            </div>


                            <div class="product-title">

                                ${escapeHtml(itemName)}

                            </div>


                            <div class="product-info-grid">


                                <div class="product-info-item">

                                    <span class="product-info-label">
                                        Designer
                                    </span>

                                    <span
                                        class="product-info-value"
                                        title="${escapeHtml(designer)}">

                                        ${escapeHtml(designer)}

                                    </span>

                                </div>


                                <div class="product-info-item">

                                    <span class="product-info-label">
                                        Item Type
                                    </span>

                                    <span
                                        class="product-info-value"
                                        title="${escapeHtml(itemType)}">

                                        ${escapeHtml(itemType)}

                                    </span>

                                </div>


                                <div class="product-info-item">

                                    <span class="product-info-label">
                                        Gender
                                    </span>

                                    <span
                                        class="product-info-value"
                                        title="${escapeHtml(gender)}">

                                        ${escapeHtml(gender)}

                                    </span>

                                </div>


                                <div class="product-info-item">

                                    <span class="product-info-label">
                                        Colour
                                    </span>

                                    <span
                                        class="product-info-value"
                                        title="${escapeHtml(colour)}">

                                        ${escapeHtml(colour)}

                                    </span>

                                </div>


                                <div class="product-info-item">

                                    <span class="product-info-label">
                                        Size
                                    </span>

                                    <span
                                        class="product-info-value"
                                        title="${escapeHtml(size)}">

                                        ${escapeHtml(size)}

                                    </span>

                                </div>


                                <div class="product-info-item">

                                    <span class="product-info-label">
                                        # 
                                    </span>

                                    <span
                                        class="product-info-value">

                                        ${index}

                                    </span>

                                </div>

                            </div>


                            <div class="product-status">

                                <span
                                    class="status-badge ${statusClass}">

                                    ${escapeHtml(status)}

                                </span>

                            </div>


                            <div class="specification-actions">


                                <button
                                    type="button"
                                    class="specification-action-btn btn-view-spec"
                                    data-id="${escapeHtml(
                                        specification.sno ||
                                        specification.id ||
                                        ''
                                    )}">

                                    <i class="bi bi-eye"></i>
                                    View

                                </button>


                                <button
                                    type="button"
                                    class="specification-action-btn btn-edit-spec"
                                    data-id="${escapeHtml(
                                        specification.sno ||
                                        specification.id ||
                                        ''
                                    )}">

                                    <i class="bi bi-pencil"></i>
                                    Edit

                                </button>


                            <button
                                type="button"
                                class="specification-action-btn btn-barcode-spec"

                                data-id="${escapeHtml(
                                    specification.sno ||
                                    specification.id ||
                                    ''
                                )}"

                                data-barcode="${escapeHtml(
                                    String(barcode || '')
                                )}"

                                data-itemname="${escapeHtml(
                                    String(itemName || '')
                                )}"

                                data-composition="${escapeHtml(
                                    String(composition || '')
                                )}"

                                data-colour="${escapeHtml(
                                    String(colour || '')
                                )}"

                                data-size="${escapeHtml(
                                    String(size || '')
                                )}"

                                data-image="${escapeHtml(
                                    String(imageUrl || '')
                                )}">

                                <i class="bi bi-upc-scan"></i>

                                Barcode

                            </button>

                            <div class="barcode-selection-row">

    <div class="barcode-qty-wrapper">

        <label class="barcode-qty-label">
            Barcode Qty
        </label>

        <input
            type="number"
            class="barcode-qty-input"
            min="1"
            step="1"
            value="1"
        >

    </div>


    <div class="barcode-check-wrapper">

        <label class="barcode-check-label">

            <input
                type="checkbox"
                class="barcode-select-checkbox"

                data-id="${escapeHtml(
                    String(
                        specification.sno ||
                        specification.id ||
                        ''
                    )
                )}"

                data-barcode="${escapeHtml(
                    String(barcode || '')
                )}"

                data-itemname="${escapeHtml(
                    String(itemName || '-')
                )}"

                data-composition="${escapeHtml(
                    String(composition || '-')
                )}"

                data-colour="${escapeHtml(
                    String(colour || '-')
                )}"

                data-size="${escapeHtml(
                    String(size || '-')
                )}"

                data-image="${escapeHtml(
                    String(imageUrl || '')
                )}"
            >

            <span>
                Select for Barcode
            </span>

        </label>

    </div>

</div>
                            



                                </div>

                            </div>

                        `;


                        /* =================================================
                        IMAGE CLICK
                        ================================================== */

                        const image =
                            card.querySelector(
                                '.specification-image-click'
                            );


                        if (image) {

                            image.addEventListener(
                                'click',
                                function () {

                                    openLargeImage(
                                        this.dataset.image
                                    );

                                }
                            );

                        }


                        /* =========================================================
                    OPEN SPECIFICATION VIEW
                ========================================================= */

                function openSpecificationView(
                    specification
                ) {

                    const value = function (
                        field,
                        fallback = '-'
                    ) {

                        const result =
                            specification[field];

                        if (
                            result === null ||
                            result === undefined ||
                            String(result).trim() === ''
                        ) {

                            return fallback;
                        }

                        return String(result);

                    };


                    /*
                    * Basic
                    */

                    document.getElementById(
                        'viewBarcode'
                    ).textContent =
                        value('barcode');


                    document.getElementById(
                        'viewSku'
                    ).textContent =
                        value('sku');


                    document.getElementById(
                        'viewItemName'
                    ).textContent =
                        value(
                            'item_name_text',
                            value('item_name')
                        );


                    document.getElementById(
                        'viewItemName2'
                    ).textContent =
                        value(
                            'item_name_text',
                            value('item_name')
                        );


                    document.getElementById(
                        'viewDesigner'
                    ).textContent =
                        value(
                            'designer_name_text',
                            value('designer_name')
                        );


                    document.getElementById(
                        'viewItemType'
                    ).textContent =
                        value(
                            'item_type_text',
                            value('item_type')
                        );


                    document.getElementById(
                        'viewGender'
                    ).textContent =
                        value(
                            'gender_text',
                            value('gender')
                        );


                    document.getElementById(
                        'viewComposition'
                    ).textContent =
                        value(
                            'composition_text',
                            value('composition')
                        );


                    document.getElementById(
                        'viewColour'
                    ).textContent =
                        value(
                            'colour_text',
                            value('colour')
                        );


                    document.getElementById(
                        'viewSize'
                    ).textContent =
                        value(
                            'size_text',
                            value('sizes')
                        );


                    /*
                    * Optional specification fields
                    */

                    document.getElementById(
                        'viewEmbellishment'
                    ).textContent =
                        value(
                            'embellishment_text',
                            value('embellishment')
                        );


                    document.getElementById(
                        'viewManufacturingProcess'
                    ).textContent =
                        value(
                            'manufacturing_process_text',
                            value('manufacturing_process')
                        );


                    document.getElementById(
                        'viewCraftsman'
                    ).textContent =
                        value(
                            'craftsman_text',
                            value('craftsman')
                        );


                    document.getElementById(
                        'viewCraftsmanCode'
                    ).textContent =
                        value('craftsman_code');


                    document.getElementById(
                        'viewManufacture'
                    ).textContent =
                        value(
                            'manufacture_text',
                            value('manufecture')
                        );


                    document.getElementById(
                        'viewClient'
                    ).textContent =
                        value(
                            'client_text',
                            value('client')
                        );


                    document.getElementById(
                        'viewClientReference'
                    ).textContent =
                        value('clientreference');


                    /*
                    * Company context
                    */

                    document.getElementById(
                        'viewCompany'
                    ).textContent =
                        value('company_name');


                    document.getElementById(
                        'viewSubCompany'
                    ).textContent =
                        value('subcompany_name');


                    document.getElementById(
                        'viewProject'
                    ).textContent =
                        value('project_name');


                    /*
                    * Status
                    */

                    const status =
                        value(
                            'status',
                            'Pending'
                        );


                    document.getElementById(
                        'viewStatus'
                    ).innerHTML = `

                        <span class="
                            status-badge
                            ${
                                status.toLowerCase() === 'done'
                                    ? 'status-success'
                                    : 'status-pending'
                            }
                        ">
                            ${escapeHtml(status)}
                        </span>

                    `;


                    /*
                    * Product Image
                    */

                    const imageContainer =
                        document.getElementById(
                            'viewSpecificationImage'
                        );


                    const imageUrl =
                        getSpecificationImage(
                            specification
                        );


                    if (imageUrl) {

                        imageContainer.innerHTML = `

                            <img
                                src="${escapeHtml(imageUrl)}"
                                alt="${escapeHtml(
                                    value(
                                        'item_name_text',
                                        'Design Image'
                                    )
                                )}"
                                onclick="openLargeImage(
                                    this.src
                                )"
                                onerror="
                                    this.parentElement.innerHTML =
                                    '<div class=&quot;no-image&quot;>
                                        <i class=&quot;bi bi-image&quot;></i>
                                        <span>Image unavailable</span>
                                    </div>';
                                "
                            >

                        `;

                    } else {

                        imageContainer.innerHTML = `

                            <div class="no-image">

                                <i class="bi bi-image"></i>

                                <span>
                                    No Image
                                </span>

                            </div>

                        `;

                    }


                    /*
|--------------------------------------------------------------------------
| DESIGN SUB IMAGES
|--------------------------------------------------------------------------
*/

const subImagesSection =
    document.getElementById(
        'viewSubImagesSection'
    );

const subImagesContainer =
    document.getElementById(
        'viewSubImages'
    );

if (
    subImagesSection &&
    subImagesContainer
) {

    const subImages =
        getExistingSpecificationSubImages(
            specification
        );

    subImagesContainer.innerHTML = '';

    if (
        subImages.length > 0
    ) {

        subImages.forEach(
            function (image, index) {

                const card =
                    document.createElement(
                        'div'
                    );

                card.className =
                    'view-sub-image-card';

                const imageName =
                    image.split('/').pop();

                card.innerHTML = `

                    <div
                        class="view-sub-image-wrapper"
                    >

                        <img
                            src="${escapeHtml(image)}"
                            alt="Design Sub Image ${index + 1}"
                            onclick="openLargeImage(this.src)"
                            loading="lazy"
                            onerror="
                                this.parentElement.innerHTML =
                                '<div class=&quot;no-image&quot;>
                                    <i class=&quot;bi bi-image&quot;></i>
                                    <span>Image unavailable</span>
                                </div>';
                            "
                        >

                    </div>

                    <div
                        class="view-sub-image-name"
                        title="${escapeHtml(imageName)}"
                    >
                        ${escapeHtml(imageName)}
                    </div>

                `;

                subImagesContainer.appendChild(
                    card
                );

            }
        );

        subImagesSection.style.display =
            '';

    } else {

        subImagesSection.style.display =
            'none';

    }
}


                    /*
                    * AI Product Information
                    */

                    const aiFields = {

                        viewAIProductName:
                            'AI_product_name',

                        viewAIProductDescription:
                            'AI_product_description',

                        viewAIMetaTitle:
                            'AI_Metatitle',

                        viewAIMetaKeywords:
                            'AI_Metakeywards',

                        viewAIMetaDescription:
                            'AI_Metadescription',

                        viewAIProductTags:
                            'AI_Producttag',

                        viewAIImageAltText:
                            'AI_Imagealttext'

                    };


                    let hasAIData = false;


                    Object.keys(aiFields).forEach(
                        function (elementId) {

                            const field =
                                aiFields[elementId];

                            const text =
                                value(
                                    field,
                                    ''
                                );


                            if (text) {

                                hasAIData = true;

                            }


                            const element =
                                document.getElementById(
                                    elementId
                                );


                            if (element) {

                                element.textContent =
                                    text || '-';

                            }

                        }
                    );


                    /*
                    * Hide AI section if no AI data exists
                    */

                    const aiSection =
                        document.getElementById(
                            'viewAISection'
                        );


                    if (aiSection) {

                        aiSection.style.display =
                            hasAIData
                                ? ''
                                : 'none';

                    }


                    /*
                    * Open Bootstrap modal
                    */

                    const modalElement =
                        document.getElementById(
                            'designSpecificationViewModal'
                        );


                    const modal =
                        bootstrap.Modal.getOrCreateInstance(
                            modalElement
                        );


                    modal.show();

                }


                        /* =================================================
                        VIEW
                        ================================================== */

                        /* =================================================
        
                VIEW SPECIFICATION
                ================================================= */



                const viewButton =
                    card.querySelector(
                        '.btn-view-spec'
                    );

                if (viewButton) {

                    viewButton.addEventListener(
                        'click',
                        function () {

                            const id =
                                this.dataset.id;

                            const specification =
                                allSpecifications.find(
                                    function (item) {

                                        return String(
                                            item.sno ||
                                            item.id ||
                                            ''
                                        ) === String(id);

                                    }
                                );


                            if (!specification) {

                                alert(
                                    'Unable to find specification data.'
                                );

                                return;
                            }


                            openSpecificationView(
                                specification
                            );

                        }
                    );

                }


                               

                                /* =================================================
                                EDIT
                                ================================================= */

                            const editButton =
                                card.querySelector(
                                    '.btn-edit-spec'
                                );


                            if (editButton) {

                                editButton.addEventListener(
                                    'click',
                                    function () {

                                        const id =
                                            this.dataset.id;


                                        const specification =
                                            allSpecifications.find(
                                                function (item) {

                                                    return String(
                                                        item.sno ||
                                                        item.id ||
                                                        ''
                                                    ) === String(id);

                                                }
                                            );


                                        if (!specification) {

                                            alert(
                                                'Unable to find specification.'
                                            );

                                            return;

                                        }


                                        editSpecification(
                                            specification
                                        );

                                    }
                                );

                            }

                                /* =================================================
                                BARCODE
                                ================================================== */

                                const barcodeButton =
                                    card.querySelector(
                                        '.btn-barcode-spec'
                                    );


                                if (barcodeButton) {

                                    barcodeButton.addEventListener(
                                        'click',
                                        function () {

                                            console.log(
                                                'Barcode:',
                                                this.dataset.barcode
                                            );

                                        }
                                    );

                                }


                                return card;

                            }



                            /* =====================================================
                            SEARCH
                            ====================================================== */

                            if (search) {

                                search.addEventListener(
                                    'input',
                                    function () {

                                        const value =
                                            this.value
                                                .trim();

                                        clearTimeout(searchTimer);

                                        searchTimer =
                                            setTimeout(
                                                function () {

                                                    currentSearch =
                                                        value;

                                                    currentPage =
                                                        1;

                                                    loadSpecifications(
                                                        1
                                                    );

                                                },
                                                350
                                            );

                                    }
                                );

                            }


                            /* =====================================================
                            PER PAGE
                            ====================================================== */

                            if (perPageSelect) {

                                perPageSelect.addEventListener(
                                    'change',
                                    function () {

                                        currentPerPage =
                                            Number(this.value) || 20;

                                        currentPage = 1;

                                        loadSpecifications(1);

                                    }
                                );

                            }


                            /* =====================================================
                            IMAGE URL
                            ====================================================== */

                            function getSpecificationImage(specification) {

                        /*
                        * ============================================================
                        * 1. Controller-generated image URL
                        * ============================================================
                        *
                        * This is the preferred source.
                        *
                        * Example:
                        * http://127.0.0.1:8000/ItemsDesigner_Masterwithbarcode/
                        * 147921111111/image.jpg
                        */

                        if (
                            specification.image_url &&
                            typeof specification.image_url === 'string' &&
                            specification.image_url.trim() !== ''
                        ) {

                            return specification.image_url;

                        }


                        /*
                        * ============================================================
                        * 2. Other possible image fields
                        * ============================================================
                        */

                        let image =
                            specification.img_path ||
                            specification.image ||
                            specification.image_path ||
                            specification.main_image ||
                            '';


                        if (!image) {

                            return '';

                        }


                        /*
                        * ============================================================
                        * 3. JSON array
                        * ============================================================
                        */

                        if (
                            typeof image === 'string' &&
                            image.trim().startsWith('[')
                        ) {

                            try {

                                const parsed =
                                    JSON.parse(image);


                                if (
                                    Array.isArray(parsed) &&
                                    parsed.length
                                ) {

                                    image = parsed[0];

                                }

                            } catch (e) {

                                console.warn(
                                    'Image JSON parse error',
                                    e
                                );

                            }

                        }


                        /*
                        * ============================================================
                        * 4. JSON object
                        * ============================================================
                        */

                        if (
                            typeof image === 'object' &&
                            image !== null
                        ) {

                            if (
                                Array.isArray(image) &&
                                image.length
                            ) {

                                image = image[0];

                            } else if (
                                image.path
                            ) {

                                image = image.path;

                            } else if (
                                image.url
                            ) {

                                image = image.url;

                            }

                        }


                        if (!image) {

                            return '';

                        }


                        image = String(image).trim();


                        /*
                        * ============================================================
                        * 5. Already a complete URL
                        * ============================================================
                        */

                        if (
                            image.startsWith('http://') ||
                            image.startsWith('https://') ||
                            image.startsWith('data:')
                        ) {

                            return image;

                        }


                        /*
                        * ============================================================
                        * 6. Laravel storage path
                        * ============================================================
                        */

                        if (
                            image.startsWith('storage/')
                        ) {

                            return '/' + image;

                        }


                        if (
                            image.startsWith('/storage/')
                        ) {

                            return image;

                        }


                        /*
                        * ============================================================
                        * 7. Public image path
                        * ============================================================
                        */

                        if (
                            image.startsWith('/')
                        ) {

                            return image;

                        }


                        /*
                        * ============================================================
                        * 8. OLD ItemsDesigner_Masterwithbarcode PATH
                        * ============================================================
                        *
                        * Database example:
                        *
                        * ../../ItemsDesigner_Masterwithbarcode/147921111111/
                        *
                        * Do NOT add /storage/ to this.
                        */

                        if (
                            image.includes('ItemsDesigner_Masterwithbarcode/')
                        ) {

                            /*
                            * Find the part beginning with:
                            *
                            * ItemsDesigner_Masterwithbarcode/
                            */

                            const marker =
                                'ItemsDesigner_Masterwithbarcode/';

                            const position =
                                image.indexOf(marker);


                            if (position !== -1) {

                                return '/' +
                                    image.substring(position);

                            }

                        }


                        /*
                        * ============================================================
                        * 9. Default Laravel storage path
                        * ============================================================
                        */

                        return '/storage/' + image;

                    }


                        /* =====================================================
                        LARGE IMAGE
                        ====================================================== */

                        function openLargeImage(
                            imageUrl
                        ) {

                            const image =
                                document.getElementById(
                                    'largeDesignImage'
                                );


                            if (!image) {

                                return;

                            }


                            image.src =
                                imageUrl;


                            const modalElement =
                                document.getElementById(
                                    'designImageModal'
                                );


                            if (
                                typeof bootstrap !==
                                'undefined'
                            ) {

                                const modal =
                                    bootstrap.Modal.getOrCreateInstance(
                                        modalElement
                                    );


                                modal.show();

                            }

                        }



                        /* =====================================================
                        ESCAPE HTML
                        ====================================================== */

                        function escapeHtml(
                            value
                        ) {

                            if (
                                value === null ||
                                value === undefined
                            ) {

                                return '';

                            }


                            return String(value)
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

                        
                        /* =========================================================
                EDIT SPECIFICATION
                ========================================================= */

                /*
                |--------------------------------------------------------------------------
                | EDIT SPECIFICATION
                |--------------------------------------------------------------------------
                */

                /*
        |--------------------------------------------------------------------------
        | GET ALL EXISTING IMAGES FROM SPECIFICATION
        |--------------------------------------------------------------------------
        */

    function getExistingSpecificationImages(
        specification
    ) {

        let imageData =
            specification.img_path ||
            '';


        if (!imageData) {

            return [];

        }


        /*
        |--------------------------------------------------------------------------
        | Already an array
        |--------------------------------------------------------------------------
        */

        if (
            Array.isArray(imageData)
        ) {

            return imageData
                .map(
                    function (image) {

                        return getSpecificationImageFromPath(
                            image
                        );

                    }
                )
                .filter(Boolean);

        }


        /*
        |--------------------------------------------------------------------------
        | JSON string
        |--------------------------------------------------------------------------
        */

        if (
            typeof imageData === 'string'
        ) {

            imageData =
                imageData.trim();


            if (
                imageData.startsWith('[')
            ) {

                try {

                    const parsed =
                        JSON.parse(
                            imageData
                        );


                    if (
                        Array.isArray(parsed)
                    ) {

                        return parsed
                            .map(
                                function (image) {

                                    return getSpecificationImageFromPath(
                                        image
                                    );

                                }
                            )
                            .filter(Boolean);

                    }

                } catch (error) {

                    console.warn(
                        'Unable to parse existing images:',
                        error
                    );

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Single image
        |--------------------------------------------------------------------------
        */

        const singleImage =
            getSpecificationImageFromPath(
                imageData
            );


        return singleImage
            ? [singleImage]
            : [];

    }

    function getExistingSpecificationSubImages(
      specification
        ) {

            let imageData =
                specification.subimg_path || '';

            if (!imageData) {
                return [];
            }

            /*
            |--------------------------------------------------------------------------
            | Already an array
            |--------------------------------------------------------------------------
            */

            if (Array.isArray(imageData)) {

                return imageData
                    .map(function (image) {

                        return getSpecificationImageFromPath(
                            image
                        );

                    })
                    .filter(Boolean);
            }


            /*
            |--------------------------------------------------------------------------
            | JSON string
            |--------------------------------------------------------------------------
            */

            if (
                typeof imageData === 'string'
            ) {

                imageData =
                    imageData.trim();

                if (
                    imageData.startsWith('[')
                ) {

                    try {

                        const parsed =
                            JSON.parse(
                                imageData
                            );

                        if (
                            Array.isArray(parsed)
                        ) {

                            return parsed
                                .map(function (image) {

                                    return getSpecificationImageFromPath(
                                        image
                                    );

                                })
                                .filter(Boolean);
                        }

                    } catch (error) {

                        console.warn(
                            'Unable to parse sub images:',
                            error
                        );

                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Single image fallback
            |--------------------------------------------------------------------------
            */

            const singleImage =
                getSpecificationImageFromPath(
                    imageData
                );

            return singleImage
                ? [singleImage]
                : [];
        }

        /*
        |--------------------------------------------------------------------------
        | CONVERT DATABASE IMAGE PATH TO URL
        |--------------------------------------------------------------------------
        */

    function getSpecificationImageFromPath(
        image
    ) {

        if (
            !image
        ) {

            return '';

        }


        /*
        |--------------------------------------------------------------------------
        | Object
        |--------------------------------------------------------------------------
        */

        if (
            typeof image === 'object'
        ) {

            if (
                image.url
            ) {

                image =
                    image.url;

            } else if (
                image.path
            ) {

                image =
                    image.path;

            } else {

                return '';

            }

        }


        image =
            String(image)
                .trim();


        if (!image) {

            return '';

        }


        /*
        |--------------------------------------------------------------------------
        | Complete URL
        |--------------------------------------------------------------------------
        */

        if (
            image.startsWith(
                'http://'
            ) ||
            image.startsWith(
                'https://'
            ) ||
            image.startsWith(
                'data:'
            )
        ) {

            return image;

        }


        /*
        |--------------------------------------------------------------------------
        | OLD PATH
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | ItemsDesigner_Masterwithbarcode/
        | 147953564101461562/
        | image.webp
        |
        */

        if (
            image.includes(
                'ItemsDesigner_Masterwithbarcode/'
            )
        ) {

            const marker =
                'ItemsDesigner_Masterwithbarcode/';


            const position =
                image.indexOf(
                    marker
                );


            if (
                position !== -1
            ) {

                return '/' +
                    image.substring(
                        position
                    );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Laravel storage path
        |--------------------------------------------------------------------------
        */

        if (
            image.startsWith(
                '/storage/'
            )
        ) {

            return image;

        }


        if (
            image.startsWith(
                'storage/'
            )
        ) {

            return '/' +
                image;

        }


        /*
        |--------------------------------------------------------------------------
        | Public path
        |--------------------------------------------------------------------------
        */

        if (
            image.startsWith('/')
        ) {

            return image;

        }


        /*
        |--------------------------------------------------------------------------
        | Default Laravel storage
        |--------------------------------------------------------------------------
        */

        return '/storage/' +
            image;

    }

        function editSpecification(
            specification
        ) {

            console.log(
                'Editing specification:',
                specification
            );


            /*
            |--------------------------------------------------------------------------
            | SHOW EDIT FORM
            |--------------------------------------------------------------------------
            */

            newSection.style.display =
                '';

            allSection.style.display =
                'none';


            /*
            |--------------------------------------------------------------------------
            | GET EXISTING PRODUCT ID
            |--------------------------------------------------------------------------
            */

            const editId =
                specification.sno ||
                specification.id ||
                '';


            /*
            |--------------------------------------------------------------------------
            | GET EXISTING BARCODE
            |--------------------------------------------------------------------------
            */

            const barcode =
                specification.barcode ||
                '';


            if (!editId) {

                alert(
                    'Unable to identify product ID.'
                );

                return;

            }


            if (!barcode) {

                alert(
                    'This product does not have a barcode.'
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | CREATE HIDDEN EDIT ID
            |--------------------------------------------------------------------------
            */

            let editIdElement =
                document.getElementById(
                    'editSpecificationId'
                );


            if (!editIdElement) {

                editIdElement =
                    document.createElement(
                        'input'
                    );

                editIdElement.type =
                    'hidden';

                editIdElement.id =
                    'editSpecificationId';

                document.body.appendChild(
                    editIdElement
                );

            }


            /*
            |--------------------------------------------------------------------------
            | CREATE HIDDEN BARCODE
            |--------------------------------------------------------------------------
            */

            let editBarcodeElement =
                document.getElementById(
                    'editSpecificationBarcode'
                );


            if (!editBarcodeElement) {

                editBarcodeElement =
                    document.createElement(
                        'input'
                    );

                editBarcodeElement.type =
                    'hidden';

                editBarcodeElement.id =
                    'editSpecificationBarcode';

                document.body.appendChild(
                    editBarcodeElement
                );

            }


            /*
            |--------------------------------------------------------------------------
            | STORE EDIT INFORMATION
            |--------------------------------------------------------------------------
            */

            editIdElement.value =
                editId;


            editBarcodeElement.value =
                barcode;


            console.log(
                'EDIT ID:',
                editId
            );


            console.log(
                'EXISTING BARCODE:',
                barcode
            );


            /*
            |--------------------------------------------------------------------------
            | FORM TITLE
            |--------------------------------------------------------------------------
            */

            const formTitle =
                document.getElementById(
                    'specificationFormTitle'
                );


            if (formTitle) {

                formTitle.textContent =
                    'Edit Design Specification';

            }


            const formSubtitle =
                document.getElementById(
                    'specificationFormSubtitle'
                );


            if (formSubtitle) {

                formSubtitle.textContent =
                    'Update garment design specification details';

            }


            /*
            |--------------------------------------------------------------------------
            | BARCODE DISPLAY
            |--------------------------------------------------------------------------
            */

            const barcodeText =
                document.getElementById(
                    'editBarcodeText'
                );


            if (barcodeText) {

                barcodeText.textContent =
                    barcode;

            }


            const barcodeBadge =
                document.getElementById(
                    'editBarcodeBadge'
                );


            if (barcodeBadge) {

                barcodeBadge.style.display =
                    'inline-flex';

            }


            /*
            |--------------------------------------------------------------------------
            | FILL SELECT FIELDS
            |--------------------------------------------------------------------------
            */

            setSelectValue(
                'designer_name',
                specification.designer_name
            );


            setSelectValue(
                'item_type',
                specification.item_type
            );


            setSelectValue(
                'gender_type',
                specification.gender
            );


            setSelectValue(
                'item_name',
                specification.item_name
            );


            setSelectValue(
                'composition',
                specification.composition
            );


            setSelectValue(
                'colour',
                specification.colour
            );


            setSelectValue(
                'sizes',
                specification.sizes
            );


            setSelectValue(
                'embellishment',
                specification.embellishment
            );


            setSelectValue(
                'manufacturing_process',
                specification.manufacturing_process
            );


            setSelectValue(
                'mcraftsman',
                specification.craftsman
            );


            setSelectValue(
                'cmbmanufacture',
                specification.manufecture
            );


            setSelectValue(
                'cmbclient',
                specification.client
            );


            /*
            |--------------------------------------------------------------------------
            | FILL TEXT FIELDS
            |--------------------------------------------------------------------------
            */

            setInputValue(
                'sku',
                specification.sku
            );


            setInputValue(
                'txt_clientreference',
                specification.clientreference
            );


            /*
            |--------------------------------------------------------------------------
            | FILL AI FIELDS
            |--------------------------------------------------------------------------
            */

            setInputValue(
                'txt_productName',
                specification.AI_product_name
            );


            setInputValue(
                'txt_productDescription',
                specification.AI_product_description
            );


            setInputValue(
                'txt_metaTitle',
                specification.AI_Metatitle
            );


            setInputValue(
                'txt_metaKeywords',
                specification.AI_Metakeywards
            );


            setInputValue(
                'txt_metaDescription',
                specification.AI_Metadescription
            );


            setInputValue(
                'txt_productTags',
                specification.AI_Producttag
            );


            setInputValue(
                'txt_image_alt_text',
                specification.AI_Imagealttext
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR NEW FILE SELECTION
            |--------------------------------------------------------------------------
            |
            | Existing images are NOT deleted.
            |
            */

            /*
            |--------------------------------------------------------------------------
            | EXISTING IMAGES
            |--------------------------------------------------------------------------
            */
            /*
            |--------------------------------------------------------------------------
            | EXISTING IMAGES
            |--------------------------------------------------------------------------
            */

            existingImages =
                getExistingSpecificationImages(
                    specification
                );

            renderSelectedImages();

            console.log(
                'Existing Images:',
                existingImages
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR ONLY NEW FILE SELECTION
            |--------------------------------------------------------------------------
            */

            selectedFiles = [];

            if (imageInput) {
                imageInput.value = '';
            }


                /*
                |--------------------------------------------------------------------------
                | SHOW EXISTING IMAGES
                |--------------------------------------------------------------------------
                */

                renderSelectedImages();


        /*
        |--------------------------------------------------------------------------
        | EXISTING DESIGN SUB IMAGES
        |--------------------------------------------------------------------------
        */

            existingSubImages =
                getExistingSpecificationSubImages(
                    specification
                );

            console.log(
                'Existing Sub Images:',
                existingSubImages
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR NEW SUB IMAGE SELECTION
            |--------------------------------------------------------------------------
            */

            selectedSubImages = [];

            if (subImagesInput) {
                subImagesInput.value = '';
            }


                /*
                |--------------------------------------------------------------------------
                | SHOW EXISTING + NEW SUB IMAGES
                |--------------------------------------------------------------------------
                */

                renderSubImagesPreview();


                /*
                |--------------------------------------------------------------------------
                | CHANGE SAVE BUTTON TO UPDATE
                |--------------------------------------------------------------------------
                */

  

            const saveButton =
                document.getElementById(
                    'btnSaveSpecification'
                );


            if (saveButton) {

                saveButton.innerHTML =
                    '<i class="bi bi-pencil-square me-1"></i> Update Specification';

            }


            /*
            |--------------------------------------------------------------------------
            | SCROLL TO FORM
            |--------------------------------------------------------------------------
            */

            newSection.scrollIntoView({

                behavior:
                    'smooth',

                block:
                    'start'

            });

        }

            function setSelectValue(
            id,
            value
            ) {

                const element =
                    document.getElementById(
                        id
                    );


                if (!element) {

                    console.warn(
                        'Select not found:',
                        id
                    );

                    return;

                }


                if (
                    value === null ||
                    value === undefined ||
                    value === ''
                ) {

                    element.value =
                        '';

                } else {

                    element.value =
                        String(value);

                }


                /*
                |--------------------------------------------------------------------------
                | Update Select2 UI
                |--------------------------------------------------------------------------
                */

                if (
                    typeof jQuery !==
                        'undefined' &&
                    jQuery.fn.select2 &&
                    jQuery(element).hasClass(
                        'select2-hidden-accessible'
                    )
                ) {

                    jQuery(element)
                        .val(element.value)
                        .trigger('change');

                }

            }

            window.renderConfirmationImages = function () {

                const container =
                    document.getElementById(
                        'confirmImages'
                    );

                if (!container) {
                    return;
                }

                container.innerHTML = '';


                /*
                |--------------------------------------------------------------------------
                | EXISTING DATABASE IMAGES
                |--------------------------------------------------------------------------
                */

                if (
                    typeof existingImages !==
                    'undefined' &&
                    Array.isArray(existingImages)
                ) {

                    existingImages.forEach(
                        function (imageUrl) {

                            if (!imageUrl) {
                                return;
                            }

                            const div =
                                document.createElement(
                                    'div'
                                );

                            div.className =
                                'confirm-image-item';

                            const img =
                                document.createElement(
                                    'img'
                                );

                            img.src =
                                imageUrl;

                            img.alt =
                                'Design Image';

                            div.appendChild(
                                img
                            );

                            container.appendChild(
                                div
                            );

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | NEW IMAGES
                |--------------------------------------------------------------------------
                */

                const imageInput =
                    document.getElementById(
                        'filenew'
                    );

                if (
                    imageInput &&
                    imageInput.files
                ) {

                    Array.from(
                        imageInput.files
                    ).forEach(
                        function (file) {

                            const div =
                                document.createElement(
                                    'div'
                                );

                            div.className =
                                'confirm-image-item';

                            const img =
                                document.createElement(
                                    'img'
                                );

                            img.alt =
                                'New Design Image';

                            div.appendChild(
                                img
                            );

                            container.appendChild(
                                div
                            );


                            const reader =
                                new FileReader();

                            reader.onload =
                                function (event) {

                                    img.src =
                                        event.target.result;

                                };

                            reader.readAsDataURL(
                                file
                            );

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | NO IMAGE
                |--------------------------------------------------------------------------
                */

                if (
                    !container.children.length
                ) {

                    container.innerHTML = `
                        <div
                            class="text-muted p-3">
                            No design images selected.
                        </div>
                    `;

                }

            }

            window.renderConfirmationSubImages = function () {

            const container =
                document.getElementById('confirmSubImages');

            if (!container) {
                return;
            }

        container.innerHTML = '';

    /*
    |--------------------------------------------------------------------------
    | NEW SUB IMAGES SELECTED BY USER
    |--------------------------------------------------------------------------
    */

    if (
        Array.isArray(selectedSubImages) &&
        selectedSubImages.length > 0
    ) {

        selectedSubImages.forEach(
            function (file) {

                if (!file) {
                    return;
                }

                const div =
                    document.createElement('div');

                div.className =
                    'confirm-image-item';

                const img =
                    document.createElement('img');

                img.alt =
                    'Design Sub Image';

                div.appendChild(img);

                container.appendChild(div);

                const reader =
                    new FileReader();

                reader.onload =
                    function (event) {

                        img.src =
                            event.target.result;

                    };

                reader.readAsDataURL(file);

            }
        );

    }

    /*
    |--------------------------------------------------------------------------
    | NO SUB IMAGES
    |--------------------------------------------------------------------------
    */

    if (!container.children.length) {

        container.innerHTML = `
            <div class="text-muted p-3">
                No design sub images selected.
            </div>
        `;

    }

};

    window.renderConfirmationMainImage = function () {

        const container =
            document.getElementById(
                'confirmSpecificationImage'
            );

        if (!container) {
            return;
        }

        container.innerHTML = '';


        let imageUrl = '';


        /*
        |--------------------------------------------------------------------------
        | EXISTING IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            typeof existingImages !==
            'undefined' &&
            Array.isArray(existingImages) &&
            existingImages.length
        ) {

            imageUrl =
                existingImages[0];

        }


        /*
        |--------------------------------------------------------------------------
        | NEW IMAGE
        |--------------------------------------------------------------------------
        */

        const imageInput =
            document.getElementById(
                'filenew'
            );


        if (
            imageInput &&
            imageInput.files &&
            imageInput.files.length
        ) {

            const file =
                imageInput.files[0];

            const reader =
                new FileReader();

            reader.onload =
                function (event) {

                    container.innerHTML = `
                        <img
                            src="${event.target.result}"
                            alt="Design Image">
                    `;

                };

            reader.readAsDataURL(
                file
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | EXISTING IMAGE
        |--------------------------------------------------------------------------
        */

        if (imageUrl) {

            container.innerHTML = `
                <img
                    src="${imageUrl}"
                    alt="Design Image"
                    onerror="
                        this.parentElement.innerHTML =
                        '<div class=&quot;confirm-no-image&quot;>' +
                        '<i class=&quot;bi bi-image&quot;></i>' +
                        '<span>No Image</span>' +
                        '</div>';
                    ">
            `;

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | NO IMAGE
        |--------------------------------------------------------------------------
        */

        container.innerHTML = `
            <div class="confirm-no-image">

                <i class="bi bi-image"></i>

                <span>
                    No Image
                </span>

            </div>
        `;

    }




        function setInputValue(
            id,
            value
        ) {

            const element =
                document.getElementById(id);


            if (!element) {
                return;
            }


            element.value =
                value === null ||
                value === undefined
                    ? ''
                    : value;

        }




                /* =====================================================
                INITIAL STATE
                ====================================================== */

                /*
                * IMPORTANT:
                *
                * New Specification is visible by default.
                *
                * We DO NOT call loadSpecifications()
                * here.
                *
                * Therefore the database table is not loaded
                * when the page opens.
                */

                showNewSpecification();


            }

            
        );

        /* =========================================================
   MASTER MANAGEMENT
    ========================================================= */

    (function () {

        let masterModal = null;

        let currentMaster = '';

        let currentSelectId = '';

        let masterRows = [];
        let masterSearchText = '';


        const modalElement =
    document.getElementById(
        'masterManagementModal'
    );


if (!modalElement) {
    return;
}


/*
|--------------------------------------------------------------------------
| Bootstrap modal
|--------------------------------------------------------------------------
| Do NOT create the Bootstrap modal here.
| Create it only when the user clicks the + button.
|--------------------------------------------------------------------------
*/

function getMasterModal() {

    if (
        typeof bootstrap === 'undefined' ||
        !bootstrap.Modal
    ) {

        console.error(
            'Bootstrap JavaScript is not loaded.'
        );

        Swal.fire({
            icon: 'error',
            title: 'Bootstrap Error',
            text:
                'Bootstrap JavaScript is not loaded on this page.'
        });

        return null;
    }


    if (!masterModal) {

        masterModal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

    }


    return masterModal;
}


        const modalTitle =
            document.getElementById(
                'masterModalTitle'
            );


        const masterModalType =
            document.getElementById(
                'masterModalType'
            );


        const selectedIdInput =
            document.getElementById(
                'masterSelectedId'
            );


        const nameInput =
            document.getElementById(
                'masterNameInput'
            );


        const codeInput =
            document.getElementById(
                'masterCodeInput'
            );


        const codeWrapper =
            document.getElementById(
                'masterCodeWrapper'
            );


        const listTitle =
            document.getElementById(
                'masterListTitle'
            );


        const listContainer =
            document.getElementById(
                'masterListContainer'
            );


        const loading =
            document.getElementById(
                'masterListLoading'
            );


        const empty =
            document.getElementById(
                'masterListEmpty'
            );


        const btnAdd =
            document.getElementById(
                'btnMasterAdd'
            );


        const btnUpdate =
            document.getElementById(
                'btnMasterUpdate'
            );


        const btnClear =
            document.getElementById(
                'btnMasterClear'
            );


        /*
        |--------------------------------------------------------------------------
        | CSRF
        |--------------------------------------------------------------------------
        */

        const csrfToken =
            document.querySelector(
                'meta[name="csrf-token"]'
            )?.getAttribute('content') ||
            '{{ csrf_token() }}';


        /*
        |--------------------------------------------------------------------------
        | Clear
        |--------------------------------------------------------------------------
        */

        function clearMasterForm() {

            nameInput.value = '';

            codeInput.value = '';

            selectedIdInput.value = '';

            btnUpdate.disabled = true;

            document
                .querySelectorAll(
                    '.master-list-row'
                )
                .forEach(function (row) {

                    row.classList.remove(
                        'table-primary'
                    );

                });

            nameInput.focus();

        }


        /*
        |--------------------------------------------------------------------------
        | Load master list
        |--------------------------------------------------------------------------
        */

        async function loadMasterList() {

            loading.style.display = 'block';

            empty.style.display = 'none';

            listContainer.innerHTML = '';


            try {

                const response =
                    await fetch(
                        `/admin/design-specifications/master/${encodeURIComponent(currentMaster)}`,
                        {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                const result =
                    await response.json();


                if (!response.ok || !result.success) {

                    throw new Error(
                        result.message ||
                        'Unable to load master list.'
                    );

                }


                masterRows =
                    result.data || [];


                renderMasterList();


            } catch (error) {

                console.error(
                    'MASTER LIST ERROR:',
                    error
                );

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });

            } finally {

                loading.style.display = 'none';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Render list
        |--------------------------------------------------------------------------
        */

        function renderMasterList() {

    listContainer.innerHTML = '';

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    const search =
        String(masterSearchText || '')
            .trim()
            .toLowerCase();


    /*
    |--------------------------------------------------------------------------
    | FILTER MASTER ROWS
    |--------------------------------------------------------------------------
    */

    const filteredRows =
        masterRows.filter(function (row) {

            const name =
                String(
                    row[
                        getNameColumn(currentMaster)
                    ] || ''
                )
                .toLowerCase();

            /*
            | For craftsman also search code
            */

            const code =
                String(
                    row.code || ''
                )
                .toLowerCase();

            return (
                name.includes(search) ||
                code.includes(search)
            );

        });


    /*
    |--------------------------------------------------------------------------
    | NO RESULT
    |--------------------------------------------------------------------------
    */

    if (!filteredRows.length) {

        empty.style.display = 'block';

        empty.textContent =
            search
                ? 'No master found for "' +
                  masterSearchText +
                  '".'
                : 'No master found.';

        return;
    }


    empty.style.display = 'none';


    /*
    |--------------------------------------------------------------------------
    | RENDER FILTERED LIST
    |--------------------------------------------------------------------------
    */

    filteredRows.forEach(function (row) {

        const rowDiv =
            document.createElement('div');


        rowDiv.className =
            'master-list-row border-bottom p-3';


        rowDiv.style.cursor =
            'pointer';


        const name =
            row[
                getNameColumn(currentMaster)
            ] || '';


        let html = `
            <div class="d-flex
                        justify-content-between
                        align-items-center">

                <div>

                    <strong>
                        ${escapeHtml(name)}
                    </strong>
        `;


        /*
        |--------------------------------------------------------------------------
        | CRAFTSMAN CODE
        |--------------------------------------------------------------------------
        */

        if (row.code) {

            html += `
                <small class="text-muted ms-2">
                    (${escapeHtml(row.code)})
                </small>
            `;
        }


        html += `
                </div>

                <i class="bi bi-chevron-right text-muted"></i>

            </div>
        `;


        rowDiv.innerHTML =
            html;


        /*
        |--------------------------------------------------------------------------
        | SELECT MASTER
        |--------------------------------------------------------------------------
        */

        rowDiv.addEventListener(
    'click',
    function () {

        /*
        |--------------------------------------------------------------------------
        | Remove previous selection
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '.master-list-row'
            )
            .forEach(function (r) {

                r.classList.remove(
                    'table-primary'
                );

            });


        /*
        |--------------------------------------------------------------------------
        | Highlight selected row
        |--------------------------------------------------------------------------
        */

        rowDiv.classList.add(
            'table-primary'
        );


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | Backend returns "sno", not "id"
        |--------------------------------------------------------------------------
        */

        selectedIdInput.value =
        String(
            row.id || ''
        );


        /*
        |--------------------------------------------------------------------------
        | Put selected name into textbox
        |--------------------------------------------------------------------------
        */

        nameInput.value =
            name;


        /*
        |--------------------------------------------------------------------------
        | Craftsman code
        |--------------------------------------------------------------------------
        */

        if (
            currentMaster ===
            'craftsman'
        ) {

            codeInput.value =
                row.code || '';

        }


        /*
        |--------------------------------------------------------------------------
        | Enable Update button
        |--------------------------------------------------------------------------
        */

        btnUpdate.disabled =
            !selectedIdInput.value;


        console.log(
            'Selected Master:',
            {
                master: currentMaster,
                id: selectedIdInput.value,
                name: nameInput.value
            }
        );

    }
);


        listContainer.appendChild(
            rowDiv
        );

    });

}

/*
|--------------------------------------------------------------------------
| MASTER SEARCH
|--------------------------------------------------------------------------
*/

const masterSearchInput =
    document.getElementById(
        'masterSearchInput'
    );


if (masterSearchInput) {

    masterSearchInput.addEventListener(
        'input',
        function () {

            masterSearchText =
                this.value || '';

            renderMasterList();

        }
    );

}

const btnClearMasterSearch =
    document.getElementById(
        'btnClearMasterSearch'
    );


if (btnClearMasterSearch) {

    btnClearMasterSearch.addEventListener(
        'click',
        function () {

            masterSearchText = '';

            if (masterSearchInput) {

                masterSearchInput.value =
                    '';

                masterSearchInput.focus();

            }

            renderMasterList();

        }
    );

}


        /*
        |--------------------------------------------------------------------------
        | Get name column
        |--------------------------------------------------------------------------
        */

        function getNameColumn(master) {

            const columns = {

                item_name:
                    'itemname',

                item_type:
                    'itemtype',

                designer:
                    'designername',

                gender:
                    'name',

                composition:
                    'composition_details',

                colour:
                    'colourname',

                size:
                    'size',

                embellishment:
                    'embellishmentname',

                manufacturing_process:
                    'manufacturing_process',

                craftsman:
                    'name',

                manufacture:
                    'name',

                client:
                    'name'

            };


            return columns[master];

        }


        /*
        |--------------------------------------------------------------------------
        | Select existing master
        |--------------------------------------------------------------------------
        */

        function selectMasterRow(
            row,
            rowElement
        ) {

            document
                .querySelectorAll(
                    '.master-list-row'
                )
                .forEach(function (row) {

                    row.classList.remove(
                        'table-primary'
                    );

                });


            rowElement.classList.add(
                'table-primary'
            );


            const nameColumn =
                getNameColumn(
                    currentMaster
                );


            nameInput.value =
                row[nameColumn] || '';


            selectedIdInput.value =
                row.id;


            codeInput.value =
             row.code || '';


            btnUpdate.disabled = false;

        }


        /*
        |--------------------------------------------------------------------------
        | Open master modal
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.btn-add-master'
                    );


                if (!button) {
                    return;
                }


                event.preventDefault();


                const title =
                    button
                        .getAttribute('title') ||
                    '';


                const masterMap = {

                    'Add Item Name':
                        'item_name',

                    'Add Item Type':
                        'item_type',

                    'Add Designer':
                        'designer',

                    'Add Gender':
                        'gender',

                    'Add Composition':
                        'composition',

                    'Add Colour':
                        'colour',

                    'Add Size':
                        'size',

                    'Add Embellishment':
                        'embellishment',

                    'Add Manufacturing Process':
                        'manufacturing_process',

                    'Add Craftsman':
                        'craftsman',

                    'Add Manufacture':
                        'manufacture',

                    'Add Client':
                        'client'

                };


                currentMaster =
                    masterMap[title];


                if (!currentMaster) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text:
                            'Master configuration not found.'
                    });

                    return;

                }


                const select =
                    document.getElementById(
                        getSelectId(currentMaster)
                    );


                currentSelectId =
                    getSelectId(currentMaster);


                modalTitle.textContent =
                    getMasterTitle(
                        currentMaster
                    );


                listTitle.textContent =
                    getMasterTitle(
                        currentMaster
                    );


                masterModalType.value =
                    currentMaster;


                codeWrapper.style.display =
                    currentMaster === 'craftsman'
                        ? 'block'
                        : 'none';


                clearMasterForm();


                const modal = getMasterModal();

                if (!modal) {
                    return;
                }

                modal.show();

                loadMasterList();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Select ID
        |--------------------------------------------------------------------------
        */

        function getSelectId(master) {

            const ids = {

                item_name:
                    'item_name',

                item_type:
                    'item_type',

                designer:
                    'designer_name',

                gender:
                    'gender_type',

                composition:
                    'composition',

                colour:
                    'colour',

                size:
                    'sizes',

                embellishment:
                    'embellishment',

                manufacturing_process:
                    'manufacturing_process',

                craftsman:
                    'mcraftsman',

                manufacture:
                    'cmbmanufacture',

                client:
                    'cmbclient'

            };


            return ids[master];

        }


        /*
        |--------------------------------------------------------------------------
        | Master title
        |--------------------------------------------------------------------------
        */

        function getMasterTitle(master) {

            const titles = {

                item_name:
                    'Item Name Master',

                item_type:
                    'Item Type Master',

                designer:
                    'Designer Master',

                gender:
                    'Gender Master',

                composition:
                    'Composition Master',

                colour:
                    'Colour Master',

                size:
                    'Size Master',

                embellishment:
                    'Embellishment Master',

                manufacturing_process:
                    'Manufacturing Process Master',

                craftsman:
                    'Craftsman Master',

                manufacture:
                    'Manufacture Master',

                client:
                    'Collection Master'

            };


            return titles[master] || 'Master';

        }


        /*
        |--------------------------------------------------------------------------
        | Add new master
        |--------------------------------------------------------------------------
        */

        btnAdd.addEventListener(
            'click',
            async function () {

                const name =
                    nameInput.value.trim();


                if (!name) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Please check',
                        text:
                            'Please enter master name.'
                    }).then(function () {

                        nameInput.focus();

                    });

                    return;

                }


                btnAdd.disabled = true;


                try {

                    const response =
                        await fetch(
                            `/admin/design-specifications/master/${encodeURIComponent(currentMaster)}`,
                            {
                                method: 'POST',

                                headers: {
                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        csrfToken
                                },

                                body: JSON.stringify({

                                    name:
                                        name

                                })
                            }
                        );


                    const result =
                        await response.json();


                    if (
                        !response.ok ||
                        !result.success
                    ) {

                        throw new Error(
                            result.message ||
                            'Unable to add master.'
                        );

                    }


                    await loadMasterList();


                    setValueInMainForm(
                        result.id,
                        result.name,
                        result.code
                    );


                    clearMasterForm();


                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: result.message,
                        timer: 1400,
                        showConfirmButton: false
                    });


                } catch (error) {

                    console.error(
                        'MASTER ADD ERROR:',
                        error
                    );

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message
                    });

                } finally {

                    btnAdd.disabled = false;

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Update selected master
        |--------------------------------------------------------------------------
        */

        btnUpdate.addEventListener(
            'click',
            async function () {

                const id =
                    String(
                        selectedIdInput.value || ''
                    ).trim();


                const name =
                    nameInput.value.trim();


                console.log(
                    'UPDATE MASTER:',
                    {
                        master: currentMaster,
                        id: id,
                        name: name
                    }
                );


                if (!id) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Please check',
                        text:
                            'Please select a master from the list.'
                    });

                    return;
                }


                if (!name) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Please check',
                        text:
                            'Master name cannot be empty.'
                    }).then(function () {

                        nameInput.focus();

                    });

                    return;

                }


                btnUpdate.disabled = true;


                try {

                    const response =
                        await fetch(
                            `/admin/design-specifications/master/${encodeURIComponent(currentMaster)}/${id}`,
                            {
                                method: 'PUT',

                                headers: {
                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        csrfToken
                                },

                                body: JSON.stringify({

                                    name:
                                        name

                                })
                            }
                        );


                    const result =
                        await response.json();


                    if (
                        !response.ok ||
                        !result.success
                    ) {

                        throw new Error(
                            result.message ||
                            'Unable to update master.'
                        );

                    }


                    await loadMasterList();


                    setValueInMainForm(
                        result.id,
                        result.name,
                        result.code
                    );


                    clearMasterForm();


                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: result.message,
                        timer: 1400,
                        showConfirmButton: false
                    });


                } catch (error) {

                    console.error(
                        'MASTER UPDATE ERROR:',
                        error
                    );

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message
                    });


                    btnUpdate.disabled = false;

                }

            }
        );


         document
        .getElementById("btnDownloadBarcodePdf")
        .addEventListener(
            "click",
            async function () {

                const loader =
                    document.getElementById(
                        "pdfLoader"
                    );

                const button =
                    this;


                try {

                    if (loader) {

                        loader.style.display =
                            "flex";

                    }

                    button.disabled =
                        true;


                    /*
                    |--------------------------------------------------------------------------
                    | SAME CONTAINER AS CORE PHP
                    |--------------------------------------------------------------------------
                    */

                    const element =
                        document.getElementById(
                            "barcodePdfContent_X"
                        );


                    if (!element) {

                        throw new Error(
                            "Barcode PDF container not found."
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CHECK LIBRARIES
                    |--------------------------------------------------------------------------
                    */

                    if (
                        typeof html2canvas !==
                        "function"
                    ) {

                        throw new Error(
                            "html2canvas is not loaded."
                        );

                    }


                    let jsPDF;


                    if (
                        window.jspdf &&
                        typeof window.jspdf.jsPDF ===
                        "function"
                    ) {

                        jsPDF =
                            window.jspdf.jsPDF;

                    } else if (
                        typeof window.jsPDF ===
                        "function"
                    ) {

                        jsPDF =
                            window.jsPDF;

                    } else {

                        throw new Error(
                            "jsPDF is not loaded."
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SAME HTML2CANVAS SETTINGS AS CORE PHP
                    |--------------------------------------------------------------------------
                    */

                    const canvas =
                        await html2canvas(
                            element,
                            {

                                scale: 1.5,

                                useCORS: true

                            }
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE A4 PDF
                    |--------------------------------------------------------------------------
                    */

                    const pdf =
                        new jsPDF(
                            "p",
                            "pt",
                            "a4"
                        );


                    const pageWidth =
                        pdf.internal.pageSize
                            .getWidth();


                    const pageHeight =
                        pdf.internal.pageSize
                            .getHeight();


                    const canvasWidth =
                        canvas.width;


                    const canvasHeight =
                        canvas.height;


                    /*
                    |--------------------------------------------------------------------------
                    | SAME CORE PHP CALCULATION
                    |--------------------------------------------------------------------------
                    */

                    const pageCanvasHeight =
                        (
                            canvasWidth *
                            pageHeight
                        ) /
                        pageWidth;


                    const pageCount =
                        Math.ceil(
                            canvasHeight /
                            pageCanvasHeight
                        );


                    console.log(
                        "Canvas:",
                        canvasWidth,
                        canvasHeight
                    );


                    console.log(
                        "PDF pages:",
                        pageCount
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SLICE CANVAS PAGE BY PAGE
                    |--------------------------------------------------------------------------
                    */

                    for (
                        let i = 0;
                        i < pageCount;
                        i++
                    ) {

                        const pageCanvas =
                            document.createElement(
                                "canvas"
                            );


                        pageCanvas.width =
                            canvasWidth;


                        pageCanvas.height =
                            Math.min(
                                pageCanvasHeight,
                                canvasHeight -
                                (
                                    i *
                                    pageCanvasHeight
                                )
                            );


                        const ctx =
                            pageCanvas.getContext(
                                "2d"
                            );


                        ctx.drawImage(
                            canvas,

                            0,

                            i *
                                pageCanvasHeight,

                            canvasWidth,

                            pageCanvas.height,

                            0,

                            0,

                            canvasWidth,

                            pageCanvas.height
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | SAME JPEG COMPRESSION AS CORE PHP
                        |--------------------------------------------------------------------------
                        */

                        const imgData =
                            pageCanvas.toDataURL(
                                "image/jpeg",
                                0.7
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | ADD PDF PAGE
                        |--------------------------------------------------------------------------
                        */

                        if (i > 0) {

                            pdf.addPage();

                        }


                        pdf.addImage(
                            imgData,
                            "JPEG",
                            0,
                            0,
                            pageWidth,
                            (
                                pageCanvas.height *
                                pageWidth
                            ) /
                            canvasWidth
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DOWNLOAD
                    |--------------------------------------------------------------------------
                    */

                    pdf.save(
                        "barcodes.pdf"
                    );


                


                    console.log(
                        "Barcode PDF generated successfully."
                    );


                } catch (error) {

                    console.error(
                        "Barcode PDF Error:",
                        error
                    );


                    alert(
                        error.message ||
                        "Unable to generate barcode PDF."
                    );


                } finally {

                    if (loader) {

                        loader.style.display =
                            "none";

                    }

                    button.disabled =
                        false;

                }

            }
        );

        document
        .getElementById("btnPrintBarcodePdf")
        .addEventListener("click", async function () {

            const loader =
                document.getElementById("pdfLoader");

            const button = this;

            try {

                if (loader) {
                    loader.style.display = "flex";
                }

                button.disabled = true;


                /* =====================================================
                GET A4 PAGES
                ===================================================== */

                const pages =
                    document.querySelectorAll(
                        "#barcodePdfContent_X .barcode-a4-page"
                    );


                if (!pages.length) {

                    alert(
                        "No barcode pages available for printing."
                    );

                    return;
                }


                /* =====================================================
                CREATE PDF
                ===================================================== */

                const { jsPDF } = window.jspdf;

                const pdf =
                    new jsPDF(
                        "p",
                        "pt",
                        "a4"
                    );


                const pageWidth =
                    pdf.internal.pageSize.getWidth();

                const pageHeight =
                    pdf.internal.pageSize.getHeight();


                /* =====================================================
                RENDER EACH PAGE
                ===================================================== */

                for (
                    let i = 0;
                    i < pages.length;
                    i++
                ) {

                    const canvas =
                        await html2canvas(
                            pages[i],
                            {
                                scale: 2,

                                useCORS: true,

                                allowTaint: false,

                                backgroundColor:
                                    "#ffffff",

                                logging: false,

                                imageTimeout: 15000
                            }
                        );


                    const imgData =
                        canvas.toDataURL(
                            "image/jpeg",
                            0.92
                        );


                    if (i > 0) {

                        pdf.addPage();

                    }


                    pdf.addImage(
                        imgData,
                        "JPEG",
                        0,
                        0,
                        pageWidth,
                        pageHeight,
                        undefined,
                        "FAST"
                    );

                }


                /* =====================================================
                OPEN PRINT DIALOG
                ===================================================== */

                pdf.autoPrint();


                const pdfBlob =
                    pdf.output("blob");


                const blobUrl =
                    URL.createObjectURL(
                        pdfBlob
                    );


                const printWindow =
                    window.open(
                        blobUrl,
                        "_blank"
                    );


                if (!printWindow) {

                    alert(
                        "Please allow pop-ups to print the PDF."
                    );

                    return;
                }


                /* =====================================================
                CLEAN URL LATER
                ===================================================== */

                setTimeout(
                    function () {

                        URL.revokeObjectURL(
                            blobUrl
                        );

                    },
                    60000
                );


            } catch (error) {

                console.error(
                    "Print PDF Error:",
                    error
                );

                alert(
                    "Unable to print barcode PDF."
                );


            } finally {

                if (loader) {

                    loader.style.display =
                        "none";

                }

                button.disabled = false;

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Set value in main form
        |--------------------------------------------------------------------------
        */

        function setValueInMainForm(
            id,
            name,
            code
        ) {

            const select =
                document.getElementById(
                    currentSelectId
                );


            if (!select) {
                return;
            }


            let option =
                Array.from(
                    select.options
                ).find(function (item) {

                    return String(
                        item.value
                    ) === String(id);

                });


            if (!option) {

                option =
                    new Option(
                        name,
                        id,
                        true,
                        true
                    );


                if (
                    currentMaster ===
                    'craftsman' &&
                    code
                ) {

                    option.dataset.code =
                        code;

                }


                select.add(option);

            } else {

                option.textContent =
                    currentMaster === 'craftsman' &&
                    code
                        ? `${name} (${code})`
                        : name;

            }


            select.value =
                String(id);


            if (
                typeof jQuery !==
                'undefined' &&
                jQuery.fn.select2
            ) {

                $(select)
                    .trigger('change');

            } else {

                select.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true
                        }
                    )
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Clear button
        |--------------------------------------------------------------------------
        */

        btnClear.addEventListener(
            'click',
            function () {

                clearMasterForm();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Escape HTML
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            const div =
                document.createElement(
                    'div'
                );

            div.textContent =
                value ?? '';

            return div.innerHTML;

        }


    })();

    

    function renderConfirmationBarcode(barcode) {

    const svg =
        document.getElementById(
            'confirmBarcodeSvg'
        );

    const preview =
        document.getElementById(
            'confirmBarcodePreview'
        );

    if (!svg || !preview) {
        return;
    }

    svg.innerHTML = '';

    if (
        !barcode ||
        barcode === '-' ||
        typeof JsBarcode !== 'function'
    ) {
        preview.style.display = 'none';
        return;
    }

    try {

        JsBarcode(
            svg,
            barcode,
            {
                format: 'CODE128',
                width: 2,
                height: 55,
                displayValue: true,
                fontSize: 13,
                margin: 8
            }
        );

        preview.style.display = 'block';

    } catch (error) {

        console.error(
            'Confirmation barcode error:',
            error
        );

        preview.style.display = 'none';
    }
}

// =========================================================
// DESIGN SUB IMAGES
// =========================================================

let selectedSubImages = [];

const subImagesInput =
    document.getElementById('sub_images');

const subImagesPreview =
    document.getElementById('subImagesPreview');


// =========================================================
// SELECT SUB IMAGES
// =========================================================

if (subImagesInput) {

    subImagesInput.addEventListener(
        'change',
        function (e) {

            const files =
                Array.from(
                    e.target.files || []
                );

            files.forEach(function (file) {

                if (
                    !file.type ||
                    !file.type.startsWith('image/')
                ) {
                    return;
                }

                selectedSubImages.push(file);

            });

            renderSubImagesPreview();

            /*
            IMPORTANT:

            We intentionally clear the input so the
            same image can be selected again.

            The actual files are kept inside
            selectedSubImages.
            */

            subImagesInput.value = '';

        }
    );

}


// =========================================================
// RENDER SUB IMAGE PREVIEW
// =========================================================

function renderSubImagesPreview() {

    const preview =
        document.getElementById('subImagesPreview');

    if (!preview) {
        return;
    }

    preview.innerHTML = '';

    /*
    |--------------------------------------------------------------------------
    | COMBINE EXISTING + NEW IMAGES
    |--------------------------------------------------------------------------
    */

    let images = [];

    /*
    |--------------------------------------------------------------------------
    | EXISTING IMAGES
    |--------------------------------------------------------------------------
    */

    if (
        Array.isArray(existingSubImages) &&
        existingSubImages.length > 0
    ) {

        existingSubImages.forEach(function (item) {

            let imagePath = '';

            /*
            | If database value is string
            */

            if (typeof item === 'string') {

                imagePath = item;

            }

            /*
            | If database value is object
            */

            else if (item && typeof item === 'object') {

                imagePath =
                    item.path ||
                    item.url ||
                    item.src ||
                    item.image ||
                    item.subimg_path ||
                    '';

            }

            if (!imagePath) {
                return;
            }

            images.push({
                type: 'existing',
                path: imagePath
            });

        });

    }


    /*
    |--------------------------------------------------------------------------
    | NEWLY SELECTED IMAGES
    |--------------------------------------------------------------------------
    */

    if (
        Array.isArray(selectedSubImages) &&
        selectedSubImages.length > 0
    ) {

        selectedSubImages.forEach(function (file) {

            if (!file) {
                return;
            }

            images.push({
                type: 'new',
                file: file
            });

        });

    }


    /*
    |--------------------------------------------------------------------------
    | NO IMAGES
    |--------------------------------------------------------------------------
    */

    if (images.length === 0) {

        preview.innerHTML = `
            <div class="col-12">
                <div class="text-muted small">
                    No sub images added.
                </div>
            </div>
        `;

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER IMAGES
    |--------------------------------------------------------------------------
    */

    images.forEach(function (image, index) {

        const col =
            document.createElement('div');

        col.className =
            'col-6 col-sm-4 col-md-3 col-lg-3 mb-3';


        const card =
            document.createElement('div');

        card.className =
            'sub-image-preview-card';


        /*
        |--------------------------------------------------------------------------
        | IMAGE WRAPPER
        |--------------------------------------------------------------------------
        */

        const imageWrapper =
            document.createElement('div');

        imageWrapper.className =
            'sub-image-preview-wrapper';


        const img =
            document.createElement('img');

        img.className =
            'sub-image-preview-img';

        img.alt =
            'Design Sub Image';


        /*
        |--------------------------------------------------------------------------
        | EXISTING IMAGE
        |--------------------------------------------------------------------------
        */

        if (image.type === 'existing') {

            let path =
                image.path;

            /*
            | Remove leading slash
            */

            path =
                path.replace(/^\/+/, '');


            /*
            | If stored path already contains public/
            */

            if (
                path.startsWith('public/')
            ) {

                path =
                    path.substring(7);

            }


            /*
            | Build browser URL
            */

            img.src =
                '/' + path;


            /*
            | Existing badge
            */

            const badge =
                document.createElement('span');

            badge.className =
                'sub-image-existing-badge';

            badge.innerHTML =
                'Existing';

            imageWrapper.appendChild(
                badge
            );

        }


        /*
        |--------------------------------------------------------------------------
        | NEW IMAGE
        |--------------------------------------------------------------------------
        */

        else if (image.type === 'new') {

            img.src =
                URL.createObjectURL(
                    image.file
                );


            /*
            | New badge
            */

            const badge =
                document.createElement('span');

            badge.className =
                'sub-image-new-badge';

            badge.innerHTML =
                'New';

            imageWrapper.appendChild(
                badge
            );

        }


        /*
        |--------------------------------------------------------------------------
        | ERROR HANDLING
        |--------------------------------------------------------------------------
        */

        img.onerror =
            function () {

                this.style.display =
                    'none';

                const errorBox =
                    document.createElement('div');

                errorBox.className =
                    'sub-image-error';

                errorBox.innerHTML = `
                    <i class="bi bi-image"></i>
                    <span>Image unavailable</span>
                `;

                imageWrapper.appendChild(
                    errorBox
                );

            };


        imageWrapper.appendChild(
            img
        );


        /*
        |--------------------------------------------------------------------------
        | FILE NAME
        |--------------------------------------------------------------------------
        */

        const filename =
            document.createElement('div');

        filename.className =
            'sub-image-preview-name';


        let displayName = '';


        if (image.type === 'existing') {

            const path =
                image.path || '';

            displayName =
                path.split('/').pop() ||
                'Sub Image';

        }
        else {

            displayName =
                image.file?.name ||
                'New Image';

        }


        filename.textContent =
            displayName;


        /*
        |--------------------------------------------------------------------------
        | REMOVE BUTTON FOR NEW IMAGES
        |--------------------------------------------------------------------------
        */

        if (image.type === 'new') {

            const removeBtn =
                document.createElement('button');

            removeBtn.type =
                'button';

            removeBtn.className =
                'sub-image-remove-btn';

            removeBtn.innerHTML =
                '<i class="bi bi-x"></i>';

            removeBtn.onclick =
                function () {

                    const newIndex =
                        selectedSubImages.indexOf(
                            image.file
                        );

                    if (newIndex !== -1) {

                        selectedSubImages.splice(
                            newIndex,
                            1
                        );

                    }

                    renderSubImagesPreview();

                };


            imageWrapper.appendChild(
                removeBtn
            );

        }


        card.appendChild(
            imageWrapper
        );

        card.appendChild(
            filename
        );

        col.appendChild(
            card
        );

        preview.appendChild(
            col
        );

    });

}
function getSubImageUrl(image)
{
    if (!image) {
        return '';
    }

    image =
        String(image)
        .trim();

    /*
    |--------------------------------------------------------------------------
    | Already full URL
    |--------------------------------------------------------------------------
    */

    if (
        image.startsWith('http://') ||
        image.startsWith('https://') ||
        image.startsWith('data:')
    ) {
        return image;
    }


    /*
    |--------------------------------------------------------------------------
    | Remove ../ or ../../ from old database paths
    |--------------------------------------------------------------------------
    */

    const marker =
        'ItemsDesigner_Masterwithbarcode/';

    const position =
        image.indexOf(marker);

    if (position !== -1) {

        return '/' +
            image.substring(position);

    }


    /*
    |--------------------------------------------------------------------------
    | Laravel storage path
    |--------------------------------------------------------------------------
    */

    if (
        image.startsWith('/storage/')
    ) {
        return image;
    }


    if (
        image.startsWith('storage/')
    ) {
        return '/' + image;
    }


    /*
    |--------------------------------------------------------------------------
    | Normal public path
    |--------------------------------------------------------------------------
    */

    if (
        image.startsWith('/')
    ) {
        return image;
    }


    return '/' + image;
}


subImagesPreview.addEventListener(
    'click',
    function (event) {

        const removeButton =
            event.target.closest(
                '.sub-image-remove'
            );


        if (!removeButton) {
            return;
        }


        const index =
            parseInt(
                removeButton.dataset.index,
                10
            );


        selectedSubImages.splice(
            index,
            1
        );


        renderSubImagesPreview();

    }
);

// =========================================================
// REMOVE SUB IMAGE
// =========================================================

function removeSubImage(index) {

    if (
        index < 0 ||
        index >= selectedSubImages.length
    ) {
        return;
    }


    selectedSubImages.splice(
        index,
        1
    );


    renderSubImagesPreview();

}

function loadSupplierProducts()
{
    const tbody =
        $('#supplierProductsTableBody');

    const loading =
        $('#supplierProductsLoading');

    const noData =
        $('#supplierProductsNoData');

    const tableWrapper =
        $('#supplierProductsTableWrapper');


    tbody.empty();

    noData.hide();

    tableWrapper.show();

    loading.show();


    $.ajax({

        url:
            "{{ route('design-specifications.supplier-products') }}",

        type:
            "GET",

        dataType:
            "json",

        success:
            function (response) {

                loading.hide();


                if (
                    !response.success ||
                    !Array.isArray(response.data) ||
                    response.data.length === 0
                ) {

                    tableWrapper.hide();

                    noData.show();

                    return;

                }


                response.data.forEach(
                    function (product, index) {

                        appendSupplierProductRow(
                            product,
                            index + 1
                        );

                    }
                );

            },

        error:
            function (xhr) {

                loading.hide();

                tableWrapper.hide();

                noData.show();

                console.error(
                    'Supplier products error:',
                    xhr
                );

            }

    });
}

function appendSupplierProductRow(
    product,
    serialNo
)
{

    const tbody =
        $('#supplierProductsTableBody');


    const mainImage =
        product.main_image
            ? '/' + String(
                product.main_image
            ).replace(/^\/+/, '')
            : '';


    const productName =
        product.name || '-';


    const supplierName =
        product.supplier_name || '-';


    const itemType =
        product.item_type || '-';


    const gender =
        product.gender || '-';


    const composition =
        product.composition || '-';


    const colour =
        product.colour || '-';


    const size =
        product.size || '-';


    let imageHtml =
        `
        <div
            class="text-muted text-center"
        >
            No Image
        </div>
        `;


    if (mainImage) {

        imageHtml = `

            <img
                src="${escapeHtml(mainImage)}"
                style="
                    width:70px;
                    height:70px;
                    object-fit:cover;
                    border-radius:6px;
                    border:1px solid #dee2e6;
                "
                onerror="
                    this.style.display='none';
                "
            >

        `;

    }


    const row = `

        <tr>

            <td>
                ${serialNo}
            </td>


            <td>
                <strong>
                    ${escapeHtml(productName)}
                </strong>
            </td>


            <td>
                ${escapeHtml(supplierName)}
            </td>


            <td>
                ${escapeHtml(itemType)}
            </td>


            <td>
                ${escapeHtml(gender)}
            </td>


            <td>
                ${escapeHtml(composition)}
            </td>


            <td>
                ${escapeHtml(colour)}
            </td>


            <td>
                ${escapeHtml(size)}
            </td>


            <td class="text-center">

                ${imageHtml}

            </td>


            <td class="text-center">

                <button
                    type="button"
                    class="btn btn-sm btn-primary btn-select-supplier-product"
                    data-product-id="${product.sno}"
                    data-product-loginsupplerid="${product.supplier_id || product.supplier_id || ''}"
                >

                    <i class="bi bi-check2-circle me-1"></i>

                    Select

                </button>

            </td>

        </tr>

    `;


    tbody.append(row);


    /*
    |--------------------------------------------------------------------------
    | STORE COMPLETE PRODUCT OBJECT
    |--------------------------------------------------------------------------
    */

    $('#supplierProductsTableBody')
        .find(
            `button[data-product-id="${product.sno}"]`
        )
        .data(
            'product',
            product
        );

}

/* =========================================================
   DISPLAY SELECTED SUPPLIER PRODUCT
========================================================= */



async function selectSupplierProduct(product,productId = '',
    loginSupplierId = '')
{
    /*
    |--------------------------------------------------------------------------
    | CHECK PRODUCT
    |--------------------------------------------------------------------------
    */

    if (!product) {

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Supplier product data not found.'
        });

        return;
    }


        showSupplierProductInfo(product,productId,
    loginSupplierId);

        selectedSupplierProduct = {
            ...product,
            product_id: productId,
            login_supplier_id: loginSupplierId
        };


    console.log(
        'SELECTED SUPPLIER PRODUCT:',
        product
    );


    /*
    |--------------------------------------------------------------------------
    | SUPPLIER DETAILS
    |--------------------------------------------------------------------------
    */

    const supplierId =
        product.supplier_id ||
        product.supplierid ||
        '';


    const supplierName =
        product.supplier_name ||
        product.suppliername ||
        '';


    /*
    |--------------------------------------------------------------------------
    | SHOW SUPPLIER
    |--------------------------------------------------------------------------
    */

    const supplierContextName =
        document.getElementById(
            'supplierContextName'
        );

    if (supplierContextName) {

        supplierContextName.textContent =
            supplierName || '-';

    }


    const supplierContextId =
        document.getElementById(
            'supplierContextId'
        );

    if (supplierContextId) {

        supplierContextId.value =
            supplierId;

    }


    /*
    |--------------------------------------------------------------------------
    | HELPER - SET SELECT VALUE
    |--------------------------------------------------------------------------
    */

    function setSelectValue(
        selector,
        value
    ) {

        const element =
            document.querySelector(
                selector
            );

        if (!element) {
            return;
        }


        value =
            value === null ||
            value === undefined
                ? ''
                : String(value);


        element.value =
            value;


        /*
        |--------------------------------------------------------------------------
        | SELECT2
        |--------------------------------------------------------------------------
        */

        if (
            typeof $ !== 'undefined' &&
            $(element).hasClass('select2-hidden-accessible')
        ) {

            $(element)
                .val(value)
                .trigger('change');

        } else {

            element.dispatchEvent(
                new Event(
                    'change',
                    {
                        bubbles: true
                    }
                )
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | HELPER - SET INPUT
    |--------------------------------------------------------------------------
    */

    function setInputValue(
        selector,
        value
    ) {

        const element =
            document.querySelector(
                selector
            );

        if (!element) {
            return;
        }


        element.value =
            value === null ||
            value === undefined
                ? ''
                : value;

    }


    /*
    |--------------------------------------------------------------------------
    | 1. GET PRODUCT DETAILS
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | The supplier-products endpoint should return the IDs
    | of master values.
    |
    | If it already returns those IDs, use them here.
    |
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | ITEM NAME
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#item_name',
        product.item_name_id ||
        product.item_name ||
        product.itemname_id ||
        product.itemnameid ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | ITEM TYPE
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#item_type',
        product.item_type_id ||
        product.item_type ||
        product.itemtype_id ||
        product.itemtypeid ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | DESIGNER
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#designer_name',
        product.designer_id ||
        product.designer ||
        product.designer_name_id ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | GENDER
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#gender_type',
        product.gender_id ||
        product.gender ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | COMPOSITION
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#composition',
        product.composition_id ||
        product.composition ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | COLOUR
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#colour',
        product.colour_id ||
        product.colour ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | SIZE
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#sizes',
        product.size_id ||
        product.sizes ||
        product.size ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | EMBELLISHMENT
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#embellishment',
        product.embellishment_id ||
        product.embellishment ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | MANUFACTURING PROCESS
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#manufacturing_process',
        product.manufacturing_process_id ||
        product.manufacturing_process ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | CRAFTSMAN
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#mcraftsman',
        product.craftsman_id ||
        product.craftsman ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | CRAFTSMAN CODE
    |--------------------------------------------------------------------------
    */

    setInputValue(
        '#craftsman_code',
        product.craftsman_code ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | MANUFACTURE
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#cmbmanufacture',
        product.manufecture_id ||
        product.manufacture_id ||
        product.manufecture ||
        product.manufacture ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | CLIENT / COLLECTION
    |--------------------------------------------------------------------------
    */

    setSelectValue(
        '#cmbclient',
        product.client_id ||
        product.client ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | SKU
    |--------------------------------------------------------------------------
    */

    setInputValue(
        '#sku',
        product.sku ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | CLIENT REFERENCE
    |--------------------------------------------------------------------------
    */

    setInputValue(
        '#txt_clientreference',
        product.clientreference ||
        product.client_reference ||
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | SUPPLIER PRODUCT NAME
    |--------------------------------------------------------------------------
    |
    | If supplier endpoint gives a product name but not item-name ID,
    | don't put the text into the select.
    |
    | The master ID should be used for #item_name.
    |
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | SHOW LOADING
    |--------------------------------------------------------------------------
    */

    Swal.fire({

        title:
            'Loading Supplier Product',

        text:
            'Loading product images...',

        allowOutsideClick:
            false,

        allowEscapeKey:
            false,

        didOpen:
            function () {

                Swal.showLoading();

            }

    });


    try {


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE IMAGE PATH
        |--------------------------------------------------------------------------
        */

        function normalizeImagePath(path)
        {

            if (!path) {
                return '';
            }


            path =
                String(path)
                    .trim();


            /*
            | Already URL
            */

            if (
                path.startsWith(
                    'http://'
                ) ||
                path.startsWith(
                    'https://'
                ) ||
                path.startsWith(
                    'blob:'
                ) ||
                path.startsWith(
                    'data:'
                )
            ) {

                return path;

            }


            /*
            | Remove quotes
            */

            path =
                path.replace(
                    /^["']|["']$/g,
                    ''
                );


            /*
            | Remove old ../../ path
            */

            const marker =
                'ItemsDesigner_Masterwithbarcode/';


            const markerPosition =
                path.indexOf(
                    marker
                );


            if (
                markerPosition !== -1
            ) {

                return '/' +
                    path.substring(
                        markerPosition
                    );

            }


            /*
            | Storage
            */

            if (
                path.startsWith(
                    '/storage/'
                )
            ) {

                return path;

            }


            if (
                path.startsWith(
                    'storage/'
                )
            ) {

                return '/' + path;

            }


            /*
            | Normal path
            */

            path =
                path.replace(
                    /^\/+/,
                    ''
                );


            return '/' + path;

        }


        /*
        |--------------------------------------------------------------------------
        | 2. MAIN IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            product.main_image
        ) {

            const mainUrl =
                normalizeImagePath(
                    product.main_image
                );


            console.log(
                'Supplier Main Image:',
                mainUrl
            );


            const response =
                await fetch(
                    mainUrl
                );


            if (!response.ok) {

                throw new Error(
                    'Unable to load supplier main image.'
                );

            }


            const blob =
                await response.blob();


            let fileName =
                String(
                    product.main_image
                )
                .split('/')
                .pop()
                .split('?')[0];


            if (!fileName) {

                fileName =
                    'supplier-main-image.jpg';

            }


            const file =
                new File(
                    [blob],
                    fileName,
                    {
                        type:
                            blob.type ||
                            'image/jpeg',

                        lastModified:
                            Date.now()
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | PUT INTO MAIN IMAGE INPUT
            |--------------------------------------------------------------------------
            */

            const mainImageInput =
                document.getElementById(
                    'filenew'
                );


            if (mainImageInput) {

                const dataTransfer =
                    new DataTransfer();


                dataTransfer.items.add(
                    file
                );


                mainImageInput.files =
                    dataTransfer.files;


                /*
                |--------------------------------------------------------------------------
                | IMPORTANT
                |--------------------------------------------------------------------------
                |
                | Your existing #filenew change handler
                | will update selectedFiles and preview.
                |
                |--------------------------------------------------------------------------
                */

                mainImageInput.dispatchEvent(
                    new Event(
                        'change',
                        {
                            bubbles: true
                        }
                    )
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | 3. SUPPLIER SUB IMAGES
        |--------------------------------------------------------------------------
        */

        selectedSubImages = [];


        let supplierSubImages =
            product.sub_images ||
            product.subimg_path ||
            [];


        /*
        |--------------------------------------------------------------------------
        | PARSE JSON
        |--------------------------------------------------------------------------
        */

        if (
            typeof supplierSubImages ===
            'string'
        ) {

            try {

                supplierSubImages =
                    JSON.parse(
                        supplierSubImages
                    );

            } catch (error) {

                console.warn(
                    'Supplier sub images JSON error:',
                    error
                );

                supplierSubImages = [];

            }

        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE ARRAY
        |--------------------------------------------------------------------------
        */

        if (
            !Array.isArray(
                supplierSubImages
            )
        ) {

            supplierSubImages = [];

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD SUB IMAGES
        |--------------------------------------------------------------------------
        */

        for (
            const subImagePath
            of supplierSubImages
        ) {

            if (!subImagePath) {
                continue;
            }


            const subUrl =
                normalizeImagePath(
                    subImagePath
                );


            console.log(
                'Supplier Sub Image:',
                subUrl
            );


            try {

                const response =
                    await fetch(
                        subUrl
                    );


                if (!response.ok) {

                    console.warn(
                        'Unable to load supplier sub image:',
                        subUrl
                    );

                    continue;

                }


                const blob =
                    await response.blob();


                let fileName =
                    String(
                        subImagePath
                    )
                    .split('/')
                    .pop()
                    .split('?')[0];


                if (!fileName) {

                    fileName =
                        'supplier-sub-image-' +
                        Date.now() +
                        '.jpg';

                }


                const file =
                    new File(
                        [blob],
                        fileName,
                        {
                            type:
                                blob.type ||
                                'image/jpeg',

                            lastModified:
                                Date.now()
                        }
                    );


                selectedSubImages.push(
                    file
                );


            } catch (error) {

                console.warn(
                    'Supplier sub image loading failed:',
                    subUrl,
                    error
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | RENDER SUB IMAGES
        |--------------------------------------------------------------------------
        */

        if (
            typeof renderSubImagesPreview ===
            'function'
        ) {

            renderSubImagesPreview();

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE SUPPLIER MODAL
        |--------------------------------------------------------------------------
        */

        const supplierModalElement =
            document.getElementById(
                'supplierProductModal'
            );


        if (
            supplierModalElement &&
            typeof bootstrap !== 'undefined' &&
            bootstrap.Modal
        ) {

            const supplierModal =
                bootstrap.Modal.getInstance(
                    supplierModalElement
                );


            if (supplierModal) {

                supplierModal.hide();

            }

        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        Swal.fire({

            icon:
                'success',

            title:
                'Product Selected',

            text:
                'Supplier product details and images have been loaded.',

            timer:
                1500,

            showConfirmButton:
                false

        });


        /*
        |--------------------------------------------------------------------------
        | DEBUG
        |--------------------------------------------------------------------------
        */

        console.log(
            'Supplier product loaded successfully:',
            product
        );


    } catch (error) {

        console.error(
            'SELECT SUPPLIER PRODUCT ERROR:',
            error
        );


        Swal.fire({

            icon:
                'error',

            title:
                'Unable to Load Product',

            text:
                error.message ||
                'Something went wrong while loading supplier product.'

        });

    }

}

function showSupplierProductInfo(product)
{
    const container =
        document.getElementById(
            'supplierProductInfo'
        );

    if (!container) {
        console.warn(
            'supplierProductInfo element not found'
        );
        return;
    }

    const rows = [
        ['Product ID', product.sno],
        ['Product Name', product.name],
        ['Supplier', product.supplier_name || product.suppliername],
        ['Stock', product.stock],
        ['Item Name', product.item_name],
        ['Item Type', product.item_type],
        ['Designer', product.designer],
        ['Gender', product.gender],
        ['Composition', product.composition],
        ['Colour', product.colour],
        ['Size', product.size || product.sizes],
        ['Embellishment', product.embellishment],
        [
            'Manufacturing Process',
            product.manufacturing_process
        ],
        ['Craftsman', product.craftsman],
        ['Craftsman Code', product.craftsman_code],
        [
            'Manufacture',
            product.manufacture ||
            product.manufecture
        ],
        ['Client / Collection', product.client],
        ['SKU', product.sku],
        [
            'Client Reference',
            product.clientreference ||
            product.client_reference
        ]
    ];

    let html = `
        <div style="
            font-size:15px;
            font-weight:700;
            margin-bottom:12px;
            color:#212529;
        ">
            Supplier Product Information
        </div>

        <table class="table table-sm table-bordered mb-0">
            <tbody>
    `;

    rows.forEach(function(row) {

        let value = row[1];

        if (
            value === null ||
            value === undefined ||
            value === ''
        ) {
            value = '-';
        }

        html += `
            <tr>
                <td style="
                    width:40%;
                    font-weight:600;
                    background:#f8f9fa;
                ">
                    ${escapeHtml(row[0])}
                </td>

                <td>
                    ${escapeHtml(String(value))}
                </td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}

</script>
@endsection