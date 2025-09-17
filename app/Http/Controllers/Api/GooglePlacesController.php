<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class GooglePlacesController extends Controller
{
    private $googleApiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->googleApiKey = env('GOOGLE_PLACES_API_KEY');
        $this->baseUrl = 'https://maps.googleapis.com/maps/api/place';
    }

    /**
     * Search for nearby places using Google Places API
     */
    public function nearbySearch(Request $request): JsonResponse
    {
        try {
            $latitude = (float) $request->query('lat');
            $longitude = (float) $request->query('lng');
            $radius = (int) $request->query('radius', 5000);
            $keyword = $request->query('keyword', 'car repair service station');
            $type = $request->query('type', 'car_repair');

            if (empty($this->googleApiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Places API key not configured'
                ], 400);
            }

            $response = Http::get("{$this->baseUrl}/nearbysearch/json", [
                'location' => "{$latitude},{$longitude}",
                'radius' => min($radius, 50000), // Google API max radius
                'type' => $type,
                'keyword' => $keyword,
                'key' => $this->googleApiKey,
            ]);

            if (!$response->ok()) {
                throw new \Exception('Google Places API request failed');
            }

            $data = $response->json();
            
            if ($data['status'] !== 'OK') {
                throw new \Exception('Google Places API error: ' . $data['status']);
            }

            $results = collect($data['results'] ?? [])->map(function ($place) use ($latitude, $longitude) {
                $lat = (float) ($place['geometry']['location']['lat'] ?? 0);
                $lng = (float) ($place['geometry']['location']['lng'] ?? 0);
                
                return [
                    'place_id' => $place['place_id'] ?? null,
                    'name' => $place['name'] ?? 'Service Station',
                    'vicinity' => $place['vicinity'] ?? '',
                    'rating' => (float) ($place['rating'] ?? 0),
                    'price_level' => $place['price_level'] ?? null,
                    'types' => $place['types'] ?? [],
                    'geometry' => [
                        'location' => [
                            'lat' => $lat,
                            'lng' => $lng,
                        ]
                    ],
                    'opening_hours' => isset($place['opening_hours']) ? [
                        'open_now' => $place['opening_hours']['open_now'] ?? false,
                        'weekday_text' => $place['opening_hours']['weekday_text'] ?? []
                    ] : null,
                    'photos' => array_map(function ($photo) {
                        return [
                            'photo_reference' => $photo['photo_reference'] ?? '',
                            'height' => $photo['height'] ?? 0,
                            'width' => $photo['width'] ?? 0,
                        ];
                    }, $place['photos'] ?? []),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'results' => $results,
                    'next_page_token' => $data['next_page_token'] ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get place details using Google Places API
     */
    public function placeDetails(Request $request): JsonResponse
    {
        try {
            $placeId = $request->query('place_id');

            if (empty($this->googleApiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Places API key not configured'
                ], 400);
            }

            if (empty($placeId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Place ID is required'
                ], 400);
            }

            $response = Http::get("{$this->baseUrl}/details/json", [
                'place_id' => $placeId,
                'fields' => 'place_id,name,formatted_address,formatted_phone_number,international_phone_number,website,rating,price_level,opening_hours,reviews,address_components,geometry,photos',
                'key' => $this->googleApiKey,
            ]);

            if (!$response->ok()) {
                throw new \Exception('Google Places API request failed');
            }

            $data = $response->json();
            
            if ($data['status'] !== 'OK') {
                throw new \Exception('Google Places API error: ' . $data['status']);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'result' => $data['result'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Text search using Google Places API
     */
    public function textSearch(Request $request): JsonResponse
    {
        try {
            $query = $request->query('query');
            $location = $request->query('lat') && $request->query('lng') 
                ? "{$request->query('lat')},{$request->query('lng')}" 
                : null;
            $radius = (int) $request->query('radius', 50000);
            $type = $request->query('type', 'car_repair');

            if (empty($this->googleApiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Places API key not configured'
                ], 400);
            }

            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Query is required'
                ], 400);
            }

            $params = [
                'query' => $query,
                'type' => $type,
                'key' => $this->googleApiKey,
            ];

            if ($location) {
                $params['location'] = $location;
                $params['radius'] = min($radius, 50000);
            }

            $response = Http::get("{$this->baseUrl}/textsearch/json", $params);

            if (!$response->ok()) {
                throw new \Exception('Google Places API request failed');
            }

            $data = $response->json();
            
            if ($data['status'] !== 'OK') {
                throw new \Exception('Google Places API error: ' . $data['status']);
            }

            $results = collect($data['results'] ?? [])->map(function ($place) {
                return [
                    'place_id' => $place['place_id'] ?? null,
                    'name' => $place['name'] ?? 'Service Station',
                    'vicinity' => $place['vicinity'] ?? '',
                    'rating' => (float) ($place['rating'] ?? 0),
                    'price_level' => $place['price_level'] ?? null,
                    'types' => $place['types'] ?? [],
                    'geometry' => $place['geometry'] ?? null,
                    'opening_hours' => isset($place['opening_hours']) ? [
                        'open_now' => $place['opening_hours']['open_now'] ?? false,
                        'weekday_text' => $place['opening_hours']['weekday_text'] ?? []
                    ] : null,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'results' => $results,
                    'next_page_token' => $data['next_page_token'] ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get photo URL from Google Places API
     */
    public function photo(Request $request): JsonResponse
    {
        try {
            $photoReference = $request->query('photo_reference');
            $maxWidth = (int) $request->query('maxwidth', 400);

            if (empty($this->googleApiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Places API key not configured'
                ], 400);
            }

            if (empty($photoReference)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo reference is required'
                ], 400);
            }

            $photoUrl = "{$this->baseUrl}/photo?photo_reference={$photoReference}&maxwidth={$maxWidth}&key={$this->googleApiKey}";

            return response()->json([
                'success' => true,
                'data' => [
                    'photo_url' => $photoUrl
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
