<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - Production Management System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 20px;

            background:
                linear-gradient(
                    135deg,
                    #f8fafc 0%,
                    #eef4fb 100%
                );

            font-family:
                Arial,
                Helvetica,
                sans-serif;
        }

        .login-wrapper {
            width: 100%;
            max-width: 460px;
        }

        .login-card {

            background: #ffffff;

            border-radius: 18px;

            padding: 35px;

            box-shadow:
                0 15px 45px rgba(15, 23, 42, 0.12);

            border: 1px solid #e5e7eb;
        }

        .login-logo {

            width: 72px;
            height: 72px;

            margin: 0 auto 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 18px;

            background: #2b5288;

            color: #ffffff;

            font-size: 32px;
        }

        .login-title {

            text-align: center;

            color: #0f172a;

            font-size: 25px;

            font-weight: 700;

            margin-bottom: 5px;
        }

        .login-subtitle {

            text-align: center;

            color: #64748b;

            margin-bottom: 28px;
        }

        .form-label {

            font-weight: 600;

            color: #334155;

            margin-bottom: 7px;
        }

        .form-control,
        .form-select {

            min-height: 48px;

            border-radius: 10px;

            border: 1px solid #cbd5e1;

            font-size: 15px;
        }

        .form-control:focus,
        .form-select:focus {

            border-color: #2b5288;

            box-shadow:
                0 0 0 3px rgba(43, 82, 136, 0.12);
        }

        .password-group {

            display: flex;
        }

        .password-group .form-control {

            border-radius: 10px 0 0 10px;
        }

        .password-toggle {

            width: 50px;

            border: 1px solid #cbd5e1;

            border-left: 0;

            border-radius: 0 10px 10px 0;

            background: #ffffff;
        }

        .password-toggle:hover {

            background: #f8fafc;
        }

        .btn-login {

            width: 100%;

            min-height: 50px;

            border: none;

            border-radius: 10px;

            background: #2b5288;

            color: #ffffff;

            font-size: 16px;

            font-weight: 600;

            transition: 0.2s;
        }

        .btn-login:hover {

            background: #234572;

            color: #ffffff;
        }

        .loading {

            display: none;
        }

        .required {

            color: #dc2626;
        }

        @media (max-width: 576px) {

            body {
                padding: 12px;
            }

            .login-card {
                padding: 25px 20px;
                border-radius: 14px;
            }

            .login-title {
                font-size: 22px;
            }

        }

    </style>

