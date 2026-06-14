import { axiosInstance } from '@shared/api/axiosInstance'

export const authApi = {
  login: async (data: { username: string; password: string }) => {
    const response = await axiosInstance.post('/auth/login', data)
    return response.data
  },
}
