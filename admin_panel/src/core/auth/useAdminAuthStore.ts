import { create } from 'zustand';
import type { AdminUserEntity, PermissionKey } from '../types/admin.types';

interface AdminAuthState {
  admin: AdminUserEntity | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  setAdminAuth: (admin: AdminUserEntity, token: string) => void;
  logoutAdmin: () => void;
  hasPermission: (permission: PermissionKey) => boolean;
}

export const useAdminAuthStore = create<AdminAuthState>((set, get) => ({
  admin: null,
  token: localStorage.getItem('tabibi_admin_token'),
  isAuthenticated: !!localStorage.getItem('tabibi_admin_token'),
  isLoading: true,

  setAdminAuth: (admin, token) => {
    localStorage.setItem('tabibi_admin_token', token);
    set({
      admin,
      token,
      isAuthenticated: true,
      isLoading: false,
    });
  },

  logoutAdmin: () => {
    localStorage.removeItem('tabibi_admin_token');
    set({
      admin: null,
      token: null,
      isAuthenticated: false,
      isLoading: false,
    });
  },

  hasPermission: (permission) => {
    const admin = get().admin;
    if (!admin) return false;
    if (admin.role === 'super_admin') return true; // Super admin possesses all capabilities
    return admin.permissions?.includes(permission) || false;
  },
}));
