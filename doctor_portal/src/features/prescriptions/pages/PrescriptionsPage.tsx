import React, { useState } from 'react';
import { Pill, Plus, Trash2, CheckCircle2, FileCheck } from 'lucide-react';

interface MedicationItem {
  id: string;
  name: string;
  dosage: string;
  frequency: string;
  duration: string;
  instructions: string;
}

export const PrescriptionsPage: React.FC = () => {
  const [patientName] = useState('محمد عبدالله باوزير');
  const [savedSuccess, setSavedSuccess] = useState(false);
  const [medications, setMedications] = useState<MedicationItem[]>([
    {
      id: '1',
      name: 'Nexium (Esomeprazole) 40mg',
      dosage: 'قرص واحد',
      frequency: 'مرة واحدة قبل الإفطار',
      duration: '14 يوم',
      instructions: 'يؤخذ على معدة فارغة قبل الأكل بـ 30 دقيقة',
    },
    {
      id: '2',
      name: 'Disflatyl (Simethicone) 40mg',
      dosage: 'قرصين للمضغ',
      frequency: '3 مرات يومياً بعد الوجبات',
      duration: '7 أيام',
      instructions: 'مضغ جيد قبل البلع',
    },
  ]);

  const addMedication = () => {
    setMedications([
      ...medications,
      {
        id: Date.now().toString(),
        name: '',
        dosage: '',
        frequency: 'مرة واحدة يومياً',
        duration: '7 أيام',
        instructions: '',
      },
    ]);
  };

  const removeMedication = (id: string) => {
    setMedications(medications.filter((item) => item.id !== id));
  };

  const updateMedication = (id: string, field: keyof MedicationItem, value: string) => {
    setMedications(
      medications.map((item) => (item.id === id ? { ...item, [field]: value } : item))
    );
  };

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
          <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">محرر الوصفات الطبية الإلكترونية (E-Prescription)</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">إصدار وتوقيع الوصفة الطبية الرسمية للمريض بعد إتمام الاستشارة</p>
        </div>

        {savedSuccess && (
          <div className="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
            <CheckCircle2 className="h-4 w-4" />
            تم توثيق وحفظ الوصفة وإتاحتها فورياً في تطبيق المريض
          </div>
        )}
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Patient Selection Banner */}
        <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-950 dark:text-purple-400 font-bold">
              <Pill className="h-5 w-5" />
            </div>
            <div>
              <p className="text-xs text-slate-400">الوصفة موجهة للمريض:</p>
              <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100">{patientName}</h2>
            </div>
          </div>

          <span className="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-[#29508B] dark:bg-blue-950 dark:text-blue-300">
            وصفة إلكترونية معتمدة
          </span>
        </div>

        {/* Medication List Form Items */}
        <div className="space-y-4">
          {medications.map((item, index) => (
            <div key={item.id} className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-3">
              <div className="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                <span className="text-xs font-bold text-[#29508B] dark:text-blue-400">الدواء #{index + 1}</span>
                {medications.length > 1 && (
                  <button
                    type="button"
                    onClick={() => removeMedication(item.id)}
                    className="text-rose-500 hover:text-rose-700 text-xs font-bold flex items-center gap-1"
                  >
                    <Trash2 className="h-3.5 w-3.5" />
                    حذف الدواء
                  </button>
                )}
              </div>

              <div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div className="space-y-1 sm:col-span-2">
                  <label className="text-[11px] font-bold text-slate-600 dark:text-slate-300">اسم العلاج العلمي أو التجاري</label>
                  <input
                    type="text"
                    required
                    value={item.name}
                    onChange={(e) => updateMedication(item.id, 'name', e.target.value)}
                    placeholder="مثال: Omeprazole 20mg"
                    className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[11px] font-bold text-slate-600 dark:text-slate-300">الجرعة المحددة</label>
                  <input
                    type="text"
                    required
                    value={item.dosage}
                    onChange={(e) => updateMedication(item.id, 'dosage', e.target.value)}
                    placeholder="مثال: قرص واحد"
                    className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[11px] font-bold text-slate-600 dark:text-slate-300">التكرار اليومي</label>
                  <input
                    type="text"
                    value={item.frequency}
                    onChange={(e) => updateMedication(item.id, 'frequency', e.target.value)}
                    placeholder="مثال: مرتين يومياً بعد الأكل"
                    className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[11px] font-bold text-slate-600 dark:text-slate-300">مدة العلاج</label>
                  <input
                    type="text"
                    value={item.duration}
                    onChange={(e) => updateMedication(item.id, 'duration', e.target.value)}
                    placeholder="مثال: 7 أيام"
                    className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                  />
                </div>

                <div className="space-y-1">
                  <label className="text-[11px] font-bold text-slate-600 dark:text-slate-300">تعليمات خاصة</label>
                  <input
                    type="text"
                    value={item.instructions}
                    onChange={(e) => updateMedication(item.id, 'instructions', e.target.value)}
                    placeholder="تعليمات الاستعمال..."
                    className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                  />
                </div>
              </div>
            </div>
          ))}
        </div>

        <div className="flex items-center justify-between">
          <button
            type="button"
            onClick={addMedication}
            className="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"
          >
            <Plus className="h-4 w-4" />
            إضافة دواء جديد للوصفة
          </button>

          <button
            type="submit"
            className="flex items-center gap-2 rounded-xl bg-purple-600 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-purple-600/20 hover:bg-purple-700"
          >
            <FileCheck className="h-4 w-4" />
            إصدار واعتماد الوصفة رسمياً
          </button>
        </div>
      </form>
    </div>
  );
};
