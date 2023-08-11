<!DOCTYPE html>
<html>
<head>
    <title>Booking Complete Details</title>
</head>
<body>
    <h1>Itinerary Details</h1>
    <ul>
        @foreach ($itinerary as $segment)
            <li>Flight Segment Number: {{ $segment['flightSegmentNumber'] }}</li>
            <li>Booking Reference: {{ $segment['bookingReference'] }}</li>
            <li>Airline: {{ $segment['airline'] }}</li>
            <!-- Add more fields as needed -->
            <br>
        @endforeach
    </ul>

    <h1>Passenger Details</h1>
    <ul>
        @foreach ($passengers as $passenger)
            <li>Passenger ID: {{ $passenger['passengeId'] }}</li>
            <li>Passenger Title: {{ $passenger['passengerTitle'] }}</li>
            <li>Passenger First Name: {{ $passenger['passengerFirstName'] }}</li>
            <!-- Add more fields as needed -->
            <br>
        @endforeach
    </ul>

    <h1>Payment Details</h1>
    <!-- Display payment details here -->
</body>
</html>
