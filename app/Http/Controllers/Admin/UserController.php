<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'role_name' => $user->role->name,
                'organization' => $user->organization?->short_name,
            ];
        });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable'],
            'organization_id' => ['integer', 'exists:organizations,id'],
            'role_id' => ['integer', 'exists:roles,id'],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make(($data['password']));
            $user->tokens()->delete();
        } else if (array_key_exists('password', $data) && $data['password'] === null) {
            unset($data['password']);
        }

        $user->update($data);

        redirect('admin.users.index');
    }

    public function show(Request $request, User $user)
    {
        return response()->json([
            'login' => $user->login,
            'name' => $user->name,
            'role_id' => $user->role_id,
            'organization_id' => $user->organization_id
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'organization_id' => ['integer', 'exists:organizations,id'],
            'role_id' => ['integer', 'exists:roles,id'],
        ]);

        $data['password'] = Hash::make(($data['password']));

        User::create($data);

        redirect('admin.users.index');
    }

    public function delete()
    {

    }
}
