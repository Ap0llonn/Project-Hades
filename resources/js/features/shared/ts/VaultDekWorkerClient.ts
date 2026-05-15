type WorkerAction = 'set' | 'get' | 'clear';

interface WorkerRequest {
    id: number;
    action: WorkerAction;
    payload?: {
        scope?: string;
        dek?: Uint8Array;
    };
}

interface WorkerResponse {
    id: number;
    ok: boolean;
    result?: unknown;
    error?: string;
}

export class VaultDekWorkerClient {
    private port: MessagePort | null = null;
    private requestId = 0;
    private readonly pending = new Map<number, {
        resolve: (value: unknown) => void;
        reject: (error: Error) => void;
    }>();

    constructor() {
        try {
            if (typeof window === 'undefined' || typeof SharedWorker === 'undefined') {
                return;
            }

            const worker = new SharedWorker(
                new URL('./vaultDek.shared-worker.ts', import.meta.url),
                { type: 'module', name: 'vault-dek-shared-worker' },
            );

            this.port = worker.port;
            this.port.onmessage = (event: MessageEvent<WorkerResponse>) => {
                const data = event.data;
                const id = Number(data?.id ?? 0);
                const pending = this.pending.get(id);
                if (!pending) {
                    return;
                }

                this.pending.delete(id);
                if (data.ok) {
                    pending.resolve(data.result);
                    return;
                }

                pending.reject(new Error(data.error ?? 'Shared worker request failed.'));
            };
            this.port.start();
        } catch {
            this.port = null;
        }
    }

    isAvailable(): boolean {
        return this.port !== null;
    }

    async setDek(scope: string, dek: Uint8Array): Promise<boolean> {
        if (!this.port) {
            return false;
        }

        await this.request('set', {
            scope,
            dek: new Uint8Array(dek),
        });

        return true;
    }

    async getDek(scope: string): Promise<Uint8Array | null> {
        if (!this.port) {
            return null;
        }

        const result = await this.request('get', { scope });
        return result instanceof Uint8Array ? result : null;
    }

    async clearDek(scope: string): Promise<boolean> {
        if (!this.port) {
            return false;
        }

        await this.request('clear', { scope });
        return true;
    }

    private request(action: WorkerAction, payload: WorkerRequest['payload']): Promise<unknown> {
        if (!this.port) {
            return Promise.resolve(null);
        }

        this.requestId += 1;
        const id = this.requestId;

        const request: WorkerRequest = {
            id,
            action,
            payload,
        };

        return new Promise((resolve, reject) => {
            this.pending.set(id, { resolve, reject });
            this.port?.postMessage(request);
        });
    }
}

export const vaultDekWorkerClient = new VaultDekWorkerClient();

