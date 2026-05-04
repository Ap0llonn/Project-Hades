export class ApiError extends Error {
    status: number;
    data: unknown;

    constructor(message: string, status: number, data: unknown) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.data = data;
    }
}

export interface ApiClientOptions extends Omit<RequestInit, 'headers' | 'body'> {
    headers?: Record<string, string>;
    body?: unknown;
}

export class ApiClient {
    private readonly defaultOptions: ApiClientOptions;

    constructor(defaultOptions: ApiClientOptions = {}) {
        this.defaultOptions = {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            ...defaultOptions,
        };
    }

    async get<T>(url: string, options: ApiClientOptions = {}): Promise<T> {
        return this.request<T>(url, { ...options, method: 'GET' });
    }

    async post<T>(url: string, body: unknown, options: ApiClientOptions = {}): Promise<T> {
        return this.request<T>(url, { ...options, method: 'POST', body });
    }

    async put<T>(url: string, body: unknown, options: ApiClientOptions = {}): Promise<T> {
        return this.request<T>(url, { ...options, method: 'PUT', body });
    }

    async patch<T>(url: string, body: unknown, options: ApiClientOptions = {}): Promise<T> {
        return this.request<T>(url, { ...options, method: 'PATCH', body });
    }

    async delete<T>(url: string, options: ApiClientOptions = {}): Promise<T> {
        return this.request<T>(url, { ...options, method: 'DELETE' });
    }

    async request<T>(url: string, options: ApiClientOptions = {}): Promise<T> {
        const merged = this.mergeOptions(options);
        const response = await fetch(url, merged);
        const data = await this.parseResponse(response);

        if (!response.ok) {
            const message = this.extractErrorMessage(data, response.status);
            throw new ApiError(message, response.status, data);
        }

        return data as T;
    }

    private mergeOptions(options: ApiClientOptions): RequestInit {
        const mergedHeaders: Record<string, string> = {
            ...(this.defaultOptions.headers ?? {}),
            ...(options.headers ?? {}),
        };

        const merged: ApiClientOptions = {
            ...this.defaultOptions,
            ...options,
            headers: mergedHeaders,
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken && !mergedHeaders['X-CSRF-TOKEN']) {
            mergedHeaders['X-CSRF-TOKEN'] = csrfToken;
        }

        if (merged.body === undefined || merged.body === null) {
            return merged;
        }

        if (this.isNativeBody(merged.body)) {
            return merged;
        }

        if (!mergedHeaders['Content-Type']) {
            mergedHeaders['Content-Type'] = 'application/json';
        }

        return {
            ...merged,
            body: JSON.stringify(merged.body),
        };
    }

    private isNativeBody(body: unknown): body is BodyInit {
        return body instanceof FormData
            || body instanceof URLSearchParams
            || body instanceof Blob
            || typeof body === 'string';
    }

    private extractErrorMessage(data: unknown, status: number): string {
        if (typeof data === 'object' && data !== null && 'message' in data) {
            const message = (data as { message?: unknown }).message;
            if (typeof message === 'string' && message.length > 0) {
                return message;
            }
        }

        return `Request failed with status ${status}.`;
    }

    private async parseResponse(response: Response): Promise<unknown> {
        if (response.status === 204 || response.status === 205) {
            return null;
        }

        const contentType = response.headers.get('content-type') ?? '';
        if (contentType.includes('application/json')) {
            return response.json();
        }

        return response.text();
    }
}
