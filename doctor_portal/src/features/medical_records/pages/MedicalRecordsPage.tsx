import React, { useState } from 'react';
import { Save, CheckCircle2, User } from 'lucide-react';

export const MedicalRecordsPage: React.FC = () => {
  const [savedSuccess, setSavedSuccess] = useState(false);
  const [recordData, setRecordData] = useState({
    patientName: 'محمد عبدالله باوزير',
    symptoms: 'ألم حاد في الشغاف وأعلى المعدة مصحوب بغثيان خفيف بعد الوجبات الدسمة.',
    examinationNotes: 'ضغط الدم 125/80 mmHg - النبض 74/min - حرارة الجسم 36.8°C.',
    diagnosis: 'التهاب المعدة الفموي الحاد (Acute Gastritis) وتشنج المريء الخفيف.',
    treatmentPlan: 'اتباع حمية خفيفة من الدهون الصعبة، أخذ مضادات الحموضة قبل الوجبات بـ 30 دقيقة.',
    recommendations: 'إجراء تحليل جرثومة المعدة H. Pylori في حال استمرار الأعراض لأكثر من أسبوعين.',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setSavedSuccess(true);
    setTimeout(() => setSavedSuccess(false), 3000);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200/80 pb-4 dark:border-slate-800">
        <div>
          <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">تدوين السجل الطبي والسريري (EHR)</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">حفظ الملاحظات التشخيصية والتوصيات الطبية المعتمدة في سجل المريض</p>
        </div>

        {savedSuccess && (
          <div className="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
            <CheckCircle2 className="h-4 w-4" />
            تم حفظ وحفظ السجل الطبي في ملف المريض رسمياً
          </div>
        )}
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
          <div className="flex items-center gap-3 border-b border-slate-100 pb-4 dark:border-slate-800">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-[#29508B] dark:bg-blue-950 dark:text-blue-400 font-bold">
              <User className="h-5 w-5" />
            </div>
            <div>
              <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100">المريض المحدد: {recordData.patientName}</h2>
              <p className="text-xs text-slate-500 dark:text-slate-400">جلسة استشارة اليوم • 10 سبتمبر 2026</p>
            </div>
          </div>

          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div className="space-y-1.5 sm:col-span-2">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">الأعراض الموصوفة والشكوى السريرية (Symptoms & Complaint)</label>
              <textarea
                rows={3}
                value={recordData.symptoms}
                onChange={(e) => setRecordData({ ...recordData, symptoms: e.target.value })}
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
              ></textarea>
            </div>

            <div className="space-y-1.5">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">ملاحظات الفحص والعلامات الحيوية (Examination Notes)</label>
              <textarea
                rows={3}
                value={recordData.examinationNotes}
                onChange={(e) => setRecordData({ ...recordData, examinationNotes: e.target.value })}
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
              ></textarea>
            </div>

            <div className="space-y-1.5">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">التشخيص الطبي الأولي (Diagnosis)</label>
              <textarea
                rows={3}
                value={recordData.diagnosis}
                onChange={(e) => setRecordData({ ...recordData, diagnosis: e.target.value })}
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
              ></textarea>
            </div>

            <div className="space-y-1.5 sm:col-span-2">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">خطة العلاج والتوصيات السريرية (Treatment Plan & Recommendations)</label>
              <textarea
                rows={3}
                value={recordData.treatmentPlan}
                onChange={(e) => setRecordData({ ...recordData, treatmentPlan: e.target.value })}
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
              ></textarea>
            </div>
          </div>
        </div>

        <div className="flex justify-end">
          <button
            type="submit"
            className="flex items-center gap-2 rounded-xl bg-[#29508B] px-7 py-3 text-sm font-bold text-white shadow-md hover:bg-[#1E3D6B]"
          >
            <Save className="h-4 w-4" />
            حفظ وتوثيق السجل الطبي في قاعدة البيانات
          </button>
        </div>
      </form>
    </div>
  );
};
