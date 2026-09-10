import React from 'react';
import { useAdminAuthStore } from '../../core/auth/useAdminAuthStore';
import type { PermissionKey } from '../../core/types/admin.types';
import { ShieldAlert } from 'lucide-react';

interface PermissionGuardProps {
  permission: PermissionKey;
  children: React.ReactNode;
}

export const PermissionGuard: React.FC<PermissionGuardProps> = ({ permission, children }) => {
  const { hasPermission } = useAdminAuthStore();

  if (!hasPermission(permission)) {
    return (
      <div className="flex flex-col items-center justify-center rounded-3xl border border-rose-200 bg-rose-50/50 p-12 text-center dark:border-rose-900/60 dark:bg-rose-950/20">
        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/60 dark:text-rose-300">
          <ShieldAlert className="h-8 w-8" />
        </div>
        <h2 className="mt-4 text-lg font-black text-rose-900 dark:text-rose-200">غير مصرح بالوصول (403 Permission Denied)</h2>
        <p className="mt-1 text-xs font-semibold text-rose-700 dark:text-rose-400 max-w-md">
          حسابك الإداري لا يملك الصلاحية المطلوبة (<span className="font-mono">{permission}</span>) لفتح أو تنفيذ هذا الموديول.
        </p>
      </div>
    );
  }

  return <>{children}</>;
};
