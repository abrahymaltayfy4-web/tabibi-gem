import { apiClient } from '../../../core/api/apiClient';
import type { ApiResponseEnvelope } from '../../../core/types/api';
import type { LoginCredentials, RegisterDoctorRequest, AuthResponseData } from '../types/auth.types';
import type { DoctorProfileEntity, UserEntity } from '../../../core/types/doctor';

export const authApi = {
  login: async (credentials: LoginCredentials): Promise<AuthResponseData> => {
    const response = await apiClient.post<ApiResponseEnvelope<AuthResponseData>>('/auth/login', credentials);
    return response.data.data;
  },

  registerDoctor: async (data: RegisterDoctorRequest): Promise<AuthResponseData> => {
    const response = await apiClient.post<ApiResponseEnvelope<AuthResponseData>>('/auth/register-doctor', data);
    return response.data.data;
  },

  getCurrentUser: async (): Promise<{ user: UserEntity; doctorProfile: DoctorProfileEntity | null }> => {
    const response = await apiClient.get<ApiResponseEnvelope<{ user: UserEntity; doctorProfile: DoctorProfileEntity | null }>>('/auth/me');
    return response.data.data;
  },

  logout: async (): Promise<void> => {
    await apiClient.post('/auth/logout');
  },
};
