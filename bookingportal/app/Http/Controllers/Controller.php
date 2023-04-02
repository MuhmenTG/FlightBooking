<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
 //   use AuthorizesRequests, ValidatesRequests;

    public function httpRequest(string $url, string $accessTtoken, string $method = 'get',  array $data = null)
    {
       
        $client = new \GuzzleHttp\Client();

        try {
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessTtoken
            ];

            if ($method === 'GET') {
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
            if($response->getStatusCode() == 200){
                return $response->getBody();
            }
            return response()->json("Could not find", 404);
        } catch (GuzzleException $exception) {
            print($exception);
            return null;
        }
        
    }
}
