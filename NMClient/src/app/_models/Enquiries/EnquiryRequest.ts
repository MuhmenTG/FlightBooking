export interface EnquiryRequest {
    name: string;
    email: string;
    subject: string;
    message: string;
    bookingReference?: string
}