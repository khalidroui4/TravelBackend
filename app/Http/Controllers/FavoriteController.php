<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's favorite destinations.
     */
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->latest()->get();
        return response()->json($favorites);
    }

    /**
     * Store a newly created favorite in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string|max:255',
            'country_name' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'rating' => 'nullable|numeric',
            'image_url' => 'nullable|string',
        ]);

        // Check if already saved to avoid duplicates
        $exists = $request->user()->favorites()
            ->where('city_name', $request->city_name)
            ->where('country_name', $request->country_name)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Destination is already in your favorites.'
            ], 400);
        }

        $favorite = $request->user()->favorites()->create($request->all());

        return response()->json($favorite, 201);
    }

    /**
     * Remove the specified favorite from storage.
     */
    public function destroy(Request $request, $id)
    {
        $favorite = $request->user()->favorites()->findOrFail($id);
        $favorite->delete();

        return response()->json([
            'message' => 'Destination removed from favorites successfully.'
        ]);
    }
}
