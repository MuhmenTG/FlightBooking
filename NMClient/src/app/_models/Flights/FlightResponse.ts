export interface FlightResponses{
    data: FlightResponse[];
}

export interface FlightResponse {
    type: string;
    id: string;
    source: string;
    instantTicketingRequired: boolean;
    nonHomogeneous: boolean;
    oneWay: boolean;
    lastTicketingDate: Date;
    numberOfBookableSeats: number;
    itineraries: Itinerary[];
    price: FlightResponsePrice;
    pricingOptions: PricingOptions;
    validatingAirlineCodes: string[];
    travelerPricings: TravelerPricing[];
}

interface Itinerary {
    duration: string;
    segments: Segment[];
}

interface Segment {
    departure: Arrival;
    arrival: Arrival;
    carrierCode: string;
    number: string;
    aircraft: Aircraft;
    operating: Operating;
    duration: string;
    id: string;
    numberOfStops: number;
    blacklistedInEU: boolean;
}

interface Aircraft {
    code: string;
}

interface Arrival {
    iataCode: string;
    terminal: string;
    at: Date;
}

interface Operating {
    carrierCode: string;
}

interface FlightResponsePrice {
    currency: string;
    total: string;
    base: string;
    fees: AdditionalService[];
    grandTotal: string;
    additionalServices: AdditionalService[];
}

interface AdditionalService {
    amount: string;
    type: string;
}

interface PricingOptions {
    fareType: string[];
    includedCheckedBagsOnly: boolean;
}

interface TravelerPricing {
    travelerId: string;
    fareOption: string;
    travelerType: string;
    price: TravelerPricingPrice;
    fareDetailsBySegment: FareDetailsBySegment[];
}

interface FareDetailsBySegment {
    segmentId: string;
    cabin: string;
    fareBasis: string;
    brandedFare: string;
    class: string;
    includedCheckedBags: IncludedCheckedBags;
}

interface IncludedCheckedBags {
    quantity: number;
}

interface TravelerPricingPrice {
    currency: string;
    total: string;
    base: string;
}