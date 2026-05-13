import type { BinarySource } from '../../../shared/utils';
import { CryptoDecryptor, CryptoEncryptor } from '../../../shared/utils';
import type { EncryptedPayload as DekEncryptedPayload } from '../../../shared/utils/crypto/CryptoEncryptor';
import { vaultDekWorkerClient } from './VaultDekWorkerClient';

export class VaultSession {
    private dek: Uint8Array | null = null;
    private scope = 'anonymous';
    private didHydrateForScope = false;

    setScope(scope: string): void {
        const trimmed = scope.trim();
        const nextScope = trimmed === '' ? 'anonymous' : trimmed;
        if (nextScope === this.scope) {
            return;
        }

        if (this.dek) {
            this.dek.fill(0);
        }

        this.dek = null;
        this.scope = nextScope;
        this.didHydrateForScope = false;
    }

    async hydrateFromWorker(): Promise<void> {
        if (this.dek || this.didHydrateForScope || !vaultDekWorkerClient.isAvailable()) {
            return;
        }

        this.didHydrateForScope = true;
        const workerDek = await vaultDekWorkerClient.getDek(this.scope);
        if (!workerDek) {
            return;
        }

        this.dek = new Uint8Array(workerDek);
    }

    unlock(dek: Uint8Array): void {
        this.dek = new Uint8Array(dek);
        this.didHydrateForScope = true;

        void vaultDekWorkerClient.setDek(this.scope, this.dek);
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
        this.didHydrateForScope = true;

        void vaultDekWorkerClient.clearDek(this.scope);
    }
}

export const vaultSession = new VaultSession();
export default vaultSession;
