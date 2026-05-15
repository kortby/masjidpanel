export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    phone_number?: string;
    age?: number;
    address?: string;
    city?: string;
    zip_code?: string;
    show_phone?: boolean;
    show_email?: boolean;
    show_age?: boolean;
    show_location?: boolean;
    show_address?: boolean;
    is_verified?: boolean;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