</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="login-logo">

            <i class="bi bi-building"></i>

        </div>

        <h2 class="login-title">
            Production Management
        </h2>

        <div class="login-subtitle">
            Sign in to continue
        </div>


        {{-- Error --}}

        @if(session('error'))

            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle me-2"></i>

                {{ session('error') }}
            </div>

        @endif


        {{-- Success --}}

        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


        {{-- Validation errors --}}

        @if($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0 ps-3">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('login.submit') }}"
            id="loginForm">

            @csrf


            {{-- Company --}}

            <div class="mb-3">

                <label class="form-label">

                    Company

                    <span class="required">*</span>

                </label>

                <select
                    name="company_id"
                    id="company_id"
                    class="form-select"
                    required>

                    <option value="">
                        Select Company
                    </option>

                    @foreach($companies as $company)

                        <option
                            value="{{ $company->companyid }}"
                            {{ old('company_id') == $company->companyid ? 'selected' : '' }}>

                            {{ $company->companyname }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Sub Company --}}

            <div class="mb-3">

                <label class="form-label">

                    Sub Company

                    <span class="required">*</span>

                </label>

                <select
                    name="sub_company_id"
                    id="sub_company_id"
                    class="form-select"
                    required
                    disabled>

                    <option value="">
                        Select Sub Company
                    </option>

                </select>

            </div>


            {{-- Project --}}

            <div class="mb-3">

                <label class="form-label">

                    Project

                    <span class="required">*</span>

                </label>

                <select
                    name="project_id"
                    id="project_id"
                    class="form-select"
                    required
                    disabled>

                    <option value="">
                        Select Project
                    </option>

                </select>

            </div>


            {{-- Username --}}

            <div class="mb-3">

                <label class="form-label">

                    Username

                    <span class="required">*</span>

                </label>

                <input
                    type="text"
                    name="username"
                    class="form-control"
                    value="{{ old('username') }}"
                    placeholder="Enter username"
                    autocomplete="username"
                    required>

            </div>


            {{-- Password --}}

            <div class="mb-3">

                <label class="form-label">

                    Password

                    <span class="required">*</span>

                </label>

                <div class="password-group">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter password"
                        autocomplete="current-password"
                        required>

                    <button
                        type="button"
                        class="password-toggle"
                        id="togglePassword">

                        <i
                            class="bi bi-eye"
                            id="passwordIcon">
                        </i>

                    </button>

                </div>

            </div>


            {{-- Remember --}}

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div class="form-check">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="remember"
                        value="1"
                        id="remember">

                    <label
                        class="form-check-label"
                        for="remember">

                        Remember me

                    </label>

                </div>

            </div>


            <button
                type="submit"
                class="btn btn-login"
                id="loginButton">

                <span class="normal-text">

                    <i class="bi bi-box-arrow-in-right me-2"></i>

                    Login

                </span>

                <span class="loading">

                    <span
                        class="spinner-border spinner-border-sm me-2">
                    </span>

                    Signing in...

                </span>

            </button>

        </form>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | Company → Sub Company
    |--------------------------------------------------------------------------
    */

    const companySelect =
        document.getElementById('company_id');

    const subCompanySelect =
        document.getElementById('sub_company_id');

    const projectSelect =
        document.getElementById('project_id');


    companySelect.addEventListener('change', function () {

    const companyId = this.value;

    // Reset Sub Company
    subCompanySelect.innerHTML =
        '<option value="">Select Sub Company</option>';

    subCompanySelect.disabled = true;

    // Reset Project
    projectSelect.innerHTML =
        '<option value="">Select Project</option>';

    projectSelect.disabled = true;


    if (!companyId) {
        return;
    }


    // Show loading
    subCompanySelect.innerHTML =
        '<option value="">Loading...</option>';


    fetch(
        '{{ route("login.subcompanies") }}?company_id='
        + encodeURIComponent(companyId)
    )

    .then(response => {

        if (!response.ok) {
            throw new Error(
                'HTTP error: ' + response.status
            );
        }

        return response.json();

    })

    .then(result => {

        console.log('Sub Company response:', result);


        // Reset dropdown
        subCompanySelect.innerHTML =
            '<option value="">Select Sub Company</option>';


        // Laravel returns a plain array
        if (Array.isArray(result) && result.length > 0) {

            result.forEach(function (item) {

                const option =
                    document.createElement('option');


                // IMPORTANT:
                // Login needs subcompanyid = 7
                option.value =
                    item.subcompanyid;


                option.textContent =
                    item.subcompanyname;


                subCompanySelect.appendChild(option);

            });


            subCompanySelect.disabled = false;


        } else {

            subCompanySelect.innerHTML =
                '<option value="">No Sub Company found</option>';

        }

    })

    .catch(error => {

        console.error(
            'Sub Company Error:',
            error
        );


        subCompanySelect.innerHTML =
            '<option value="">Unable to load</option>';

        subCompanySelect.disabled = true;

    });

});


    /*
    |--------------------------------------------------------------------------
    | Sub Company → Project
    |--------------------------------------------------------------------------
    */

 /*
|--------------------------------------------------------------------------
| Sub Company → Project
|--------------------------------------------------------------------------
*/

subCompanySelect.addEventListener('change', function () {

    const companyId =
        companySelect.value;

    const subCompanyId =
        this.value;


    // Reset Project
    projectSelect.innerHTML =
        '<option value="">Select Project</option>';

    projectSelect.disabled = true;


    if (!companyId || !subCompanyId) {
        return;
    }


    // Show loading
    projectSelect.innerHTML =
        '<option value="">Loading...</option>';


    fetch(
        '{{ route("login.projects") }}'
        + '?company_id='
        + encodeURIComponent(companyId)
        + '&sub_company_id='
        + encodeURIComponent(subCompanyId)
    )

    .then(response => {

        if (!response.ok) {

            throw new Error(
                'HTTP error: ' + response.status
            );

        }

        return response.json();

    })

    .then(result => {

        console.log(
            'Project response:',
            result
        );


        projectSelect.innerHTML =
            '<option value="">Select Project</option>';


        if (
            Array.isArray(result) &&
            result.length > 0
        ) {

            result.forEach(function (item) {

                const option =
                    document.createElement('option');


                option.value =
                    item.projectid;


                option.textContent =
                    item.projectname;


                projectSelect.appendChild(option);

            });


            projectSelect.disabled = false;


        } else {

            projectSelect.innerHTML =
                '<option value="">No Project found</option>';

        }

    })

    .catch(error => {

        console.error(
            'Project Error:',
            error
        );


        projectSelect.innerHTML =
            '<option value="">Unable to load</option>';

        projectSelect.disabled = true;

    });

});

    /*
    |--------------------------------------------------------------------------
    | Password Show / Hide
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('togglePassword')
        .addEventListener('click', function () {

            const password =
                document.getElementById('password');

            const icon =
                document.getElementById('passwordIcon');


            if (password.type === 'password') {

                password.type = 'text';

                icon.classList.remove('bi-eye');

                icon.classList.add('bi-eye-slash');

            } else {

                password.type = 'password';

                icon.classList.remove('bi-eye-slash');

                icon.classList.add('bi-eye');

            }

        });


    /*
    |--------------------------------------------------------------------------
    | Login Loading
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('loginForm')
        .addEventListener('submit', function () {

            const button =
                document.getElementById('loginButton');

            button.disabled = true;

            button.querySelector('.normal-text')
                .style.display = 'none';

            button.querySelector('.loading')
                .style.display = 'inline';

        });

});

</script>

</body>

</html>