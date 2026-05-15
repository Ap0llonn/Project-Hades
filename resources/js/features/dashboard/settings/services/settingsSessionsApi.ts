import { route } from 'ziggy-js';
import { ApiClient } from '../../../../shared/api/ApiClient';

export interface SettingsSessionItem {
    id: string;
    channel: 'web' | 'extension';
    device_name: string;
    browser: string;
    platform: string;
    ip_address: string;
    last_active_at: string | null;
    is_current: boolean;
    can_revoke: boolean;
}

interface SessionsResponse {
    data: SettingsSessionItem[];
}

class SettingsSessionsApi {
    private readonly client: ApiClient;

    constructor(client: ApiClient = new ApiClient()) {
        this.client = client;
    }

    async listSessions(): Promise<SettingsSessionItem[]> {
        const response = await this.client.get<SessionsResponse>(route('settings.sessions.read'));
        return Array.isArray(response.data) ? response.data : [];
    }

    async revokeSession(sessionId: string, channel: SettingsSessionItem['channel']): Promise<void> {
        await this.client.delete(route('settings.sessions.revoke', { sessionId }), {
            body: { channel },
        });
    }
}

export const settingsSessionsApi = new SettingsSessionsApi();
