import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { Stethoscope, Lock, Mail, User, Phone, FileBadge, ArrowLeft, AlertCircle } from 'lucide-react';
import { useAuthStore } from '../../../core/auth/useAuthStore';

export const RegisterPage: React.FC = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    specialtyId: '1',
    licenseNumber: '',
    password: '',
    passwordConfirmation: '',
  });

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const { setAuth } = useAuthStore();
  const navigate = useNavigate();

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (formData.password !== formData.passwordConfirmation) {
      setErrorMessage('كلمتا المرور غير متطابقتين.');
      return;
    }

    setIsSubmitting(true);
    setErrorMessage(null);

    // Simulate Registration API & Redirect to Onboarding Wizard
    setTimeout(() => {
      setAuth(
        {
          id: 99,
          name: formData.name,
          email: formData.email,
          phone: formData.phone,
          role: 'doctor',
          createdAt: new Date().toISOString(),
        },
        {
          id: 99,
          userId: 99,
          specialtyId: Number(formData.specialtyId),
          specialtyNameAr: 'أمراض الباطنية والجهاز الهضمي',
          specialtyNameEn: 'Internal Medicine',
          licenseNumber: formData.licenseNumber,
          qualificationAr: 'بكالوريوس طب وجراحة',
          qualificationEn: 'MBBS Doctor of Medicine',
          experienceYears: 5,
          consultationFeeYer: 10000,
          consultationDurationMinutes: 30,
          verificationStatus: 'draft',
        },
        'mock_sanctum_token_registered_99'
      );
      navigate('/onboarding');
    }, 1000);
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50/50 p-4 dark:from-[#0C162A] dark:to-[#0C1A37]">
      <div className="w-full max-w-xl space-y-6 rounded-3xl border border-slate-200/80 bg-white/90 p-8 shadow-2xl backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/90">
        <div className="text-center space-y-2">
          <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#29508B] to-[#1E3D6B] text-white shadow-lg shadow-[#29508B]/30">
            <Stethoscope className="h-7 w-7" />
          </div>
          <h1 className="text-2xl font-black tracking-tight text-slate-900 dark:text-slate-100">الانضمام كطبيب في منصة طبيبي</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">سجّل بياناتك المهنية لإنشاء عيادتك الرقمية وتلقي طلبات المرضى</p>
        </div>

        {errorMessage && (
          <div className="flex items-center gap-2.5 rounded-2xl bg-rose-50 p-3.5 text-xs font-semibold text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
            <AlertCircle className="h-4 w-4 shrink-0" />
            <span>{errorMessage}</span>
          </div>
        )}

        <form onSubmit={handleSubmit} className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div className="space-y-1.5 sm:col-span-2">
            <label className="text-xs font-bold text-slate-700 dark:text-slate-300">الاسم الكامل (مع الألقاب الطبية)</label>
            <div className="relative">
              <input
                type="text"
                name="name"
                required
                value={formData.name}
                onChange={handleChange}
                placeholder="د. خالد محمد عبدالله"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pl-10 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
              <User className="absolute left-3 top-3.5 h-4 w-4 text-slate-400" />
            </div>
          </div>

          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-700 dark:text-slate-300">البريد الإلكتروني المهني</label>
            <div className="relative">
              <input
                type="email"
                name="email"
                required
                value={formData.email}
                onChange={handleChange}
                placeholder="dr.khaled@example.com"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pl-10 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
              <Mail className="absolute left-3 top-3.5 h-4 w-4 text-slate-400" />
            </div>
          </div>

          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-700 dark:text-slate-300">رقم الهاتف (اليمن)</label>
            <div className="relative">
              <input
                type="tel"
                name="phone"
                required
                value={formData.phone}
                onChange={handleChange}
                placeholder="770000000"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pl-10 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
              <Phone className="absolute left-3 top-3.5 h-4 w-4 text-slate-400" />
            </div>
          </div>

          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-700 dark:text-slate-300">التخصص الطبي الرئيسي</label>
            <select
              name="specialtyId"
              value={formData.specialtyId}
              onChange={handleChange}
              className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
            >
              <option value="1">أمراض الباطنية والجهاز الهضمي</option>
              <option value="2">أمراض القلب والأوعية الدموية</option>
              <option value="3">طب الأطباء والأطفال الحديثي الولادة</option>
              <option value="4">النساء والتوليد والعقم</option>
              <option value="5">طب ومزاحمة الجلدية والتجميل</option>
              <option value="6">أمراض العظام والمفاصل</option>
            </select>
          </div>

          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-700 dark:text-slate-300">رقم ترخيص مزاولة المهنة</label>
            <div className="relative">
              <input
                type="text"
                name="licenseNumber"
                required
                value={formData.licenseNumber}
                onChange={handleChange}
                placeholder="YEM-LIC-77492"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pl-10 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
              <FileBadge className="absolute left-3 top-3.5 h-4 w-4 text-slate-400" />
            </div>
          </div>

          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-700 dark:text-slate-300">كلمة المرور</label>
            <div className="relative">
              <input
                type="password"
                name="password"
                required
                value={formData.password}
                onChange={handleChange}
                placeholder="••••••••"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pl-10 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
              <Lock className="absolute left-3 top-3.5 h-4 w-4 text-slate-400" />
            </div>
          </div>

          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-700 dark:text-slate-300">تأكيد كلمة المرور</label>
            <div className="relative">
              <input
                type="password"
                name="passwordConfirmation"
                required
                value={formData.passwordConfirmation}
                onChange={handleChange}
                placeholder="••••••••"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pl-10 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
              <Lock className="absolute left-3 top-3.5 h-4 w-4 text-slate-400" />
            </div>
          </div>

          <div className="sm:col-span-2 pt-2">
            <button
              type="submit"
              disabled={isSubmitting}
              className="flex w-full items-center justify-center gap-2 rounded-xl bg-[#29508B] py-3.5 text-sm font-bold text-white shadow-lg shadow-[#29508B]/25 hover:bg-[#1E3D6B] active:scale-[0.98] transition-all disabled:opacity-50"
            >
              {isSubmitting ? 'جاري تسجيل البيانات وتأسيس الحساب...' : 'متابعة الخطوة التالية (رفع الوثائق)'}
              <ArrowLeft className="h-4 w-4" />
            </button>
          </div>
        </form>

        <div className="text-center text-xs font-semibold text-slate-500 dark:text-slate-400">
          لديك حساب طبيب بالفعل؟{' '}
          <Link to="/login" className="font-bold text-[#29508B] hover:underline dark:text-blue-400">
            تسجيل الدخول هنا
          </Link>
        </div>
      </div>
    </div>
  );
};
