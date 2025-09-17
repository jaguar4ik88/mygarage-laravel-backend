<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ServiceStationController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()?->id ?? $request->query('user_id');
        if ($userId) {
            $stations = ServiceStation::where('user_id', $userId)->get();
        } else {
            $stations = ServiceStation::whereNull('user_id')->get();
        }

        return response()->json([
            'success' => true,
            'data' => $stations
        ]);
    }

    public function byUser($userId)
    {
        $stations = ServiceStation::where('user_id', $userId)->get();
        return response()->json([
            'success' => true,
            'data' => $stations,
        ]);
    }

    public function nearby(Request $request)
    {
        $latitude = (float) $request->query('lat');
        $longitude = (float) $request->query('lng');
        $radiusMeters = (int) $request->query('radius', 5000); // meters, increased default
        $keyword = $request->query('keyword', 'car repair service station');
        $type = $request->query('type', 'car_repair');

        $googleKey = env('GOOGLE_PLACES_API_KEY');
        $googleUrl = env('GOOGLE_PLACES_BASE_URL', 'https://maps.googleapis.com/maps/api/place/nearbysearch/json');
        $overpassUrl = env('OVERPASS_API_URL', 'https://overpass-api.de/api/interpreter');

        // Helper to compute distance (meters)
        $haversine = function(float $lat1, float $lon1, float $lat2, float $lon2): float {
            $R = 6371000; // meters
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            return $R * $c;
        };

        // Try Google first if key is present
        if (!empty($googleKey)) {
            try {
                \Log::info('Using Google Places API for nearby search', [
                    'lat' => $latitude,
                    'lng' => $longitude,
                    'radius' => $radiusMeters
                ]);
                
                $resp = Http::timeout(10)->get($googleUrl, [
                    'location' => $latitude.','.$longitude,
                    'radius' => min($radiusMeters, 50000), // Google Places API max radius is 50km
                    'type' => $type,
                    'keyword' => $keyword,
                    'key' => $googleKey,
                ]);
                
                if ($resp->ok()) {
                    $json = $resp->json();
                    
                    if ($json['status'] === 'OK') {
                        $results = collect($json['results'] ?? [])->map(function ($r) use ($latitude, $longitude, $haversine) {
                            $lat = (float) ($r['geometry']['location']['lat'] ?? 0);
                            $lng = (float) ($r['geometry']['location']['lng'] ?? 0);
                            return [
                                'id' => $r['place_id'] ?? md5(($r['name'] ?? '').$lat.$lng),
                                'name' => $r['name'] ?? 'Service Station',
                                'address' => $r['vicinity'] ?? ($r['formatted_address'] ?? ''),
                                'phone' => null,
                                'rating' => isset($r['rating']) ? (float) $r['rating'] : 0,
                                'latitude' => $lat,
                                'longitude' => $lng,
                                'distance' => round($haversine($latitude, $longitude, $lat, $lng)),
                                'types' => $r['types'] ?? [],
                                'price_level' => $r['price_level'] ?? null,
                                'opening_hours' => isset($r['opening_hours']) ? [
                                    'open_now' => $r['opening_hours']['open_now'] ?? false,
                                    'weekday_text' => $r['opening_hours']['weekday_text'] ?? []
                                ] : null,
                                'is_google_place' => true,
                            ];
                        })->sortBy('distance')->values();

                        return response()->json([
                            'success' => true,
                            'data' => $results,
                            'source' => 'google_places',
                            'count' => $results->count()
                        ]);
                    } else {
                        \Log::warning('Google Places API returned error status', [
                            'status' => $json['status'] ?? 'UNKNOWN'
                        ]);
                    }
                } else {
                    \Log::warning('Google Places API request failed', [
                        'status_code' => $resp->status()
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::error('Google Places API error, falling back to Overpass', [
                    'error' => $e->getMessage()
                ]);
                // Fallback to Overpass
            }
        } else {
            \Log::info('Google Places API key not configured, using Overpass API');
        }

        // Overpass fallback (or primary if no Google key)
        try {
            \Log::info('Using Overpass API for nearby search', [
                'lat' => $latitude,
                'lng' => $longitude,
                'radius' => $radiusMeters
            ]);
            
            // Overpass API supports up to 100km radius, but we'll limit to reasonable values
            $around = max(200, min($radiusMeters, 50000));
            
            // Enhanced query to include more service station types
            $query = '[out:json][timeout:25];('.
                'node(around:'.$around.','.$latitude.','.$longitude.')["amenity"~"^(car_repair|car_wash|fuel)$"];'.
                'node(around:'.$around.','.$latitude.','.$longitude.')["shop"="car_repair"];'.
                'node(around:'.$around.','.$latitude.','.$longitude.')["service"="car_repair"];'.
                'way(around:'.$around.','.$latitude.','.$longitude.')["amenity"~"^(car_repair|car_wash|fuel)$"];'.
                'way(around:'.$around.','.$latitude.','.$longitude.')["shop"="car_repair"];'.
                'way(around:'.$around.','.$latitude.','.$longitude.')["service"="car_repair"];'.
                'relation(around:'.$around.','.$latitude.','.$longitude.')["amenity"~"^(car_repair|car_wash|fuel)$"];'.
                'relation(around:'.$around.','.$latitude.','.$longitude.')["shop"="car_repair"];'.
                'relation(around:'.$around.','.$latitude.','.$longitude.')["service"="car_repair"];'.
                ');out center meta;';
                
            $resp = Http::timeout(30)->asForm()->post($overpassUrl, [ 'data' => $query ]);
            
            if ($resp->ok()) {
                $json = $resp->json();
                $elements = collect($json['elements'] ?? []);
                
                $results = $elements->map(function ($el) use ($latitude, $longitude, $haversine) {
                    $center = $el['center'] ?? null;
                    $lat = (float) ($el['lat'] ?? ($center['lat'] ?? 0));
                    $lng = (float) ($el['lon'] ?? ($center['lon'] ?? 0));
                    $tags = $el['tags'] ?? [];
                    
                    // Enhanced name extraction
                    $name = $tags['name'] ?? 
                           $tags['brand'] ?? 
                           $tags['operator'] ?? 
                           'Service Station';
                           
                    // Enhanced address extraction
                    $addr = trim(implode(' ', array_filter([
                        $tags['addr:street'] ?? '',
                        $tags['addr:housenumber'] ?? '',
                        $tags['addr:city'] ?? '',
                        $tags['addr:postcode'] ?? ''
                    ])));
                    
                    // Enhanced phone extraction
                    $phone = $tags['phone'] ?? 
                            $tags['contact:phone'] ?? 
                            $tags['contact:mobile'] ?? null;
                            
                    // Extract service types
                    $serviceTypes = [];
                    if (isset($tags['amenity'])) {
                        $serviceTypes[] = $tags['amenity'];
                    }
                    if (isset($tags['service'])) {
                        $serviceTypes[] = $tags['service'];
                    }
                    if (isset($tags['shop'])) {
                        $serviceTypes[] = $tags['shop'];
                    }
                    
                    // Extract website if available
                    $website = $tags['website'] ?? 
                              $tags['contact:website'] ?? null;
                    
                    return [
                        'id' => (string) ($el['id'] ?? md5($name.$lat.$lng)),
                        'name' => $name,
                        'address' => $addr,
                        'phone' => $phone,
                        'website' => $website,
                        'rating' => 0, // Overpass doesn't provide ratings
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'distance' => round($haversine($latitude, $longitude, $lat, $lng)),
                        'types' => $serviceTypes,
                        'is_google_place' => false,
                        'source' => 'overpass',
                        'tags' => $tags, // Include all tags for debugging
                    ];
                })->sortBy('distance')->values();

                \Log::info('Overpass API search completed', [
                    'found' => $results->count(),
                    'radius_used' => $around
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $results,
                    'source' => 'overpass',
                    'count' => $results->count(),
                    'radius_used' => $around
                ]);
            } else {
                \Log::warning('Overpass API request failed', [
                    'status_code' => $resp->status()
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('Overpass API error', [
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }

    public function store(Request $request)
    {
        $userId = $request->user()?->id ?? $request->input('user_id');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'rating' => 'nullable|numeric|min:0|max:5',
            'distance' => 'nullable|numeric|min:0',
            'types' => 'nullable|array',
        ]);

        $station = ServiceStation::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? '',
            'rating' => $validated['rating'] ?? 0,
            'distance' => $validated['distance'] ?? null,
            'latitude' => isset($validated['latitude']) ? (float)$validated['latitude'] : 0.0,
            'longitude' => isset($validated['longitude']) ? (float)$validated['longitude'] : 0.0,
            'types' => $validated['types'] ?? [],
        ]);

        return response()->json([
            'success' => true,
            'data' => $station,
        ], 201);
    }

    public function destroy($id)
    {
        $station = ServiceStation::find($id);
        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $station->delete();
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $station = ServiceStation::find($id);
        if (!$station) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'rating' => 'nullable|numeric|min:0|max:5',
            'distance' => 'nullable|numeric|min:0',
            'types' => 'nullable|array',
        ]);

        $station->update([
            'name' => $validated['name'] ?? $station->name,
            'description' => $validated['description'] ?? $station->description,
            'phone' => $validated['phone'] ?? $station->phone,
            'address' => $validated['address'] ?? $station->address,
            'latitude' => array_key_exists('latitude', $validated) ? (float)$validated['latitude'] : $station->latitude,
            'longitude' => array_key_exists('longitude', $validated) ? (float)$validated['longitude'] : $station->longitude,
            'rating' => array_key_exists('rating', $validated) ? (float)$validated['rating'] : $station->rating,
            'distance' => array_key_exists('distance', $validated) ? (float)$validated['distance'] : $station->distance,
            'types' => array_key_exists('types', $validated) ? $validated['types'] : $station->types,
        ]);

        return response()->json(['success' => true, 'data' => $station]);
    }
}
