import React, { useState } from 'react';
import { ChevronRight, ChevronLeft, Video } from 'lucide-react';

export const CalendarPage: React.FC = () => {
  const [viewMode, setViewMode] = useState<'day' | 'week' | 'month'>('week');

  const daysOfWeek = [
    { day: 'السبت', date: '05 سبتمبر' },
    { day: 'الأحد', date: '06 سبتمبر' },
    { day: 'الإثنين', date: '07 سبتمبر' },
    { day: 'الثلاثاء', date: '08 سبتمبر' },
    { day: 'الأربعاء', date: '09 سبتمبر' },
    { day: 'الخميس', date: '10 سبتمبر', isToday: true },
    { day: 'الجمعة', date: '11 سبتمبر' },
  ];

  const timeSlots = ['09:00 ص', '10:00 ص', '11:00 ص', '12:00 م', '01:00 م', '04:00 م', '05:00 م'];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200/80 pb-4 dark:border-slate-800">
        <div>
          <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">التقويم الطبي التفاعلي</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">تصفح الفترات والمواعيد المحجوزة والمتاحة عبر التقويم</p>
        </div>

        <div className="flex items-center gap-2">
          <div className="flex rounded-xl border border-slate-200 bg-white p-1 dark:border-slate-800 dark:bg-slate-900">
            {(['day', 'week', 'month'] as const).map((mode) => (
              <button
                key={mode}
                onClick={() => setViewMode(mode)}
                className={`rounded-lg px-3 py-1.5 text-xs font-bold transition-all ${
                  viewMode === mode
                    ? 'bg-[#29508B] text-white shadow-sm'
                    : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100'
                }`}
              >
                {mode === 'day' ? 'اليوم' : mode === 'week' ? 'الأسبوع' : 'الشهر'}
              </button>
            ))}
          </div>

          <div className="flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-2 py-1 dark:border-slate-800 dark:bg-slate-900 text-slate-600 dark:text-slate-300">
            <button className="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">
              <ChevronRight className="h-4 w-4" />
            </button>
            <span className="text-xs font-bold px-2">سبتمبر 2026</span>
            <button className="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">
              <ChevronLeft className="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>

      {/* Calendar Grid View */}
      <div className="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div className="grid grid-cols-8 border-b border-slate-100 bg-slate-50/80 dark:border-slate-800 dark:bg-slate-800/50 text-center text-xs font-bold text-slate-700 dark:text-slate-300">
          <div className="py-3.5 border-l border-slate-100 dark:border-slate-800">الوقت</div>
          {daysOfWeek.map((d) => (
            <div key={d.day} className={`py-3.5 ${d.isToday ? 'bg-[#29508B]/10 text-[#29508B] dark:text-blue-400 font-black' : ''}`}>
              <p>{d.day}</p>
              <p className="text-[10px] font-normal text-slate-400">{d.date}</p>
            </div>
          ))}
        </div>

        <div className="divide-y divide-slate-100 dark:divide-slate-800">
          {timeSlots.map((time, idx) => (
            <div key={time} className="grid grid-cols-8 min-h-[70px] text-xs">
              <div className="flex items-center justify-center border-l border-slate-100 font-mono font-bold text-slate-400 dark:border-slate-800">
                {time}
              </div>

              {daysOfWeek.map((_, dIdx) => (
                <div key={dIdx} className="p-1.5 border-l border-slate-100/60 dark:border-slate-800/60 relative">
                  {idx === 1 && dIdx === 5 && (
                    <div className="h-full rounded-xl bg-gradient-to-br from-[#29508B] to-[#1E3D6B] p-2 text-white shadow-sm text-right space-y-1">
                      <p className="font-bold text-[11px] truncate">محمد باوزير</p>
                      <p className="text-[9px] text-blue-100 flex items-center gap-1">
                        <Video className="h-3 w-3" />
                        استشارة فيديو
                      </p>
                    </div>
                  )}

                  {idx === 3 && dIdx === 2 && (
                    <div className="h-full rounded-xl bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 p-2 border border-emerald-200 dark:border-emerald-800 text-right space-y-1">
                      <p className="font-bold text-[11px] truncate">فاطمة سالم</p>
                      <p className="text-[9px] text-emerald-600 dark:text-emerald-400">متابعة نتجية فحوصات</p>
                    </div>
                  )}
                </div>
              ))}
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};
