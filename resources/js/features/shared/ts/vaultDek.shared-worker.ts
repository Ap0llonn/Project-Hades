type WorkerAction = 'set' | 'get' | 'clear';

interface WorkerMessage {
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

const dekByScope = new Map<string, Uint8Array>();

const sanitizeScope = (scope: unknown): string => {
    if (typeof scope !== 'string') {
        return 'anonymous';
    }

    const trimmed = scope.trim();
    return trimmed === '' ? 'anonymous' : trimmed;
};

const post = (port: MessagePort, response: WorkerResponse): void => {
    port.postMessage(response);
};

const onMessage = (port: MessagePort, event: MessageEvent<WorkerMessage>): void => {
    const request = event.data;
    const id = Number(request?.id ?? 0);
    const action = request?.action;
    const scope = sanitizeScope(request?.payload?.scope);

    try {
        if (action === 'set') {
            const dek = request?.payload?.dek;
            if (!(dek instanceof Uint8Array)) {
                post(port, { id, ok: false, error: 'Invalid DEK payload.' });
                return;
            }

            dekByScope.set(scope, new Uint8Array(dek));
            post(port, { id, ok: true, result: true });
            return;
        }

        if (action === 'get') {
            const dek = dekByScope.get(scope);
            post(port, {
                id,
                ok: true,
                result: dek ? new Uint8Array(dek) : null,
            });
            return;
        }

        if (action === 'clear') {
            const existing = dekByScope.get(scope);
            if (existing) {
                existing.fill(0);
            }
            dekByScope.delete(scope);
            post(port, { id, ok: true, result: true });
            return;
        }

        post(port, { id, ok: false, error: 'Unsupported worker action.' });
    } catch {
        post(port, { id, ok: false, error: 'Worker operation failed.' });
    }
};

self.addEventListener('connect', (event: MessageEvent): void => {
    const connectEvent = event as MessageEvent & { ports?: MessagePort[] };
    const port = connectEvent.ports?.[0];
    if (!port) {
        return;
    }

    port.onmessage = (messageEvent) => onMessage(port, messageEvent as MessageEvent<WorkerMessage>);
    port.start();
});

