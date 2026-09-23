import { create } from 'zustand';
import type { DoctorProfileEntity, UserEntity, DoctorVerificationStatus } from '../types/doctor';

interface AuthState {
  user: UserEntity | null;
  doctorProfile: DoctorProfileEntity | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  setAuth: (user: UserEntity, doctorProfile: DoctorProfileEntity | null, token: string) => void;
  setDoctorProfile: (profile: DoctorProfileEntity) => void;
  logout: () => void;
  setLoading: (loading: boolean) => void;
  getVerificationStatus: () => DoctorVerificationStatus;
}

const getStoredItem = <T>(key: string): T | null => {
  try {
    const item = localStorage.getItem(key);
    return item ? JSON.parse(item) : null;
  } catch {
    return null;
  }
};

export const useAuthStore = create<AuthState>((set, get) => ({
  user: getStoredItem<UserEntity>('tabibi_doctor_user'),
  doctorProfile: getStoredItem<DoctorProfileEntity>('tabibi_doctor_profile'),
  token: localStorage.getItem('tabibi_doctor_token'),
  isAuthenticated: !!localStorage.getItem('tabibi_doctor_token'),
  isLoading: false,

  setAuth: (user, doctorProfile, token) => {
    localStorage.setItem('tabibi_doctor_token', token);
    if (user) localStorage.setItem('tabibi_doctor_user', JSON.stringify(user));
    if (doctorProfile) localStorage.setItem('tabibi_doctor_profile', JSON.stringify(doctorProfile));
    set({
      user,
      doctorProfile,
      token,
      isAuthenticated: true,
      isLoading: false,
    });
  },

  setDoctorProfile: (doctorProfile) => {
    if (doctorProfile) localStorage.setItem('tabibi_doctor_profile', JSON.stringify(doctorProfile));
    set({ doctorProfile });
  },

  logout: () => {
    localStorage.removeItem('tabibi_doctor_token');
    localStorage.removeItem('tabibi_doctor_user');
    localStorage.removeItem('tabibi_doctor_profile');
    set({
      user: null,
      doctorProfile: null,
      token: null,
      isAuthenticated: false,
      isLoading: false,
    });
  },

  setLoading: (isLoading) => set({ isLoading }),

  getVerificationStatus: () => {
    return get().doctorProfile?.verificationStatus || 'draft';
  },
}));

