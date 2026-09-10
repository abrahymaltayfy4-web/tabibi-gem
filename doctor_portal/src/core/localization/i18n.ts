import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import LanguageDetector from 'i18next-browser-languagedetector';

const resources = {
  ar: {
    translation: {
      app_name: 'طبيبي — بوابة الطبيب',
      welcome: 'مرحباً بك دكتور',
      login: 'تسجيل الدخول',
      register: 'إنشاء حساب جديد',
      email: 'البريد الإلكتروني',
      password: 'كلمة المرور',
      dashboard: 'لوحة التحكم المركزية',
      appointments: 'المواعيد والإدارات',
      consultations: 'غرفة الاستشارة',
      profile: 'الملف المهني',
      availability: 'جدول الأوقات',
      earnings: 'الأرباح والرصيد',
      settings: 'الإعدادات',
      verification_pending: 'طلب التوثيق قيد المراجعة لدى الإدارة',
      verification_approved: 'حساب موثق ومفعل',
      logout: 'تسجيل الخروج',
    },
  },
  en: {
    translation: {
      app_name: 'TABIBI — Doctor Portal',
      welcome: 'Welcome Doctor',
      login: 'Login',
      register: 'Create Account',
      email: 'Email Address',
      password: 'Password',
      dashboard: 'Dashboard',
      appointments: 'Appointments',
      consultations: 'Consultation Room',
      profile: 'Medical Profile',
      availability: 'Availability',
      earnings: 'Earnings & Balance',
      settings: 'Settings',
      verification_pending: 'Verification Request Under Admin Review',
      verification_approved: 'Verified & Active Doctor Account',
      logout: 'Logout',
    },
  },
};

i18n
  .use(LanguageDetector)
  .use(initReactI18next)
  .init({
    resources,
    fallbackLng: 'ar',
    supportedLngs: ['ar', 'en'],
    interpolation: {
      escapeValue: false,
    },
  });

export default i18n;
