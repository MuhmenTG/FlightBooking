export interface SearchHotelsRequest {
    cityCode: string;
    adults: number;
    checkInDate: Date;
    checkOutDate: Date;
    roomQuantity: string;
    priceRange: string;
    paymentPolicy: string;
    boardType: string;
}