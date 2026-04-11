import {
    AES_GCM,
    DEFAULT_IV_LENGTH,
    DEFAULT_SALT_LENGTH,
    EncryptedPayload,
    EncryptParams,
    KdfParams,
    BinarySource,
    PBKDF2,
    createPasswordKey,
    deriveAesKey,
    getCrypto,
    getKdfParams,
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
        iterations: number;
        hash: string;
        keyLengthBits: number;
    }> {
        const saltBytes = salt ? normalizeBytes(salt, 'salt') : randomCryptoBytes(DEFAULT_SALT_LENGTH);
        const { iterations, keyLengthBits, hash } = getKdfParams(params);
        const passwordKey = await createPasswordKey(password);
        const bits = await getCrypto().subtle.deriveBits(
            {
                name: PBKDF2,
                salt: saltBytes,
                iterations,
                hash,
            },
            passwordKey,
            keyLengthBits,
        );

        return {
            kdfValueBase64: toBase64(new Uint8Array(bits)),
            saltBase64: toBase64(saltBytes),
            iterations,
            hash,
            keyLengthBits,
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

        const saltBytes = params.salt
            ? normalizeBytes(params.salt, 'salt')
            : randomCryptoBytes(params.saltLength ?? DEFAULT_SALT_LENGTH);
        const ivBytes = params.iv
            ? normalizeBytes(params.iv, 'iv')
            : randomCryptoBytes(params.ivLength ?? DEFAULT_IV_LENGTH);
        const { key, saltBase64, iterations, hash, keyLengthBits } = await deriveAesKey(
            password,
            saltBytes,
            params,
        );
        const encrypted = await getCrypto().subtle.encrypt(
            {
                name: AES_GCM,
                iv: ivBytes,
            },
            key,
            utf8ToBytes(plainText),
        );

        return {
            ciphertextBase64: toBase64(new Uint8Array(encrypted)),
            ivBase64: toBase64(ivBytes),
            saltBase64,
            iterations,
            hash,
            keyLengthBits,
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
