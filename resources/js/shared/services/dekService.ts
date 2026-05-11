import { route } from 'ziggy-js';
import { ApiClient } from '../api/ApiClient';

export interface DekAuthMethod {
    primary: string;
    mfa: string;
}

export interface DekWrapperPayload {
    type: string;
    ciphertext: string;
    nonce: string;
    tag: string;
    prf_salt: string | null;
    prf_params: Record<string, unknown> | null;
    credential_id: string | null;
    metadata: Record<string, unknown> | null;
}

export interface DekBootstrapResponse {
    auth_method: DekAuthMethod;
    wrapped_private_key: unknown;
    dek_wrapper: DekWrapperPayload;
}

const apiClient = new ApiClient();

class DekService {
    private readonly client: ApiClient;

    constructor(client: ApiClient = apiClient) {
        this.client = client;
    }

    fetchBootstrap(): Promise<DekBootstrapResponse> {
        return this.client.get<DekBootstrapResponse>(route('vault.bootstrap'));
    }
}

export const dekService = new DekService();
export { DekService };

