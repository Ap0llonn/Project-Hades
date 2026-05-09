import type { BinarySource } from '../../../shared/utils';
import { CryptoDecryptor, CryptoEncryptor } from '../../../shared/utils';
import type { EncryptedPayload as DekEncryptedPayload } from '../../../shared/utils/crypto/CryptoEncryptor';

export class VaultSession {
    private dek: Uint8Array | null = null;

    unlock(dek: Uint8Array): void {
        this.dek = new Uint8Array(dek);
    }

    getDek(): Uint8Array {
        if (!this.dek) {
            throw new Error('Vault locked');
        }

        return this.dek;
    }

    isUnlocked(): boolean {
        return this.dek !== null;
    }

    async encryptPassword(
        password: string,
        iv?: BinarySource,
    ): Promise<DekEncryptedPayload> {
        return CryptoEncryptor.encryptPasswordWithDek(password, this.getDek(), iv);
    }

    async decryptPassword(payload: DekEncryptedPayload): Promise<string> {
        return CryptoDecryptor.decryptPasswordWithDek(payload, this.getDek());
    }

    lock(): void {
        if (this.dek) {
            this.dek.fill(0);
        }

        this.dek = null;
    }
}

export const vaultSession = new VaultSession();
export default vaultSession;
