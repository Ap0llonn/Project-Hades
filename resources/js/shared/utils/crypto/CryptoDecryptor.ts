import {
    EncryptedPayload,
    bytesToUtf8,
    deriveSecretboxKey,
    getSodium,
    normalizeBytes,
} from './cryptoCore';

export class CryptoDecryptor {
    static async decryptWithPassword(payload: EncryptedPayload, password: string): Promise<string> {
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
        const plaintextBytes = sodiumApi.crypto_secretbox_open_easy(
            cipherBytes,
            nonceBytes,
            keyBytes,
        );

        return bytesToUtf8(plaintextBytes);
    }

    static async unwrapPrivateKey(wrapperPayload: EncryptedPayload, password: string): Promise<string> {
        return this.decryptWithPassword(wrapperPayload, password);
    }
}

export default CryptoDecryptor;
