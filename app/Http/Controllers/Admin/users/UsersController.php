<?php

namespace App\Http\Controllers\admin\users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    public function index()
    {
        return view('Admin.Users.users.index');
    }

    public function create()
    {
        return view('Admin.Users.users.create');
    }

    public function edit(User $user)
    {
        return view('Admin.Users.users.edit', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        Session::flash('swal', [
            'icon' => 'success',
            'title' => '¡Eliminado con éxito!',
            'text' => 'El probeedor se ha eliminado con éxito',
        ]);

        return redirect()->route('admin.purchases.suppliers.index');
    }
}
