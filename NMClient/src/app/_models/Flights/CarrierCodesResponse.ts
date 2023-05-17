export interface CarrierCodesResponse {
    data: Data[]
}

interface Data {
    commonName: string;
    type: string;
    iataCode: string;
    icaoCode: string;
    businessName: string;
}