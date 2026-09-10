import React from 'react';
import { Navigate, Outlet } from 'react-router-dom';
import { useAuthStore } from '../../core/auth/useAuthStore';

export const VerificationGuard: React.FC = () => {
  const { doctorProfile } = useAuthStore();

  const status = doctorProfile?.verificationStatus || 'approved'; // Default to approved in dev fallback if not set

  if (status === 'draft') {
    return <Navigate to="/onboarding" replace />;
  }

  if (status === 'pending' || status === 'under_review') {
    return <Navigate to="/verification-status" replace />;
  }

  if (status === 'suspended') {
    return <Navigate to="/account-suspended" replace />;
  }

  return <Outlet />;
};
