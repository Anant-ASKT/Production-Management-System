@extends('layouts.app')

@section('title', 'Module Access')

@section('page-title', 'Module Access')


@section('content')

<div class="container-fluid p-0">


    {{-- PAGE HEADER --}}

    <div class="d-flex flex-column flex-md-row
                align-items-md-center
                justify-content-between
                gap-3 mb-4">

        <div>

            <h2 class="mb-1"
                style="font-size:22px;font-weight:700;">

                Module Access

            </h2>

            <p class="text-muted mb-0"
               style="font-size:13px;">

                Assign application modules and permissions to users.

            </p>

        </div>

    </div>


    {{-- SUCCESS MESSAGE --}}

    @if(session('success'))

        <div class="alert alert-success
                    d-flex align-items-center
                    gap-2">

            <i class="bi bi-check-circle-fill"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- USER SELECT --}}

    <div class="card border-0 shadow-sm mb-4"
         style="border-radius:14px;">

        <div class="card-body p-3 p-md-4">

            <form
                method="GET"
                action="{{ route('admin.module-access') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-12 col-md-8 col-lg-6">

                        <label
                            class="form-label fw-semibold"
                            style="font-size:13px;">

                            Select User

                            <span class="text-danger">
                                *
                            </span>

                        </label>


                        <select
                            name="user_id"
                            class="form-select"
                            required>

                            <option value="">
                                Select User
                            </option>

                            @foreach($users as $user)

                                <option
                                    value="{{ $user->id }}"
                                    @selected(
                                        $selectedUser &&
                                        $selectedUser->id == $user->id
                                    )>

                                    {{ $user->name }}
                                    -
                                    {{ $user->username }}

                                    @if($user->isAdmin())
                                        (Admin)
                                    @else
                                        (User)
                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-12 col-md-auto">

                        <button
                            type="submit"
                            class="btn btn-primary px-4">

                            <i class="bi bi-search me-1"></i>

                            Load Permissions

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    @if($selectedUser)


        {{-- SELECTED USER --}}

        <div class="card border-0 shadow-sm mb-4"
             style="border-radius:14px;">

            <div class="card-body p-3 p-md-4">

                <div class="d-flex
                            align-items-center
                            gap-3">

                    <div
                        style="
                            width:48px;
                            height:48px;
                            border-radius:50%;
                            background:#eaf1f9;
                            color:#2b5288;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-weight:700;
                        ">

                        {{ strtoupper(
                            substr($selectedUser->name, 0, 1)
                        ) }}

                    </div>


                    <div>

                        <div
                            style="
                                font-weight:700;
                                font-size:15px;
                            ">

                            {{ $selectedUser->name }}

                        </div>

                        <div
                            class="text-muted"
                            style="font-size:12px;">

                            Username:
                            {{ $selectedUser->username }}

                            <span class="mx-1">
                                •
                            </span>

                            Role:
                            {{ ucfirst($selectedUser->role) }}

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- PERMISSIONS --}}

        <form
            method="POST"
            action="{{ route('admin.module-access.save') }}">

            @csrf

            <input
                type="hidden"
                name="user_id"
                value="{{ $selectedUser->id }}">


            <div class="card border-0 shadow-sm"
                 style="border-radius:14px;">

                <div class="card-header bg-white border-0
                            p-3 p-md-4">

                    <div class="d-flex
                                flex-column
                                flex-md-row
                                justify-content-between
                                align-items-md-center
                                gap-2">

                        <div>

                            <h5 class="mb-1"
                                style="font-weight:700;">

                                Module Permissions

                            </h5>

                            <p
                                class="text-muted mb-0"
                                style="font-size:12px;">

                                Select the permissions this user should have.

                            </p>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-check2-circle me-1"></i>

                            Save Permissions

                        </button>

                    </div>

                </div>


                {{-- DESKTOP TABLE --}}

                <div class="table-responsive d-none d-md-block">

                    <table
                        class="table align-middle mb-0">

                        <thead>

                            <tr>

                                <th
                                    style="
                                        padding-left:24px;
                                        min-width:250px;
                                    ">

                                    Module

                                </th>

                                <th class="text-center">
                                    View
                                </th>

                                <th class="text-center">
                                    Add
                                </th>

                                <th class="text-center">
                                    Edit
                                </th>

                                <th class="text-center">
                                    Delete
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($modules as $module)

                                @php

                                    $permission =
                                        $permissions[$module->id]
                                        ?? [];

                                @endphp


                                <tr>

                                    <td
                                        style="
                                            padding-left:24px;
                                        ">

                                        <div
                                            class="d-flex
                                                   align-items-center
                                                   gap-3">

                                            <div
                                                style="
                                                    width:38px;
                                                    height:38px;
                                                    border-radius:9px;
                                                    background:#eaf1f9;
                                                    color:#2b5288;
                                                    display:flex;
                                                    align-items:center;
                                                    justify-content:center;
                                                ">

                                                <i
                                                    class="bi {{ $module->icon ?: 'bi-grid' }}">
                                                </i>

                                            </div>


                                            <div>

                                                <div
                                                    style="
                                                        font-size:13px;
                                                        font-weight:600;
                                                    ">

                                                    {{ $module->module_name }}

                                                </div>

                                                <div
                                                    class="text-muted"
                                                    style="font-size:10px;">

                                                    {{ $module->module_slug }}

                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- VIEW --}}

                                    <td class="text-center">

                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="permissions[{{ $module->id }}][can_view]"
                                            value="1"
                                            @checked(
                                                $permission['can_view']
                                                ?? false
                                            )>

                                    </td>


                                    {{-- ADD --}}

                                    <td class="text-center">

                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="permissions[{{ $module->id }}][can_add]"
                                            value="1"
                                            @checked(
                                                $permission['can_add']
                                                ?? false
                                            )>

                                    </td>


                                    {{-- EDIT --}}

                                    <td class="text-center">

                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="permissions[{{ $module->id }}][can_edit]"
                                            value="1"
                                            @checked(
                                                $permission['can_edit']
                                                ?? false
                                            )>

                                    </td>


                                    {{-- DELETE --}}

                                    <td class="text-center">

                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="permissions[{{ $module->id }}][can_delete]"
                                            value="1"
                                            @checked(
                                                $permission['can_delete']
                                                ?? false
                                            )>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
                                        class="text-center
                                               text-muted
                                               py-5">

                                        No active modules found.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- MOBILE CARDS --}}

                <div class="d-md-none p-3">

                    @forelse($modules as $module)

                        @php

                            $permission =
                                $permissions[$module->id]
                                ?? [];

                        @endphp


                        <div
                            class="border rounded-3 p-3 mb-3">

                            <div
                                class="d-flex
                                       align-items-center
                                       gap-3 mb-3">

                                <div
                                    style="
                                        width:40px;
                                        height:40px;
                                        border-radius:9px;
                                        background:#eaf1f9;
                                        color:#2b5288;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                    ">

                                    <i
                                        class="bi {{ $module->icon ?: 'bi-grid' }}">
                                    </i>

                                </div>


                                <div>

                                    <div
                                        style="
                                            font-weight:600;
                                            font-size:13px;
                                        ">

                                        {{ $module->module_name }}

                                    </div>

                                    <div
                                        class="text-muted"
                                        style="font-size:10px;">

                                        {{ $module->module_slug }}

                                    </div>

                                </div>

                            </div>


                            <div class="row g-2">

                                <div class="col-6">

                                    <label
                                        class="border rounded-2
                                               p-2 w-100
                                               d-flex
                                               align-items-center
                                               gap-2">

                                        <input
                                            type="checkbox"
                                            class="form-check-input m-0"
                                            name="permissions[{{ $module->id }}][can_view]"
                                            value="1"
                                            @checked(
                                                $permission['can_view']
                                                ?? false
                                            )>

                                        <span
                                            style="font-size:12px;">
                                            View
                                        </span>

                                    </label>

                                </div>


                                <div class="col-6">

                                    <label
                                        class="border rounded-2
                                               p-2 w-100
                                               d-flex
                                               align-items-center
                                               gap-2">

                                        <input
                                            type="checkbox"
                                            class="form-check-input m-0"
                                            name="permissions[{{ $module->id }}][can_add]"
                                            value="1"
                                            @checked(
                                                $permission['can_add']
                                                ?? false
                                            )>

                                        <span
                                            style="font-size:12px;">
                                            Add
                                        </span>

                                    </label>

                                </div>


                                <div class="col-6">

                                    <label
                                        class="border rounded-2
                                               p-2 w-100
                                               d-flex
                                               align-items-center
                                               gap-2">

                                        <input
                                            type="checkbox"
                                            class="form-check-input m-0"
                                            name="permissions[{{ $module->id }}][can_edit]"
                                            value="1"
                                            @checked(
                                                $permission['can_edit']
                                                ?? false
                                            )>

                                        <span
                                            style="font-size:12px;">
                                            Edit
                                        </span>

                                    </label>

                                </div>


                                <div class="col-6">

                                    <label
                                        class="border rounded-2
                                               p-2 w-100
                                               d-flex
                                               align-items-center
                                               gap-2">

                                        <input
                                            type="checkbox"
                                            class="form-check-input m-0"
                                            name="permissions[{{ $module->id }}][can_delete]"
                                            value="1"
                                            @checked(
                                                $permission['can_delete']
                                                ?? false
                                            )>

                                        <span
                                            style="font-size:12px;">
                                            Delete
                                        </span>

                                    </label>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div
                            class="text-center
                                   text-muted
                                   py-5">

                            No active modules found.

                        </div>

                    @endforelse

                </div>


                {{-- BOTTOM SAVE --}}

                @if($modules->count())

                    <div
                        class="border-top
                               p-3 p-md-4
                               d-flex
                               justify-content-end">

                        <button
                            type="submit"
                            class="btn btn-primary px-4">

                            <i class="bi bi-check2-circle me-1"></i>

                            Save Permissions

                        </button>

                    </div>

                @endif

            </div>

        </form>

    @endif

</div>

@endsection