import React, { useState } from 'react';
import { Plus, CheckCircle2 } from 'lucide-react';

export const AdminSpecialtiesPage: React.FC = () => {
  const [specialties, setSpecialties] = useState([
    { id: 1, nameAr: 'أمراض الباطنية والجهاز الهضمي', nameEn: 'Internal Medicine', doctorsCount: 42, active: true },
    { id: 2, nameAr: 'أمراض القلب والأوعية الدموية', nameEn: 'Cardiology', doctorsCount: 28, active: true },
    { id: 3, nameAr: 'طب الأطباء والأطفال الحديثي الولادة', nameEn: 'Pediatrics', doctorsCount: 35, active: true },
    { id: 4, nameAr: 'النساء والتوليد والعقم', nameEn: 'Obstetrics & Gynecology', doctorsCount: 30, active: true },
    { id: 5, nameAr: 'طب ومزاحمة الجلدية والتجميل', nameEn: 'Dermatology', doctorsCount: 25, active: true },
  ]);

  const [newSpecialty, setNewSpecialty] = useState({ nameAr: '', nameEn: '' });
  const [savedSuccess, setSavedSuccess] = useState(false);

  const handleAdd = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newSpecialty.nameAr.trim()) return;
    setSpecialties([
      ...specialties,
      {
        id: Date.now(),
        nameAr: newSpecialty.nameAr,
        nameEn: newSpecialty.nameEn || 'Medical Specialty',
        doctorsCount: 0,
        active: true,
      },
    ]);
    setNewSpecialty({ nameAr: '', nameEn: '' });
    setSavedSuccess(true);
    setTimeout(() => setSavedSuccess(false), 3000);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800 pb-4">
        <div>
          <h1 className="text-2xl font-black text-white">إدارة وتصنيف التخصصات الطبية (Specialties Taxonomy)</h1>
          <p className="text-xs font-semibold text-slate-400">إضافة وتحديث دليل التخصصات الطبية المعتمدة في منصة طبيبي</p>
        </div>

        {savedSuccess && (
          <div className="flex items-center gap-2 rounded-xl bg-emerald-950 px-4 py-2 text-xs font-bold text-emerald-300 border border-emerald-800">
            <CheckCircle2 className="h-4 w-4" />
            تم حفظ التخصص الطبي بنجاح
          </div>
        )}
      </div>

      {/* Add Specialty Form */}
      <form onSubmit={handleAdd} className="rounded-2xl border border-slate-800 bg-slate-900 p-5 space-y-4">
        <h2 className="text-xs font-bold text-slate-300 flex items-center gap-2">
          <Plus className="h-4 w-4 text-blue-400" />
          إضافة تخصص طبي جديد للشجرة
        </h2>

        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <input
            type="text"
            required
            value={newSpecialty.nameAr}
            onChange={(e) => setNewSpecialty({ ...newSpecialty, nameAr: e.target.value })}
            placeholder="اسم التخصص بالعربية (مثال: جراحة العظام)"
            className="rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-xs text-white focus:border-blue-500 focus:outline-none"
          />

          <input
            type="text"
            value={newSpecialty.nameEn}
            onChange={(e) => setNewSpecialty({ ...newSpecialty, nameEn: e.target.value })}
            placeholder="اسم التخصص بالإنجليزية (مثال: Orthopedic Surgery)"
            className="rounded-xl border border-slate-800 bg-slate-950 px-4 py-2.5 text-xs text-white focus:border-blue-500 focus:outline-none"
          />
        </div>

        <button
          type="submit"
          className="rounded-xl bg-[#29508B] px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-700"
        >
          إضافة التخصص للقائمة
        </button>
      </form>

      {/* Specialties Data List */}
      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-right text-xs">
            <thead className="bg-slate-950 text-slate-400 border-b border-slate-800 font-bold">
              <tr>
                <th className="px-5 py-3.5">اسم التخصص (عربي)</th>
                <th className="px-5 py-3.5">اسم التخصص (إنجليزي)</th>
                <th className="px-5 py-3.5">عدد الأطباء المسجلين</th>
                <th className="px-5 py-3.5">الحالة</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 font-medium text-slate-300">
              {specialties.map((sp) => (
                <tr key={sp.id} className="hover:bg-slate-800/40">
                  <td className="px-5 py-4 font-bold text-white">{sp.nameAr}</td>
                  <td className="px-5 py-4 text-slate-400 font-mono">{sp.nameEn}</td>
                  <td className="px-5 py-4 font-mono font-bold text-blue-400">{sp.doctorsCount} طبيب</td>
                  <td className="px-5 py-4">
                    <span className="rounded-full bg-emerald-950 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-800">مفعل</span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
