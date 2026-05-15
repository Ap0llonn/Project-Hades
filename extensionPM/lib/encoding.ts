export const bytesToBase64 = (bytes: Uint8Array) =>
  btoa(String.fromCharCode(...bytes))

export const base64ToBytes = (value: string) =>
  Uint8Array.from(atob(value), (char) => char.charCodeAt(0))

export const bytesToBase64Url = (bytes: Uint8Array) =>
  bytesToBase64(bytes).replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/g, "")

export const stringToUtf8 = (value: string) => new TextEncoder().encode(value)

export const utf8ToString = (value: Uint8Array) => new TextDecoder().decode(value)
