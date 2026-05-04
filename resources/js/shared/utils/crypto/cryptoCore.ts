import sodium from 'libsodium-wrappers-sumo';

export const DEFAULT_KEY_LENGTH_BITS = 256;
export const DEFAULT_SALT_LENGTH = 16;
export const DEFAULT_IV_LENGTH = 24;
export const DEFAULT_ARGON_OPS_LIMIT = 3;
export const DEFAULT_ARGON_MEMORY_KIB = 65536;
export const DEFAULT_ARGON_TYPE: ArgonTypeName = 'Argon2id13';

export type ArgonTypeName = 'Argon2id13' | 'Argon2i13';
export type CryptoBytes = Uint8Array<ArrayBuffer>;
export type BinarySource = Uint8Array<ArrayBufferLike> | string;

export interface KdfParams {
    keyLengthBits?: number;
    argonOpsLimit?: number;
    argonMemoryKb?: number;
    argonType?: ArgonTypeName;
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
    keyLengthBits?: number;
    kdfAlgorithm?: 'argon2id13' | 'argon2i13';
    argonOpsLimit?: number;
    argonMemoryKb?: number;
    argonType?: ArgonTypeName;
}

const encoder = new TextEncoder();
const decoder = new TextDecoder();
let sodiumReadyPromise: Promise<typeof sodium> | null = null;

function asCryptoBytes(bytes: Uint8Array<ArrayBufferLike>): CryptoBytes {
    if (bytes.buffer instanceof ArrayBuffer) {
        return bytes as CryptoBytes;
    }

    return new Uint8Array(bytes) as CryptoBytes;
}

export async function getSodium(): Promise<typeof sodium> {
    if (!sodiumReadyPromise) {
        sodiumReadyPromise = sodium.ready.then(() => sodium);
    }

    return sodiumReadyPromise;
}

export async function randomCryptoBytes(length = DEFAULT_SALT_LENGTH): Promise<CryptoBytes> {
    const sodiumApi = await getSodium();

    return asCryptoBytes(sodiumApi.randombytes_buf(length));
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
    const normalizedBase64 = normalizeBase64Value(base64Value);

    if (typeof atob === 'function') {
        const binary = atob(normalizedBase64);
        const bytes = new Uint8Array(binary.length);

        for (let index = 0; index < binary.length; index += 1) {
            bytes[index] = binary.charCodeAt(index);
        }

        return asCryptoBytes(bytes);
    }

    if (maybeBuffer) {
        return asCryptoBytes(new Uint8Array(maybeBuffer.from(normalizedBase64, 'base64')));
    }

    throw new Error('No base64 decoder available in this environment.');
}

function normalizeBase64Value(value: string): string {
    const compactValue = value.replace(/\s+/g, '');
    const standardAlphabet = compactValue.replace(/-/g, '+').replace(/_/g, '/');
    const remainder = standardAlphabet.length % 4;

    if (remainder === 0) {
        return standardAlphabet;
    }

    return standardAlphabet.padEnd(standardAlphabet.length + (4 - remainder), '=');
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

export function getKdfParams(params: KdfParams = {}): Required<KdfParams> {
    return {
        keyLengthBits: params.keyLengthBits ?? DEFAULT_KEY_LENGTH_BITS,
        argonOpsLimit: params.argonOpsLimit ?? DEFAULT_ARGON_OPS_LIMIT,
        argonMemoryKb: params.argonMemoryKb ?? DEFAULT_ARGON_MEMORY_KIB,
        argonType: params.argonType ?? DEFAULT_ARGON_TYPE,
    };
}

function mapArgonAlgorithm(sodiumApi: typeof sodium, argonType: ArgonTypeName): number {
    if (argonType === 'Argon2i13') {
        return sodiumApi.crypto_pwhash_ALG_ARGON2I13;
    }

    return sodiumApi.crypto_pwhash_ALG_ARGON2ID13;
}

function ensureSecretboxKeyLength(sodiumApi: typeof sodium, keyLengthBits: number): void {
    const requiredBits = sodiumApi.crypto_secretbox_KEYBYTES * 8;

    if (keyLengthBits !== requiredBits) {
        throw new Error(`keyLengthBits must be ${requiredBits} for libsodium crypto_secretbox.`);
    }
}

export async function deriveArgon2Bytes(
    password: string,
    salt: BinarySource,
    params: KdfParams = {},
): Promise<{
    keyBytes: CryptoBytes;
    saltBase64: string;
    keyLengthBits: number;
    argonOpsLimit: number;
    argonMemoryKb: number;
    argonType: ArgonTypeName;
}> {
    if (!password) {
        throw new Error('Password is required for key derivation.');
    }

    const sodiumApi = await getSodium();
    const saltBytes = normalizeBytes(salt, 'salt');
    const { keyLengthBits, argonOpsLimit, argonMemoryKb, argonType } = getKdfParams(params);
    const keyBytes = asCryptoBytes(
        sodiumApi.crypto_pwhash(
            keyLengthBits / 8,
            password,
            saltBytes,
            argonOpsLimit,
            argonMemoryKb * 1024,
            mapArgonAlgorithm(sodiumApi, argonType),
        ),
    );

    return {
        keyBytes,
        saltBase64: toBase64(saltBytes),
        keyLengthBits,
        argonOpsLimit,
        argonMemoryKb,
        argonType,
    };
}

export async function deriveSecretboxKey(
    password: string,
    salt: BinarySource,
    params: KdfParams = {},
): Promise<{
    keyBytes: CryptoBytes;
    saltBase64: string;
    keyLengthBits: number;
    argonOpsLimit: number;
    argonMemoryKb: number;
    argonType: ArgonTypeName;
}> {
    const sodiumApi = await getSodium();
    const derived = await deriveArgon2Bytes(password, salt, params);

    ensureSecretboxKeyLength(sodiumApi, derived.keyLengthBits);

    return derived;
}
