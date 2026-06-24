<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of all users for admin.
     */
    public function index(Request $request)
    {
        // Authorize: check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
        }

        // Get all users with their favorites count
        $users = User::withCount('favorites')->latest()->get();

        return response()->json($users);
    }

    /**
     * Remove a user from the system.
     */
    public function destroy(Request $request, $id)
    {
        // Authorize: check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
        }

        $user = User::findOrFail($id);

        // Prevent self-deletion
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'You cannot delete your own admin account.'], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.'
        ]);
    }
}
