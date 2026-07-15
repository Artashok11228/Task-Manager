<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{


    public function getAll()
    {
        $roles = Role::all();

        if ($roles->isEmpty()) {
            return response()->json([
                'message' => 'There is no role'
            ]);
        }

        return response()->json($roles);

    }

    public function addRole(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
        ]);
    }
}
