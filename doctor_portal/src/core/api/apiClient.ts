import axios, { AxiosError } from 'axios';
import type { InternalAxiosRequestConfig } from 'axios';
import { env } from '../../app/config/env';
import type { ApiResponseEnvelope } from '../types/api';

export const apiClient = axios.create({
  baseURL: env.apiBaseUrl,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 15000,
});

// Request Interceptor: Attach Sanctum Bearer Token from Session Storage
apiClient.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = localStorage.getItem('tabibi_doctor_token');
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response Interceptor: Handle API Envelope and Unified Errors
apiClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error: AxiosError<ApiResponseEnvelope<null>>) => {
    if (error.response) {
      const status = error.response.status;
      const serverMessage = error.response.data?.message || 'حدث خطأ في الخادم';

      if (status === 401) {
        localStorage.removeItem('tabibi_doctor_token');
        if (!window.location.pathname.includes('/login')) {
          window.location.href = '/login';
        }
      }

      return Promise.reject({
        statusCode: status,
        message: serverMessage,
        errors: error.response.data?.errors,
      });
    }

    return Promise.reject({
      statusCode: 0,
      message: 'تعذر الاتصال بالشبكة. يرجى التحقق من اتصال الإنترنت.',
    });
  }
);
