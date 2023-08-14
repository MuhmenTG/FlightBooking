<!DOCTYPE html>
<html>
<head>
    <title>Booking Complete Details</title>
</head>
<body><style>
    .passenger-container {
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 20px;
    }

    .passenger-info {
        display: flex;
        flex-wrap: wrap;
    }

    .passenger-label {
        font-weight: bold;
        width: 150px;
    }

    .passenger-value {
        flex: 1;
    }
</style>

<h1>Passenger Details</h1>
@foreach ($bookingComplete['passenger'] as $traveller)
    <div class="passenger-container">
        <div class="passenger-info">
            <div class="passenger-label">Passenger ID:</div>
            <div class="passenger-value">{{ $traveller['passengerId'] }}</div>
        </div>
        <div class="passenger-info">
            <div class="passenger-label">Title:</div>
            <div class="passenger-value">{{ $traveller['passengerTitle'] }}</div>
        </div>
        <div class="passenger-info">
            <div class="passenger-label">First Name:</div>
            <div class="passenger-value">{{ $traveller['passengerFirstName'] }}</div>
        </div>
        <!-- Repeat the same structure for other passenger details -->
    </div>
@endforeach

    <h1>Itinerary Details</h1>
    @foreach ($bookingComplete['flight'] as $segment)

    <h1>Booking status: CONFIRMED</h1>
    <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;">
        <p>
            <strong>Flight:</strong> {{ $segment['airline'] }}   {{ $segment['flightNumber'] }}
        </p>
        <p>
            <strong>Departure:</strong> {{ $segment['departureFrom'] }}  -  {{ $segment['departureTerminal'] }}
        </p>
        <p>
            <strong>Departure Time:</strong> {{ $segment['departureDateTime'] }}
        </p>
        <p>
            <strong>Arrival:</strong> {{ $segment['arrivalTo'] }}  -  {{ $segment['arrivalTerminal'] }}
        </p>
        
        <p>
            <strong>Arrival:</strong> {{ $segment['arrivalDate'] }}
        </p>
        <p>
            <strong>Duration:</strong> {{ $segment['flightDuration'] }}
        </p>
        <!-- Add more information if needed -->
    </div>
@endforeach


    <h1>Payment Details</h1>
    <ul>
        <li>Transaction Date: {{ $bookingComplete['payment']['transactionDate'] }}</li>
        <li>Payment Amount: {{ $bookingComplete['payment']['paymentAmount'] }} {{ $bookingComplete['payment']['paymentCurrency'] }}</li>
        <li>Payment Type: {{ $bookingComplete['payment']['paymentType'] }}</li>
        <li>Payment Status: {{ $bookingComplete['payment']['paymentStatus'] }}</li>
        <li>Payment Method: {{ $bookingComplete['payment']['paymentMethod'] }}</li>
        <li>Payment Gateway Processor: {{ $bookingComplete['payment']['paymentGatewayProcessor'] }}</li>
    </ul>
    <!-- Add payment details if needed -->
</body>
</html>
