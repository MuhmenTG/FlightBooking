<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureflightDataIsPresent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $jsonString = $request->getContent();
        $jsonData = json_decode($jsonString, true);

        // Check if there was an error during JSON decoding
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON content.'], 400);
        }

        // Check if all keys are present
        $requiredKeys = ['type', 'id', 'source', 'instantTicketingRequired', 'nonHomogeneous', 'oneWay', 'lastTicketingDate', 'lastTicketingDateTime', 'numberOfBookableSeats', 'itineraries', 'price', 'pricingOptions', 'validatingAirlineCodes', 'travelerPricings'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $jsonData)) {
                return response()->json(['error' => "Key '{$key}' is missing from JSON."], 400);
            }
        }

        // Check if all nested objects and arrays are present
        $requiredObjects = [
            'itineraries' => ['duration', 'segments'],
            'segments' => ['departure', 'arrival', 'carrierCode', 'number', 'aircraft', 'operating', 'duration', 'id', 'numberOfStops', 'blacklistedInEU'],
            'departure' => ['iataCode', 'at'],
            'arrival' => ['iataCode', 'at'],
            'aircraft' => ['code'],
            'operating' => ['carrierCode'],
            'price' => ['currency', 'total', 'base', 'fees', 'grandTotal'],
            'pricingOptions' => ['fareType', 'includedCheckedBagsOnly'],
            'travelerPricings' => ['travelerId', 'fareOption', 'travelerType', 'price', 'fareDetailsBySegment'],
            'fareDetailsBySegment' => ['segmentId', 'cabin', 'fareBasis', 'brandedFare', 'class', 'includedCheckedBags']
        ];

        foreach ($requiredObjects as $object => $keys) {
            if (array_key_exists($object, $jsonData)) {
                foreach ($jsonData[$object] as $item) {
                    foreach ($keys as $key) {
                        if (!array_key_exists($key, $item)) {
                            return response()->json(['error' => "Key '{$key}' is missing from '{$object}' object in JSON."], 400);
                        }
                    }
                }
            }
        }

        // If all keys, arrays, and objects exist, call the next middleware
        return $next($request);
}

}
