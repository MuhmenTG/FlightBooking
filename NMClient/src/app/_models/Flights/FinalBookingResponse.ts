export interface FinalBookingResponse {
    bookingReference?: string;
    navigationId?: number;
    flight: Flight[];
    passengers: Passenger[];
    payment?: Payment;
}

interface Flight {
    airline: string;
    arrivalDate: Date;
    arrivalTerminal: string;
    arrivalTo: string;
    bookingReference: string;
    bookingStatus: string;
    departureDateTime: Date;
    departureFrom: string;
    departureTerminal: string;
    flightDuration: string;
    flightNumber: string;
    flightSegmentNumber: number;
    paymentStatus: string;
}

interface Passenger {
    connectedPaymentReference: number;
    conntecedBookingReference: string;
    passengerDateOfBirth: Date;
    passengerEmail: string;
    passengerFirstName: string;
    passengerId: number;
    passengerLastName: string;
    passengerTitle: string;
    passengerType: string;
    passengerticketNumber: string;
}

interface Payment {
    inConnectionWithBookingReference: string;
    paymentAmount: string;
    paymentCurrency: string;
    paymentGatewayProcessor: string;
    paymentMethod: string;
    paymentStatus: string;
    paymentType: string;
    transactionDate: Date
}
