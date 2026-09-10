import React, { useState } from 'react';
import { ShieldAlert, FileCheck } from 'lucide-react';

export const AdminAuditLogsPage: React.FC = () => {
  const [activeTab, setActiveTab] = useState<'system' | 'medical'>('system');

  const systemLogs = [
    { id: 'LOG-901', admin: 'المهندس الإداري الأعلى', action: 'اعتماد وتوثيق حساب طبيب', entity: 'د. أحمد علي البعداني', ip: '197.160.2.14', time: '10 سبتمبر 2026 - 11:30 ص' },
    { id: 'LOG-899', admin: 'المدقق المالي', action: 'المصادقة على تحويل أرباح 100,000 YER', entity: 'REQ-101 (د. سلوى)', ip: '197.160.2.20', time: '09 سبتمبر 2026 - 04:15 م' },
    { id: 'LOG-880', admin: 'مشرف المحتوى', action: 'إخفاء تقييم مخالف وسيئ النية', entity: 'Review #182', ip: '197.160.5.88', time: '05 سبتمبر 2026 - 02:00 م' },
  ];

  const medicalLogs = [
    { id: 'MED-042', admin: 'مسؤول الرقابة السريرية', patient: 'محمد عبدالله باوزير', reason: 'فحص بلاغ حول وصفة علاجية مكررة', accessTime: '10 سبتمبر 2026 - 10:45 ص' },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800 pb-4">
        <div>
          <h1 className="text-2xl font-black text-white">سجلات التتبع والرقابة السيادية (Audit Logs)</h1>
          <p className="text-xs font-semibold text-slate-400">تتبع كافة تحركات مسؤولي الإدارة وقيد الوصول الاستثنائي للسجلات الطبية</p>
        </div>

        <div className="flex rounded-xl border border-slate-800 bg-slate-900 p-1">
          <button
            onClick={() => setActiveTab('system')}
            className={`rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all ${
              activeTab === 'system' ? 'bg-[#29508B] text-white shadow-sm' : 'text-slate-400 hover:text-white'
            }`}
          >
            سجلات التحركات الإدارية
          </button>
          <button
            onClick={() => setActiveTab('medical')}
            className={`rounded-lg px-3.5 py-1.5 text-xs font-bold transition-all ${
              activeTab === 'medical' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'
            }`}
          >
            تتبع الوصول الطبي (EHR Audit)
          </button>
        </div>
      </div>

      {activeTab === 'system' ? (
        <div className="rounded-2xl border border-slate-800 bg-slate-900 p-6 space-y-4">
          <h2 className="text-sm font-bold text-white flex items-center gap-2">
            <ShieldAlert className="h-4 w-4 text-blue-400" />
            سجل تحركات وقرارات مسؤولي النظام
          </h2>

          <div className="overflow-x-auto">
            <table className="w-full text-right text-xs">
              <thead className="bg-slate-950 text-slate-400 border-b border-slate-800 font-bold">
                <tr>
                  <th className="px-4 py-3">معرف السجل</th>
                  <th className="px-4 py-3">المسؤول المنفذ</th>
                  <th className="px-4 py-3">نوع الإجراء الصادر</th>
                  <th className="px-4 py-3">الطرف المستهدف</th>
                  <th className="px-4 py-3">عنوان IP</th>
                  <th className="px-4 py-3">التاريخ والوقت</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800 font-medium text-slate-300">
                {systemLogs.map((log) => (
                  <tr key={log.id} className="hover:bg-slate-800/40">
                    <td className="px-4 py-3.5 font-mono font-bold text-blue-400">{log.id}</td>
                    <td className="px-4 py-3.5 font-bold text-white">{log.admin}</td>
                    <td className="px-4 py-3.5 text-emerald-400 font-semibold">{log.action}</td>
                    <td className="px-4 py-3.5">{log.entity}</td>
                    <td className="px-4 py-3.5 font-mono text-slate-400">{log.ip}</td>
                    <td className="px-4 py-3.5 text-slate-400">{log.time}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      ) : (
        <div className="rounded-2xl border border-purple-900/60 bg-slate-900 p-6 space-y-4">
          <div className="flex items-center gap-2 text-purple-300 font-bold text-sm">
            <FileCheck className="h-5 w-5 text-purple-400" />
            سجل الوصول المشروط والاستثنائي للسجلات الطبية السريرية (Medical Access Audit)
          </div>
          <p className="text-xs text-slate-400 leading-relaxed">
            وفقاً لسياسة الخصوصية، يتم قيد وتسجيل أية محاولة للاطلاع الاستثنائي على سجلات مرضى منصة طبيبي لمنع أي انتهاك للسرية الطبية.
          </p>

          <div className="overflow-x-auto">
            <table className="w-full text-right text-xs">
              <thead className="bg-slate-950 text-slate-400 border-b border-slate-800 font-bold">
                <tr>
                  <th className="px-4 py-3">معرف القيد</th>
                  <th className="px-4 py-3">المسؤول الفاحص</th>
                  <th className="px-4 py-3">المريض المستهدف</th>
                  <th className="px-4 py-3">سبب الفحص الاستثنائي المكتوب</th>
                  <th className="px-4 py-3">وقت وتاريخ الدخول</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800 font-medium text-slate-300">
                {medicalLogs.map((med) => (
                  <tr key={med.id} className="hover:bg-slate-800/40">
                    <td className="px-4 py-3.5 font-mono font-bold text-purple-400">{med.id}</td>
                    <td className="px-4 py-3.5 font-bold text-white">{med.admin}</td>
                    <td className="px-4 py-3.5 text-slate-200">{med.patient}</td>
                    <td className="px-4 py-3.5 text-amber-300">{med.reason}</td>
                    <td className="px-4 py-3.5 text-slate-400">{med.accessTime}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
};
