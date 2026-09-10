import React from 'react';
import { Navigate, Outlet } from 'react-router-dom';
import { useAdminAuthStore } from '../../core/auth/useAdminAuthStore';

export const AdminAuthGuard: React.FC = () => {
  const { isAuthenticated, isLoading } = useAdminAuthStore();

  if (isLoading) {
    return (
      <div className="flex h-screen w-full items-center justify-center bg-slate-950 text-white">
        <div className="flex flex-col items-center gap-3">
          <div className="h-10 w-10 animate-spin rounded-full border-4 border-[#29508B] border-t-transparent"></div>
          <p className="text-xs font-semibold text-slate-400">جاري التحقق من التواجد والسيادة الإدارية...</p>
        </div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  return <Outlet />;
};
