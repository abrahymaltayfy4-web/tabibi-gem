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

export const useAuthStore = create<AuthState>((set, get) => ({
  user: null,
  doctorProfile: null,
  token: localStorage.getItem('tabibi_doctor_token'),
  isAuthenticated: !!localStorage.getItem('tabibi_doctor_token'),
  isLoading: true,

  setAuth: (user, doctorProfile, token) => {
    localStorage.setItem('tabibi_doctor_token', token);
    set({
      user,
      doctorProfile,
      token,
      isAuthenticated: true,
      isLoading: false,
    });
  },

  setDoctorProfile: (doctorProfile) => {
    set({ doctorProfile });
  },

  logout: () => {
    localStorage.removeItem('tabibi_doctor_token');
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
