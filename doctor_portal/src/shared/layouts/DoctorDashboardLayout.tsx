import React, { useState } from 'react';
import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { 
  LayoutDashboard, 
  CalendarDays, 
  Clock, 
  Users, 
  Video, 
  FileText, 
  Pill, 
  Wallet, 
  Star, 
  UserCheck, 
  Settings, 
  LogOut, 
  Menu, 
  X, 
  Bell, 
  ShieldCheck,
  Stethoscope
} from 'lucide-react';
import { useAuthStore } from '../../core/auth/useAuthStore';

interface NavItem {
  label: string;
  path: string;
  icon: React.ElementType;
}

const navItems: NavItem[] = [
  { label: 'لوحة التحكم', path: '/dashboard', icon: LayoutDashboard },
  { label: 'المواعيد والحجوزات', path: '/appointments', icon: Users },
  { label: 'التقويم الطبي', path: '/calendar', icon: CalendarDays },
  { label: 'جدول الأوقات المتاحة', path: '/availability', icon: Clock },
  { label: 'غرفة الاستشارة المباشرة', path: '/consultations', icon: Video },
  { label: 'السجلات الطبية', path: '/medical-records', icon: FileText },
  { label: 'الوصفات الإلكترونية', path: '/prescriptions', icon: Pill },
  { label: 'الأرباح والسحوبات', path: '/earnings', icon: Wallet },
  { label: 'تقييمات المرضى', path: '/reviews', icon: Star },
  { label: 'الملف المهني والتوثيق', path: '/profile', icon: UserCheck },
  { label: 'إعدادات الحساب', path: '/settings', icon: Settings },
];

export const DoctorDashboardLayout: React.FC = () => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const { user, doctorProfile, logout } = useAuthStore();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const getStatusBadge = () => {
    const status = doctorProfile?.verificationStatus || 'approved';
    switch (status) {
      case 'approved':
        return (
          <span className="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
            <ShieldCheck className="h-3.5 w-3.5" />
            حساب موثق ومفعل
          </span>
        );
      case 'pending':
      case 'under_review':
        return (
          <span className="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
            قيد مراجعة التوثيق
          </span>
        );
      default:
        return null;
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 font-sans text-slate-800 dark:bg-[#0C162A] dark:text-slate-100 flex flex-col">
      {/* Top Glass Navbar */}
      <header className="sticky top-0 z-40 flex h-16 w-full items-center justify-between border-b border-slate-200/80 bg-white/80 px-4 backdrop-blur-md dark:border-slate-800/80 dark:bg-[#0C162A]/80 md:px-6">
        <div className="flex items-center gap-3">
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="rounded-lg p-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 lg:hidden"
            aria-label="تنويل القائمة الجانبية"
          >
            {sidebarOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
          </button>

          <div className="flex items-center gap-2.5">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#29508B] to-[#1E3D6B] text-white shadow-md shadow-[#29508B]/20">
              <Stethoscope className="h-5 w-5" />
            </div>
            <div>
              <span className="text-lg font-bold tracking-tight text-[#29508B] dark:text-blue-400">طبيبي</span>
              <span className="mr-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400">بوابة الطبيب</span>
            </div>
          </div>
        </div>

        <div className="flex items-center gap-4">
          {getStatusBadge()}

          <button
            className="relative rounded-full p-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors"
            aria-label="الإشعارات"
          >
            <Bell className="h-5 w-5" />
            <span className="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
          </button>

          <div className="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

          <div className="flex items-center gap-3">
            <div className="h-9 w-9 rounded-full bg-[#29508B]/10 dark:bg-blue-500/20 text-[#29508B] dark:text-blue-300 flex items-center justify-center font-bold text-sm border border-[#29508B]/20">
              {user?.name?.charAt(0) || 'د'}
            </div>
            <div className="hidden sm:block text-right">
              <p className="text-sm font-bold text-slate-900 dark:text-slate-100">{user?.name || 'د. أحمد علي'}</p>
              <p className="text-xs text-slate-500 dark:text-slate-400">{doctorProfile?.specialtyNameAr || 'استشاري أمراض الباطنية'}</p>
            </div>
          </div>
        </div>
      </header>

      <div className="flex flex-1 overflow-hidden">
        {/* Sidebar Navigation */}
        <aside
          className={`fixed inset-y-0 right-0 z-30 w-64 transform border-l border-slate-200/80 bg-white/95 backdrop-blur-md dark:border-slate-800/80 dark:bg-[#0C162A]/95 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 ${
            sidebarOpen ? 'translate-x-0' : 'translate-x-full'
          } pt-16 lg:pt-0 flex flex-col justify-between`}
        >
          <div className="space-y-1 p-3 overflow-y-auto">
            {navItems.map((item) => (
              <NavLink
                key={item.path}
                to={item.path}
                onClick={() => setSidebarOpen(false)}
                className={({ isActive }) =>
                  `flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all ${
                    isActive
                      ? 'bg-[#29508B] text-white shadow-md shadow-[#29508B]/25'
                      : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800/60'
                  }`
                }
              >
                <item.icon className="h-5 w-5 shrink-0" />
                <span>{item.label}</span>
              </NavLink>
            ))}
          </div>

          <div className="p-3 border-t border-slate-200/80 dark:border-slate-800/80">
            <button
              onClick={handleLogout}
              className="flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/40 transition-colors"
            >
              <LogOut className="h-5 w-5 shrink-0" />
              <span>تسجيل الخروج</span>
            </button>
          </div>
        </aside>

        {/* Main Content Area */}
        <main className="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
          <div className="mx-auto max-w-7xl">
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  );
};
