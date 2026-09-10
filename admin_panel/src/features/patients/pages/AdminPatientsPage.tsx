import React, { useState } from 'react';
import { Search } from 'lucide-react';

export const AdminPatientsPage: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [patients, setPatients] = useState([
    { id: 1, name: 'محمد عبدالله باوزير', phone: '771234567', email: 'm.bawazir@example.com', appointmentsCount: 5, status: 'active' },
    { id: 2, name: 'فاطمة أحمد سالم', phone: '779876543', email: 'fatima@example.com', appointmentsCount: 3, status: 'active' },
    { id: 3, name: 'عمر خالد العمودي', phone: '733445566', email: 'omar@example.com', appointmentsCount: 2, status: 'active' },
  ]);

  const toggleSuspend = (id: number) => {
    setPatients(
      patients.map((pat) =>
        pat.id === id ? { ...pat, status: pat.status === 'active' ? 'suspended' : 'active' } : pat
      )
    );
  };

  const filteredPatients = patients.filter((pat) => pat.name.includes(searchQuery) || pat.phone.includes(searchQuery));

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800 pb-4">
        <div>
          <h1 className="text-2xl font-black text-white">إدارة وتصفح حسابات المرضى</h1>
          <p className="text-xs font-semibold text-slate-400">متابعة حسابات المرضى المسجلين والحالة الإدارية مع الحفاظ التام على سرية البيانات السريرية</p>
        </div>
      </div>

      <div className="relative max-w-md">
        <input
          type="text"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          placeholder="البحث باسم المريض أو رقم الهاتف..."
          className="w-full rounded-xl border border-slate-800 bg-slate-900 py-2.5 pl-10 pr-4 text-xs font-medium text-white focus:border-blue-500 focus:outline-none"
        />
        <Search className="absolute left-3 top-3 h-4 w-4 text-slate-500" />
      </div>

      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-right text-xs">
            <thead className="bg-slate-950 text-slate-400 border-b border-slate-800 font-bold">
              <tr>
                <th className="px-5 py-3.5">اسم المريض</th>
                <th className="px-5 py-3.5">رقم الهاتف</th>
                <th className="px-5 py-3.5">البريد الإلكتروني</th>
                <th className="px-5 py-3.5">إجمالي المواعيد</th>
                <th className="px-5 py-3.5">الحالة</th>
                <th className="px-5 py-3.5 text-center">إجراءات الحظر والتفعيل</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 font-medium text-slate-300">
              {filteredPatients.map((pat) => (
                <tr key={pat.id} className="hover:bg-slate-800/40">
                  <td className="px-5 py-4 font-bold text-white">{pat.name}</td>
                  <td className="px-5 py-4 text-slate-300">{pat.phone}</td>
                  <td className="px-5 py-4 text-slate-400">{pat.email}</td>
                  <td className="px-5 py-4 font-bold text-blue-400">{pat.appointmentsCount} مواعيد</td>
                  <td className="px-5 py-4">
                    {pat.status === 'active' ? (
                      <span className="rounded-full bg-emerald-950 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-800">نشط</span>
                    ) : (
                      <span className="rounded-full bg-rose-950 px-3 py-1 text-xs font-bold text-rose-300 border border-rose-800">محظور</span>
                    )}
                  </td>
                  <td className="px-5 py-4 text-center">
                    <button
                      onClick={() => toggleSuspend(pat.id)}
                      className={`rounded-xl px-3 py-1.5 text-xs font-bold transition-all ${
                        pat.status === 'active'
                          ? 'bg-rose-950 text-rose-300 border border-rose-800 hover:bg-rose-900'
                          : 'bg-emerald-950 text-emerald-300 border border-emerald-800 hover:bg-emerald-900'
                      }`}
                    >
                      {pat.status === 'active' ? 'حظر الحساب' : 'فك الحظر'}
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
