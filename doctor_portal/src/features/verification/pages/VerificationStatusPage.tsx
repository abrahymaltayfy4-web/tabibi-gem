import React from 'react';
import { useNavigate } from 'react-router-dom';
import { ShieldAlert, ShieldCheck, Clock, ArrowLeft, RefreshCw } from 'lucide-react';
import { useAuthStore } from '../../../core/auth/useAuthStore';

export const VerificationStatusPage: React.FC = () => {
  const { doctorProfile, setDoctorProfile, logout } = useAuthStore();
  const navigate = useNavigate();

  const status = doctorProfile?.verificationStatus || 'pending';

  const handleSimulateApprove = () => {
    if (doctorProfile) {
      setDoctorProfile({
        ...doctorProfile,
        verificationStatus: 'approved',
      });
    }
    navigate('/dashboard');
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50/50 p-4 dark:from-[#0C162A] dark:to-[#0C1A37]">
      <div className="w-full max-w-xl space-y-6 rounded-3xl border border-slate-200/80 bg-white/95 p-8 shadow-2xl backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/95 text-center">
        {status === 'pending' || status === 'under_review' ? (
          <div className="space-y-4">
            <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800 shadow-xl shadow-amber-500/10">
              <Clock className="h-10 w-10 animate-pulse" />
            </div>

            <div className="space-y-2">
              <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">طلب التوثيق قيد مراجعة الإدارة</h1>
              <p className="text-xs font-semibold text-slate-500 dark:text-slate-400 leading-relaxed max-w-md mx-auto">
                تم استلام ترخيص مزاولة المهنة والمؤهلات بنجاح. تعكف الإدارة العامة لمنصة طبيبي حالياً على التثبت من التراخيص والوثائق المرفقة.
              </p>
            </div>

            <div className="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60 space-y-2 text-right text-xs">
              <div className="flex justify-between text-slate-600 dark:text-slate-400">
                <span className="font-bold">رقم الترخيص المسجل:</span>
                <span className="font-mono font-bold text-slate-900 dark:text-slate-100">{doctorProfile?.licenseNumber || 'YEM-LIC-77492'}</span>
              </div>
              <div className="flex justify-between text-slate-600 dark:text-slate-400">
                <span className="font-bold">حالة الطلب الحالية:</span>
                <span className="font-bold text-amber-600 dark:text-amber-400">قيد الفحص الدقيق (Under Review)</span>
              </div>
              <div className="flex justify-between text-slate-600 dark:text-slate-400">
                <span className="font-bold">الوقت المتوقع للرد:</span>
                <span className="font-bold text-slate-900 dark:text-slate-100">خلال 24 إلى 48 ساعة</span>
              </div>
            </div>

            <div className="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
              <button
                onClick={handleSimulateApprove}
                className="flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-xs font-bold text-white shadow-md hover:bg-emerald-700"
              >
                <RefreshCw className="h-4 w-4" />
                محاكاة موافقة الأدمن (وضع التطوير)
              </button>

              <button
                onClick={() => {
                  logout();
                  navigate('/login');
                }}
                className="flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-3 text-xs font-bold text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:text-slate-400"
              >
                تسجيل الخروج
              </button>
            </div>
          </div>
        ) : status === 'rejected' ? (
          <div className="space-y-4">
            <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800 shadow-xl shadow-rose-500/10">
              <ShieldAlert className="h-10 w-10" />
            </div>

            <div className="space-y-2">
              <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">تعذر توثيق الحساب حالياً</h1>
              <p className="text-xs font-semibold text-rose-600 dark:text-rose-400 leading-relaxed max-w-md mx-auto">
                سبب الرفض الصادر من الإدارة: {doctorProfile?.rejectionReason || 'وثيقة ترخيص مزاولة المهنة المرفقة غير واضحة أو منتهية الصلاحية.'}
              </p>
            </div>

            <button
              onClick={() => navigate('/onboarding')}
              className="flex items-center justify-center gap-2 rounded-xl bg-[#29508B] px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-[#1E3D6B]"
            >
              إعادة رفع الوثائق المصححة
              <ArrowLeft className="h-4 w-4" />
            </button>
          </div>
        ) : (
          <div className="space-y-4">
            <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 shadow-xl shadow-emerald-500/10">
              <ShieldCheck className="h-10 w-10" />
            </div>

            <div className="space-y-2">
              <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">حسابك موثق ومفعل بنجاح 🎉</h1>
              <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">
                يمكنك الآن استقبال حجز الاستشارات وتعديل الجدول والأوقات المتاحة.
              </p>
            </div>

            <button
              onClick={() => navigate('/dashboard')}
              className="flex items-center justify-center gap-2 rounded-xl bg-[#29508B] px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-[#1E3D6B]"
            >
              الدخول لعيادتك الرقمية
              <ArrowLeft className="h-4 w-4" />
            </button>
          </div>
        )}
      </div>
    </div>
  );
};
