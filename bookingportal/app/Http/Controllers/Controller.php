<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;
class Controller extends BaseController
{
 //   use AuthorizesRequests, ValidatesRequests;
    const CONTENT_TYPE_JSON = 'application/json';
    const AUTHORIZATION_BEARER = 'Bearer ';
    const HEADER_ACCEPT = 'Accept';
    const HEADER_AUTHORIZATION = 'Authorization';
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_HTTP_METHOD_OVERRIDE = 'X-HTTP-Method-Override';
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_BAD_REQUEST = 400;
    const HTTP_STATUS_UNAUTHORIZED = 401;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_INTERNAL_SERVER_ERROR = 500;
    const FLIGHT_DATA = 'data';
    const FLIGHT_OFFERS_PRICING = 'flight-offers-pricing';

    public function sendhttpRequest(string $url, string $accessToken, string $method = self::HTTP_METHOD_GET, array $data = null)
    {
        $client = new Client();
        try {
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ];

            $options = [
                'headers' => $headers,
            ];

            if ($method !== self::HTTP_METHOD_GET) {
                $headers[self::HEADER_CONTENT_TYPE] = self::CONTENT_TYPE_JSON;
                $headers[self::HEADER_HTTP_METHOD_OVERRIDE] = self::HTTP_METHOD_GET;

                $options['json'] = $data;
            }

            $response = $client->request($method, $url, $options);

            switch ($response->getStatusCode()) {
                case 200:
                    return $response->getBody();
                case 400:
                    return response()->json(['error' => 'Choose another flight'], 400);
                case 401:
                    return response()->json(['error' => 'Unauthorized'], 401);
                case 404:
                    return response()->json(['error' => 'Not Found'], 404);
                default:
                    return response()->json(['error' => 'Something went wrong'], $response->getStatusCode());
            }
        } catch (GuzzleException $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

}
