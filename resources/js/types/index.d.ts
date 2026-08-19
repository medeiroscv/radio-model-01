export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    avatar?: string | null;
    phone?: string | null;
    is_active?: boolean;
    roles?: Array<{ id: number; name: string }>;
}

export interface Station {
    id?: number;
    name?: string;
    legal_name?: string;
    frequency?: string;
    slogan?: string;
    city?: string;
    state?: string;
    country?: string;
    timezone?: string;
    website_url?: string;
    email?: string;
    phone?: string;
    whatsapp?: string;
    address?: string;
    logo_primary?: string | null;
    logo_small?: string | null;
    favicon?: string | null;
    primary_color?: string;
    secondary_color?: string;
    accent_color?: string;
    background_color?: string;
    surface_color?: string;
    text_color?: string;
    muted_color?: string;
    border_color?: string;
    font_family?: string;
    button_style?: string;
    dark_mode_enabled?: boolean;
    floating_player_enabled?: boolean;
    is_installed?: boolean;
}

export interface Flash {
    success?: string | null;
    error?: string | null;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User | null;
        permissions?: string[];
        roles?: string[];
    };
    station?: Station | null;
    appSettings?: Record<string, any>;
    socialLinks?: Array<{ platform: string; url: string }>;
    mainMenu?: any;
    streamStatus?: any;
    flash?: Flash;
};