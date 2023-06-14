export interface AccountResponse {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    agentPermission: boolean;
    adminPermission: boolean;
    accountStatus: string;
}