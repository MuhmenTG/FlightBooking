export interface SearchFlightsRequest {
    travelType: number
    originLocationCode: string
    destinationLocationCode: string
    departureDateVar?: Date | null
    returnDateVar?: Date | null
    departureDate: string
    returnDate: string;
    travelClass: string
    travelClassVar: string;
    adults: number;
    children: number;
    infants: number;
    nonStop: boolean;
}