export const DEFAULT_KDF_ITERATIONS = 210000;
export const DEFAULT_KEY_LENGTH_BITS = 256;
export const DEFAULT_SALT_LENGTH = 16;
export const DEFAULT_IV_LENGTH = 12;
export const DEFAULT_HASH = 'SHA-256';
export const PBKDF2 = 'PBKDF2';
export const AES_GCM = 'AES-GCM';

export type CryptoBytes = Uint8Array<ArrayBuffer>;
export type BinarySource = Uint8Array<ArrayBufferLike> | string;

export interface KdfParams {
    iterations?: number;
    keyLengthBits?: number;
    hash?: string;
}

export interface EncryptParams extends KdfParams {
    salt?: BinarySource;
    iv?: BinarySource;
    saltLength?: number;
    ivLength?: number;
}

export interface EncryptedPayload {
    ciphertextBase64: string;
    ivBase64: string;
    saltBase64: string;
    iterations?: number;
    hash?: string;
    keyLengthBits?: number;
}

const encoder = new TextEncoder();
const decoder = new TextDecoder();

export function getCrypto(): Crypto {
    const cryptoApi = globalThis?.crypto;

    if (!cryptoApi?.subtle || typeof cryptoApi.getRandomValues !== 'function') {
        throw new Error('Web Crypto API is not available in this environment.');
    }

    return cryptoApi;
}

export function randomBytes(length = DEFAULT_SALT_LENGTH): Uint8Array {
    const bytes = new Uint8Array(length);
    getCrypto().getRandomValues(bytes);

    return bytes;
}

function asCryptoBytes(bytes: Uint8Array<ArrayBufferLike>): CryptoBytes {
    if (bytes.buffer instanceof ArrayBuffer) {
        return bytes as CryptoBytes;
    }

    return new Uint8Array(bytes) as CryptoBytes;
}

export function randomCryptoBytes(length = DEFAULT_SALT_LENGTH): CryptoBytes {
    return asCryptoBytes(randomBytes(length));
}

export function utf8ToBytes(value: string): CryptoBytes {
    return asCryptoBytes(encoder.encode(value));
}

export function bytesToUtf8(bytes: Uint8Array): string {
    return decoder.decode(bytes);
}

export function toBase64(bytes: Uint8Array | ArrayBuffer): string {
    const typedBytes = bytes instanceof Uint8Array ? bytes : new Uint8Array(bytes);
    const maybeBuffer = (globalThis as { Buffer?: { from: (value: Uint8Array) => { toString: (encoding: string) => string } } }).Buffer;

    if (typeof btoa === 'function') {
        let binary = '';

        typedBytes.forEach((byte) => {
            binary += String.fromCharCode(byte);
        });

        return btoa(binary);
    }

    if (maybeBuffer) {
        return maybeBuffer.from(typedBytes).toString('base64');
    }

    throw new Error('No base64 encoder available in this environment.');
}

export function fromBase64(base64Value: string): CryptoBytes {
    const maybeBuffer = (globalThis as { Buffer?: { from: (value: string, encoding: string) => Uint8Array } }).Buffer;

    if (typeof atob === 'function') {
        const binary = atob(base64Value);
        const bytes = new Uint8Array(binary.length);

        for (let index = 0; index < binary.length; index += 1) {
            bytes[index] = binary.charCodeAt(index);
        }

        return asCryptoBytes(bytes);
    }

    if (maybeBuffer) {
        return asCryptoBytes(new Uint8Array(maybeBuffer.from(base64Value, 'base64')));
    }

    throw new Error('No base64 decoder available in this environment.');
}

export function normalizeBytes(value: BinarySource, fieldName: string): CryptoBytes {
    if (value instanceof Uint8Array) {
        return asCryptoBytes(value);
    }

    if (typeof value === 'string') {
        return fromBase64(value);
    }

    throw new Error(`${fieldName} must be a base64 string or Uint8Array.`);
}

export async function createPasswordKey(password: string): Promise<CryptoKey> {
    if (!password) {
        throw new Error('Password is required for key derivation.');
    }

    return getCrypto().subtle.importKey(
        'raw',
        utf8ToBytes(password),
        PBKDF2,
        false,
        ['deriveBits', 'deriveKey'],
    );
}

export function getKdfParams(params: KdfParams = {}): Required<KdfParams> {
    return {
        iterations: params.iterations ?? DEFAULT_KDF_ITERATIONS,
        keyLengthBits: params.keyLengthBits ?? DEFAULT_KEY_LENGTH_BITS,
        hash: params.hash ?? DEFAULT_HASH,
    };
}

export async function deriveAesKey(
    password: string,
    salt: BinarySource,
    params: KdfParams = {},
): Promise<{
    key: CryptoKey;
    saltBase64: string;
    iterations: number;
    hash: string;
    keyLengthBits: number;
}> {
    const saltBytes = normalizeBytes(salt, 'salt');
    const { iterations, keyLengthBits, hash } = getKdfParams(params);
    const passwordKey = await createPasswordKey(password);
    const key = await getCrypto().subtle.deriveKey(
        {
            name: PBKDF2,
            salt: saltBytes,
            iterations,
            hash,
        },
        passwordKey,
        {
            name: AES_GCM,
            length: keyLengthBits,
        },
        false,
        ['encrypt', 'decrypt'],
    );

    return {
        key,
        saltBase64: toBase64(saltBytes),
        iterations,
        hash,
        keyLengthBits,
    };
}
