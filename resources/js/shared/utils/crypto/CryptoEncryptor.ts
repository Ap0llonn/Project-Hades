import {
    DEFAULT_IV_LENGTH,
    DEFAULT_SALT_LENGTH,
    ArgonTypeName,
    BinarySource,
    EncryptedPayload,
    EncryptParams,
    KdfParams,
    deriveArgon2Bytes,
    deriveSecretboxKey,
    getSodium,
    normalizeBytes,
    randomCryptoBytes,
    toBase64,
    utf8ToBytes,
} from './cryptoCore';

export class CryptoEncryptor {
    static async deriveKdfValue(
        password: string,
        salt?: BinarySource,
        params: KdfParams = {},
    ): Promise<{
        kdfValueBase64: string;
        saltBase64: string;
        keyLengthBits: number;
        argonOpsLimit: number;
        argonMemoryKb: number;
        argonType: ArgonTypeName;
    }> {
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
            kdfValueBase64: toBase64(keyBytes),
            saltBase64,
            keyLengthBits,
            argonOpsLimit,
            argonMemoryKb,
            argonType,
        };
    }

    static async encryptWithPassword(
        plainText: string,
        password: string,
        params: EncryptParams = {},
    ): Promise<EncryptedPayload> {
        if (typeof plainText !== 'string') {
            throw new Error('plainText must be a string.');
        }

        const sodiumApi = await getSodium();
        const saltBytes = params.salt
            ? normalizeBytes(params.salt, 'salt')
            : await randomCryptoBytes(params.saltLength ?? DEFAULT_SALT_LENGTH);
        const nonceBytes = params.iv
            ? normalizeBytes(params.iv, 'iv')
            : await randomCryptoBytes(params.ivLength ?? DEFAULT_IV_LENGTH);

        if (nonceBytes.length !== sodiumApi.crypto_secretbox_NONCEBYTES) {
            throw new Error(`iv must be ${sodiumApi.crypto_secretbox_NONCEBYTES} bytes for libsodium crypto_secretbox.`);
        }

        const {
            keyBytes,
            saltBase64,
            keyLengthBits,
            argonOpsLimit,
            argonMemoryKb,
            argonType,
        } = await deriveSecretboxKey(password, saltBytes, params);
        const ciphertext = sodiumApi.crypto_secretbox_easy(
            utf8ToBytes(plainText),
            nonceBytes,
            keyBytes,
        );

        return {
            ciphertextBase64: toBase64(ciphertext),
            ivBase64: toBase64(nonceBytes),
            saltBase64,
            keyLengthBits,
            kdfAlgorithm: argonType === 'Argon2i13' ? 'argon2i13' : 'argon2id13',
            argonOpsLimit,
            argonMemoryKb,
            argonType,
        };
    }

    static async wrapPrivateKey(
        masterPrivateKey: string,
        password: string,
        params: EncryptParams = {},
    ): Promise<EncryptedPayload> {
        return this.encryptWithPassword(masterPrivateKey, password, params);
    }
}

export default CryptoEncryptor;
