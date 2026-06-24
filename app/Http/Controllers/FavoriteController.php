<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

    /**
     * Fetch country details from REST Countries API server-side to bypass CORS policy.
     */
    public function countryDetails($name)
    {
        try {
            $response = Http::get("https://restcountries.com/v3.1/name/" . urlencode($name) . "?fullText=true");

            if ($response->failed()) {
                // Try fuzzy match
                $response = Http::get("https://restcountries.com/v3.1/name/" . urlencode($name));
            }

            if ($response->failed()) {
                return response()->json(['message' => 'Country details not found.'], 404);
            }

            $data = $response->json()[0] ?? null;

            if (!$data) {
                return response()->json(['message' => 'Country details not found.'], 404);
            }

            $currencyKey = isset($data['currencies']) ? array_key_first($data['currencies']) : null;
            $currency = $currencyKey ? $data['currencies'][$currencyKey] : null;
            $langKeys = isset($data['languages']) ? array_keys($data['languages']) : [];
            $languages = array_map(fn($k) => $data['languages'][$k], $langKeys);
            $languages = implode(', ', array_slice($languages, 0, 3));

            return response()->json([
                'name' => $data['name']['common'] ?? $name,
                'officialName' => $data['name']['official'] ?? '',
                'flag' => $data['flags']['svg'] ?? ($data['flags']['png'] ?? ''),
                'flagAlt' => $data['flags']['alt'] ?? '',
                'capital' => isset($data['capital']) ? $data['capital'][0] : 'N/A',
                'region' => $data['region'] ?? 'N/A',
                'subregion' => $data['subregion'] ?? 'N/A',
                'population' => isset($data['population']) ? number_format($data['population']) : 'N/A',
                'currencyName' => $currency ? ($currency['name'] . ' (' . ($currency['symbol'] ?? '') . ')') : 'N/A',
                'languages' => $languages ?: 'N/A',
                'mapLink' => $data['maps']['googleMaps'] ?? '',
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch country details: ' . $e->getMessage()], 500);
        }
    }
}
