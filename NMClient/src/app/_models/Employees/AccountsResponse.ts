export interface AccountsResponse {
    formatedAgents: agent[]
}

export interface agent {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    travelAgentPermission: number;
    administratorPermission: number;
    accountStatus: string;
    registeredAt: string;
}