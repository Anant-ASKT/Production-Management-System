@extends('layouts.app')

@section('title', 'Make All Masters')

@section('content')
<div class="container-fluid py-3 make-all-masters-page">

    <div class="page-header mb-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="page-title-icon">
                <i class="bi bi-database-gear"></i>
            </div>
            <div>
                <h3 class="page-title mb-1">Make All Masters</h3>
                <p class="page-subtitle mb-0">
                    Create and manage all masters from one place
                </p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @php
            $masters = [
                ['key' => 'item_name', 'title' => 'Item Name', 'icon' => 'bi-box-seam', 'description' => 'Manage item names'],
                ['key' => 'item_type', 'title' => 'Item Type', 'icon' => 'bi-tags', 'description' => 'Manage item types'],
                ['key' => 'designer', 'title' => 'Designer', 'icon' => 'bi-person-badge', 'description' => 'Manage designers'],
                ['key' => 'gender', 'title' => 'Gender', 'icon' => 'bi-gender-ambiguous', 'description' => 'Manage gender types'],
                ['key' => 'composition', 'title' => 'Composition', 'icon' => 'bi-layers', 'description' => 'Manage compositions'],
                ['key' => 'yarn', 'title' => 'Yarn Name', 'icon' => 'bi-bezier2', 'description' => 'Manage yarn names'],
                ['key' => 'colour', 'title' => 'Colour', 'icon' => 'bi-palette2', 'description' => 'Manage colours'],
                ['key' => 'size', 'title' => 'Size', 'icon' => 'bi-rulers', 'description' => 'Manage sizes'],
                ['key' => 'embellishment', 'title' => 'Embellishment', 'icon' => 'bi-stars', 'description' => 'Manage embellishments'],
                ['key' => 'manufacturing_process', 'title' => 'Manufacturing Process', 'icon' => 'bi-gear-wide-connected', 'description' => 'Manage manufacturing processes'],
                ['key' => 'craftsman', 'title' => 'Craftsman', 'icon' => 'bi-person-workspace', 'description' => 'Manage craftsmen'],
                ['key' => 'manufacture', 'title' => 'Manufacture', 'icon' => 'bi-tools', 'description' => 'Manage manufacture records'],
                ['key' => 'client', 'title' => 'Collection', 'icon' => 'bi-collection', 'description' => 'Manage collections / clients'],
            ];
        @endphp

        @foreach($masters as $master)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="master-card h-100" data-master="{{ $master['key'] }}">
                    <div class="master-card-icon">
                        <i class="bi {{ $master['icon'] }}"></i>
                    </div>

                    <div class="master-card-content">
                        <h5>{{ $master['title'] }}</h5>
                        <p>{{ $master['description'] }}</p>
                    </div>

                    <button
                        type="button"
                        class="btn btn-primary btn-open-master"
                        data-master="{{ $master['key'] }}">
                        <i class="bi bi-plus-lg me-1"></i>
                        Manage
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- MASTER MANAGEMENT MODAL -->
<div
    class="modal fade"
    id="masterManagementModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="masterModalTitle">
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
                    aria-label="Close">
                </button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="masterModalType">
                <input type="hidden" id="masterSelectedId" value="">

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
                        autocomplete="off">
                </div>

                <div
                    class="mb-3"
                    id="masterCodeWrapper"
                    style="display:block;">

                    <label class="form-label">
                        Code
                    </label>

                    <input
                        type="text"
                        id="masterCodeInput"
                        class="form-control"
                        placeholder="Enter 3 to 4 character code"
                        autocomplete="off"
                        minlength="3"
                        maxlength="4">
                </div>

                <div class="d-flex gap-2 mb-4">
                    <button
                        type="button"
                        class="btn btn-success"
                        id="btnMasterAdd">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add New
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="btnMasterUpdate"
                        disabled>
                        <i class="bi bi-pencil me-1"></i>
                        Update Selected
                    </button>

                    <button
                        type="button"
                        class="btn btn-light"
                        id="btnMasterClear">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Clear
                    </button>
                </div>

                <div class="border rounded overflow-hidden">

                    <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <strong>
                            Existing
                            <span id="masterListTitle">Masters</span>
                        </strong>

                        <span
                            id="masterListCount"
                            class="badge bg-primary">
                            0
                        </span>
                    </div>

                    <div class="p-3 border-bottom">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input
                                type="text"
                                id="masterSearchInput"
                                class="form-control"
                                placeholder="Search master..."
                                autocomplete="off">

                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                id="btnClearMasterSearch"
                                title="Clear search">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div
                        id="masterListLoading"
                        class="text-center p-4"
                        style="display:none;">
                        <div class="spinner-border text-primary"></div>
                        <div class="mt-2">Loading...</div>
                    </div>

                    <div
                        id="masterListEmpty"
                        class="text-center text-muted p-4"
                        style="display:none;">
                        No records found.
                    </div>

                    <div
                        id="masterListContainer"
                        style="max-height:350px; overflow-y:auto;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.make-all-masters-page {
    min-height: calc(100vh - 80px);
}

