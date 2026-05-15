import type { EncryptedPayload } from "~lib/types"
import { base64ToBytes, bytesToBase64, bytesToBase64Url, stringToUtf8, utf8ToString } from "~lib/encoding"

const AES_GCM_IV_BYTES = 12

const randomBytes = (length: number) => {
  const bytes = new Uint8Array(length)
  crypto.getRandomValues(bytes)
  return bytes
}

const importAesKey = (rawKey: string) =>
  crypto.subtle.importKey("raw", base64ToBytes(rawKey), "AES-GCM", false, [
    "encrypt",
    "decrypt"
  ])

export const createRandomToken = (byteLength = 32) =>
  bytesToBase64Url(randomBytes(byteLength))

export const createPkceVerifier = () => createRandomToken(48)

export const createPkceChallenge = async (verifier: string) => {
  const digest = await crypto.subtle.digest("SHA-256", stringToUtf8(verifier))
  return bytesToBase64Url(new Uint8Array(digest))
}

export const createSessionKeyMaterial = () => bytesToBase64(randomBytes(32))

export const encryptString = async (
  rawKey: string,
  value: string
): Promise<EncryptedPayload> => {
  const iv = randomBytes(AES_GCM_IV_BYTES)
  const key = await importAesKey(rawKey)
  const encrypted = await crypto.subtle.encrypt(
    {
      name: "AES-GCM",
      iv
    },
    key,
    stringToUtf8(value)
  )

  return {
    ciphertext: bytesToBase64(new Uint8Array(encrypted)),
    iv: bytesToBase64(iv),
    createdAt: Date.now()
  }
}

export const decryptString = async (rawKey: string, payload: EncryptedPayload) => {
  const key = await importAesKey(rawKey)
  const decrypted = await crypto.subtle.decrypt(
    {
      name: "AES-GCM",
      iv: base64ToBytes(payload.iv)
    },
    key,
    base64ToBytes(payload.ciphertext)
  )

  return utf8ToString(new Uint8Array(decrypted))
}
