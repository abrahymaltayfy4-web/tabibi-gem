import React, { useState } from 'react';
import { Wallet } from 'lucide-react';

interface PayoutRequest {
  id: string;
  doctorName: string;
  amountYer: number;
  bankName: string;
  accountNumber: string;
  requestDate: string;
  status: 'pending' | 'approved' | 'rejected';
}

export const AdminFinancialsPage: React.FC = () => {
  const [payouts, setPayouts] = useState<PayoutRequest[]>([
    {
      id: 'REQ-101',
      doctorName: 'د. أحمد علي البعداني',
      amountYer: 100000,
      bankName: 'بنك الكريمي للتمويل الأصغر',
      accountNumber: '3012948291',
      requestDate: '10 سبتمبر 2026',
      status: 'pending',
    },
    {
      id: 'REQ-102',
      doctorName: 'د. سلوى عبدالجليل المقطري',
      amountYer: 150000,
      bankName: 'بنك التضامن الإسلامي',
      accountNumber: '7729104812',
      requestDate: '09 سبتمبر 2026',
      status: 'approved',
    },
  ]);

  const handleApprovePayout = (id: string) => {
    setPayouts(payouts.map((item) => (item.id === id ? { ...item, status: 'approved' } : item)));
  };

  const handleRejectPayout = (id: string) => {
    setPayouts(payouts.map((item) => (item.id === id ? { ...item, status: 'rejected' } : item)));
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800 pb-4">
        <div>
          <h1 className="text-2xl font-black text-white">سجل العمولات والتحويلات المالية (YER Ledger)</h1>
          <p className="text-xs font-semibold text-slate-400">متابعة عمولة المنصة 15%، المصادقة على طلبات تحويل الأرباح للأطباء والتسوية</p>
        </div>
      </div>

      {/* Financial Summary */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div className="rounded-2xl border border-slate-800 bg-slate-900 p-5 space-y-2">
          <span className="text-xs font-bold text-slate-400">عمولة المنصة الصافية (15%)</span>
          <p className="text-2xl font-black text-emerald-400">1,912,500 <span className="text-xs font-bold text-slate-400">ر.ي YER</span></p>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900 p-5 space-y-2">
          <span className="text-xs font-bold text-slate-400">أرباح الأطباء المعلقة بالسحب</span>
          <p className="text-2xl font-black text-amber-400">250,000 <span className="text-xs font-bold text-slate-400">ر.ي YER</span></p>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900 p-5 space-y-2">
          <span className="text-xs font-bold text-slate-400">إجمالي التحويلات المكتملة</span>
          <p className="text-2xl font-black text-[#29508B]">10,837,500 <span className="text-xs font-bold text-slate-400">ر.ي YER</span></p>
        </div>
      </div>

      {/* Payout Approval Requests Table */}
      <div className="rounded-2xl border border-slate-800 bg-slate-900 p-6 space-y-4">
        <h2 className="text-sm font-bold text-white flex items-center gap-2">
          <Wallet className="h-4 w-4 text-blue-400" />
          طلبات تحويل أرباح الأطباء (Payout Requests)
        </h2>

        <div className="overflow-x-auto">
          <table className="w-full text-right text-xs">
            <thead className="bg-slate-950 text-slate-400 border-b border-slate-800 font-bold">
              <tr>
                <th className="px-4 py-3">رقم الطلب</th>
                <th className="px-4 py-3">الطبيب صاحب الطلب</th>
                <th className="px-4 py-3">المبلغ المطلوب (YER)</th>
                <th className="px-4 py-3">وسيلة التحويل والبنك</th>
                <th className="px-4 py-3">التاريخ</th>
                <th className="px-4 py-3">الحالة</th>
                <th className="px-4 py-3 text-center">المصادقة الإدارية</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 font-medium">
              {payouts.map((item) => (
                <tr key={item.id} className="hover:bg-slate-800/40">
                  <td className="px-4 py-3.5 font-mono font-bold text-blue-400">{item.id}</td>
                  <td className="px-4 py-3.5 font-bold text-white">{item.doctorName}</td>
                  <td className="px-4 py-3.5 font-mono font-bold text-emerald-400">{item.amountYer.toLocaleString()} ر.ي</td>
                  <td className="px-4 py-3.5 text-slate-300">{item.bankName} ({item.accountNumber})</td>
                  <td className="px-4 py-3.5 text-slate-400">{item.requestDate}</td>
                  <td className="px-4 py-3.5">
                    {item.status === 'approved' ? (
                      <span className="rounded-full bg-emerald-950 px-2.5 py-0.5 text-[11px] font-bold text-emerald-300 border border-emerald-800">تم التحويل</span>
                    ) : item.status === 'rejected' ? (
                      <span className="rounded-full bg-rose-950 px-2.5 py-0.5 text-[11px] font-bold text-rose-300 border border-rose-800">مرفوض</span>
                    ) : (
                      <span className="rounded-full bg-amber-950 px-2.5 py-0.5 text-[11px] font-bold text-amber-300 border border-amber-800">بانتظار التأكيد</span>
                    )}
                  </td>
                  <td className="px-4 py-3.5 text-center">
                    {item.status === 'pending' && (
                      <div className="flex items-center justify-center gap-2">
                        <button
                          onClick={() => handleApprovePayout(item.id)}
                          className="rounded-lg bg-emerald-600 px-3 py-1 text-[11px] font-bold text-white hover:bg-emerald-700"
                        >
                          موافقة وتحويل
                        </button>
                        <button
                          onClick={() => handleRejectPayout(item.id)}
                          className="rounded-lg bg-rose-600 px-3 py-1 text-[11px] font-bold text-white hover:bg-rose-700"
                        >
                          رفض
                        </button>
                      </div>
                    )}
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
