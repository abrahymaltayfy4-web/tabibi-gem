import React from 'react';
import { Calendar, Video, Wallet, Clock, ArrowUpRight, CheckCircle2 } from 'lucide-react';
import { useAuthStore } from '../../../core/auth/useAuthStore';

export const DashboardPage: React.FC = () => {
  const { user } = useAuthStore();

  return (
    <div className="space-y-6">
      {/* Welcome Banner */}
      <div className="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#29508B] to-[#1E3D6B] p-6 text-white shadow-xl shadow-[#29508B]/15 md:p-8">
        <div className="relative z-10 space-y-2">
          <div className="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold backdrop-blur-md">
            <span className="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            العيادة الرقمية متصلة وجاهزة
          </div>
          <h1 className="text-2xl font-bold md:text-3xl">أهلاً بك، {user?.name || 'د. أحمد علي'} 👋</h1>
          <p className="max-w-2xl text-sm text-blue-100/90 leading-relaxed">
            مرحباً بك في لوحة تحكم عيادتك الرقمية على منصة طبيبي. تفقّد مواعيد اليوم وتأهّب للجلسات الطبية القادمة مع المرضى.
          </p>
        </div>
      </div>

      {/* Overview Stat Cards */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">مواعيد اليوم</span>
            <div className="rounded-xl bg-blue-50 p-2.5 text-[#29508B] dark:bg-blue-950/60 dark:text-blue-400">
              <Calendar className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-2xl font-black text-slate-900 dark:text-slate-100">6</span>
            <span className="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center">
              <ArrowUpRight className="h-3.5 w-3.5" /> +2 جديدة
            </span>
          </div>
        </div>

        <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">الاستشارات المكتملة</span>
            <div className="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">
              <CheckCircle2 className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-2xl font-black text-slate-900 dark:text-slate-100">128</span>
            <span className="text-xs text-slate-500 dark:text-slate-400">استشارة ناجحة</span>
          </div>
        </div>

        <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">جلسة مرئية قادمة</span>
            <div className="rounded-xl bg-purple-50 p-2.5 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400">
              <Video className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-2">
            <span className="text-2xl font-black text-slate-900 dark:text-slate-100">10:30 ص</span>
            <span className="text-xs text-purple-600 dark:text-purple-400 font-semibold">بعد 25 دقيقة</span>
          </div>
        </div>

        <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">الرصيد المتاح (YER)</span>
            <div className="rounded-xl bg-amber-50 p-2.5 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400">
              <Wallet className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3 flex items-baseline gap-1">
            <span className="text-2xl font-black text-slate-900 dark:text-slate-100">245,000</span>
            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">ر.ي</span>
          </div>
        </div>
      </div>

      {/* Today's Appointments List */}
      <div className="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div className="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
          <div>
            <h2 className="text-lg font-bold text-slate-900 dark:text-slate-100">مواعيد اليوم السرية</h2>
            <p className="text-xs text-slate-500 dark:text-slate-400">قائمة استشارات المرضى المحجوزة والمؤكدة لهذا اليوم</p>
          </div>
          <button className="text-xs font-bold text-[#29508B] hover:underline dark:text-blue-400">عرض كافة المواعيد</button>
        </div>

        <div className="mt-4 divide-y divide-slate-100 dark:divide-slate-800">
          {[
            { patient: 'محمد عبدالله باوزير', type: 'استشارة فيديو أونلاين', time: '10:30 صباحاً', status: 'مؤكدة والمبلغ مدفوع', statusColor: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' },
            { patient: 'فاطمة أحمد سالم', type: 'متابعة نتايج وفحوصات', time: '11:15 صباحاً', status: 'قيد الانتظار', statusColor: 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' },
            { patient: 'عمر خالد العمودي', type: 'استشارة فيديو أونلاين', time: '04:00 مساءً', status: 'مؤكدة والمبلغ مدفوع', statusColor: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' },
          ].map((item, idx) => (
            <div key={idx} className="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
              <div className="flex items-center gap-3">
                <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-sm">
                  {item.patient.charAt(0)}
                </div>
                <div>
                  <h3 className="text-sm font-bold text-slate-900 dark:text-slate-100">{item.patient}</h3>
                  <p className="text-xs text-slate-500 dark:text-slate-400">{item.type}</p>
                </div>
              </div>

              <div className="flex items-center justify-between sm:justify-end gap-4">
                <span className="flex items-center gap-1.5 text-xs font-semibold text-slate-600 dark:text-slate-400">
                  <Clock className="h-3.5 w-3.5" />
                  {item.time}
                </span>

                <span className={`rounded-full px-3 py-1 text-xs font-semibold ${item.statusColor}`}>
                  {item.status}
                </span>

                <button className="rounded-xl bg-[#29508B] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-[#1E3D6B] transition-colors">
                  دخول الغرفة
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};
