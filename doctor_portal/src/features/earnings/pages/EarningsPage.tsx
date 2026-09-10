import React, { useState } from 'react';
import { Wallet, Banknote, ShieldCheck, History, Send } from 'lucide-react';

export const EarningsPage: React.FC = () => {
  const [payoutAmount, setPayoutAmount] = useState('');
  const [payoutSuccess, setPayoutSuccess] = useState(false);

  const stats = {
    grossRevenueYer: 320000,
    platformCommissionYer: 48000, // 15%
    netEarningsYer: 272000,
    availableBalanceYer: 245000,
    pendingBalanceYer: 27000,
  };

  const transactions = [
    { id: 'TX-901', patient: 'محمد عبدالله باوزير', date: '10 سبتمبر 2026', grossFee: 15000, commission: 2250, net: 12750, status: 'مكتملة ومتاحة' },
    { id: 'TX-902', patient: 'فاطمة أحمد سالم', date: '10 سبتمبر 2026', grossFee: 15000, commission: 2250, net: 12750, status: 'قيد التعليق (24 ساعة)' },
    { id: 'TX-890', patient: 'عمر خالد العمودي', date: '08 سبتمبر 2026', grossFee: 15000, commission: 2250, net: 12750, status: 'مكتملة ومتاحة' },
    { id: 'TX-882', patient: 'سارة محسن الدبعي', date: '05 سبتمبر 2026', grossFee: 15000, commission: 2250, net: 12750, status: 'تم السحب للحساب البنكي' },
  ];

  const handlePayoutSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setPayoutSuccess(true);
    setPayoutAmount('');
    setTimeout(() => setPayoutSuccess(false), 4000);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200/80 pb-4 dark:border-slate-800">
        <div>
          <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">سجل الأرباح والمحافظ المالية (YER)</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">استعراض دخل الاستشارات، خصم عمولة المنصة (15%)، وطلب سحب الرصيد</p>
        </div>
      </div>

      {/* Financial Overview Stat Cards */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">الرصيد المتاح للسحب</span>
            <div className="rounded-xl bg-emerald-50 p-2.5 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
              <Wallet className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3">
            <span className="text-2xl font-black text-slate-900 dark:text-slate-100">{stats.availableBalanceYer.toLocaleString()}</span>
            <span className="mr-1 text-xs font-bold text-slate-500">ر.ي YER</span>
          </div>
        </div>

        <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">إجمالي الأرباح الصافية</span>
            <div className="rounded-xl bg-blue-50 p-2.5 text-[#29508B] dark:bg-blue-950 dark:text-blue-400">
              <Banknote className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3">
            <span className="text-2xl font-black text-slate-900 dark:text-slate-100">{stats.netEarningsYer.toLocaleString()}</span>
            <span className="mr-1 text-xs font-bold text-slate-500">ر.ي YER</span>
          </div>
        </div>

        <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">عمولة المنصة (15%)</span>
            <div className="rounded-xl bg-purple-50 p-2.5 text-purple-600 dark:bg-purple-950 dark:text-purple-400">
              <ShieldCheck className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3">
            <span className="text-2xl font-black text-slate-900 dark:text-slate-100">{stats.platformCommissionYer.toLocaleString()}</span>
            <span className="mr-1 text-xs font-bold text-slate-500">ر.ي YER</span>
          </div>
        </div>

        <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold text-slate-500 dark:text-slate-400">رصيد قيد التسوية</span>
            <div className="rounded-xl bg-amber-50 p-2.5 text-amber-600 dark:bg-amber-950 dark:text-amber-400">
              <History className="h-5 w-5" />
            </div>
          </div>
          <div className="mt-3">
            <span className="text-2xl font-black text-slate-900 dark:text-slate-100">{stats.pendingBalanceYer.toLocaleString()}</span>
            <span className="mr-1 text-xs font-bold text-slate-500">ر.ي YER</span>
          </div>
        </div>
      </div>

      {/* Payout Form & Transactions Split */}
      <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {/* Payout Request Box */}
        <div className="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
          <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
            <Send className="h-4 w-4 text-[#29508B]" />
            طلب سحب الرصيد للحساب البنكي
          </h2>

          {payoutSuccess && (
            <div className="rounded-xl bg-emerald-50 p-3 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200">
              تم إرسال طلب السحب بنجاح إلى الإدارة المالية!
            </div>
          )}

          <form onSubmit={handlePayoutSubmit} className="space-y-3">
            <div className="space-y-1">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">المبلغ المراد سحبه (YER)</label>
              <input
                type="number"
                required
                max={stats.availableBalanceYer}
                value={payoutAmount}
                onChange={(e) => setPayoutAmount(e.target.value)}
                placeholder="مثال: 100000"
                className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:outline-none dark:border-slate-800 dark:bg-slate-800 dark:text-slate-100"
              />
            </div>

            <div className="space-y-1">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">وسيلة التجميع/البنك اليمني</label>
              <select className="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:outline-none dark:border-slate-800 dark:bg-slate-800 dark:text-slate-100">
                <option>بنك التضامن الإسلامي</option>
                <option>بنك الكريمي للتمويل الأصغر</option>
                <option>محفظة جيب / جوال بنك</option>
              </select>
            </div>

            <button
              type="submit"
              className="w-full rounded-xl bg-[#29508B] py-3 text-xs font-bold text-white shadow-md hover:bg-[#1E3D6B]"
            >
              تأكيد طلب تحويل الأرباح
            </button>
          </form>
        </div>

        {/* Transactions Table */}
        <div className="lg:col-span-2 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
          <h2 className="text-sm font-bold text-slate-900 dark:text-slate-100">سجل المعاملات والاستشارات الأخيرة</h2>

          <div className="divide-y divide-slate-100 dark:divide-slate-800">
            {transactions.map((tx) => (
              <div key={tx.id} className="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between text-xs">
                <div>
                  <p className="font-bold text-slate-900 dark:text-slate-100">{tx.patient}</p>
                  <p className="text-[11px] text-slate-400">{tx.id} • {tx.date}</p>
                </div>

                <div className="flex items-center gap-4">
                  <div className="text-right">
                    <p className="font-bold text-emerald-600 dark:text-emerald-400">+{tx.net.toLocaleString()} ر.ي</p>
                    <p className="text-[10px] text-slate-400">الخصم (15%): -{tx.commission.toLocaleString()} ر.ي</p>
                  </div>
                  <span className="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                    {tx.status}
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};
