<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CompanyMaster;
use App\Models\CompanySubMaster;
use App\Models\ProjectMaster;
use App\Models\PMUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show login page.
     */
    public function showLogin()
    {
        /*
        |--------------------------------------------------------------------------
        | If already logged in
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {

            $user = Auth::user();

            if ($user instanceof PMUser && $user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('user.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Company list
        |--------------------------------------------------------------------------
        */

        $companies = CompanyMaster::orderBy('companyname')
            ->get();

        return response()
            ->view('auth.login', compact('companies'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }


    /**
     * Login user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'company_id' => ['required', 'integer'],
            'sub_company_id' => ['required', 'integer'],
            'project_id' => ['required', 'integer'],
        ]);


        $user = PMUser::where('username', $request->username)
            ->where('company_id', $request->company_id)
            ->where('sub_company_id', $request->sub_company_id)
            ->where('project_id', $request->project_id)
            ->where('status', true)
            ->first();


        if (
            !$user ||
            !Hash::check(
                $request->password,
                $user->password
            )
        ) {

            return back()
                ->withInput(
                    $request->except('password')
                )
                ->with(
                    'error',
                    'Invalid username, password or company/project selection.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        Auth::login(
            $user,
            $request->boolean('remember')
        );


        /*
        |--------------------------------------------------------------------------
        | Regenerate session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        if ($user->isAdmin()) {

            return redirect()
                ->route('admin.dashboard')
                ->with('login_success', true);
        }


        /*
        |--------------------------------------------------------------------------
        | Normal user
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('user.dashboard')
            ->with('login_success', true);
    }


    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Logout from Laravel
        |--------------------------------------------------------------------------
        */

        Auth::logout();


        /*
        |--------------------------------------------------------------------------
        | Completely invalidate current session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();


        /*
        |--------------------------------------------------------------------------
        | Generate new CSRF token
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerateToken();


        /*
        |--------------------------------------------------------------------------
        | Redirect to login without caching
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out successfully.');
    }


    /**
     * Get sub companies.
     */
    public function getSubCompanies(Request $request)
{
    $companyId = $request->input('company_id');

    if (!$companyId) {
        return response()->json([]);
    }

    $subCompanies = CompanySubMaster::where(
            'companyid',
            $companyId
        )
        ->orderBy('subcompanyname', 'asc')
        ->get([
            'sno',
            'companyid',
            'subcompanyid',
            'subcompanyname'
        ]);

    return response()->json($subCompanies);
}

    /**
     * Get projects.
     */
    public function getProjects(Request $request)
    {
        $companyId = $request->get('company_id');
        $subCompanyId = $request->get('sub_company_id');

        $projects = ProjectMaster::where(
                'companyid',
                $companyId
            )
            ->where(
                'subcompanyid',
                $subCompanyId
            )
            ->orderBy('projectname')
            ->get();

        return response()->json($projects);
    }
}