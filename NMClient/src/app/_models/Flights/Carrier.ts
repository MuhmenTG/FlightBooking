export interface Carrier {
    commonName: string;
}

interface Data {
    type: string;
    iataCode: string;
    icaoCode: string;
    businessName: string;
}