<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'patient' => 'Patient user with booking and EHR access',
            'doctor' => 'Verified doctor with online clinic and clinical notes access',
            'admin' => 'Platform administrator with verification and refund management',
            'super_admin' => 'Super administrator with full RBAC and system governance',
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'sanctum', 'description' => $description]
            );
        }

        $permissions = [
            'appointment.create' => 'Booking',
            'appointment.cancel' => 'Booking',
            'appointment.view' => 'Booking',
            'doctor.availability.manage' => 'Doctor Clinic',
            'doctor.verify' => 'Admin Governance',
            'ehr.read' => 'Clinical Access',
            'ehr.write' => 'Clinical Access',
            'prescription.create' => 'Clinical Access',
            'payment.refund' => 'Financial Admin',
            'role.manage' => 'Super Admin',
        ];

        foreach ($permissions as $name => $group) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'sanctum', 'group_name' => $group]
            );
        }

        // Assign permissions to roles
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->permissions()->sync(Permission::all());
        }

        $doctorRole = Role::where('name', 'doctor')->first();
        if ($doctorRole) {
            $doctorPermissions = Permission::whereIn('name', [
                'doctor.availability.manage',
                'appointment.view',
                'ehr.read',
                'ehr.write',
                'prescription.create',
            ])->get();
            $doctorRole->permissions()->sync($doctorPermissions);
        }

        $patientRole = Role::where('name', 'patient')->first();
        if ($patientRole) {
            $patientPermissions = Permission::whereIn('name', [
                'appointment.create',
                'appointment.cancel',
                'appointment.view',
                'ehr.read',
            ])->get();
            $patientRole->permissions()->sync($patientPermissions);
        }
    }
}
