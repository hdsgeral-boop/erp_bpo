'use client';

import { useState, useEffect } from 'react';
import { usePathname } from 'next/navigation';
import Link from 'next/link';
import api from '@/lib/api';
import { 
  Menu, 
  Bell, 
  User, 
  Building, 
  ChevronRight, 
  SidebarOpen, 
  SidebarClose,
  ShieldCheck 
} from 'lucide-react';

interface HeaderProps {
  sidebarExpanded: boolean;
  setSidebarExpanded: (expanded: boolean) => void;
  setMobileSidebarOpen: (open: boolean) => void;
}

export default function Header({ 
  sidebarExpanded, 
  setSidebarExpanded, 
  setMobileSidebarOpen 
}: HeaderProps) {
  const pathname = usePathname();
  const [user, setUser] = useState<any>(null);
  const [allCompanies, setAllCompanies] = useState<any[]>([]);

  useEffect(() => {
    if (typeof window !== 'undefined') {
      const storedUser = localStorage.getItem('erp_user');
      if (storedUser) {
        try {
          const parsed = JSON.parse(storedUser);
          setUser(parsed);
          if (parsed.companies && parsed.companies.length > 0) {
            setAllCompanies(parsed.companies);
          }
        } catch (e) {
          console.error(e);
        }
      }
    }

    // Buscar lista atualizada de empresas da API
    api.get('/user/companies')
      .then(res => {
        if (res.data.success && Array.isArray(res.data.data)) {
          setAllCompanies(res.data.data);
        }
      })
      .catch(err => {
        console.error('Erro ao carregar empresas do utilizador:', err);
      });
  }, []);

  const getBreadcrumbs = (path: string) => {
    const parts = path.split('/').filter(Boolean);
    if (parts.length === 0) return [{ name: 'Portal', active: true, href: '/dashboard' }];

    return parts.map((part, index) => {
      let name = part;
      if (part === 'dashboard') name = 'Dashboard';
      else if (part === 'vendas') name = 'Vendas';
      else if (part === 'compras') name = 'Compras';
      else if (part === 'rh') name = 'Recursos Humanos';
      else if (part === 'ativos') name = 'Ativos Fixos';
      else if (part === 'tesouraria') name = 'Tesouraria';
      else if (part === 'contabilidade') name = 'Contabilidade';
      else if (part === 'logistica') name = 'Logística';
      else if (part === 'admin') name = 'Administração';
      else if (part === 'sgd') name = 'Gestão Documental';
      else if (part === 'ai') name = 'Assistente IA';
      else if (part === 'documentos') name = 'Documentos';
      else if (part === 'pos') name = 'POS Frente Caixa';
      else if (part === 'saft') name = 'Exportar SAF-T';
      else if (part === 'pedidos') name = 'Pedidos Internos';
      else if (part === 'encomendas') name = 'Encomendas';
      else if (part === 'faturas') name = 'Faturas Fornecedor';
      else if (part === 'funcionarios') name = 'Funcionários';
      else if (part === 'contratos') name = 'Contratos';
      else if (part === 'salarios') name = 'Processar Salários';
      else if (part === 'contas') name = 'Contas';
      else if (part === 'contas-correntes') name = 'Contas Correntes';
      else if (part === 'reconciliacao') name = 'Reconciliação';
      else if (part === 'plano') name = 'Plano de Contas';
      else if (part === 'diarios') name = 'Diários';
      else if (part === 'balancete') name = 'Balancete';
      else if (part === 'artigos') name = 'Artigos';
      else if (part === 'armazens') name = 'Armazéns';
      else if (part === 'inventario') name = 'Inventário';
      else if (part === 'series') name = 'Séries';
      else if (part === 'users') name = 'Utilizadores';
      else if (part === 'config') name = 'Configurações';

      if (name === part) {
        name = part.charAt(0).toUpperCase() + part.slice(1);
      }

      return {
        name,
        active: index === parts.length - 1,
        href: '/' + parts.slice(0, index + 1).join('/')
      };
    });
  };

  const breadcrumbs = getBreadcrumbs(pathname);

  return (
    <header className="sticky top-0 z-30 flex items-center justify-between h-16 px-6 bg-white border-b border-slate-200/80 shadow-sm backdrop-blur-md bg-white/95">
      {/* Left panel: Controls & Breadcrumbs */}
      <div className="flex items-center gap-4">
        {/* Sidebar toggler for Mobile */}
        <button
          onClick={() => setMobileSidebarOpen(true)}
          className="text-slate-500 hover:text-slate-800 p-1.5 rounded-lg hover:bg-slate-100 lg:hidden cursor-pointer transition-colors"
        >
          <Menu className="h-5.5 w-5.5" />
        </button>

        {/* Sidebar toggler for Desktop */}
        <button
          onClick={() => setSidebarExpanded(!sidebarExpanded)}
          className="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 hidden lg:block cursor-pointer transition-colors"
          title={sidebarExpanded ? "Recolher menu lateral" : "Expandir menu lateral"}
        >
          {sidebarExpanded ? <SidebarClose className="h-5 w-5" /> : <SidebarOpen className="h-5 w-5" />}
        </button>

        {/* Breadcrumb Navigation / Multi-Company Switcher */}
        <nav className="hidden sm:flex items-center gap-1.5 text-sm" aria-label="Breadcrumb">
          <div className="flex items-center gap-1.5 text-slate-700 bg-slate-100 hover:bg-slate-200/80 px-2.5 py-1 rounded-lg border border-slate-200 transition-colors">
            <Building className="h-4 w-4 shrink-0 text-blue-600" />
            <select
              value={user?.company?.id || ''}
              onChange={async (e) => {
                const newCompanyId = parseInt(e.target.value);
                if (!newCompanyId) return;
                try {
                  const res = await api.post('/switch-company', { company_id: newCompanyId });
                  if (res.data.success && user) {
                    const updatedCompany = allCompanies.find((c: any) => c.id === newCompanyId) || res.data.company || { id: newCompanyId };
                    const updatedUser = { ...user, company: updatedCompany, companies: allCompanies };
                    localStorage.setItem('erp_user', JSON.stringify(updatedUser));
                    window.location.reload();
                  }
                } catch (err) {
                  console.error('Erro ao alterar empresa:', err);
                }
              }}
              className="bg-transparent font-bold text-slate-800 text-xs focus:outline-none cursor-pointer pr-1"
            >
              {allCompanies.length > 0 ? (
                allCompanies.map((comp: any) => (
                  <option key={comp.id} value={comp.id}>
                    🏢 {comp.name}
                  </option>
                ))
              ) : (
                <option value={user?.company?.id || 1}>
                  🏢 {user?.company?.name || 'VLC — Volt Light Consulvolt'}
                </option>
              )}
            </select>
          </div>
          
          {breadcrumbs.map((crumb, idx) => (
            <div key={idx} className="flex items-center gap-1.5">
              <ChevronRight className="h-3.5 w-3.5 text-slate-300 shrink-0" />
              {crumb.active ? (
                <span className="font-extrabold text-slate-850 truncate max-w-[150px]">{crumb.name}</span>
              ) : (
                <Link 
                  href={crumb.href}
                  className="font-medium text-slate-400 hover:text-slate-600 transition-colors truncate max-w-[150px]"
                >
                  {crumb.name}
                </Link>
              )}
            </div>
          ))}
        </nav>
      </div>

      {/* Right panel: Alerts & User info */}
      <div className="flex items-center gap-4">
        {/* Environment Badge */}
        <span className="hidden md:inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wide">
          <ShieldCheck className="h-3.5 w-3.5" />
          SaaS Seguro
        </span>

        {/* Notification Bell */}
        <button className="relative p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-all cursor-pointer">
          <Bell className="h-5 w-5" />
          <span className="absolute top-1.5 right-1.5 h-2 w-2 bg-blue-600 rounded-full ring-2 ring-white" />
        </button>

        {/* Profile Details Card */}
        <div className="flex items-center gap-3 pl-3 border-l border-slate-200">
          <div className="flex flex-col text-right hidden md:block">
            <span className="text-sm font-bold text-slate-800 tracking-tight">{user?.name || 'Administrador'}</span>
            <span className="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">
              {user?.roles?.[0]?.name || 'Super Admin'}
            </span>
          </div>
          
          <div className="h-10 w-10 rounded-xl bg-slate-900 flex items-center justify-center border border-slate-850 shadow-inner group-hover:scale-105 transition-transform duration-200">
            <User className="h-5 w-5 text-slate-300" />
          </div>
        </div>
      </div>
    </header>
  );
}
