import React from 'react';
import { Navigate, Outlet } from 'react-router-dom';
import { useAdminAuthStore } from '../../core/auth/useAdminAuthStore';

export const AdminPublicGuard: React.FC = () => {
  const { isAuthenticated } = useAdminAuthStore();

  if (isAuthenticated) {
    return <Navigate to="/dashboard" replace />;
  }

  return <Outlet />;
};
