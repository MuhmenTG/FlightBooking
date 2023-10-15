export interface EnquiryResponses {
    enquiryResponses: EnquiryResponse[];
}

export interface EnquiryResponse {
    id: number;
    name: string;
    email: string;
    subject: string;
    bookingReference?: string;
    message: string;
    time: Date;
    isSolved: boolean;
    created_at: Date;
    updated_at: Date;
}