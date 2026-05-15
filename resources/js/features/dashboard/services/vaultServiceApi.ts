import { route } from 'ziggy-js';
import { ApiClient } from '../../../shared/api/ApiClient';

export type ApiServiceType = 'login' | 'credit_card' | 'note' | 'identity';

export interface EncryptedServicePayload {
    ciphertextBase64: string;
    ivBase64: string;
    version?: number;
    algorithm?: string;
    encoding?: string;
    schema?: number;
    createdAt?: string;
}

export interface ServiceRecord {
    id: string;
    user_id: string;
    type: ApiServiceType;
    favorite: boolean;
    status: 'active' | 'archived';
    payload: EncryptedServicePayload;
    created_at: string | null;
    updated_at: string | null;
}

export interface RecipientPublicKeyRecord {
    user_id: string;
    email: string;
    public_key: string;
}

export interface ServiceShareKeyEnvelope {
    ciphertextBase64: string;
    algorithm: 'libsodium.crypto_box_seal' | 'libsodium.crypto_box_easy';
    ivBase64?: string;
    senderPublicKeyBase64?: string;
    version?: number;
    schema?: number;
}

export interface SharedServiceRecord {
    id: string;
    service_id: string;
    owner_user_id: string;
    recipient_user_id: string;
    key_envelope: ServiceShareKeyEnvelope;
    created_at: string | null;
    updated_at: string | null;
    shared_by: {
        id: string;
        email: string;
        public_key: string;
    } | null;
    service: ServiceRecord | null;
}

interface ServiceListResponse {
    data: ServiceRecord[];
}

interface ServiceSingleResponse {
    data: ServiceRecord;
}

interface RecipientPublicKeyResponse {
    data: RecipientPublicKeyRecord;
}

interface ShareSingleResponse {
    data: SharedServiceRecord;
}

interface ShareListResponse {
    data: SharedServiceRecord[];
}

const apiClient = new ApiClient();

class VaultServiceApi {
    private readonly client: ApiClient;

    constructor(client: ApiClient = apiClient) {
        this.client = client;
    }

    async list(params: { type?: ApiServiceType; status?: 'active' | 'archived'; search?: string } = {}): Promise<ServiceRecord[]> {
        const query = new URLSearchParams();
        if (params.type) {
            query.set('type', params.type);
        }
        if (params.status) {
            query.set('status', params.status);
        }
        if (params.search && params.search.trim() !== '') {
            query.set('search', params.search.trim());
        }

        const baseUrl = route('service.read');
        const url = query.size > 0 ? `${baseUrl}?${query.toString()}` : baseUrl;
        const response = await this.client.get<ServiceListResponse>(url);
        return Array.isArray(response.data) ? response.data : [];
    }

    async create(input: {
        type: ApiServiceType;
        favorite?: boolean;
        status?: 'active' | 'archived';
        payload: EncryptedServicePayload;
    }): Promise<ServiceRecord> {
        const response = await this.client.post<ServiceSingleResponse>(route('service.create'), input);
        return response.data;
    }

    async update(
        id: string,
        input: {
            type?: ApiServiceType;
            favorite?: boolean;
            status?: 'active' | 'archived';
            payload?: EncryptedServicePayload;
        },
    ): Promise<ServiceRecord> {
        const response = await this.client.put<ServiceSingleResponse>(route('service.update', { serviceId: id }), input);
        return response.data;
    }

    async remove(id: string): Promise<void> {
        await this.client.delete(route('service.delete', { serviceId: id }));
    }

    async lookupRecipientPublicKey(email: string): Promise<RecipientPublicKeyRecord> {
        const query = new URLSearchParams();
        query.set('email', email.trim());
        const url = `${route('service.share.recipient')}?${query.toString()}`;
        const response = await this.client.get<RecipientPublicKeyResponse>(url);
        return response.data;
    }

    async share(
        serviceId: string,
        input: {
            recipient_email: string;
            key_envelope: ServiceShareKeyEnvelope;
        },
    ): Promise<SharedServiceRecord> {
        const response = await this.client.post<ShareSingleResponse>(
            route('service.share.create', { serviceId }),
            input,
        );

        return response.data;
    }

    async listIncomingShares(params: { type?: ApiServiceType; status?: 'active' | 'archived'; search?: string } = {}): Promise<SharedServiceRecord[]> {
        const query = new URLSearchParams();
        if (params.type) {
            query.set('type', params.type);
        }
        if (params.status) {
            query.set('status', params.status);
        }
        if (params.search && params.search.trim() !== '') {
            query.set('search', params.search.trim());
        }

        const baseUrl = route('service.share.incoming');
        const url = query.size > 0 ? `${baseUrl}?${query.toString()}` : baseUrl;
        const response = await this.client.get<ShareListResponse>(url);
        return Array.isArray(response.data) ? response.data : [];
    }

    async revokeShare(serviceId: string, shareId: string): Promise<void> {
        await this.client.delete(route('service.share.revoke', { serviceId, shareId }));
    }
}

export const vaultServiceApi = new VaultServiceApi();
export { VaultServiceApi };
