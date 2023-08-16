export interface CitySearchResponse {
    city: City[];
}

interface City {
    id: number;
    airportIcao: string;
    airportName: string;
    city: string;
    country: string;
}