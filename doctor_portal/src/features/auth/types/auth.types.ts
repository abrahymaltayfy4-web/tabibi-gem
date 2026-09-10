import type { UserEntity, DoctorProfileEntity } from '../../../core/types/doctor';

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterDoctorRequest {
  name: string;
  email: string;
  phone: string;
  password: string;
  passwordConfirmation: string;
  specialtyId: number;
  licenseNumber: string;
}

export interface AuthResponseData {
  user: UserEntity;
  doctorProfile: DoctorProfileEntity | null;
  token: string;
}
