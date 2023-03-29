export interface SearchFlightsRequest {
    travelType: number
    originLocationCode: string
    destinationLocationCode: string
    departureDate: string
    returnDate: string
    class: string
    adults: number
}