export interface BookingResponse {
    bookings: Booking[];
}

interface Booking {
    id: number;
    airline: string;
    arrivalDate: Date;
    arrivalTerminal: string;
    arrivalTo: string;
    bookingReference: string;
    departureDateTime: Date;
    departureFrom: string;
    departureTerminal: string;
    flightDuration: string;
    flightNumber: string;
    isPaid: boolean;
    passengers: Passenger[];
}

export interface Passenger {
    id: number;
    paymentInfoId?: number;
    bookingReference: string;
    dateOfBirth: string;
    email: string;
    firstName: string;
    lastName: string;
    passengerType?: string;
    gender?: string;
    ticketNumber?: string;
    isCancelled?: boolean;
}