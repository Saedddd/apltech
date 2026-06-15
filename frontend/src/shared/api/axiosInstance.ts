import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'https://apltech-backend.onrender.com/'

export const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 30000,
})

axiosInstance.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      console.log('Authorization header set:', config.headers.Authorization)
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error('Response error:', error.response?.status, error.response?.data)

    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('username')
      window.location.href = '/'
    }
    return Promise.reject(error)
  }
)
