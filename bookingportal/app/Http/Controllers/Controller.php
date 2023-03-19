<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
 //   use AuthorizesRequests, ValidatesRequests;

    public function makeRequest(string $url, string $accessTtoken, string $method = 'get',  array $data = null)
    {
        $client = new \GuzzleHttp\Client();

        try {
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessTtoken
            ];

            if ($method === 'get') {
                $response = $client->get($url, [
                    'headers' => $headers,
                ]);
            } else {
                $headers['Content-Type'] = 'application/json';
                $headers['X-HTTP-Method-Override'] = 'GET';

                $response = $client->post($url, [
                    'headers' => $headers,
                    'json' => $data,
                ]);
            }

            return $response->getBody();
        } catch (GuzzleException $exception) {
            print($exception);
            return null;
        }
        
    }
}
