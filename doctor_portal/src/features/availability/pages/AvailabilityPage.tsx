import React, { useState } from 'react';
import { Clock, CheckCircle2, Save, AlertCircle, ToggleLeft, ToggleRight } from 'lucide-react';

interface DaySchedule {
  day: string;
  nameAr: string;
  enabled: boolean;
  startTime: string;
  endTime: string;
}

export const AvailabilityPage: React.FC = () => {
  const [schedule, setSchedule] = useState<DaySchedule[]>([
    { day: 'saturday', nameAr: 'السبت', enabled: true, startTime: '09:00', endTime: '14:00' },
    { day: 'sunday', nameAr: 'الأحد', enabled: true, startTime: '09:00', endTime: '14:00' },
    { day: 'monday', nameAr: 'الإثنين', enabled: true, startTime: '09:00', endTime: '14:00' },
    { day: 'tuesday', nameAr: 'الثلاثاء', enabled: true, startTime: '09:00', endTime: '14:00' },
    { day: 'wednesday', nameAr: 'الأربعاء', enabled: true, startTime: '09:00', endTime: '14:00' },
    { day: 'thursday', nameAr: 'الخميس', enabled: true, startTime: '09:00', endTime: '12:00' },
    { day: 'friday', nameAr: 'الجمعة (إجازة)', enabled: false, startTime: '16:00', endTime: '20:00' },
  ]);

  const [isVacation, setIsVacation] = useState(false);
  const [savedSuccess, setSavedSuccess] = useState(false);

  const toggleDay = (index: number) => {
    const newSchedule = [...schedule];
    newSchedule[index].enabled = !newSchedule[index].enabled;
    setSchedule(newSchedule);
  };

  const updateTime = (index: number, field: 'startTime' | 'endTime', value: string) => {
    const newSchedule = [...schedule];
    newSchedule[index][field] = value;
    setSchedule(newSchedule);
  };

  const handleSave = () => {
    setSavedSuccess(true);
    setTimeout(() => setSavedSuccess(false), 3000);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200/80 pb-4 dark:border-slate-800">
        <div>
          <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">جدول الأوقات والفترات المتاحة</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">إدارة أوقات العمل التكرارية الأسبوعية وتحديد فترات الاستراحة بين المرضى</p>
        </div>

        {savedSuccess && (
          <div className="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
            <CheckCircle2 className="h-4 w-4" />
            تم حفظ الجدول الأسبوعي بنجاح
          </div>
        )}
      </div>

      {/* Temporary Vacation Banner */}
      <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className={`p-2.5 rounded-xl ${isVacation ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-[#29508B]'}`}>
            <AlertCircle className="h-5 w-5" />
          </div>
          <div>
            <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100">وضع الإجازة / التعطيل المؤقت</h2>
            <p className="text-xs text-slate-500 dark:text-slate-400">إيقاف استقبال الحجوزات الأونلاين مؤقتاً أثناء الإجازات الطارئة</p>
          </div>
        </div>

        <button onClick={() => setIsVacation(!isVacation)} className="text-slate-700 dark:text-slate-300">
          {isVacation ? <ToggleRight className="h-9 w-9 text-rose-600" /> : <ToggleLeft className="h-9 w-9 text-slate-400" />}
        </button>
      </div>

      {/* Weekly Days Schedule Manager */}
      <div className="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
        <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
          <Clock className="h-4 w-4 text-[#29508B]" />
          جدول الأيام السبعة الأسبوعية
        </h2>

        <div className="divide-y divide-slate-100 dark:divide-slate-800">
          {schedule.map((item, index) => (
            <div key={item.day} className="flex flex-col gap-3 py-3.5 sm:flex-row sm:items-center sm:justify-between">
              <div className="flex items-center gap-3">
                <input
                  type="checkbox"
                  checked={item.enabled}
                  onChange={() => toggleDay(index)}
                  className="h-4 w-4 rounded border-slate-300 text-[#29508B] focus:ring-[#29508B]"
                />
                <span className={`text-sm font-bold ${item.enabled ? 'text-slate-900 dark:text-slate-100' : 'text-slate-400 line-through'}`}>
                  {item.nameAr}
                </span>
              </div>

              {item.enabled ? (
                <div className="flex items-center gap-2 text-xs">
                  <span className="font-semibold text-slate-500">من:</span>
                  <input
                    type="time"
                    value={item.startTime}
                    onChange={(e) => updateTime(index, 'startTime', e.target.value)}
                    className="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 font-mono text-xs font-bold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                  />
                  <span className="font-semibold text-slate-500">إلى:</span>
                  <input
                    type="time"
                    value={item.endTime}
                    onChange={(e) => updateTime(index, 'endTime', e.target.value)}
                    className="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 font-mono text-xs font-bold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                  />
                </div>
              ) : (
                <span className="text-xs font-semibold text-slate-400">غير متاح للحجز</span>
              )}
            </div>
          ))}
        </div>
      </div>

      <div className="flex justify-end">
        <button
          onClick={handleSave}
          className="flex items-center gap-2 rounded-xl bg-[#29508B] px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-[#1E3D6B]"
        >
          <Save className="h-4 w-4" />
          حفظ الجدول والفرز الإجمالي
        </button>
      </div>
    </div>
  );
};
