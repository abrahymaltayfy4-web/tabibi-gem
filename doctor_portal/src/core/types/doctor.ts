export type DoctorVerificationStatus = 
  | 'draft'
  | 'pending'
  | 'under_review'
  | 'approved'
  | 'rejected'
  | 'suspended';

export interface UserEntity {
  id: number;
  name: string;
  email: string;
  phone?: string;
  role: 'doctor' | 'patient' | 'admin';
  avatarUrl?: string;
  emailVerifiedAt?: string;
  createdAt: string;
}

export interface DoctorProfileEntity {
  id: number;
  userId: number;
  specialtyId: number;
  specialtyNameAr: string;
  specialtyNameEn: string;
  subSpecialty?: string;
  bioAr?: string;
  bioEn?: string;
  licenseNumber: string;
  qualificationAr: string;
  qualificationEn: string;
  experienceYears: number;
  consultationFeeYer: number;
  consultationDurationMinutes: number;
  verificationStatus: DoctorVerificationStatus;
  rejectionReason?: string;
  submittedAt?: string;
  verifiedAt?: string;
  user?: UserEntity;
}
