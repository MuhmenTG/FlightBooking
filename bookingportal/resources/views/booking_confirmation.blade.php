<!DOCTYPE html>
<html>
<head>
    <title>Booking Complete Details</title>
</head>
<body>

<style>
    .container {
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 20px;
    }
</style>

<h1>Passenger Details</h1>
<!-- Jeg har ændret variabel til 'passengers' og rettet stavefejl -->
@foreach ($bookingComplete['passengers'] as $traveller)
    <div class="container">
        <p>
            <strong>Name:</strong> {{ $traveller['title'] }} {{ $traveller['firstName'] }} {{ $traveller['lastName'] }}
        </p>
        <p>
            <strong>Passenger type:</strong> {{ $traveller['passengerType'] }}
        </p>
        <p>
            <strong>Ticket number:</strong> {{ $traveller['ticketNumber'] }}
        </p>
        <!-- Repeat the same structure for other passenger details -->
    </div>
@endforeach

<h1>Itinerary Details</h1>
@foreach ($bookingComplete['flight'] as $segment)
    <strong>Booking status: CONFIRMED</strong>
    <div class="container">
        <p>
            <strong>Flight:</strong> {{ $segment['airline'] }} {{ $segment['flightNumber'] }}
        </p>
        <p>
            <strong>Departure:</strong> {{ $segment['departureFrom'] }} - {{ $segment['departureTerminal'] }}
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
    <div class="container">
        <p>
            <strong>Transaction Date:</strong> {{ $bookingComplete['payment']['transactionDate'] }}
        </p>
        <p>
            <strong>Payment Amount:</strong> {{ $bookingComplete['payment']['paymentAmount'] }} {{ $bookingComplete['payment']['paymentCurrency'] }}
        </p>
        <p>
            <strong>Payment Type:</strong> {{ $bookingComplete['payment']['paymentType'] }}
        </p>
        <p>
            <strong>Payment Method:</strong> {{ $bookingComplete['payment']['paymentMethod'] }}
        </p>
    </div>
    <!-- Add payment details if needed -->
</body>
</html>
