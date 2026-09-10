import React, { useState } from 'react';
import type { DoctorVerificationItem } from '../../../core/types/admin.types';
import { CheckCircle2, XCircle, FileText, Search } from 'lucide-react';

export const DoctorVerificationsPage: React.FC = () => {
  const [selectedStatus, setSelectedStatus] = useState<string>('pending');
  const [searchQuery, setSearchQuery] = useState('');
  const [activeModalItem, setActiveModalItem] = useState<DoctorVerificationItem | null>(null);
  const [rejectionReason, setRejectionReason] = useState('');

  const [verifications, setVerifications] = useState<DoctorVerificationItem[]>([
    {
      id: 501,
      doctorId: 12,
      doctorName: 'د. خالد محمد عبدالله',
      doctorEmail: 'dr.khaled@example.com',
      specialtyNameAr: 'أمراض الباطنية والجهاز الهضمي',
      licenseNumber: 'YEM-LIC-77492',
      submittedAt: '10 سبتمبر 2026',
      status: 'pending',
      licenseDocUrl: 'license_yem_77492.pdf',
      degreeDocUrl: 'degree_phd_med.pdf',
      identityDocUrl: 'identity_national_card.jpg',
    },
    {
      id: 502,
      doctorId: 14,
      doctorName: 'د. سلوى عبدالجليل المقطري',
      doctorEmail: 'dr.salwa@example.com',
      specialtyNameAr: 'أمراض النساء والتوليد والعقم',
      licenseNumber: 'YEM-LIC-88319',
      submittedAt: '09 سبتمبر 2026',
      status: 'pending',
      licenseDocUrl: 'license_yem_88319.pdf',
    },
    {
      id: 503,
      doctorId: 1,
      doctorName: 'د. أحمد علي البعداني',
      doctorEmail: 'doctor@tabibi.ye',
      specialtyNameAr: 'استشاري أمراض الباطنية والقلب',
      licenseNumber: 'YEM-MED-8842',
      submittedAt: '01 سبتمبر 2026',
      status: 'approved',
      reviewedByAdmin: 'المهندس الإداري الأعلى',
    },
  ]);

  const filteredItems = verifications.filter((item) => {
    const matchesSearch = item.doctorName.includes(searchQuery) || item.licenseNumber.includes(searchQuery);
    const matchesStatus = selectedStatus === 'all' || item.status === selectedStatus;
    return matchesSearch && matchesStatus;
  });

  const handleApprove = (id: number) => {
    setVerifications(
      verifications.map((item) =>
        item.id === id
          ? { ...item, status: 'approved', reviewedByAdmin: 'المهندس الإداري الأعلى' }
          : item
      )
    );
    setActiveModalItem(null);
  };

  const handleReject = (id: number) => {
    if (!rejectionReason.trim()) return;
    setVerifications(
      verifications.map((item) =>
        item.id === id
          ? { ...item, status: 'rejected', rejectionReason, reviewedByAdmin: 'المهندس الإداري الأعلى' }
          : item
      )
    );
    setRejectionReason('');
    setActiveModalItem(null);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-800 pb-4">
        <div>
          <h1 className="text-2xl font-black text-white">توثيق وتراخيص الأطباء المعتمدة</h1>
          <p className="text-xs font-semibold text-slate-400">فحص التراخيص والشهادات والهوية الوطنية وتوثيق العيادات الرقمية</p>
        </div>
      </div>

      {/* Filter and Search Bar */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative flex-1 max-w-md">
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="البحث باسم الطبيب أو رقم الترخيص..."
            className="w-full rounded-xl border border-slate-800 bg-slate-900 py-2.5 pl-10 pr-4 text-xs font-medium text-white focus:border-blue-500 focus:outline-none"
          />
          <Search className="absolute left-3 top-3 h-4 w-4 text-slate-500" />
        </div>

        <div className="flex items-center gap-2 overflow-x-auto">
          {[
            { id: 'all', label: 'الكل' },
            { id: 'pending', label: 'طلبات جديدة (Pending)' },
            { id: 'approved', label: 'موثق ومفعل (Approved)' },
            { id: 'rejected', label: 'مرفوض (Rejected)' },
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setSelectedStatus(tab.id)}
              className={`rounded-xl px-3.5 py-2 text-xs font-bold whitespace-nowrap transition-colors ${
                selectedStatus === tab.id
                  ? 'bg-[#29508B] text-white shadow-sm'
                  : 'bg-slate-900 text-slate-400 border border-slate-800 hover:text-white'
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>
      </div>

      {/* Verifications Data Table */}
      <div className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-right text-xs">
            <thead className="bg-slate-950 text-slate-400 border-b border-slate-800 font-bold">
              <tr>
                <th className="px-5 py-3.5">الطبيب المتقدم</th>
                <th className="px-5 py-3.5">التخصص والطبيعة</th>
                <th className="px-5 py-3.5">رقم الترخيص</th>
                <th className="px-5 py-3.5">تاريخ التقديم</th>
                <th className="px-5 py-3.5">الحالة</th>
                <th className="px-5 py-3.5 text-center">فحص الوثائق والإجراء</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/60 font-medium">
              {filteredItems.map((item) => (
                <tr key={item.id} className="hover:bg-slate-800/40 transition-colors">
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-3">
                      <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-950 text-blue-300 font-bold text-xs border border-blue-800">
                        {item.doctorName.charAt(0)}
                      </div>
                      <div>
                        <p className="font-bold text-white">{item.doctorName}</p>
                        <p className="text-[11px] text-slate-400">{item.doctorEmail}</p>
                      </div>
                    </div>
                  </td>
                  <td className="px-5 py-4 text-slate-300">{item.specialtyNameAr}</td>
                  <td className="px-5 py-4 font-mono font-bold text-blue-400">{item.licenseNumber}</td>
                  <td className="px-5 py-4 text-slate-400">{item.submittedAt}</td>
                  <td className="px-5 py-4">
                    {item.status === 'approved' ? (
                      <span className="rounded-full bg-emerald-950/80 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-800">موثق ومعتمد</span>
                    ) : item.status === 'rejected' ? (
                      <span className="rounded-full bg-rose-950/80 px-3 py-1 text-xs font-semibold text-rose-300 border border-rose-800">مرفوض</span>
                    ) : (
                      <span className="rounded-full bg-amber-950/80 px-3 py-1 text-xs font-semibold text-amber-300 border border-amber-800">قيد الفحص الدقيق</span>
                    )}
                  </td>
                  <td className="px-5 py-4 text-center">
                    <button
                      onClick={() => setActiveModalItem(item)}
                      className="inline-flex items-center gap-1.5 rounded-xl bg-[#29508B] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-blue-700"
                    >
                      <FileText className="h-3.5 w-3.5" />
                      فحص التراخيص
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Inspection Modal */}
      {activeModalItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm">
          <div className="w-full max-w-xl space-y-6 rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-2xl text-right">
            <div className="flex items-center justify-between border-b border-slate-800 pb-3">
              <h2 className="text-base font-black text-white">فحص وثائق: {activeModalItem.doctorName}</h2>
              <button onClick={() => setActiveModalItem(null)} className="text-slate-400 hover:text-white">✕</button>
            </div>

            <div className="space-y-3 text-xs">
              <div className="rounded-xl bg-slate-950 p-3 space-y-1">
                <p className="text-slate-400">رقم ترخيص مزاولة المهنة:</p>
                <p className="font-mono font-bold text-blue-400 text-sm">{activeModalItem.licenseNumber}</p>
              </div>

              <div className="space-y-2">
                <p className="font-bold text-slate-300">الوثائق والتراخيص المرفوعة المأذونة:</p>
                <div className="space-y-2">
                  <div className="flex items-center justify-between rounded-xl bg-slate-950 p-3 border border-slate-800">
                    <span className="font-semibold text-slate-300">نسخة ترخيص مزاولة المهنة (PDF)</span>
                    <button className="text-blue-400 hover:underline font-bold">معاينة عبر رابط مؤقت مشفر</button>
                  </div>
                  <div className="flex items-center justify-between rounded-xl bg-slate-950 p-3 border border-slate-800">
                    <span className="font-semibold text-slate-300">شهادة الدكتوراة / البكالوريوس (PDF)</span>
                    <button className="text-blue-400 hover:underline font-bold">معاينة عبر رابط مؤقت مشفر</button>
                  </div>
                </div>
              </div>

              {activeModalItem.status === 'pending' && (
                <div className="space-y-2 pt-2">
                  <label className="font-bold text-slate-300">سبب الرفض (إجباري في حال عدم قبول التوثيق):</label>
                  <textarea
                    rows={2}
                    value={rejectionReason}
                    onChange={(e) => setRejectionReason(e.target.value)}
                    placeholder="تدوين ملاحظات الرفض الصريحة التي ستصل للطبيب..."
                    className="w-full rounded-xl border border-slate-800 bg-slate-950 p-3 text-xs text-white focus:border-blue-500 focus:outline-none"
                  ></textarea>
                </div>
              )}
            </div>

            {activeModalItem.status === 'pending' && (
              <div className="flex items-center justify-end gap-3 pt-2">
                <button
                  onClick={() => handleReject(activeModalItem.id)}
                  className="flex items-center gap-1.5 rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-rose-700"
                >
                  <XCircle className="h-4 w-4" />
                  رفض التوثيق
                </button>
                <button
                  onClick={() => handleApprove(activeModalItem.id)}
                  className="flex items-center gap-1.5 rounded-xl bg-emerald-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 shadow-md shadow-emerald-600/20"
                >
                  <CheckCircle2 className="h-4 w-4" />
                  اعتماد وتوثيق الحساب
                </button>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
};
