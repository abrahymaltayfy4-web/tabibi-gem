import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthGuard } from './AuthGuard';
import { VerificationGuard } from './VerificationGuard';
import { PublicGuard } from './PublicGuard';
import { DoctorDashboardLayout } from '../../shared/layouts/DoctorDashboardLayout';

import { LoginPage } from '../../features/auth/pages/LoginPage';
import { RegisterPage } from '../../features/auth/pages/RegisterPage';
import { OnboardingPage } from '../../features/onboarding/pages/OnboardingPage';
import { VerificationStatusPage } from '../../features/verification/pages/VerificationStatusPage';
import { DashboardPage } from '../../features/dashboard/pages/DashboardPage';
import { ProfilePage } from '../../features/profile/pages/ProfilePage';
import { AvailabilityPage } from '../../features/availability/pages/AvailabilityPage';
import { AppointmentsPage } from '../../features/appointments/pages/AppointmentsPage';
import { CalendarPage } from '../../features/calendar/pages/CalendarPage';
import { ConsultationsPage } from '../../features/consultation/pages/ConsultationsPage';
import { MedicalRecordsPage } from '../../features/medical_records/pages/MedicalRecordsPage';
import { PrescriptionsPage } from '../../features/prescriptions/pages/PrescriptionsPage';
import { EarningsPage } from '../../features/earnings/pages/EarningsPage';
import { ReviewsPage } from '../../features/reviews/pages/ReviewsPage';

// Placeholder view for Settings
const FeaturePlaceholder: React.FC<{ title: string; description: string }> = ({ title, description }) => (
  <div className="rounded-2xl border border-slate-200/80 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-3">
    <h2 className="text-xl font-bold text-slate-900 dark:text-slate-100">{title}</h2>
    <p className="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{description}</p>
  </div>
);

export const AppRouter: React.FC = () => {
  return (
    <BrowserRouter>
      <Routes>
        {/* Public Routes */}
        <Route element={<PublicGuard />}>
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
        </Route>

        {/* Protected Doctor Routes */}
        <Route element={<AuthGuard />}>
          <Route path="/onboarding" element={<OnboardingPage />} />
          <Route path="/verification-status" element={<VerificationStatusPage />} />

          <Route element={<VerificationGuard />}>
            <Route element={<DoctorDashboardLayout />}>
              <Route path="/dashboard" element={<DashboardPage />} />
              <Route path="/profile" element={<ProfilePage />} />
              <Route path="/availability" element={<AvailabilityPage />} />
              <Route path="/appointments" element={<AppointmentsPage />} />
              <Route path="/calendar" element={<CalendarPage />} />
              <Route path="/consultations" element={<ConsultationsPage />} />
              <Route path="/medical-records" element={<MedicalRecordsPage />} />
              <Route path="/prescriptions" element={<PrescriptionsPage />} />
              <Route path="/earnings" element={<EarningsPage />} />
              <Route path="/reviews" element={<ReviewsPage />} />

              <Route
                path="/settings"
                element={<FeaturePlaceholder title="إعدادات الحساب والأمان" description="إدارة كلمة المرور، تفضيلات التنبيهات، اختيار اللغات، والجلسات النشطة." />}
              />
            </Route>
          </Route>
        </Route>

        {/* Fallback Catch-All Route */}
        <Route path="*" element={<Navigate to="/dashboard" replace />} />
      </Routes>
    </BrowserRouter>
  );
};
