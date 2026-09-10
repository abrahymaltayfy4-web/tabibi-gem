import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { Stethoscope, Lock, Mail, AlertCircle, ArrowLeft } from 'lucide-react';
import { useAuthStore } from '../../../core/auth/useAuthStore';

export const LoginPage: React.FC = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const { setAuth } = useAuthStore();
  const navigate = useNavigate();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrorMessage(null);

    // Simulate Sanctum Auth Request for Dev Scaffolding
    setTimeout(() => {
      if (email === 'doctor@tabibi.ye' && password === 'password') {
        setAuth(
          {
            id: 1,
            name: 'د. أحمد علي البعداني',
            email: 'doctor@tabibi.ye',
            role: 'doctor',
            createdAt: new Date().toISOString(),
          },
          {
            id: 1,
            userId: 1,
            specialtyId: 1,
            specialtyNameAr: 'استشاري أمراض الباطنية والقلب',
            specialtyNameEn: 'Cardiology & Internal Medicine',
            licenseNumber: 'YEM-MED-8842',
            qualificationAr: 'دكتوراه في الطب الباطني',
            qualificationEn: 'PhD in Internal Medicine',
            experienceYears: 12,
            consultationFeeYer: 15000,
            consultationDurationMinutes: 30,
            verificationStatus: 'approved',
          },
          'mock_sanctum_bearer_token_12345'
        );
        navigate('/dashboard');
      } else {
        setIsSubmitting(false);
        setErrorMessage('البريد الإلكتروني أو كلمة المرور غير صحيحة. جرب doctor@tabibi.ye / password');
      }
    }, 800);
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50/50 p-4 dark:from-[#0C162A] dark:to-[#0C1A37]">
      <div className="w-full max-w-md space-y-6 rounded-3xl border border-slate-200/80 bg-white/90 p-8 shadow-2xl backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/90">
        <div className="text-center space-y-2">
          <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#29508B] to-[#1E3D6B] text-white shadow-lg shadow-[#29508B]/30">
            <Stethoscope className="h-7 w-7" />
          </div>
          <h1 className="text-2xl font-black tracking-tight text-slate-900 dark:text-slate-100">دخول عيادة الطبيب</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">سجّل دخولك للوصول إلى استشارات ورعاية المرضى</p>
        </div>

        {errorMessage && (
          <div className="flex items-center gap-2.5 rounded-2xl bg-rose-50 p-3.5 text-xs font-semibold text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
            <AlertCircle className="h-4 w-4 shrink-0" />
            <span>{errorMessage}</span>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-700 dark:text-slate-300">البريد الإلكتروني المهني</label>
            <div className="relative">
              <input
                type="email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="doctor@tabibi.ye"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pl-10 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
              <Mail className="absolute left-3 top-3.5 h-4 w-4 text-slate-400" />
            </div>
          </div>

          <div className="space-y-1.5">
            <div className="flex items-center justify-between">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">كلمة المرور</label>
              <a href="#" className="text-xs font-bold text-[#29508B] hover:underline dark:text-blue-400">نسيت كلمة المرور؟</a>
            </div>
            <div className="relative">
              <input
                type="password"
                required
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pl-10 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
              <Lock className="absolute left-3 top-3.5 h-4 w-4 text-slate-400" />
            </div>
          </div>

          <button
            type="submit"
            disabled={isSubmitting}
            className="flex w-full items-center justify-center gap-2 rounded-xl bg-[#29508B] py-3.5 text-sm font-bold text-white shadow-lg shadow-[#29508B]/25 hover:bg-[#1E3D6B] active:scale-[0.98] transition-all disabled:opacity-50"
          >
            {isSubmitting ? 'جاري التحقق والمصادقة...' : 'دخول البوابة الطبية'}
            <ArrowLeft className="h-4 w-4" />
          </button>
        </form>

        <div className="text-center text-xs font-semibold text-slate-500 dark:text-slate-400">
          طبيب جديد؟{' '}
          <Link to="/register" className="font-bold text-[#29508B] hover:underline dark:text-blue-400">
            تقديم طلب انضمام كطبيب
          </Link>
        </div>
      </div>
    </div>
  );
};
