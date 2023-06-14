export interface LoginResponse {
    user: User;
    token: string;
}

interface User {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    isAdmin: boolean;
}