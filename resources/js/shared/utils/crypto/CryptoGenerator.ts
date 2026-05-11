import {
    DEFAULT_SALT_LENGTH,
    ArgonTypeName,
    BinarySource,
    getSodium,
    KdfParams,
    deriveArgon2Bytes,
    normalizeBytes,
    randomCryptoBytes,
    toBase64,
} from './cryptoCore';

export const DEFAULT_MASTER_KEY_BYTES = 32;
export const DEFAULT_RECOVERY_CODES_COUNT = 12;
export const DEFAULT_RECOVERY_CODE_SEGMENTS = 3;
export const DEFAULT_RECOVERY_CODE_SEGMENT_LENGTH = 4;
export const RECOVERY_CODE_CHARSET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

export interface DerivedClientKey {
    derivedKeyBase64: string;
    saltBase64: string;
    keyLengthBits: number;
    argonOpsLimit: number;
    argonMemoryKb: number;
    argonType: ArgonTypeName;
}

export interface GeneratedClientKeyPair {
    masterKeyBase64: string;
    publicKeyBase64: string;
    privateKeyBase64: string;
    saltBase64: string;
}

export class CryptoGenerator {
    static async generateMasterKey(byteLength = DEFAULT_MASTER_KEY_BYTES): Promise<string> {
        if (byteLength === DEFAULT_MASTER_KEY_BYTES) {
            return this.generateSymmetricKey();
        }

        return toBase64(await randomCryptoBytes(byteLength));
    }

    static async generateSymmetricKey(): Promise<string> {
        const sodium = await getSodium();
        return toBase64(sodium.crypto_secretbox_keygen());
    }

    static async generateAsymmetricKeyPair(): Promise<{
        publicKeyBase64: string;
        privateKeyBase64: string;
    }> {
        const sodium = await getSodium();
        const keyPair = sodium.crypto_box_keypair();

        return {
            publicKeyBase64: toBase64(keyPair.publicKey),
            privateKeyBase64: toBase64(keyPair.privateKey),
        };
    }

    static async generateSalt(byteLength = DEFAULT_SALT_LENGTH): Promise<string> {
        return toBase64(await randomCryptoBytes(byteLength));
    }

    static async deriveKeyFromPassword(
        password: string,
        salt?: BinarySource,
        params: KdfParams = {},
    ): Promise<DerivedClientKey> {
        const saltBytes = salt ? normalizeBytes(salt, 'salt') : await randomCryptoBytes(DEFAULT_SALT_LENGTH);
        const {
            keyBytes,
            saltBase64,
            keyLengthBits,
            argonOpsLimit,
            argonMemoryKb,
            argonType,
        } = await deriveArgon2Bytes(password, saltBytes, params);

        return {
            derivedKeyBase64: toBase64(keyBytes),
            saltBase64,
            keyLengthBits,
            argonOpsLimit,
            argonMemoryKb,
            argonType,
        };
    }

    static async generateClientKeyPair(
        masterKeyBytes = DEFAULT_MASTER_KEY_BYTES,
        saltBytes = DEFAULT_SALT_LENGTH,
    ): Promise<GeneratedClientKeyPair> {
        const asymmetricKeyPair = await this.generateAsymmetricKeyPair();

        return {
            masterKeyBase64: await this.generateMasterKey(masterKeyBytes),
            publicKeyBase64: asymmetricKeyPair.publicKeyBase64,
            privateKeyBase64: asymmetricKeyPair.privateKeyBase64,
            saltBase64: await this.generateSalt(saltBytes),
        };
    }

    static async generateRecoveryTokens(
        count = DEFAULT_RECOVERY_CODES_COUNT,
        segments = DEFAULT_RECOVERY_CODE_SEGMENTS,
        segmentLength = DEFAULT_RECOVERY_CODE_SEGMENT_LENGTH,
    ): Promise<string[]> {
        const totalCharsNeeded = count * segments * segmentLength;
        const bytes = await randomCryptoBytes(totalCharsNeeded);

        let byteIndex = 0;

        return Array.from({ length: count }, () => {
            const tokenParts = Array.from({ length: segments }, () => {
                let tokenPart = '';

                for (let i = 0; i < segmentLength; i += 1) {
                    const byte = bytes[byteIndex];
                    byteIndex += 1;
                    tokenPart += RECOVERY_CODE_CHARSET[byte % RECOVERY_CODE_CHARSET.length];
                }

                return tokenPart;
            });

            return tokenParts.join('-');
        });
    }

    static async hashTokens(tokens: string[]): Promise<string[]> {
        if (!Array.isArray(tokens)) {
            throw new Error('tokens must be an array of strings.');
        }

        if (tokens.length === 0) {
            return [];
        }

        const sodium = await getSodium();

        return tokens.map((token) => {
            if (typeof token !== 'string') {
                throw new Error('Every token must be a string.');
            }

            const normalizedToken = token.trim().toUpperCase();
            if (!normalizedToken) {
                throw new Error('Tokens cannot be empty.');
            }

            return toBase64(sodium.crypto_hash_sha256(normalizedToken));
        });
    }
}

export default CryptoGenerator;
