'use client';

import { useState, useEffect } from 'react';
import { useRouter, usePathname } from 'next/navigation';
import Sidebar from './Sidebar';
import Header from './Header';

export default function AppLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const pathname = usePathname();

  const [authorized, setAuthorized] = useState(false);
  const [sidebarExpanded, setSidebarExpanded] = useState(true);
  const [mobileSidebarOpen, setMobileSidebarOpen] = useState(false);

  // Routes that do not require authentication or the sidebar layout
  const isPublicRoute = pathname === '/login' || pathname === '/';

  useEffect(() => {
    if (isPublicRoute) {
      setAuthorized(true);
      return;
    }

    const token = localStorage.getItem('erp_token');
    if (!token) {
      router.push('/login');
    } else {
      setAuthorized(true);
    }
  }, [pathname, router, isPublicRoute]);

  if (!authorized) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-950 text-white">
        <div className="flex flex-col items-center gap-4">
          <svg className="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24">
            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
          </svg>
          <span className="text-sm font-semibold tracking-wide text-slate-400">A validar credenciais...</span>
        </div>
      </div>
    );
  }

  // Render public routes directly without layout
  if (isPublicRoute) {
    return <>{children}</>;
  }

  return (
    <div className="flex h-screen overflow-hidden bg-slate-900/5">
      {/* Double Column Sidebar */}
      <Sidebar 
        sidebarExpanded={sidebarExpanded} 
        setSidebarExpanded={setSidebarExpanded}
        mobileSidebarOpen={mobileSidebarOpen}
        setMobileSidebarOpen={setMobileSidebarOpen}
      />

      {/* Main content wrapper */}
      <div 
        className={`flex flex-col flex-1 overflow-hidden transition-all duration-300 ease-in-out ${
          sidebarExpanded ? 'lg:pl-[304px]' : 'lg:pl-[80px]'
        }`}
      >
        {/* Header toolbar */}
        <Header 
          sidebarExpanded={sidebarExpanded}
          setSidebarExpanded={setSidebarExpanded}
          setMobileSidebarOpen={setMobileSidebarOpen} 
        />

        {/* Scrollable canvas */}
        <main className="flex-1 overflow-y-auto px-6 py-8 bg-[#f8fafc]">
          <div className="max-w-7xl mx-auto space-y-6">
            {children}
          </div>
        </main>
      </div>
    </div>
  );
}
