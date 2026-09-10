export type AdminRole = 
  | 'super_admin'
  | 'verification_officer'
  | 'financial_auditor'
  | 'content_moderator'
  | 'clinical_safety_officer';

export type PermissionKey =
  | 'doctor.view'
  | 'doctor.verify'
  | 'doctor.suspend'
  | 'patient.view'
  | 'patient.suspend'
  | 'appointment.view'
  | 'appointment.cancel'
  | 'payment.view'
  | 'payment.refund'
  | 'payout.manage'
  | 'medical_record.view'
  | 'audit.view'
  | 'settings.manage';

export interface AdminUserEntity {
  id: number;
  name: string;
  email: string;
  role: AdminRole;
  permissions: PermissionKey[];
  avatarUrl?: string;
  createdAt: string;
}

export interface DoctorVerificationItem {
  id: number;
  doctorId: number;
  doctorName: string;
  doctorEmail: string;
  specialtyNameAr: string;
  licenseNumber: string;
  submittedAt: string;
  status: 'pending' | 'under_review' | 'approved' | 'rejected' | 'suspended';
  licenseDocUrl?: string;
  degreeDocUrl?: string;
  identityDocUrl?: string;
  rejectionReason?: string;
  reviewedByAdmin?: string;
}

export interface PlatformFinancialMetrics {
  totalGrossRevenueYer: number;
  platformCommissionYer: number; // 15%
  doctorEarningsTotalYer: number;
  pendingPayoutsYer: number;
  completedPayoutsYer: number;
  refundsTotalYer: number;
}
