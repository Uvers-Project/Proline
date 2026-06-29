import { ref } from 'vue'

const isVisible = ref(false)
const modalOptions = ref({
  title: '',
  message: '',
  type: 'alert', // 'alert' or 'confirm'
  confirmText: 'OK',
  cancelText: 'Cancel',
  onConfirm: null,
  onCancel: null
})

export const useModal = () => {
  const showAlert = (message, title = 'Alert') => {
    isVisible.value = true
    modalOptions.value = {
      title,
      message,
      type: 'alert',
      confirmText: 'OK'
    }
  }

  const showConfirm = (message, title = 'Confirm') => {
    return new Promise((resolve) => {
      isVisible.value = true
      modalOptions.value = {
        title,
        message,
        type: 'confirm',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        onConfirm: () => resolve(true),
        onCancel: () => resolve(false)
      }
    })
  }

  const close = () => {
    isVisible.value = false
  }

  const confirm = () => {
    if (modalOptions.value.onConfirm) {
      modalOptions.value.onConfirm()
    }
    close()
  }

  const cancel = () => {
    if (modalOptions.value.onCancel) {
      modalOptions.value.onCancel()
    }
    close()
  }

  return {
    isVisible,
    modalOptions,
    showAlert,
    showConfirm,
    close,
    confirm,
    cancel
  }
}
