import {
    DEFAULT_IV_LENGTH,
    DEFAULT_SALT_LENGTH,
    ArgonTypeName,
    BinarySource,
    EncryptParams,
    KdfParams,
    deriveArgon2Bytes,
    getSodium,
    normalizeBytes,
    randomCryptoBytes,
    toBase64,
    utf8ToBytes,
} from './cryptoCore';

export interface DerivedKeyResult {
    keyBytes: Uint8Array;
    saltBase64: string;
    keyLengthBits: number;
    argonOpsLimit: number;
    argonMemoryKb: number;
    argonType: ArgonTypeName;
    kdfAlgorithm: 'argon2i13' | 'argon2id13';
}

export interface EncryptedPayload {
    ciphertextBase64: string;
    ivBase64: string;
}

export interface WrappedMasterKeyPayload extends EncryptedPayload {
    saltBase64: string;
    keyLengthBits: number;
    kdfAlgorithm: 'argon2i13' | 'argon2id13';
    argonOpsLimit: number;
    argonMemoryKb: number;
    argonType: ArgonTypeName;
}

export interface AsymmetricEncryptedPayload {
    ciphertextBase64: string;
    algorithm: 'libsodium.crypto_box_seal' | 'libsodium.crypto_box_easy';
    ivBase64?: string;
    senderPublicKeyBase64?: string;
}

export class CryptoEncryptor {

    /**
     * Derive a cryptographic key from a user password.
     * Use this only for:
     * - wrapping/unwrapping the master key
     * - password change flows
     */
    static async deriveKeyFromPassword(
        password: string,
        salt?: BinarySource,
        params: KdfParams = {},
    ): Promise<DerivedKeyResult> {
        if (typeof password !== 'string' || password.length === 0) {
            throw new Error('password must be a non-empty string.');
        }

        const saltBytes = salt
            ? normalizeBytes(salt, 'salt')
            : await randomCryptoBytes(DEFAULT_SALT_LENGTH);

        const {
            keyBytes,
            saltBase64,
            keyLengthBits,
            argonOpsLimit,
            argonMemoryKb,
            argonType,
        } = await deriveArgon2Bytes(password, saltBytes, params);

        return {
            keyBytes,
            saltBase64,
            keyLengthBits,
            argonOpsLimit,
            argonMemoryKb,
            argonType,
            kdfAlgorithm: argonType === 'Argon2i13' ? 'argon2i13' : 'argon2id13',
        };
    }

    /**
     * Encrypt plaintext with an already available symmetric key.
     * This is what you should use for:
     * - vault items
     * - private key wrapping with master key
     * - any normal runtime encryption
     */
    static async encryptWithKey(
        plainText: string,
        key: BinarySource,
        iv?: BinarySource,
    ): Promise<EncryptedPayload> {
        if (typeof plainText !== 'string') {
            throw new Error('plainText must be a string.');
        }

        const sodium = await getSodium();
        const keyBytes = normalizeBytes(key, 'key');

        if (keyBytes.length !== sodium.crypto_secretbox_KEYBYTES) {
            throw new Error(`key must be ${sodium.crypto_secretbox_KEYBYTES} bytes.`);
        }

        const nonceBytes = iv
            ? normalizeBytes(iv, 'iv')
            : await randomCryptoBytes(DEFAULT_IV_LENGTH);

        if (nonceBytes.length !== sodium.crypto_secretbox_NONCEBYTES) {
            throw new Error(`iv must be ${sodium.crypto_secretbox_NONCEBYTES} bytes.`);
        }

        const ciphertext = sodium.crypto_secretbox_easy(
            utf8ToBytes(plainText),
            nonceBytes,
            keyBytes,
        );

        return {
            ciphertextBase64: toBase64(ciphertext),
            ivBase64: toBase64(nonceBytes),
        };
    }

    /**
     * Encrypt a password/value with the unlocked DEK.
     * This is intended for vault item password fields.
     */
    static async encryptPasswordWithDek(
        password: string,
        dek: BinarySource,
        iv?: BinarySource,
    ): Promise<EncryptedPayload> {
        if (typeof password !== 'string' || password.length === 0) {
            throw new Error('password must be a non-empty string.');
        }

        return this.encryptWithKey(password, dek, iv);
    }

    /**
     * Wrap the master key with a key derived from the user's password.
     * Use at signup and password rotation only.
     */
    static async wrapKeyWithPassword(
        masterKey: BinarySource,
        password: string,
        params: EncryptParams = {},
    ): Promise<WrappedMasterKeyPayload> {
        const masterKeyBytes = normalizeBytes(masterKey, 'masterKey');
        const { keyBytes, saltBase64, keyLengthBits, argonOpsLimit, argonMemoryKb, argonType, kdfAlgorithm } =
            await this.deriveKeyFromPassword(
                password,
                params.salt,
                {
                    keyLengthBits: params.keyLengthBits,
                    argonOpsLimit: params.argonOpsLimit,
                    argonMemoryKb: params.argonMemoryKb,
                    argonType: params.argonType,
                },
            );

        const encrypted = await this.encryptBytesWithKey(
            masterKeyBytes,
            keyBytes,
            params.iv,
        );

        return {
            ...encrypted,
            saltBase64,
            keyLengthBits,
            kdfAlgorithm,
            argonOpsLimit,
            argonMemoryKb,
            argonType,
        };
    }

