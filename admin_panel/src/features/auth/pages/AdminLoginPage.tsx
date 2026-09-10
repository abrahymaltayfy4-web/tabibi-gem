import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Shield, Lock, Mail, AlertCircle, ArrowLeft } from 'lucide-react';
import { useAdminAuthStore } from '../../../core/auth/useAdminAuthStore';

export const AdminLoginPage: React.FC = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const { setAdminAuth } = useAdminAuthStore();
  const navigate = useNavigate();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrorMessage(null);

    setTimeout(() => {
      if (email === 'admin@tabibi.ye' && password === 'password') {
        setAdminAuth(
          {
            id: 1,
            name: 'المهندس الإداري الأعلى',
            email: 'admin@tabibi.ye',
            role: 'super_admin',
            permissions: [
              'doctor.view',
              'doctor.verify',
              'doctor.suspend',
              'patient.view',
              'patient.suspend',
              'appointment.view',
              'appointment.cancel',
              'payment.view',
              'payment.refund',
              'payout.manage',
              'medical_record.view',
              'audit.view',
              'settings.manage',
            ],
            createdAt: new Date().toISOString(),
          },
          'mock_sanctum_admin_token_999'
        );
        navigate('/dashboard');
      } else {
        setIsSubmitting(false);
        setErrorMessage('بيانات دخول الأدمن غير صحيحة. جرب admin@tabibi.ye / password');
      }
    }, 800);
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-slate-950 p-4">
      <div className="w-full max-w-md space-y-6 rounded-3xl border border-slate-800 bg-slate-900/90 p-8 shadow-2xl backdrop-blur-xl">
        <div className="text-center space-y-2">
          <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#29508B] to-blue-700 text-white shadow-lg shadow-blue-900/40">
            <Shield className="h-7 w-7" />
          </div>
          <h1 className="text-2xl font-black tracking-tight text-white">دخول لوحة التحكم العليا</h1>
          <p className="text-xs font-semibold text-slate-400">بوابة الإدارة المركزية وحوكمة منصة طبيبي</p>
        </div>

        {errorMessage && (
          <div className="flex items-center gap-2.5 rounded-2xl bg-rose-950/60 p-3.5 text-xs font-semibold text-rose-300 border border-rose-800">
            <AlertCircle className="h-4 w-4 shrink-0" />
            <span>{errorMessage}</span>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-300">البريد الإلكتروني الإداري</label>
            <div className="relative">
              <input
                type="email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="admin@tabibi.ye"
                className="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 pl-10 text-sm font-medium text-white focus:border-blue-500 focus:outline-none"
              />
              <Mail className="absolute left-3 top-3.5 h-4 w-4 text-slate-500" />
            </div>
          </div>

          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-300">كلمة المرور</label>
            <div className="relative">
              <input
                type="password"
                required
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••"
                className="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 pl-10 text-sm font-medium text-white focus:border-blue-500 focus:outline-none"
              />
              <Lock className="absolute left-3 top-3.5 h-4 w-4 text-slate-500" />
            </div>
          </div>

          <button
            type="submit"
            disabled={isSubmitting}
            className="flex w-full items-center justify-center gap-2 rounded-xl bg-[#29508B] py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-900/30 hover:bg-blue-700 transition-all disabled:opacity-50"
          >
            {isSubmitting ? 'جاري المصادقة والتحقق من الصلاحيات...' : 'مصادقة ودخول الإدارة العليا'}
            <ArrowLeft className="h-4 w-4" />
          </button>
        </form>
      </div>
    </div>
  );
};
