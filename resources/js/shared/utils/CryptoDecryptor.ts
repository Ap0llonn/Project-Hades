import {
    AES_GCM,
    EncryptedPayload,
    bytesToUtf8,
    deriveAesKey,
    getCrypto,
    normalizeBytes,
} from './cryptoCore';

export class CryptoDecryptor {
    static async decryptWithPassword(payload: EncryptedPayload, password: string): Promise<string> {
        if (!payload?.ciphertextBase64 || !payload?.ivBase64 || !payload?.saltBase64) {
            throw new Error('Payload must include ciphertextBase64, ivBase64, and saltBase64.');
        }

        const ivBytes = normalizeBytes(payload.ivBase64, 'iv');
        const cipherBytes = normalizeBytes(payload.ciphertextBase64, 'ciphertext');
        const { key } = await deriveAesKey(password, payload.saltBase64, {
            iterations: payload.iterations,
            keyLengthBits: payload.keyLengthBits,
            hash: payload.hash,
        });
        const decrypted = await getCrypto().subtle.decrypt(
            {
                name: AES_GCM,
                iv: ivBytes,
            },
            key,
            cipherBytes,
        );

        return bytesToUtf8(new Uint8Array(decrypted));
    }

    static async unwrapPrivateKey(wrapperPayload: EncryptedPayload, password: string): Promise<string> {
        return this.decryptWithPassword(wrapperPayload, password);
    }
}

export default CryptoDecryptor;
