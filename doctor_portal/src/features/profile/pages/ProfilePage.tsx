import React, { useState } from 'react';
import { ShieldCheck, Stethoscope, Save, Banknote, CheckCircle2 } from 'lucide-react';
import { useAuthStore } from '../../../core/auth/useAuthStore';

export const ProfilePage: React.FC = () => {
  const { doctorProfile, setDoctorProfile } = useAuthStore();
  const [savedSuccess, setSavedSuccess] = useState(false);

  const [formData, setFormData] = useState({
    specialtyNameAr: doctorProfile?.specialtyNameAr || 'استشاري أمراض الباطنية والقلب',
    subSpecialty: doctorProfile?.subSpecialty || 'قسطرة القلب والشرايين التاجية',
    qualificationAr: doctorProfile?.qualificationAr || 'دكتوراه في الطب الباطني والجهاز الهضمي',
    experienceYears: doctorProfile?.experienceYears || 12,
    consultationFeeYer: doctorProfile?.consultationFeeYer || 15000,
    consultationDurationMinutes: doctorProfile?.consultationDurationMinutes || 30,
    bioAr: doctorProfile?.bioAr || 'استشاري في الطب الباطني بخبرة تتجاوز 12 عاماً في التشخيص والعلاج وتقديم الاستشارات الطبية التخصصية.',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (doctorProfile) {
      setDoctorProfile({
        ...doctorProfile,
        ...formData,
      });
    }
    setSavedSuccess(true);
    setTimeout(() => setSavedSuccess(false), 3000);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200/80 pb-4 dark:border-slate-800">
        <div>
          <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">إدارة الملف المهني والتوثيق</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">تحديث مؤهلاتك، رسوم الاستشارة، والسيرة الذاتية المهنية</p>
        </div>

        {savedSuccess && (
          <div className="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
            <CheckCircle2 className="h-4 w-4" />
            تم حفظ التعديلات بنجاح
          </div>
        )}
      </div>

      <form onSubmit={handleSubmit} className="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {/* Left 2 Columns: Editable Doctor Profile Form */}
        <div className="lg:col-span-2 space-y-6">
          <div className="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
              <Stethoscope className="h-4 w-4 text-[#29508B]" />
              التخصص والمؤهلات الطبية
            </h2>

            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div className="space-y-1.5">
                <label className="text-xs font-bold text-slate-700 dark:text-slate-300">التخصص الرئيسي</label>
                <input
                  type="text"
                  value={formData.specialtyNameAr}
                  onChange={(e) => setFormData({ ...formData, specialtyNameAr: e.target.value })}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                />
              </div>

              <div className="space-y-1.5">
                <label className="text-xs font-bold text-slate-700 dark:text-slate-300">التخصص الدقيق (Sub-Specialty)</label>
                <input
                  type="text"
                  value={formData.subSpecialty}
                  onChange={(e) => setFormData({ ...formData, subSpecialty: e.target.value })}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                />
              </div>

              <div className="space-y-1.5 sm:col-span-2">
                <label className="text-xs font-bold text-slate-700 dark:text-slate-300">المؤهلات الأكاديمية والشهادات العليا</label>
                <input
                  type="text"
                  value={formData.qualificationAr}
                  onChange={(e) => setFormData({ ...formData, qualificationAr: e.target.value })}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                />
              </div>

              <div className="space-y-1.5">
                <label className="text-xs font-bold text-slate-700 dark:text-slate-300">عدد سنوات الخبرة العملية</label>
                <input
                  type="number"
                  value={formData.experienceYears}
                  onChange={(e) => setFormData({ ...formData, experienceYears: Number(e.target.value) })}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                />
              </div>
            </div>
          </div>

          <div className="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
              <Banknote className="h-4 w-4 text-[#29508B]" />
              تسعير الاستشارة ومدة الجلسة
            </h2>

            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div className="space-y-1.5">
                <label className="text-xs font-bold text-slate-700 dark:text-slate-300">سعر الاستشارة (YER)</label>
                <input
                  type="number"
                  step="1000"
                  value={formData.consultationFeeYer}
                  onChange={(e) => setFormData({ ...formData, consultationFeeYer: Number(e.target.value) })}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                />
              </div>

              <div className="space-y-1.5">
                <label className="text-xs font-bold text-slate-700 dark:text-slate-300">مدة الجلسة بالدقائق</label>
                <select
                  value={formData.consultationDurationMinutes}
                  onChange={(e) => setFormData({ ...formData, consultationDurationMinutes: Number(e.target.value) })}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                >
                  <option value="15">15 دقيقة</option>
                  <option value="30">30 دقيقة</option>
                  <option value="45">45 دقيقة</option>
                  <option value="60">60 دقيقة</option>
                </select>
              </div>

              <div className="space-y-1.5 sm:col-span-2">
                <label className="text-xs font-bold text-slate-700 dark:text-slate-300">نبذة عن الطبيب (تظهر للمرضى في التطبيق)</label>
                <textarea
                  rows={4}
                  value={formData.bioAr}
                  onChange={(e) => setFormData({ ...formData, bioAr: e.target.value })}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-4 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100"
                ></textarea>
              </div>
            </div>
          </div>

          <div className="flex justify-end">
            <button
              type="submit"
              className="flex items-center gap-2 rounded-xl bg-[#29508B] px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-[#1E3D6B]"
            >
              <Save className="h-4 w-4" />
              حفظ التغييرات المهنية
            </button>
          </div>
        </div>

        {/* Right Column: Read-Only Admin Authoritative Boundaries */}
        <div className="space-y-6">
          <div className="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
              <ShieldCheck className="h-4 w-4 text-emerald-600" />
              بيانات التوثيق والسيادة (Authoritative Bounds)
            </h2>

            <div className="space-y-3 text-xs">
              <div className="flex justify-between border-b border-slate-100 pb-2.5 dark:border-slate-800">
                <span className="font-bold text-slate-500">حالة التوثيق:</span>
                <span className="font-bold text-emerald-600 dark:text-emerald-400">موثق ومفعل (Approved)</span>
              </div>

              <div className="flex justify-between border-b border-slate-100 pb-2.5 dark:border-slate-800">
                <span className="font-bold text-slate-500">رقم الترخيص المعتمد:</span>
                <span className="font-mono font-bold text-slate-900 dark:text-slate-100">{doctorProfile?.licenseNumber || 'YEM-MED-8842'}</span>
              </div>

              <div className="flex justify-between border-b border-slate-100 pb-2.5 dark:border-slate-800">
                <span className="font-bold text-slate-500">عمولة المنصة المقتطعة:</span>
                <span className="font-bold text-[#29508B] dark:text-blue-400">15% ثابتة</span>
              </div>

              <div className="flex justify-between">
                <span className="font-bold text-slate-500">تاريخ الانضمام:</span>
                <span className="font-medium text-slate-700 dark:text-slate-300">سبتمبر 2026</span>
              </div>
            </div>

            <p className="text-[11px] text-slate-400 leading-relaxed pt-2">
              ملاحظة أمنية: لا يمكن للطبيب تعديل حالة التوثيق أو رقم الترخيص أو نسبة العمولة محلياً؛ خادم Laravel هو السلطة النهائية المعتمدة.
            </p>
          </div>
        </div>
      </form>
    </div>
  );
};
