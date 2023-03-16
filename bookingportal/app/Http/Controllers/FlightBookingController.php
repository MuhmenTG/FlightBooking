<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Arr;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Echo_;
use Amadeus\Amadeus;
use Amadeus\Exceptions\ResponseException;
use App\Models\FlightBooking;
use App\Models\PassengerInfo;
use DateTime;

use function PHPSTORM_META\type;

class FlightBookingController extends Controller
{
    //


    //We have made this only because there is not any frontend yet, where acess token comes from
    public function getAccessToken()
    {
        $url = 'https://test.api.amadeus.com/v1/security/oauth2/token';

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($url, [
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'xlUodVi30L0U8snyBsa1qenY4BNyUjMA',
                    'client_secret' => 'A2GpGXyewfl0G3gu'
                ]
            ]);

            $response = $response->getBody();
            $accessTtoken = json_decode($response)->access_token;
            echo $accessTtoken;
        } catch (GuzzleException $exception) {
            dd($exception);
        }
    }

    public function searchFlights(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'originLocationCode'      => 'required|string',
            'destinationLocationCode' => 'required|string',
            'departureDate'           => 'required|string',
            'returnDate'              => 'string',
            'adults'                  => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $url = 'https://test.api.amadeus.com/v2/shopping/flight-offers';

        $originLocationCode = $request->input('originLocationCode');
        $destinationLocationCode = $request->input('destinationLocationCode');
        $departureDate = $request->input('departureDate');
        $returnDate = $request->input('returnDate');
        $adults =  $request->input('adults');

        $data = [
            'originLocationCode'      => $originLocationCode,
            'destinationLocationCode' => $destinationLocationCode,
            'departureDate'           => $departureDate,
            'returnDate'              => $returnDate,
            'adults'                  => $adults
        ];

        $searchData = Arr::query($data);
        $url .= '?' . $searchData;


        $accessTtoken = 'nQKqltWJEJ7tyZAYa3mkE6w0SVdB';
        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessTtoken
                ],

            ]);
            return $response->getBody();
        } catch (GuzzleException $exception) {
            print($exception);
        }
    }

    public function selectFlightOffer(Request $request)
    {
        $url = 'https://test.api.amadeus.com/v1/shopping/flight-offers/pricing';

        $jsonFlightData = $request->json()->all();

        $data = array(
            "data" => array(
                "type" => "flight-offers-pricing",
                "flightOffers" => [$jsonFlightData]
            )
        );

        $accessTtoken = 'nQKqltWJEJ7tyZAYa3mkE6w0SVdB';

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-HTTP-Method-Override' => 'GET',
                    'Authorization' => 'Bearer ' . $accessTtoken
                ],
                'json' => $data
            ]);

            print_r($response->getStatusCode());
            return $response->getBody();
        } catch (GuzzleException $exception) {
            return $exception->getMessage();
        }
    }

    public function flightConfirmation(Request $request)
    {
        $FlightData = $request->json()->all();

        if(!$FlightData == null){
            foreach ($FlightData["itineraries"] as $itinerary) {
                foreach ($itinerary["segments"] as $segment) 
                {
                    $departureIata = $segment["departure"]["iataCode"];
                    $departureTerminal = isset($segment['departure']['terminal']) ? $segment['departure']['terminal'] : null;
                    $departureTime = $segment["departure"]["at"];
                    $arrivalIata = $segment["arrival"]["iataCode"];
                    $arrivalTerminal = isset($segment['arrival']['terminal']) ? $segment['arrival']['terminal'] : null;
                    $arrivalTime = $segment["arrival"]["at"];
                    $carrierCode = $segment["carrierCode"];
                    $flightNumber = $segment["number"];
                    $duration = $segment["duration"];
                    $bookingReference = "something";
    
                    $flightBooking = new FlightBooking();
                    $flightBooking->setBookingReference($bookingReference);
                    $flightBooking->setAirline($carrierCode);
                    $flightBooking->setFlightNumber($flightNumber);
                    $flightBooking->setDepartureFrom($departureIata);
                    $flightBooking->setDepartureDateTime($departureTime);
                    $flightBooking->setDepartureTerminal($departureTerminal);
                    $flightBooking->setArrivelTo($arrivalIata);
                    $flightBooking->setArrivelDate($arrivalTime);
                    $flightBooking->getArrivelTerminal($arrivalTerminal);
                    $flightBooking->setFlightDuration($duration);
                    $flightBooking->setIsBookingConfirmed(true);
                    $flightBooking->save();
    
                }
            }         
            foreach($FlightData["passengers"] as $passenger){
                
                $firstName = $passenger["firstName"];
                $lastName = $passenger["lastName"];
                $dateOfBirth = $passenger["dateOfBirth"];
                $email = $passenger["email"];
                $passengerType = $passenger["passengerType"];
                $ticketNumber = $this->generateTicketNumber(14);

                $passengerInfo = new PassengerInfo();
                $passengerInfo->setPNR($bookingReference);
                $passengerInfo->setPaymentInfoId(1);
                $passengerInfo->setFirstName($firstName);
                $passengerInfo->setLastName($lastName);
                $passengerInfo->setDateOfBirth($dateOfBirth);
                $passengerInfo->setEmail($email);
                $passengerInfo->setPassengerType($passengerType);
                $passengerInfo->setTicketNumber($ticketNumber);
                $passengerInfo->save();

                
            }
        }


        
    }

    private function generateTicketNumber($length) {
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }
}


