export interface FAQS {
    FAQS: FAQ[]
}

export interface FAQ {
    id: number;
    question: string;
    answer: string;
    created_at: string;
    updated_at: string;
}

export interface FAQRequest {
    id: number;
    question: string;
    answer: string;
}