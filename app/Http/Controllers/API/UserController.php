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
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'department_id' => 'nullable|exists:departments,id',
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
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'department_id' => 'nullable|exists:departments,id',
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
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}