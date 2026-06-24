<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // LIST USERS
    public function index()
    {
        $users = User::with('department')->get();

        return response()->json($users);
    }

    // CREATE USER
    public function store(Request $request)
    {
        // Only super_admin & admin can create users
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Only super_admin can create another super_admin
        if (
            $request->role === 'super_admin' &&
            auth()->user()->role !== 'super_admin'
        ) {
            return response()->json([
                'message' => 'Only Super Admin can create Super Admin accounts'
            ], 403);
        }

        // Maximum 2 super_admin accounts
        if ($request->role === 'super_admin') {

            $count = User::where('role', 'super_admin')->count();

            if ($count >= 2) {
                return response()->json([
                    'message' => 'Only 2 super admin accounts allowed'
                ], 400);
            }
        }

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'department_id' => 'nullable|exists:departments,id',
            'emp_id' => 'nullable|string|max:255|unique:users,emp_id',
            'status' => 'required',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    // SHOW SINGLE USER
    public function show(string $id)
    {
        $user = User::with('department')->findOrFail($id);

        return response()->json($user);
    }

    // UPDATE USER
    public function update(Request $request, string $id)
    {   
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'department_id' => 'nullable|exists:departments,id',
            'emp_id' => 'nullable|string|max:255|unique:users,emp_id,' . $id,
            'status' => 'required',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    // DELETE USER
    public function destroy(string $id)
    {   
        if (auth()->user()->role !== 'super_admin') {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}