import { CustomerInfo } from "../CustomerInfo";
import { FlightOfferPrice, Itinerary, PricingOptions, TravelerPricing } from "./FlightInfoResponse";

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