import {
    DEFAULT_SALT_LENGTH,
    ArgonTypeName,
    BinarySource,
    KdfParams,
    deriveArgon2Bytes,
    normalizeBytes,
    randomCryptoBytes,
    toBase64,
} from './cryptoCore';

export const DEFAULT_MASTER_KEY_BYTES = 32;
export const DEFAULT_PRIVATE_KEY_BYTES = 32;

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
    privateKeyBase64: string;
    saltBase64: string;
}

export class CryptoGenerator {
    static async generateMasterKey(byteLength = DEFAULT_MASTER_KEY_BYTES): Promise<string> {
        return toBase64(await randomCryptoBytes(byteLength));
    }

    static async generatePrivateKey(byteLength = DEFAULT_PRIVATE_KEY_BYTES): Promise<string> {
        return toBase64(await randomCryptoBytes(byteLength));
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
        privateKeyBytes = DEFAULT_PRIVATE_KEY_BYTES,
        saltBytes = DEFAULT_SALT_LENGTH,
    ): Promise<GeneratedClientKeyPair> {
        return {
            masterKeyBase64: await this.generateMasterKey(masterKeyBytes),
            privateKeyBase64: await this.generatePrivateKey(privateKeyBytes),
            saltBase64: await this.generateSalt(saltBytes),
        };
    }
}

export default CryptoGenerator;
