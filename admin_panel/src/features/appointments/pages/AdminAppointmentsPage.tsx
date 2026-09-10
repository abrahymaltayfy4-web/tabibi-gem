import React, { useState } from 'react';
import { Search } from 'lucide-react';

export const AdminAppointmentsPage: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedStatus, setSelectedStatus] = useState('all');

  const appointments = [
    { id: 101, doctorName: 'د. أحمد علي البعداني', patientName: 'محمد عبدالله باوزير', type: 'استشارة فيديو', feeYer: 15000, status: 'Confirmed', date: '10 سبتمبر 2026' },
    { id: 102, doctorName: 'د. أحمد علي البعداني', patientName: 'فاطمة أحمد سالم', type: 'متابعة فحوصات', feeYer: 15000, status: 'Pending', date: '10 سبتمبر 2026' },
    { id: 104, doctorName: 'د. سارة خليل الأغبري', patientName: 'سارة محسن الدبعي', type: 'استشارة فيديو', feeYer: 20000, status: 'Completed', date: '09 سبتمبر 2026' },
    { id: 105, doctorName: 'د. وليد الحكيمي', patientName: 'علي حسين العولقي', type: 'استشارة صوتية', feeYer: 12000, status: 'CancelledByPatient', date: '08 سبتمبر 2026' },
  ];

  const filtered = appointments.filter((app) => {
    const matchesSearch = app.doctorName.includes(searchQuery) || app.patientName.includes(searchQuery);
    const matchesStatus = selectedStatus === 'all' || app.status === selectedStatus;
    return matchesSearch && matchesStatus;
  });

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800 pb-4">
        <div>
          <h1 className="text-2xl font-black text-white">رقابة وإشراف المواعيد والحجوزات (Appointments Oversight)</h1>
          <p className="text-xs font-semibold text-slate-400">تتبع حركة المواعيد عبر المنصة، حالات الحجز، والإشراف على التسوية</p>
        </div>
      </div>

      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative flex-1 max-w-md">
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="البحث باسم الطبيب أو المريض..."
            className="w-full rounded-xl border border-slate-800 bg-slate-900 py-2.5 pl-10 pr-4 text-xs font-medium text-white focus:border-blue-500 focus:outline-none"
          />
          <Search className="absolute left-3 top-3 h-4 w-4 text-slate-500" />
        </div>

        <div className="flex items-center gap-2 overflow-x-auto">
          {['all', 'Confirmed', 'Pending', 'Completed', 'CancelledByPatient'].map((st) => (
            <button
              key={st}
              onClick={() => setSelectedStatus(st)}
              className={`rounded-xl px-3.5 py-2 text-xs font-bold whitespace-nowrap transition-colors ${
                selectedStatus === st
                  ? 'bg-[#29508B] text-white shadow-sm'
                  : 'bg-slate-900 text-slate-400 border border-slate-800 hover:text-white'
              }`}
            >
              {st === 'all' ? 'كافة الحالات' : st}
            </button>
          ))}
        </div>
      </div>

      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-right text-xs">
            <thead className="bg-slate-950 text-slate-400 border-b border-slate-800 font-bold">
              <tr>
                <th className="px-5 py-3.5">معرف الموعد</th>
                <th className="px-5 py-3.5">الطبيب</th>
                <th className="px-5 py-3.5">المريض</th>
                <th className="px-5 py-3.5">نوع الجلسة</th>
                <th className="px-5 py-3.5">الرسوم (YER)</th>
                <th className="px-5 py-3.5">تاريخ الموعد</th>
                <th className="px-5 py-3.5">حالة الموعد</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 font-medium text-slate-300">
              {filtered.map((item) => (
                <tr key={item.id} className="hover:bg-slate-800/40">
                  <td className="px-5 py-4 font-mono font-bold text-blue-400">#{item.id}</td>
                  <td className="px-5 py-4 font-bold text-white">{item.doctorName}</td>
                  <td className="px-5 py-4 text-slate-200">{item.patientName}</td>
                  <td className="px-5 py-4 text-slate-400">{item.type}</td>
                  <td className="px-5 py-4 font-mono font-bold text-emerald-400">{item.feeYer.toLocaleString()} ر.ي</td>
                  <td className="px-5 py-4 text-slate-400">{item.date}</td>
                  <td className="px-5 py-4">
                    <span className="rounded-full bg-slate-950 px-3 py-1 text-xs font-bold text-slate-300 border border-slate-800">
                      {item.status}
                    </span>
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
