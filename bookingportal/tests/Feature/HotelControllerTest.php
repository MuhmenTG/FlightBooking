<?php

use App\Http\Controllers\HotelBookingController;
use Illuminate\Http\Request;
use Tests\TestCase;

class HotelControllerTest extends TestCase
{
    public function testSearchHotelShouldReturnHotelResults()
    {
        $request = new Request([
            'cityCode' => 'PAR',
            'adults' => 2,
            'checkInDate' => '2023-04-01',
            'checkOutDate' => '2023-04-05',
        ]);
        $request->headers->set('Authorization', 'Bearer abc123');

        
        $controller = $this->getMockBuilder(HotelBookingController::class)
            ->onlyMethods(['httpRequest', 'getSpecificHotelsRoomAvailability'])
            ->getMock();

        $controller->expects($this->once())
            ->method('httpRequest')
            ->with('https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city?cityCode=PAR', 'abc123')
            ->willReturn('{"data": [{"hotelId": "123"}, {"hotelId": "456"}]}');

        $controller->expects($this->once())
            ->method('getSpecificHotelsRoomAvailability')
            ->with('123,456', 2, '2023-04-01', '2023-04-05', 'abc123')
            ->willReturn(['hotels' => []]);

        $response = $controller->searchHotel($request);

        // Assert that the response is correct
        $this->assertEquals(['hotels' => []], $response);
    }

    public function testSearchHotelShouldReturnStatusCode400WhenValidationFails()
    {
        $request = new Request([
            'cityCode' => '',
            'adults' => 0,
            'checkInDate' => '',
            'checkOutDate' => '',
        ]);
        $request->headers->set('Authorization', 'Bearer abc123');

        $controller = $this->getMockBuilder(HotelBookingController::class)
            ->onlyMethods(['httpRequest', 'getSpecificHotelsRoomAvailability'])
            ->getMock();

        $controller->expects($this->never())
            ->method('httpRequest');

        $controller->expects($this->never())
            ->method('getSpecificHotelsRoomAvailability');

        $response = $controller->searchHotel($request);

        $this->assertEquals(400, $response->getStatusCode());

    }

}
