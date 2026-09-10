import axios, { AxiosError } from 'axios';
import type { InternalAxiosRequestConfig } from 'axios';

export const adminApiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1/admin',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 15000,
});

adminApiClient.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = localStorage.getItem('tabibi_admin_token');
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

adminApiClient.interceptors.response.use(
  (response) => response,
  (error: AxiosError<{ message?: string; errors?: Record<string, string[]> }>) => {
    if (error.response) {
      const status = error.response.status;
      const message = error.response.data?.message || 'حدث خطأ في تنفيذ الطلب الإداري';

      if (status === 401) {
        localStorage.removeItem('tabibi_admin_token');
        if (!window.location.pathname.includes('/login')) {
          window.location.href = '/login';
        }
      }

      return Promise.reject({
        statusCode: status,
        message,
        errors: error.response.data?.errors,
      });
    }

    return Promise.reject({
      statusCode: 0,
      message: 'تعذر الاتصال بالشبكة الخادمة. يرجى التحقق من الاتصال بالإنترنت.',
    });
  }
);
