<?php

namespace App\Http\Controllers;

use App\Models\PMModule;
use App\Models\PMUser;
use App\Models\PMUserModuleAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModuleAccessController extends Controller
{
    /**
     * Show module access page.
     */
   public function index(Request $request)
    {
        /** @var PMUser $loggedUser */
        $loggedUser = Auth::user();

        if (!$loggedUser) {
            return redirect()->route('login');
        }

        if (strtolower(trim((string) $loggedUser->role)) !== 'admin') {
            abort(403, 'You are not authorized to manage module access.');
        }

        $users = PMUser::where('company_id', $loggedUser->company_id)
            ->where('sub_company_id', $loggedUser->sub_company_id)
            ->where('project_id', $loggedUser->project_id)
            ->orderBy('name')
            ->get();

        $selectedUser = null;
        $modules = collect();
        $permissions = [];

        if ($request->filled('user_id')) {

            $selectedUser = PMUser::where('id', $request->user_id)
                ->where('company_id', $loggedUser->company_id)
                ->where('sub_company_id', $loggedUser->sub_company_id)
                ->where('project_id', $loggedUser->project_id)
                ->first();

            if ($selectedUser) {

                $modules = PMModule::where('company_id', $loggedUser->company_id)
                    ->where('sub_company_id', $loggedUser->sub_company_id)
                    ->where('project_id', $loggedUser->project_id)
                    ->where('status', 1)
                    ->orderBy('sort_order')
                    ->get();

                $accessRows = PMUserModuleAccess::where('company_id', $loggedUser->company_id)
                    ->where('sub_company_id', $loggedUser->sub_company_id)
                    ->where('project_id', $loggedUser->project_id)
                    ->where('user_id', $selectedUser->id)
                    ->get();

                foreach ($accessRows as $access) {

                    $permissions[$access->module_id] = [
                        'can_view' => $access->can_view,
                        'can_add' => $access->can_add,
                        'can_edit' => $access->can_edit,
                        'can_delete' => $access->can_delete,
                    ];
                }
            }
        }

        return view('admin.module-access', compact(
            'users',
            'selectedUser',
            'modules',
            'permissions'
        ));
    }


    /**
     * Save module permissions.
     */
    public function save(Request $request)
    {
        /** @var PMUser $loggedUser */
        $loggedUser = Auth::user();

        if (!$loggedUser) {
                return redirect()->route('login');
            }

            if (strtolower(trim((string) $loggedUser->role)) !== 'admin') {
                abort(403, 'You are not authorized to manage module access.');
            }

        $request->validate([
            'user_id' => ['required', 'integer'],
            'permissions' => ['nullable', 'array'],
        ]);

        $selectedUser = PMUser::where('id', $request->user_id)
            ->where('company_id', $loggedUser->company_id)
            ->where('sub_company_id', $loggedUser->sub_company_id)
            ->where('project_id', $loggedUser->project_id)
            ->firstOrFail();

        $modules = PMModule::where('company_id', $loggedUser->company_id)
            ->where('sub_company_id', $loggedUser->sub_company_id)
            ->where('project_id', $loggedUser->project_id)
            ->where('status', 1)
            ->get();

        $permissions = $request->input('permissions', []);

        DB::transaction(function () use (
            $loggedUser,
            $selectedUser,
            $modules,
            $permissions
        ) {

            foreach ($modules as $module) {

                $modulePermission =
                    $permissions[$module->id] ?? [];

                $canView =
                    !empty($modulePermission['can_view']);

                $canAdd =
                    !empty($modulePermission['can_add']);

                $canEdit =
                    !empty($modulePermission['can_edit']);

                $canDelete =
                    !empty($modulePermission['can_delete']);

                PMUserModuleAccess::updateOrCreate(

                    [
                        'company_id' => $loggedUser->company_id,
                        'sub_company_id' => $loggedUser->sub_company_id,
                        'project_id' => $loggedUser->project_id,
                        'user_id' => $selectedUser->id,
                        'module_id' => $module->id,
                    ],

                    [
                        'can_view' => $canView,
                        'can_add' => $canAdd,
                        'can_edit' => $canEdit,
                        'can_delete' => $canDelete,
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.module-access', [
                'user_id' => $selectedUser->id,
            ])
            ->with(
                'success',
                'Module permissions saved successfully.'
            );
    }
}