.page-header {
    padding: 8px 4px;
}

.page-title-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eef2ff;
    color: #4f46e5;
    font-size: 23px;
}

.page-title {
    font-weight: 700;
    color: #172033;
}

.page-subtitle {
    color: #718096;
    font-size: 14px;
}

.master-card {
    padding: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(15, 23, 42, .05);
    display: flex;
    flex-direction: column;
    transition: .18s ease;
}

.master-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(15, 23, 42, .10);
    border-color: #c7d2fe;
}

.master-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #334155;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 14px;
}

.master-card-content {
    flex: 1;
}

.master-card h5 {
    margin-bottom: 5px;
    font-size: 16px;
    font-weight: 700;
    color: #172033;
}

.master-card p {
    margin-bottom: 18px;
    font-size: 13px;
    color: #64748b;
}

.master-card .btn {
    align-self: flex-start;
}

#masterManagementModal {
    z-index: 99999 !important;
}

.modal-backdrop {
    z-index: 99998 !important;
}

#masterManagementModal .modal-dialog {
    z-index: 100000 !important;
}

.master-list-row {
    cursor: pointer;
}

.master-list-row:hover {
    background: #f8fafc;
}

.master-list-row.table-primary {
    background: #e0e7ff !important;
}

.master-list-code {
    font-family: monospace;
    font-size: 12px;
    color: #475569;
}

