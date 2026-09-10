import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AdminAuthGuard } from './AdminAuthGuard';
import { PermissionGuard } from './PermissionGuard';
import { AdminPublicGuard } from './AdminPublicGuard';
import { AdminDashboardLayout } from '../../shared/layouts/AdminDashboardLayout';

import { AdminLoginPage } from '../../features/auth/pages/AdminLoginPage';
import { AdminDashboardPage } from '../../features/dashboard/pages/AdminDashboardPage';
import { DoctorVerificationsPage } from '../../features/verifications/pages/DoctorVerificationsPage';
import { AdminDoctorsPage } from '../../features/doctors/pages/AdminDoctorsPage';
import { AdminPatientsPage } from '../../features/patients/pages/AdminPatientsPage';
import { AdminFinancialsPage } from '../../features/financials/pages/AdminFinancialsPage';
import { AdminSpecialtiesPage } from '../../features/specialties/pages/AdminSpecialtiesPage';
import { AdminAppointmentsPage } from '../../features/appointments/pages/AdminAppointmentsPage';
import { AdminAuditLogsPage } from '../../features/audit_logs/pages/AdminAuditLogsPage';

const FeaturePlaceholder: React.FC<{ title: string; description: string }> = ({ title, description }) => (
  <div className="rounded-2xl border border-slate-800 bg-slate-900 p-8 space-y-3">
    <h2 className="text-xl font-bold text-white">{title}</h2>
    <p className="text-xs text-slate-400 leading-relaxed">{description}</p>
  </div>
);

export const AppRouter: React.FC = () => {
  return (
    <BrowserRouter>
      <Routes>
        {/* Public Admin Routes */}
        <Route element={<AdminPublicGuard />}>
          <Route path="/login" element={<AdminLoginPage />} />
        </Route>

        {/* Protected Admin Routes */}
        <Route element={<AdminAuthGuard />}>
          <Route element={<AdminDashboardLayout />}>
            <Route path="/dashboard" element={<AdminDashboardPage />} />

            <Route
              path="/verifications"
              element={
                <PermissionGuard permission="doctor.verify">
                  <DoctorVerificationsPage />
                </PermissionGuard>
              }
            />

            <Route
              path="/doctors"
              element={
                <PermissionGuard permission="doctor.view">
                  <AdminDoctorsPage />
                </PermissionGuard>
              }
            />

            <Route
              path="/patients"
              element={
                <PermissionGuard permission="patient.view">
                  <AdminPatientsPage />
                </PermissionGuard>
              }
            />

            <Route
              path="/specialties"
              element={
                <PermissionGuard permission="settings.manage">
                  <AdminSpecialtiesPage />
                </PermissionGuard>
              }
            />

            <Route
              path="/appointments"
              element={
                <PermissionGuard permission="appointment.view">
                  <AdminAppointmentsPage />
                </PermissionGuard>
              }
            />

            <Route
              path="/financials"
              element={
                <PermissionGuard permission="payment.view">
                  <AdminFinancialsPage />
                </PermissionGuard>
              }
            />

            <Route
              path="/audit-logs"
              element={
                <PermissionGuard permission="audit.view">
                  <AdminAuditLogsPage />
                </PermissionGuard>
              }
            />

            <Route
              path="/medical-audit"
              element={
                <PermissionGuard permission="medical_record.view">
                  <AdminAuditLogsPage />
                </PermissionGuard>
              }
            />

            <Route path="/reports" element={<FeaturePlaceholder title="التقارير التحليلية والنمو" description="استعراض مؤشرات النمو، الأداء المالي، الاستشارات المكتملة، وتصنيف الأطباء بالجمهورية اليمنية." />} />
            <Route path="/moderation" element={<FeaturePlaceholder title="الرقابة على المحتوى والتقييمات" description="مراجعة التقييمات المعلمة كبلاغات وإخفاء النصوص المخالفة لقواعد المنصة." />} />
            <Route path="/ai-governance" element={<FeaturePlaceholder title="حوكمة الذكاء الاصطناعي والسلامة" description="تتبع مؤشرات المساعد الذكي للأعراض، خوادم النموذج، وتنبيهات الطوارئ." />} />
            <Route path="/notifications" element={<FeaturePlaceholder title="مركز التنبيهات العام" description="إرسال التنبيهات العامة والخاصة عبر FCM والبرودكاست." />} />
            <Route path="/rbac" element={<FeaturePlaceholder title="الأدوار والصلاحيات (Spatie RBAC)" description="إدارة أدوار المسئولين والموظفين وتخصيص الصلاحيات الدقيقة." />} />
            <Route path="/settings" element={<FeaturePlaceholder title="إعدادات المنصة الكلية" description="إدارة نسبة العمولة (15%)، السياسات، ووسائل الدفع الرقمية." />} />
          </Route>
        </Route>

        <Route path="*" element={<Navigate to="/dashboard" replace />} />
      </Routes>
    </BrowserRouter>
  );
};
