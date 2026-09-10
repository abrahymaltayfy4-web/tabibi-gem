export const env = {
  apiBaseUrl: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1',
  appName: import.meta.env.VITE_APP_NAME || 'طبيبي — بوابة الطبيب',
  reverbKey: import.meta.env.VITE_REVERB_APP_KEY || 'tabibi_reverb_key',
  reverbHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
  reverbPort: import.meta.env.VITE_REVERB_PORT || 8080,
  reverbScheme: import.meta.env.VITE_REVERB_SCHEME || 'http',
  defaultLocale: 'ar',
} as const;
