export interface SearchFlightsRequest {
    originLocationCode: string
    destinationLocationCode: string
    departureDate: string
    returnDate: string
    adults: number
}