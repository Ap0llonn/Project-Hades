class VaultSession {
    private dek: Uint8Array | null = null;

    unlock(dek: Uint8Array) {
        this.dek = dek;
    }

    getDek(): Uint8Array {
        if (!this.dek) throw new Error('Vault locked');
        return this.dek;
    }

    lock() {
        if (this.dek) this.dek.fill(0);
        this.dek = null;
    }
}
