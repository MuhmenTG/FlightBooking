export interface SearchFlightsRequest {
    travelType: number
    originLocationCode: string
    destinationLocationCode: string
    departureDateVar?: Date | null
    returnDateVar?: Date | null
    departureDate: string
    returnDate: string;
    travelClass: string
    adults: number
}