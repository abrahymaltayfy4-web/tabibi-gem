import React, { useState } from 'react';
import { Search, Video } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

interface Appointment {
  id: number;
  patientName: string;
  patientPhone: string;
  type: string;
  date: string;
  time: string;
  feeYer: number;
  status: 'confirmed' | 'pending' | 'completed' | 'cancelled';
  notes?: string;
}

export const AppointmentsPage: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedStatus, setSelectedStatus] = useState<string>('all');
  const navigate = useNavigate();

  const mockAppointments: Appointment[] = [
    {
      id: 101,
      patientName: 'محمد عبدالله باوزير',
      patientPhone: '771234567',
      type: 'استشارة فيديو أونلاين',
      date: '2026-09-10',
      time: '10:30 صباحاً',
      feeYer: 15000,
      status: 'confirmed',
      notes: 'يعاني من آلام متكررة في المعدة وحموضة بعد الوجبات',
    },
    {
      id: 102,
      patientName: 'فاطمة أحمد سالم',
      patientPhone: '779876543',
      type: 'متابعة نتجية الفحوصات والتحاليل',
      date: '2026-09-10',
      time: '11:15 صباحاً',
      feeYer: 15000,
      status: 'pending',
      notes: 'طلب مراجعة نتيجة تحليل صورة الدم الكاملة CBC',
    },
    {
      id: 103,
      patientName: 'عمر خالد العمودي',
      patientPhone: '733445566',
      type: 'استشارة فيديو أونلاين',
      date: '2026-09-10',
      time: '04:00 مساءً',
      feeYer: 15000,
      status: 'confirmed',
    },
    {
      id: 104,
      patientName: 'سارة محسن الدبعي',
      patientPhone: '775511223',
      type: 'استشارة فيديو أونلاين',
      date: '2026-09-09',
      time: '05:30 مساءً',
      feeYer: 15000,
      status: 'completed',
      notes: 'تم صرف وصفة إلكترونية وتمت التوصية بالراحة والتحاليل الدورية',
    },
    {
      id: 105,
      patientName: 'علي حسين العولقي',
      patientPhone: '711223344',
      type: 'استشارة صوتية أونلاين',
      date: '2026-09-08',
      time: '08:00 مساءً',
      feeYer: 15000,
      status: 'cancelled',
      notes: 'تم الإلغاء بواسطة المريض واستعادة المبلغ المستحق',
    },
  ];

  const filteredAppointments = mockAppointments.filter((item) => {
    const matchesSearch = item.patientName.includes(searchQuery) || item.patientPhone.includes(searchQuery);
    const matchesStatus = selectedStatus === 'all' || item.status === selectedStatus;
    return matchesSearch && matchesStatus;
  });

  const getStatusBadge = (status: Appointment['status']) => {
    switch (status) {
      case 'confirmed':
        return <span className="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">مؤكدة ومدفوعة</span>;
      case 'pending':
        return <span className="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">بانتظار التأكيد</span>;
      case 'completed':
        return <span className="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">استشارة مكتملة</span>;
      case 'cancelled':
        return <span className="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">ملغية</span>;
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200/80 pb-4 dark:border-slate-800">
        <div>
          <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">إدارة المواعيد والحجوزات الطبية</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">استعراض ومتابعة حالات كافة حجز استشارات المرضى في المنصة</p>
        </div>
      </div>

      {/* Filter and Search Toolbar */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative flex-1 max-w-md">
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="البحث باسم المريض أو رقم الهاتف..."
            className="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-xs font-medium text-slate-900 shadow-sm focus:border-[#29508B] focus:outline-none dark:border-slate-800 dark:bg-slate-900 dark:text-slate-100"
          />
          <Search className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
        </div>

        <div className="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
          {[
            { id: 'all', label: 'كافة المواعيد' },
            { id: 'confirmed', label: 'المؤكدة' },
            { id: 'pending', label: 'بانتظار التأكيد' },
            { id: 'completed', label: 'المكتملة' },
            { id: 'cancelled', label: 'الملغية' },
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setSelectedStatus(tab.id)}
              className={`rounded-xl px-3.5 py-2 text-xs font-bold whitespace-nowrap transition-colors ${
                selectedStatus === tab.id
                  ? 'bg-[#29508B] text-white shadow-sm'
                  : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-300'
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>
      </div>

      {/* Appointments Data Table */}
      <div className="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div className="overflow-x-auto">
          <table className="w-full text-right text-xs">
            <thead className="bg-slate-50 text-slate-500 border-b border-slate-100 dark:bg-slate-800/60 dark:text-slate-400 dark:border-slate-800 font-bold">
              <tr>
                <th className="px-5 py-3.5">المريض</th>
                <th className="px-5 py-3.5">نوع الاستشارة</th>
                <th className="px-5 py-3.5">التاريخ والوقت</th>
                <th className="px-5 py-3.5">الرسوم (YER)</th>
                <th className="px-5 py-3.5">حالة الموعد</th>
                <th className="px-5 py-3.5 text-center">الإجراء المتاح</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
              {filteredAppointments.map((item) => (
                <tr key={item.id} className="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-3">
                      <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        {item.patientName.charAt(0)}
                      </div>
                      <div>
                        <p className="font-bold text-slate-900 dark:text-slate-100">{item.patientName}</p>
                        <p className="text-[11px] text-slate-400">{item.patientPhone}</p>
                      </div>
                    </div>
                  </td>
                  <td className="px-5 py-4 text-slate-700 dark:text-slate-300">{item.type}</td>
                  <td className="px-5 py-4">
                    <p className="font-bold text-slate-900 dark:text-slate-100">{item.date}</p>
                    <p className="text-[11px] text-slate-400">{item.time}</p>
                  </td>
                  <td className="px-5 py-4 font-mono font-bold text-slate-900 dark:text-slate-100">{item.feeYer.toLocaleString()} ر.ي</td>
                  <td className="px-5 py-4">{getStatusBadge(item.status)}</td>
                  <td className="px-5 py-4 text-center">
                    <button
                      onClick={() => navigate('/consultations')}
                      className="inline-flex items-center gap-1.5 rounded-xl bg-[#29508B] px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-[#1E3D6B]"
                    >
                      <Video className="h-3.5 w-3.5" />
                      فتح الاستشارة
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
