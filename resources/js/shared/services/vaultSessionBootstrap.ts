import { base64URLStringToBuffer } from '@simplewebauthn/browser';
import { dekService } from './dekService';
import { CryptoDecryptor } from '../utils';
import { vaultSession } from '../../features/shared/ts/VaultSession';

export const LOGIN_MASTER_PASSWORD_STORAGE_KEY = 'vaultguardian.login.master_password';

interface InertiaPageLike {
    component?: string;
}

let bootstrapInFlight: Promise<void> | null = null;

const asPositiveIntegerOrUndefined = (value: unknown): number | undefined => {
    const parsedValue = Number(value);

    if (!Number.isFinite(parsedValue) || parsedValue <= 0) {
        return undefined;
    }

    return parsedValue;
};

const asPrfResultBytes = (prfResult: unknown): Uint8Array | null => {
    if (prfResult instanceof ArrayBuffer) {
        return new Uint8Array(prfResult);
    }

    if (ArrayBuffer.isView(prfResult)) {
        return new Uint8Array(prfResult.buffer, prfResult.byteOffset, prfResult.byteLength);
    }

    if (Array.isArray(prfResult)) {
        return new Uint8Array(prfResult);
    }

    return null;
};

const extractPrfResultFromExtensions = (clientExtensionResults: unknown): Uint8Array | null => {
    const extensionResults = clientExtensionResults as {
        prf?: {
            results?: { first?: unknown };
            first?: unknown;
        };
    } | null;

    const prf = extensionResults?.prf;
    if (!prf || typeof prf !== 'object') {
        return null;
    }

    const nestedResult = prf.results?.first;
    if (nestedResult !== undefined) {
        return asPrfResultBytes(nestedResult);
    }

    if (prf.first !== undefined) {
        return asPrfResultBytes(prf.first);
    }

    return null;
};

const derivePasskeyPrfKeyBytes = async (
    credentialId: string,
    prfSaltBytes: Uint8Array,
): Promise<Uint8Array> => {
    const publicKey: PublicKeyCredentialRequestOptions = {
        challenge: window.crypto.getRandomValues(new Uint8Array(32)),
        rpId: window.location.hostname,
        userVerification: 'required',
        allowCredentials: [
            {
                id: base64URLStringToBuffer(credentialId),
                type: 'public-key',
            },
        ],
        extensions: {
            prf: {
                eval: {
                    first: prfSaltBytes,
                },
            },
        } as AuthenticationExtensionsClientInputs,
    };

    const assertion = await navigator.credentials.get({ publicKey });
    if (!(assertion instanceof PublicKeyCredential)) {
        throw new Error('Unable to evaluate PRF for this passkey.');
    }

    const prfBytes = extractPrfResultFromExtensions(assertion.getClientExtensionResults?.());
    if (!prfBytes || prfBytes.length === 0) {
        throw new Error('This passkey does not expose PRF output on this browser/device.');
    }

    return prfBytes;
};

const isAuthenticatedAppPage = (page?: InertiaPageLike): boolean => {
    const component = String(page?.component ?? '');
    return component.startsWith('dashboard/');
};

export const ensureAuthenticatedVaultSession = async (page?: InertiaPageLike): Promise<void> => {
    if (!isAuthenticatedAppPage(page) || vaultSession.isUnlocked()) {
        return;
    }

    if (bootstrapInFlight) {
        return bootstrapInFlight;
    }

    bootstrapInFlight = (async () => {
        try {
            const bootstrap = await dekService.fetchBootstrap();
            const wrapper = bootstrap?.dek_wrapper;
            if (!wrapper) {
                return;
            }

            if (wrapper.type === 'password') {
                const masterPassword = sessionStorage.getItem(LOGIN_MASTER_PASSWORD_STORAGE_KEY) ?? '';
                if (masterPassword === '') {
                    return;
                }

                const prfParams = wrapper.prf_params && typeof wrapper.prf_params === 'object'
                    ? wrapper.prf_params
                    : {};
                const keyParams = prfParams as Record<string, unknown>;
                const argonTypeCandidate = keyParams.type;
                const argonType = argonTypeCandidate === 'Argon2i13' || argonTypeCandidate === 'Argon2id13'
                    ? argonTypeCandidate
                    : undefined;
                const prfSalt = typeof wrapper.prf_salt === 'string' ? wrapper.prf_salt : '';
                if (prfSalt === '') {
                    return;
                }

                const dekBytes = await CryptoDecryptor.decryptBytesWithPassword({
                    ciphertextBase64: wrapper.ciphertext,
                    ivBase64: wrapper.nonce,
                    saltBase64: prfSalt,
                    keyLengthBits: asPositiveIntegerOrUndefined(keyParams.keyLengthBits),
                    argonOpsLimit: asPositiveIntegerOrUndefined(keyParams.opsLimit),
                    argonMemoryKb: asPositiveIntegerOrUndefined(keyParams.memoryKb),
                    argonType,
                }, masterPassword);

                vaultSession.unlock(dekBytes);
                sessionStorage.removeItem(LOGIN_MASTER_PASSWORD_STORAGE_KEY);
                return;
            }

            if (wrapper.type === 'passkey') {
                const credentialId = typeof wrapper.credential_id === 'string'
                    ? wrapper.credential_id.trim()
                    : '';
                const prfSalt = typeof wrapper.prf_salt === 'string'
                    ? wrapper.prf_salt.trim()
                    : '';

                if (credentialId === '' || prfSalt === '') {
                    return;
                }

                const prfKeyBytes = await derivePasskeyPrfKeyBytes(
                    credentialId,
                    new Uint8Array(base64URLStringToBuffer(prfSalt)),
                );

                const dekBytes = await CryptoDecryptor.decryptBytesWithKey({
                    ciphertextBase64: wrapper.ciphertext,
                    ivBase64: wrapper.nonce,
                }, prfKeyBytes);

                vaultSession.unlock(dekBytes);
            }
        } catch {

            sessionStorage.removeItem(LOGIN_MASTER_PASSWORD_STORAGE_KEY);
        }
    })();

    try {
        await bootstrapInFlight;
    } finally {
        bootstrapInFlight = null;
    }
};
