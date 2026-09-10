import React from 'react';
import { Users, Stethoscope, UserCheck, CalendarDays, Wallet, ShieldCheck, ArrowUpRight, AlertCircle, ArrowLeft } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

export const AdminDashboardPage: React.FC = () => {
  const navigate = useNavigate();

  return (
    <div className="space-y-6">
      {/* Header Banner */}
      <div className="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#29508B] to-blue-900 p-6 text-white shadow-xl md:p-8">
        <div className="relative z-10 space-y-2">
          <div className="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold backdrop-blur-md">
            <span className="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            نظام الحوكمة والرقابة المركزية يعمل بكفاءة 100%
          </div>
          <h1 className="text-2xl font-bold md:text-3xl">لوحة القيادة والمراقبة العليا — منصة طبيبي</h1>
          <p className="max-w-2xl text-xs text-blue-100 leading-relaxed">
            استعراض المؤشرات المركزية، طلبات التوثيق المعلقة للأطباء، حركة الاستشارات والمالية (عمولة المنصة 15%).
          </p>
        </div>
      </div>

      {/* Pending Verifications Action Banner */}
      <div className="rounded-2xl border border-amber-800/60 bg-amber-950/40 p-4 text-amber-200 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <AlertCircle className="h-5 w-5 text-amber-400 shrink-0" />
          <p className="text-xs font-bold">يوجد 3 طلبات توثيق أطباء جديدة بانتظار فحص التراخيص والشهادات.</p>
        </div>

        <button
          onClick={() => navigate('/verifications')}
          className="flex items-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-1.5 text-xs font-bold text-slate-950 hover:bg-amber-400 transition-colors"
        >
          فحص الطلبات الآن
          <ArrowLeft className="h-3.5 w-3.5" />
        </button>
      </div>

      {/* Platform Level Metrics Cards */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div className="rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-sm">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-400">إجمالي الأطباء المعتمدين</span>
            <div className="rounded-xl bg-blue-950 p-2.5 text-blue-400 border border-blue-800">
              <Stethoscope className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-2xl font-black text-white">185</span>
            <span className="text-xs text-emerald-400 font-semibold flex items-center">
              <ArrowUpRight className="h-3.5 w-3.5" /> +12 هذا الشهر
            </span>
          </div>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-sm">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-400">إجمالي المرضى المسجلين</span>
            <div className="rounded-xl bg-purple-950 p-2.5 text-purple-400 border border-purple-800">
              <Users className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-2xl font-black text-white">1,420</span>
            <span className="text-xs text-emerald-400 font-semibold flex items-center">
              <ArrowUpRight className="h-3.5 w-3.5" /> +85 جديد
            </span>
          </div>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-sm">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-400">دخل المنصة صافي (15%)</span>
            <div className="rounded-xl bg-emerald-950 p-2.5 text-emerald-400 border border-emerald-800">
              <Wallet className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3">
            <span className="text-2xl font-black text-white">1,912,500</span>
            <span className="mr-1 text-xs font-bold text-slate-400">ر.ي YER</span>
          </div>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900 p-5 shadow-sm">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-400">الاستشارات المكتملة</span>
            <div className="rounded-xl bg-amber-950 p-2.5 text-amber-400 border border-amber-800">
              <CalendarDays className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-2xl font-black text-white">850</span>
            <span className="text-xs text-slate-400">استشارة ناجحة</span>
          </div>
        </div>
      </div>

      {/* Quick Navigation Cards */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div
          onClick={() => navigate('/verifications')}
          className="cursor-pointer rounded-2xl border border-slate-800 bg-slate-900 p-5 hover:border-blue-500/50 transition-all space-y-2"
        >
          <div className="flex items-center gap-2 text-blue-400 font-bold text-sm">
            <UserCheck className="h-4 w-4" />
            توثيق وتراخيص الأطباء
          </div>
          <p className="text-xs text-slate-400 leading-relaxed">مراجعة تراخيص مزاولة المهنة والشهادات الصادرة والموافقة عليها.</p>
        </div>

        <div
          onClick={() => navigate('/financials')}
          className="cursor-pointer rounded-2xl border border-slate-800 bg-slate-900 p-5 hover:border-blue-500/50 transition-all space-y-2"
        >
          <div className="flex items-center gap-2 text-emerald-400 font-bold text-sm">
            <ShieldCheck className="h-4 w-4" />
            المحافظ والعمولات (15%)
          </div>
          <p className="text-xs text-slate-400 leading-relaxed">متابعة تحويلات أرباح الأطباء والمصادقة على طلبات السحب للبنك.</p>
        </div>

        <div
          onClick={() => navigate('/audit-logs')}
          className="cursor-pointer rounded-2xl border border-slate-800 bg-slate-900 p-5 hover:border-blue-500/50 transition-all space-y-2"
        >
          <div className="flex items-center gap-2 text-purple-400 font-bold text-sm">
            <AlertCircle className="h-4 w-4" />
            سجلات التتبع والرقابة (Audit Logs)
          </div>
          <p className="text-xs text-slate-400 leading-relaxed">تتبع كافة العمليات والقرارات الصادرة عن موظفي الإدارة.</p>
        </div>
      </div>
    </div>
  );
};
