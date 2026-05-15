import {
    BinarySource,
    EncryptedPayload,
    bytesToUtf8,
    deriveSecretboxKey,
    getSodium,
    normalizeBytes,
} from './cryptoCore';

export class CryptoDecryptor {
    static async decryptBytesWithKey(
        payload: { ciphertextBase64: string; ivBase64: string },
        key: BinarySource,
    ): Promise<Uint8Array> {
        if (!payload?.ciphertextBase64 || !payload?.ivBase64) {
            throw new Error('Payload must include ciphertextBase64 and ivBase64.');
        }

        const sodiumApi = await getSodium();
        const nonceBytes = normalizeBytes(payload.ivBase64, 'iv');
        const cipherBytes = normalizeBytes(payload.ciphertextBase64, 'ciphertext');
        const keyBytes = normalizeBytes(key, 'key');

        if (nonceBytes.length !== sodiumApi.crypto_secretbox_NONCEBYTES) {
            throw new Error(`iv must be ${sodiumApi.crypto_secretbox_NONCEBYTES} bytes for libsodium crypto_secretbox.`);
        }

        if (keyBytes.length !== sodiumApi.crypto_secretbox_KEYBYTES) {
            throw new Error(`key must be ${sodiumApi.crypto_secretbox_KEYBYTES} bytes for libsodium crypto_secretbox.`);
        }

        return sodiumApi.crypto_secretbox_open_easy(
            cipherBytes,
            nonceBytes,
            keyBytes,
        );
    }

    static async decryptBytesWithPassword(payload: EncryptedPayload, password: string): Promise<Uint8Array> {
        if (!payload?.ciphertextBase64 || !payload?.ivBase64 || !payload?.saltBase64) {
            throw new Error('Payload must include ciphertextBase64, ivBase64, and saltBase64.');
        }

        const sodiumApi = await getSodium();
        const nonceBytes = normalizeBytes(payload.ivBase64, 'iv');
        const cipherBytes = normalizeBytes(payload.ciphertextBase64, 'ciphertext');

        if (nonceBytes.length !== sodiumApi.crypto_secretbox_NONCEBYTES) {
            throw new Error(`iv must be ${sodiumApi.crypto_secretbox_NONCEBYTES} bytes for libsodium crypto_secretbox.`);
        }

        const { keyBytes } = await deriveSecretboxKey(password, payload.saltBase64, {
            keyLengthBits: payload.keyLengthBits,
            argonOpsLimit: payload.argonOpsLimit,
            argonMemoryKb: payload.argonMemoryKb,
            argonType: payload.argonType,
        });

        return sodiumApi.crypto_secretbox_open_easy(
            cipherBytes,
            nonceBytes,
            keyBytes,
        );
    }

    static async decryptWithPassword(payload: EncryptedPayload, password: string): Promise<string> {
        const plaintextBytes = await this.decryptBytesWithPassword(payload, password);
        return bytesToUtf8(plaintextBytes);
    }

    /**
     * Decrypt a password/value using the unlocked DEK.
     * This is intended for vault item password fields.
     */
    static async decryptPasswordWithDek(
        payload: { ciphertextBase64: string; ivBase64: string },
        dek: BinarySource,
    ): Promise<string> {
        const plaintextBytes = await this.decryptBytesWithKey(payload, dek);
        return bytesToUtf8(plaintextBytes);
    }

    static async unwrapPrivateKey(wrapperPayload: EncryptedPayload, password: string): Promise<string> {
        return this.decryptWithPassword(wrapperPayload, password);
    }

    static async decryptWithPrivateKey(
        payload: { ciphertextBase64: string },
        recipientPublicKey: BinarySource,
        recipientPrivateKey: BinarySource,
    ): Promise<string> {
        if (!payload?.ciphertextBase64) {
            throw new Error('Payload must include ciphertextBase64.');
        }

        const sodiumApi = await getSodium();
        const cipherBytes = normalizeBytes(payload.ciphertextBase64, 'ciphertext');
        const publicKeyBytes = normalizeBytes(recipientPublicKey, 'recipientPublicKey');
        const privateKeyBytes = normalizeBytes(recipientPrivateKey, 'recipientPrivateKey');

        if (publicKeyBytes.length !== sodiumApi.crypto_box_PUBLICKEYBYTES) {
            throw new Error(`recipientPublicKey must be ${sodiumApi.crypto_box_PUBLICKEYBYTES} bytes.`);
        }

        if (privateKeyBytes.length !== sodiumApi.crypto_box_SECRETKEYBYTES) {
            throw new Error(`recipientPrivateKey must be ${sodiumApi.crypto_box_SECRETKEYBYTES} bytes.`);
        }

        const plainBytes = sodiumApi.crypto_box_seal_open(
            cipherBytes,
            publicKeyBytes,
            privateKeyBytes,
        );

        return bytesToUtf8(plainBytes);
    }
}

export default CryptoDecryptor;
