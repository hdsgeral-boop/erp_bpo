'use client';

import { useState, useEffect } from 'react';
import { Menu, Bell, User, Building } from 'lucide-react';

interface HeaderProps {
  setSidebarOpen: (open: boolean) => void;
}

export default function Header({ setSidebarOpen }: HeaderProps) {
  const [user, setUser] = useState<any>(null);

  useEffect(() => {
    if (typeof window !== 'undefined') {
      const storedUser = localStorage.getItem('erp_user');
      if (storedUser) {
        try {
          setUser(JSON.parse(storedUser));
        } catch (e) {
          console.error(e);
        }
      }
    }
  }, []);

  return (
    <header className="sticky top-0 z-30 flex items-center justify-between h-16 px-6 bg-white border-b border-slate-200/80 shadow-sm">
      {/* Sidebar toggle button (Mobile only) */}
      <button
        onClick={() => setSidebarOpen(true)}
        className="text-slate-500 hover:text-slate-700 lg:hidden cursor-pointer"
      >
        <Menu className="h-6 w-6" />
      </button>

      {/* Breadcrumbs or active status info */}
      <div className="hidden sm:flex items-center gap-2">
        <Building className="h-4 w-4 text-slate-400" />
        <span className="text-sm font-semibold text-slate-600">
          {user?.company?.name || 'Consulvolt Lda.'}
        </span>
        <span className="text-slate-300">/</span>
        <span className="text-xs px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-150 font-bold uppercase tracking-wider">
          SaaS Produção
        </span>
      </div>

      {/* Right panel profile details */}
      <div className="flex items-center gap-4">
        {/* Notifications */}
        <button className="relative p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-100 transition-colors">
          <Bell className="h-5 w-5" />
          <span className="absolute top-1.5 right-1.5 h-2.5 w-2.5 bg-blue-600 rounded-full ring-2 ring-white" />
        </button>

        {/* User Card */}
        <div className="flex items-center gap-3 pl-3 border-l border-slate-200">
          <div className="flex flex-col text-right hidden md:block">
            <span className="text-sm font-bold text-slate-800">{user?.name || 'Administrador'}</span>
            <span className="text-xs font-medium text-slate-400 capitalize">
              {user?.roles?.[0]?.name || 'Super Admin'}
            </span>
          </div>
          <div className="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center border border-slate-200 shadow-inner">
            <User className="h-5 w-5 text-slate-600" />
          </div>
        </div>
      </div>
    </header>
  );
}
