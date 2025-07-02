<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all()->map(function ($role) {
            return [
                'label' => $role->name,
                'value' => $role->id,
            ];
        });

        return response()->json($roles);
    }
}
