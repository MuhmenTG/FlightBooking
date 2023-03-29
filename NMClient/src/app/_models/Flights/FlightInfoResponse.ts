import { CustomerInfo } from "../CustomerInfo";

export interface FlightInfoResponse {
    data: Data;
}

export interface Data {
    type: string;
    flightOffers: FlightOffer[];
    bookingRequirements: BookingRequirements;
}

export interface BookingRequirements {
    emailAddressRequired: boolean;
    mobilePhoneNumberRequired: boolean;
}

export interface FlightOffer {
    passengers: CustomerInfo[]
    type: string;
    id: string;
    source: string;
    instantTicketingRequired: boolean;
    nonHomogeneous: boolean;
    paymentCardRequired: boolean;
    lastTicketingDate: Date;
    itineraries: Itinerary[];
    price: FlightOfferPrice;
    pricingOptions: PricingOptions;
    validatingAirlineCodes: string[];
    travelerPricings: TravelerPricing[];
}

export interface Itinerary {
    segments: Segment[];
}

export interface Segment {
    departure: Arrival;
    arrival: Arrival;
    carrierCode: string;
    number: string;
    aircraft: Aircraft;
    operating: Operating;
    duration: string;
    id: string;
    numberOfStops: number;
    co2Emissions: Co2Emission[];
}

export interface Aircraft {
    code: string;
}

export interface Arrival {
    iataCode: string;
    at: Date;
    terminal?: string;
}

export interface Co2Emission {
    weight: number;
    weightUnit: string;
    cabin: string;
}

export interface Operating {
    carrierCode: string;
}

export interface FlightOfferPrice {
    currency: string;
    total: string;
    base: string;
    fees: Fee[];
    grandTotal: string;
    billingCurrency: string;
}

export interface Fee {
    amount: string;
    type: string;
}

export interface PricingOptions {
    fareType: string[];
    includedCheckedBagsOnly: boolean;
}

export interface TravelerPricing {
    travelerId: string;
    fareOption: string;
    travelerType: string;
    price: TravelerPricingPrice;
    fareDetailsBySegment: FareDetailsBySegment[];
}

export interface FareDetailsBySegment {
    segmentId: string;
    cabin: string;
    fareBasis: string;
    brandedFare: string;
    class: string;
    includedCheckedBags: IncludedCheckedBags;
}

export interface IncludedCheckedBags {
    quantity: number;
}

export interface TravelerPricingPrice {
    currency: string;
    total: string;
    base: string;
    taxes: Tax[];
    refundableTaxes: string;
}

export interface Tax {
    amount: string;
    code: string;
}