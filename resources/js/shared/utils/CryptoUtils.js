const DEFAULT_KDF_ITERATIONS = 210000;
const DEFAULT_KEY_LENGTH_BITS = 256;
const DEFAULT_SALT_LENGTH = 16;
const DEFAULT_IV_LENGTH = 12;
const DEFAULT_HASH = 'SHA-256';
const PBKDF2 = 'PBKDF2';
const AES_GCM = 'AES-GCM';

export class CryptoUtils {
    static encoder = new TextEncoder();
    static decoder = new TextDecoder();

    static getCrypto() {
        const cryptoApi = globalThis?.crypto;

        if (!cryptoApi?.subtle || typeof cryptoApi.getRandomValues !== 'function') {
            throw new Error('Web Crypto API is not available in this environment.');
        }

        return cryptoApi;
    }

    static randomBytes(length = DEFAULT_SALT_LENGTH) {
        const bytes = new Uint8Array(length);
        this.getCrypto().getRandomValues(bytes);

        return bytes;
    }

    static utf8ToBytes(value) {
        return this.encoder.encode(value);
    }

    static bytesToUtf8(bytes) {
        return this.decoder.decode(bytes);
    }

    static toBase64(bytes) {
        const typedBytes = bytes instanceof Uint8Array ? bytes : new Uint8Array(bytes);

        if (typeof btoa === 'function') {
            let binary = '';

            typedBytes.forEach((byte) => {
                binary += String.fromCharCode(byte);
            });

            return btoa(binary);
        }

        if (typeof Buffer !== 'undefined') {
            return Buffer.from(typedBytes).toString('base64');
        }

        throw new Error('No base64 encoder available in this environment.');
    }

    static fromBase64(base64Value) {
        if (typeof atob === 'function') {
            const binary = atob(base64Value);
            const bytes = new Uint8Array(binary.length);

            for (let index = 0; index < binary.length; index += 1) {
                bytes[index] = binary.charCodeAt(index);
            }

            return bytes;
        }

        if (typeof Buffer !== 'undefined') {
            return new Uint8Array(Buffer.from(base64Value, 'base64'));
        }

        throw new Error('No base64 decoder available in this environment.');
    }

    static normalizeBytes(value, fieldName) {
        if (value instanceof Uint8Array) {
            return value;
        }

        if (typeof value === 'string') {
            return this.fromBase64(value);
        }

        throw new Error(`${fieldName} must be a base64 string or Uint8Array.`);
    }

    static async createPasswordKey(password) {
        if (!password) {
            throw new Error('Password is required for key derivation.');
        }

        return this.getCrypto().subtle.importKey(
            'raw',
            this.utf8ToBytes(password),
            PBKDF2,
            false,
            ['deriveBits', 'deriveKey'],
        );
    }

    static getKdfParams(params = {}) {
        return {
            iterations: params.iterations ?? DEFAULT_KDF_ITERATIONS,
            keyLengthBits: params.keyLengthBits ?? DEFAULT_KEY_LENGTH_BITS,
            hash: params.hash ?? DEFAULT_HASH,
        };
    }

    static async deriveKdfValue(password, salt, params = {}) {
        const saltBytes = salt
            ? this.normalizeBytes(salt, 'salt')
            : this.randomBytes(DEFAULT_SALT_LENGTH);
        const { iterations, keyLengthBits, hash } = this.getKdfParams(params);
        const passwordKey = await this.createPasswordKey(password);
        const bits = await this.getCrypto().subtle.deriveBits(
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
            kdfValueBase64: this.toBase64(new Uint8Array(bits)),
            saltBase64: this.toBase64(saltBytes),
            iterations,
            hash,
            keyLengthBits,
        };
    }

    static async deriveAesKey(password, salt, params = {}) {
        const saltBytes = this.normalizeBytes(salt, 'salt');
        const { iterations, keyLengthBits, hash } = this.getKdfParams(params);
        const passwordKey = await this.createPasswordKey(password);
        const key = await this.getCrypto().subtle.deriveKey(
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
            saltBase64: this.toBase64(saltBytes),
            iterations,
            hash,
            keyLengthBits,
        };
    }

    static async encryptWithPassword(plainText, password, params = {}) {
        if (typeof plainText !== 'string') {
            throw new Error('plainText must be a string.');
        }

        const saltBytes = params.salt
            ? this.normalizeBytes(params.salt, 'salt')
            : this.randomBytes(params.saltLength ?? DEFAULT_SALT_LENGTH);
        const ivBytes = params.iv
            ? this.normalizeBytes(params.iv, 'iv')
            : this.randomBytes(params.ivLength ?? DEFAULT_IV_LENGTH);
        const { key, saltBase64, iterations, hash, keyLengthBits } = await this.deriveAesKey(
            password,
            saltBytes,
            params,
        );
        const encrypted = await this.getCrypto().subtle.encrypt(
            {
                name: AES_GCM,
                iv: ivBytes,
            },
            key,
            this.utf8ToBytes(plainText),
        );

        return {
            ciphertextBase64: this.toBase64(new Uint8Array(encrypted)),
            ivBase64: this.toBase64(ivBytes),
            saltBase64,
            iterations,
            hash,
            keyLengthBits,
        };
    }

    static async decryptWithPassword(payload, password) {
        if (!payload?.ciphertextBase64 || !payload?.ivBase64 || !payload?.saltBase64) {
            throw new Error('Payload must include ciphertextBase64, ivBase64, and saltBase64.');
        }

        const ivBytes = this.normalizeBytes(payload.ivBase64, 'iv');
        const cipherBytes = this.normalizeBytes(payload.ciphertextBase64, 'ciphertext');
        const { key } = await this.deriveAesKey(password, payload.saltBase64, {
            iterations: payload.iterations,
            keyLengthBits: payload.keyLengthBits,
            hash: payload.hash,
        });
        const decrypted = await this.getCrypto().subtle.decrypt(
            {
                name: AES_GCM,
                iv: ivBytes,
            },
            key,
            cipherBytes,
        );

        return this.bytesToUtf8(new Uint8Array(decrypted));
    }

    static async wrapPrivateKey(masterPrivateKey, password, params = {}) {
        return this.encryptWithPassword(masterPrivateKey, password, params);
    }

    static async unwrapPrivateKey(wrapperPayload, password) {
        return this.decryptWithPassword(wrapperPayload, password);
    }
}

export default CryptoUtils;