@media (max-width: 575px) {
    .master-card {
        padding: 16px;
    }

    #masterManagementModal .modal-dialog {
        margin: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let masterModal = null;
    let currentMaster = '';
    let masterRows = [];
    let masterSearchText = '';
    let codeManuallyEdited = false;

    const modalElement =
        document.getElementById('masterManagementModal');

    if (!modalElement) {
        return;
    }

    const modalTitle =
        document.getElementById('masterModalTitle');

    const masterModalType =
        document.getElementById('masterModalType');

    const selectedIdInput =
        document.getElementById('masterSelectedId');

    const nameInput =
        document.getElementById('masterNameInput');

    const codeInput =
        document.getElementById('masterCodeInput');

    const codeWrapper =
        document.getElementById('masterCodeWrapper');

    const listTitle =
        document.getElementById('masterListTitle');

    const listContainer =
        document.getElementById('masterListContainer');

    const loading =
        document.getElementById('masterListLoading');

    const empty =
        document.getElementById('masterListEmpty');

    const listCount =
        document.getElementById('masterListCount');

    const searchInput =
        document.getElementById('masterSearchInput');

    const btnAdd =
        document.getElementById('btnMasterAdd');

    const btnUpdate =
        document.getElementById('btnMasterUpdate');

    const btnClear =
        document.getElementById('btnMasterClear');

    const btnClearSearch =
        document.getElementById('btnClearMasterSearch');

    const csrfToken =
        document.querySelector(
            'meta[name="csrf-token"]'
        )?.getAttribute('content') ||
        '{{ csrf_token() }}';

    function getMasterModal() {

        if (
            typeof bootstrap === 'undefined' ||
            !bootstrap.Modal
        ) {
            Swal.fire({
                icon: 'error',
                title: 'Bootstrap Error',
                text: 'Bootstrap JavaScript is not loaded on this page.'
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

    function getMasterTitle(master) {

        const titles = {
            item_name: 'Item Name Master',
            item_type: 'Item Type Master',
            designer: 'Designer Master',
            gender: 'Gender Master',
            composition: 'Composition Master',
            yarn: 'Yarn Master',
            colour: 'Colour Master',
            size: 'Size Master',
            embellishment: 'Embellishment Master',
            manufacturing_process: 'Manufacturing Process Master',
            craftsman: 'Craftsman Master',
            manufacture: 'Manufacture Master',
            client: 'Collection Master'
        };

        return titles[master] || 'Master';
    }

    function clearMasterForm() {

        nameInput.value = '';
        selectedIdInput.value = '';
        btnUpdate.disabled = true;

        codeManuallyEdited = false;
        codeInput.readOnly = false;
        codeInput.value = '';

        document
            .querySelectorAll('.master-list-row')
            .forEach(function (row) {
                row.classList.remove('table-primary');
            });

        nameInput.focus();
    }

    function generateMasterCode(name) {

        const value =
            String(name || '').trim();

        if (!value) {
            return '';
        }

        const prefix =
            value
                .split(/\s+/)
                .filter(Boolean)
                .map(function (word) {
                    return word.charAt(0);
                })
                .join('')
                .replace(/[^A-Za-z0-9]/g, '')
                .toUpperCase();

        return (prefix + 'XXX').substring(0, 3);
    }

    function renderMasterList() {

        listContainer.innerHTML = '';

        const search =
            masterSearchText
                .trim()
                .toLowerCase();

        const filtered =
            masterRows.filter(function (row) {

                const values =
                    Object.values(row)
                        .map(function (value) {
                            return String(value ?? '').toLowerCase();
                        })
                        .join(' ');

                return !search || values.includes(search);
            });

        listCount.textContent = filtered.length;

        if (!filtered.length) {
            empty.style.display = 'block';
            return;
        }

        empty.style.display = 'none';

        filtered.forEach(function (row) {

            const rowElement =
                document.createElement('div');

            rowElement.className =
                'master-list-row border-bottom p-3 d-flex justify-content-between align-items-center gap-3';

            const name =
                row[currentMaster + '_name'] ||
                row.itemname ||
                row.itemtype ||
                row.designername ||
                row.name ||
                row.composition_details ||
                row.yarnname ||
                row.colourname ||
                row.size ||
                row.embellishmentname ||
                row.manufacturing_process ||
                '';

            const code =
                row.code || '';

            rowElement.innerHTML = `
                <div class="flex-grow-1">
                    <div class="fw-semibold">
                        ${escapeHtml(name)}
                    </div>
                    ${
                        code
                            ? `<div class="master-list-code mt-1">
                                Code: ${escapeHtml(code)}
                               </div>`
                            : ''
                    }
                </div>

                <div class="text-muted small">
                    ID: ${escapeHtml(row.id)}
                </div>
            `;

            rowElement.addEventListener(
                'click',
                function () {

                    document
                        .querySelectorAll('.master-list-row')
                        .forEach(function (item) {
                            item.classList.remove(
                                'table-primary'
                            );
                        });

                    rowElement.classList.add(
                        'table-primary'
                    );

                    selectedIdInput.value =
                        row.id || '';

                    nameInput.value =
                        name;

                    codeInput.value =
                        code || generateMasterCode(name);

                    codeManuallyEdited = true;
                    codeInput.readOnly = false;

                    btnUpdate.disabled =
                        !selectedIdInput.value;

                    nameInput.focus();
                }
            );

            listContainer.appendChild(rowElement);
        });
    }

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

    function openMaster(master) {

        currentMaster = master;

        masterModalType.value =
            currentMaster;

        modalTitle.textContent =
            getMasterTitle(currentMaster);

        listTitle.textContent =
            getMasterTitle(currentMaster);

        /*
         * All masters use a 3-4 character code.
         * A new code is generated automatically from the name
         * and remains editable by the user.
         */
        codeWrapper.style.display = 'block';

        clearMasterForm();

        const modal =
            getMasterModal();

        if (!modal) {
            return;
        }

        modal.show();

        loadMasterList();
    }

    document
        .querySelectorAll('.btn-open-master')
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {
                    openMaster(
                        button.dataset.master
                    );
                }
            );
        });

    btnAdd.addEventListener(
        'click',
        async function () {

            const name =
                nameInput.value.trim();

            if (!name) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please check',
                    text: 'Please enter master name.'
                }).then(function () {
                    nameInput.focus();
                });

                return;
            }

            const code =
                codeInput.value.trim();

            if (
                code.length < 3 ||
                code.length > 4
            ) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please check',
                    text: 'Code must be 3 or 4 characters.'
                }).then(function () {
                    codeInput.focus();
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
                                name: name,
                                code:
                                    currentMaster === 'craftsman'
                                        ? codeInput.value.trim()
                                        : ''
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

    btnUpdate.addEventListener(
        'click',
        async function () {

            const id =
                String(
                    selectedIdInput.value || ''
                ).trim();

            const name =
                nameInput.value.trim();

            if (!id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please check',
                    text: 'Please select a master from the list.'
                });

                return;
            }

            if (!name) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please check',
                    text: 'Master name cannot be empty.'
                }).then(function () {
                    nameInput.focus();
                });

                return;
            }

            const code =
                codeInput.value.trim();

            if (
                code.length < 3 ||
                code.length > 4
            ) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please check',
                    text: 'Code must be 3 or 4 characters.'
                }).then(function () {
                    codeInput.focus();
                });

                return;
            }

            btnUpdate.disabled = true;

            try {

                const response =
                    await fetch(
                        `/admin/design-specifications/master/${encodeURIComponent(currentMaster)}/${encodeURIComponent(id)}`,
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
                                name: name,
                                code:
                                    currentMaster === 'craftsman'
                                        ? codeInput.value.trim()
                                        : ''
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
                    'MASTER UPDATE ERROR:',
                    error
                );

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });

            } finally {
                btnUpdate.disabled = false;
            }
        }
    );

    btnClear.addEventListener(
        'click',
        function () {
            clearMasterForm();
        }
    );

    searchInput.addEventListener(
        'input',
        function () {
            masterSearchText =
                this.value || '';

            renderMasterList();
        }
    );

    btnClearSearch.addEventListener(
        'click',
        function () {
            searchInput.value = '';
            masterSearchText = '';
            renderMasterList();
            searchInput.focus();
        }
    );

    nameInput.addEventListener(
        'input',
        function () {

            if (codeManuallyEdited) {
                return;
            }

            codeInput.value =
                generateMasterCode(
                    this.value
                );
        }
    );

    codeInput.addEventListener(
        'input',
        function () {

            codeManuallyEdited = true;

            this.value =
                this.value
                    .replace(/\s/g, '')
                    .substring(0, 4)
                    .toUpperCase();
        }
    );

    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return '';
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
</script>
@endsection