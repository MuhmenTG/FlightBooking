export interface FAQS {
    FAQS: FAQ[]
}

interface FAQ {
    id: number;
    question: string;
    answer: string;
    created_at: string;
    updated_at: string;
}