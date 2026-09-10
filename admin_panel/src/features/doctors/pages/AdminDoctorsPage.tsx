import React, { useState } from 'react';
import { Search } from 'lucide-react';

export const AdminDoctorsPage: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [doctors, setDoctors] = useState([
    { id: 1, name: 'د. أحمد علي البعداني', specialty: 'استشاري أمراض الباطنية والقلب', license: 'YEM-MED-8842', feeYer: 15000, status: 'active' },
    { id: 2, name: 'د. سارة خليل الأغبري', specialty: 'طب ومزاحمة الجلدية والتجميل', license: 'YEM-MED-9912', feeYer: 20000, status: 'active' },
    { id: 3, name: 'د. وليد منصور الحكيمي', specialty: 'طب الأطباء والأطفال الحديثي الولادة', license: 'YEM-MED-4410', feeYer: 12000, status: 'suspended' },
  ]);

  const toggleSuspend = (id: number) => {
    setDoctors(
      doctors.map((doc) =>
        doc.id === id ? { ...doc, status: doc.status === 'active' ? 'suspended' : 'active' } : doc
      )
    );
  };

  const filteredDoctors = doctors.filter((doc) => doc.name.includes(searchQuery) || doc.license.includes(searchQuery));

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800 pb-4">
        <div>
          <h1 className="text-2xl font-black text-white">دليل الأطباء والعيادات الرقمية</h1>
          <p className="text-xs font-semibold text-slate-400">إدارة حسابات الأطباء المعتمدين وتأكيد الحظر أو التفعيل وتراخيص العيادات</p>
        </div>
      </div>

      <div className="relative max-w-md">
        <input
          type="text"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          placeholder="البحث باسم الطبيب أو رقم الترخيص..."
          className="w-full rounded-xl border border-slate-800 bg-slate-900 py-2.5 pl-10 pr-4 text-xs font-medium text-white focus:border-blue-500 focus:outline-none"
        />
        <Search className="absolute left-3 top-3 h-4 w-4 text-slate-500" />
      </div>

      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-right text-xs">
            <thead className="bg-slate-950 text-slate-400 border-b border-slate-800 font-bold">
              <tr>
                <th className="px-5 py-3.5">اسم الطبيب</th>
                <th className="px-5 py-3.5">التخصص الطبي</th>
                <th className="px-5 py-3.5">رقم الترخيص</th>
                <th className="px-5 py-3.5">سعر الاستشارة (YER)</th>
                <th className="px-5 py-3.5">حالة الحساب والعيادة</th>
                <th className="px-5 py-3.5 text-center">إجراءات الإدارة</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 font-medium text-slate-300">
              {filteredDoctors.map((doc) => (
                <tr key={doc.id} className="hover:bg-slate-800/40">
                  <td className="px-5 py-4 font-bold text-white">{doc.name}</td>
                  <td className="px-5 py-4 text-slate-300">{doc.specialty}</td>
                  <td className="px-5 py-4 font-mono font-bold text-blue-400">{doc.license}</td>
                  <td className="px-5 py-4 font-mono text-emerald-400 font-bold">{doc.feeYer.toLocaleString()} ر.ي</td>
                  <td className="px-5 py-4">
                    {doc.status === 'active' ? (
                      <span className="rounded-full bg-emerald-950 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-800">نشط وموثق</span>
                    ) : (
                      <span className="rounded-full bg-rose-950 px-3 py-1 text-xs font-bold text-rose-300 border border-rose-800">موقوف مؤقتاً</span>
                    )}
                  </td>
                  <td className="px-5 py-4 text-center">
                    <button
                      onClick={() => toggleSuspend(doc.id)}
                      className={`rounded-xl px-3 py-1.5 text-xs font-bold transition-all ${
                        doc.status === 'active'
                          ? 'bg-rose-950 text-rose-300 border border-rose-800 hover:bg-rose-900'
                          : 'bg-emerald-950 text-emerald-300 border border-emerald-800 hover:bg-emerald-900'
                      }`}
                    >
                      {doc.status === 'active' ? 'تجميد العيادة' : 'إلغاء التجميد'}
                    </button>
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
