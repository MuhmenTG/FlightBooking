export interface AccountResponse {
    formatedAgents: agent[]
}

export interface agent {
    agentId: number;
    firstName: string;
    lastName: string;
    email: string;
    travelAgentPermission: number;
    administratorPermission: number;
    accountStatus: string;
}