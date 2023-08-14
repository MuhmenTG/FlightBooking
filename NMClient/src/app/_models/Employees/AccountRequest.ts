export interface AccountRequest {
    agentId?: number
    firstName: string;
    lastName: string;
    email: string;
    status: string;
    isAdmin: number;
    isAgent: number;
}