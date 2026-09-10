import React, { useState } from 'react';
import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { 
  Shield, 
  LayoutDashboard, 
  UserCheck, 
  Stethoscope, 
  Users, 
  FolderTree, 
  CalendarDays, 
  Wallet, 
  BarChart3, 
  MessageSquare, 
  Bell, 
  ShieldAlert, 
  Lock, 
  Settings, 
  LogOut, 
  Menu, 
  X, 
  Sparkles,
  FileCheck
} from 'lucide-react';
import { useAdminAuthStore } from '../../core/auth/useAdminAuthStore';

const navSections = [
  {
    title: 'الإشراف وتوثيق الحسابات',
    items: [
      { label: 'لوحة القيادة المركزية', path: '/dashboard', icon: LayoutDashboard },
      { label: 'توثيق وتراخيص الأطباء', path: '/verifications', icon: UserCheck, badge: '3 جديدة' },
      { label: 'دليل الأطباء والعيادات', path: '/doctors', icon: Stethoscope },
      { label: 'إدارة وتصفح المرضى', path: '/patients', icon: Users },
      { label: 'شجرة التخصصات الطبية', path: '/specialties', icon: FolderTree },
    ],
  },
  {
    title: 'العمليات والمالية (YER)',
    items: [
      { label: 'رقابة كافة المواعيد', path: '/appointments', icon: CalendarDays },
      { label: 'السجل المالي والسحوبات', path: '/financials', icon: Wallet },
      { label: 'التقارير التحليلية والنمو', path: '/reports', icon: BarChart3 },
      { label: 'الرقابة على المحتوى والتقييمات', path: '/moderation', icon: MessageSquare },
      { label: 'حوكمة الذكاء الاصطناعي', path: '/ai-governance', icon: Sparkles },
    ],
  },
  {
    title: 'الأمان وإعدادات النظام',
    items: [
      { label: 'مركز التنبيهات العام', path: '/notifications', icon: Bell },
      { label: 'سجلات التتبع (Audit Logs)', path: '/audit-logs', icon: ShieldAlert },
      { label: 'تتبع الوصول الطبي (EHR Audit)', path: '/medical-audit', icon: FileCheck },
      { label: 'الأدوار والصلاحيات (RBAC)', path: '/rbac', icon: Lock },
      { label: 'إعدادات المنصة الكلية', path: '/settings', icon: Settings },
    ],
  },
];

export const AdminDashboardLayout: React.FC = () => {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const { admin, logoutAdmin } = useAdminAuthStore();
  const navigate = useNavigate();

  const handleLogout = () => {
    logoutAdmin();
    navigate('/login');
  };

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 font-sans flex flex-col antialiased">
      {/* Top Admin Control Bar */}
      <header className="sticky top-0 z-40 flex h-16 w-full items-center justify-between border-b border-slate-800 bg-slate-900/90 px-4 backdrop-blur-md md:px-6">
        <div className="flex items-center gap-3">
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="rounded-lg p-2 text-slate-400 hover:bg-slate-800 lg:hidden"
          >
            {sidebarOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
          </button>

          <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#29508B] to-blue-700 text-white shadow-lg shadow-blue-900/30">
              <Shield className="h-5 w-5" />
            </div>
            <div>
              <span className="text-lg font-black tracking-tight text-white">طبيبي</span>
              <span className="mr-1.5 text-xs font-bold text-blue-400">الإدارة العليا السيادية</span>
            </div>
          </div>
        </div>

        <div className="flex items-center gap-4">
          <span className="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-emerald-950/80 px-3 py-1 text-xs font-semibold text-emerald-300 border border-emerald-800">
            <span className="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            الخادم المركزي متصل
          </span>

          <div className="h-6 w-px bg-slate-800 hidden sm:block"></div>

          <div className="flex items-center gap-3">
            <div className="h-9 w-9 rounded-full bg-[#29508B]/30 text-blue-300 flex items-center justify-center font-bold text-sm border border-blue-500/30">
              {admin?.name?.charAt(0) || 'أ'}
            </div>
            <div className="hidden sm:block text-right">
              <p className="text-xs font-bold text-slate-100">{admin?.name || 'مدير النظام الأعلى'}</p>
              <p className="text-[10px] text-blue-400 font-semibold">{admin?.role || 'Super Admin'}</p>
            </div>
          </div>
        </div>
      </header>

      <div className="flex flex-1 overflow-hidden">
        {/* Admin Sidebar Navigation */}
        <aside
          className={`fixed inset-y-0 right-0 z-30 w-64 transform border-l border-slate-800 bg-slate-900/95 backdrop-blur-md transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 ${
            sidebarOpen ? 'translate-x-0' : 'translate-x-full'
          } pt-16 lg:pt-0 flex flex-col justify-between`}
        >
          <div className="space-y-4 p-3 overflow-y-auto">
            {navSections.map((sec, secIdx) => (
              <div key={secIdx} className="space-y-1">
                <p className="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">{sec.title}</p>
                {sec.items.map((item) => (
                  <NavLink
                    key={item.path}
                    to={item.path}
                    onClick={() => setSidebarOpen(false)}
                    className={({ isActive }) =>
                      `flex items-center justify-between rounded-xl px-3 py-2 text-xs font-bold transition-all ${
                        isActive
                          ? 'bg-[#29508B] text-white shadow-md shadow-blue-900/40'
                          : 'text-slate-400 hover:bg-slate-800/80 hover:text-slate-200'
                      }`
                    }
                  >
                    <div className="flex items-center gap-2.5">
                      <item.icon className="h-4 w-4 shrink-0" />
                      <span>{item.label}</span>
                    </div>

                    {item.badge && (
                      <span className="rounded-full bg-rose-500/20 px-2 py-0.5 text-[10px] font-bold text-rose-300 border border-rose-500/30">
                        {item.badge}
                      </span>
                    )}
                  </NavLink>
                ))}
              </div>
            ))}
          </div>

          <div className="p-3 border-t border-slate-800">
            <button
              onClick={handleLogout}
              className="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-bold text-rose-400 hover:bg-rose-950/40 transition-colors"
            >
              <LogOut className="h-4 w-4 shrink-0" />
              <span>إنهاء الجلسة الإدارية</span>
            </button>
          </div>
        </aside>

        {/* Main Content Area */}
        <main className="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 bg-[#0B1329]">
          <div className="mx-auto max-w-7xl">
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  );
};
