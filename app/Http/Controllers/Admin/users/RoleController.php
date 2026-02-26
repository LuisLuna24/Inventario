<?php

namespace App\Http\Controllers\admin\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        Gate::authorize('view-roles');
        return view('admin.users.roles.index');
    }

    public function create()
    {
        Gate::authorize('create-roles');
        return view('admin.users.roles.create');
    }

    public function edit(Role $role)
    {
        Gate::authorize('edit-roles');
        return view('admin.users.roles.edit', [
            'role' => $role,
        ]);
    }

    public function destroy(Role $role)
    {
        Gate::authorize('delete-roles');
        if ($role->user()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar el role porque tiene usuarios asociados!',
            ]);
            return;
        }

        $role->delete();
        return redirect('admin.roles.index');
    }
}