    /**
     * Wrap a private key with the already unlocked master key.
     * This is the correct model for sharing-ready architecture.
     */
    static async wrapPrivateKeyWithMasterKey(
        privateKey: BinarySource,
        masterKey: BinarySource,
        iv?: BinarySource,
    ): Promise<EncryptedPayload> {
        const privateKeyBytes = normalizeBytes(privateKey, 'privateKey');
        return this.encryptBytesWithKey(privateKeyBytes, masterKey, iv);
    }

    /**
     * Encrypt raw bytes with a symmetric key.
     * Useful when the payload is already binary.
     */
    static async encryptBytesWithKey(
        plainBytes: BinarySource,
        key: BinarySource,
        iv?: BinarySource,
    ): Promise<EncryptedPayload> {
        const sodium = await getSodium();
        const messageBytes = normalizeBytes(plainBytes, 'plainBytes');
        const keyBytes = normalizeBytes(key, 'key');

        if (keyBytes.length !== sodium.crypto_secretbox_KEYBYTES) {
            throw new Error(`key must be ${sodium.crypto_secretbox_KEYBYTES} bytes.`);
        }

        const nonceBytes = iv
            ? normalizeBytes(iv, 'iv')
            : await randomCryptoBytes(DEFAULT_IV_LENGTH);

        if (nonceBytes.length !== sodium.crypto_secretbox_NONCEBYTES) {
            throw new Error(`iv must be ${sodium.crypto_secretbox_NONCEBYTES} bytes.`);
        }

        const ciphertext = sodium.crypto_secretbox_easy(
            messageBytes,
            nonceBytes,
            keyBytes,
        );

        return {
            ciphertextBase64: toBase64(ciphertext),
            ivBase64: toBase64(nonceBytes),
        };
    }

    static async encryptWithPublicKey(
        plainText: string,
        recipientPublicKey: BinarySource,
    ): Promise<AsymmetricEncryptedPayload> {
        if (typeof plainText !== 'string') {
            throw new Error('plainText must be a string.');
        }

        const sodium = await getSodium();
        const publicKeyBytes = normalizeBytes(recipientPublicKey, 'recipientPublicKey');

        if (publicKeyBytes.length !== sodium.crypto_box_PUBLICKEYBYTES) {
            throw new Error(`recipientPublicKey must be ${sodium.crypto_box_PUBLICKEYBYTES} bytes.`);
        }

        const ciphertext = sodium.crypto_box_seal(
            utf8ToBytes(plainText),
            publicKeyBytes,
        );

        return {
            ciphertextBase64: toBase64(ciphertext),
            algorithm: 'libsodium.crypto_box_seal',
        };
    }

    static async encryptWithRecipientPublicAndSenderPrivateKey(
        plainText: string,
        recipientPublicKey: BinarySource,
        senderPrivateKey: BinarySource,
    ): Promise<AsymmetricEncryptedPayload> {
        if (typeof plainText !== 'string') {
            throw new Error('plainText must be a string.');
        }

        const sodium = await getSodium();
        const recipientPublicKeyBytes = normalizeBytes(recipientPublicKey, 'recipientPublicKey');
        const senderPrivateKeyBytes = normalizeBytes(senderPrivateKey, 'senderPrivateKey');

        if (recipientPublicKeyBytes.length !== sodium.crypto_box_PUBLICKEYBYTES) {
            throw new Error(`recipientPublicKey must be ${sodium.crypto_box_PUBLICKEYBYTES} bytes.`);
        }

        if (senderPrivateKeyBytes.length !== sodium.crypto_box_SECRETKEYBYTES) {
            throw new Error(`senderPrivateKey must be ${sodium.crypto_box_SECRETKEYBYTES} bytes.`);
        }

        const nonce = await randomCryptoBytes(sodium.crypto_box_NONCEBYTES);
        const ciphertext = sodium.crypto_box_easy(
            utf8ToBytes(plainText),
            nonce,
            recipientPublicKeyBytes,
            senderPrivateKeyBytes,
        );

        const senderPublicKeyBytes = sodium.crypto_scalarmult_base(senderPrivateKeyBytes);

        return {
            ciphertextBase64: toBase64(ciphertext),
            algorithm: 'libsodium.crypto_box_easy',
            ivBase64: toBase64(nonce),
            senderPublicKeyBase64: toBase64(senderPublicKeyBytes),
        };
    }
}

export default CryptoEncryptor;
