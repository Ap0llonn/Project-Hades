import type { BackgroundRequest, BackgroundResponse } from "~lib/types"

const sendRuntimeMessage = <T>(message: BackgroundRequest) =>
  new Promise<T>((resolve, reject) => {
    chrome.runtime.sendMessage(message, (response: T) => {
      if (chrome.runtime.lastError) {
        reject(new Error(chrome.runtime.lastError.message))
        return
      }

      resolve(response)
    })
  })

export const sendBackgroundMessage = async <T>(message: BackgroundRequest) => {
  const response = await sendRuntimeMessage<BackgroundResponse<T>>(message)

  if (!response.ok) {
    throw new Error(response.error)
  }

  return response.data
}
