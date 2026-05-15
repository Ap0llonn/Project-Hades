import { route } from 'ziggy-js';
import { ApiClient } from '../../../../shared/api/ApiClient';

interface ProfileResponse {
    data: {
        id: string;
        email: string;
        first_name: string;
        last_name: string;
    };
}

class SettingsProfileApi {
    private readonly client: ApiClient;

    constructor(client: ApiClient = new ApiClient()) {
        this.client = client;
    }

    async updateProfile(input: { first_name: string; last_name: string }): Promise<ProfileResponse['data']> {
        const response = await this.client.put<ProfileResponse>(route('settings.profile.update'), input);
        return response.data;
    }
}

export const settingsProfileApi = new SettingsProfileApi();
