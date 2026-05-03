import { http } from '@/shared/api/http'
import type { LoginRequest, LoginResponse, User } from '@/modules/auth/types/auth.types'

export class AuthService {
  async login(payload: LoginRequest): Promise<LoginResponse> {
    const { data } = await http.post<LoginResponse>('/auth/login', payload)

    return data
  }

  async logout(): Promise<void> {
    await http.post('/auth/logout')
  }

  async me(): Promise<User> {
    const { data } = await http.get<User>('/user')

    return data
  }
}

export const authService = new AuthService()
