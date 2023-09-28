export interface AccountRequest {
    id?: number
    firstName: string;
    lastName: string;
    email: string;
    status: string;
    isAdmin: number;
    isAgent: number;
}