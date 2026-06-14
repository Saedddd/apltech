import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'))
  const username = ref<string | null>(localStorage.getItem('username'))
  const isAuthenticated = ref(!!token.value)

  const updateMenu = () => {
    window.dispatchEvent(new Event('auth-change'))
  }

  const setAuth = (userToken: string, userName: string) => {
    token.value = userToken
    username.value = userName
    isAuthenticated.value = true
    localStorage.setItem('token', userToken)
    localStorage.setItem('username', userName)
    updateMenu()
  }

  const clearAuth = () => {
    token.value = null
    username.value = null
    isAuthenticated.value = false
    localStorage.removeItem('token')
    localStorage.removeItem('username')
    updateMenu()
  }

  const checkAuth = () => {
    const storedToken = localStorage.getItem('token')
    const storedUsername = localStorage.getItem('username')

    if (storedToken && storedUsername) {
      token.value = storedToken
      username.value = storedUsername
      isAuthenticated.value = true
      return true
    } else {
      clearAuth()
      return false
    }
  }

  return {
    token,
    username,
    isAuthenticated,
    setAuth,
    clearAuth,
    checkAuth,
    updateMenu,
  }
})
