'use client';

import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { useState } from 'react';
import { 
  LayoutDashboard, 
  DollarSign, 
  ShoppingCart, 
  Users, 
  HardDrive, 
  Briefcase, 
  BookOpen, 
  Warehouse, 
  FileText, 
  MessageSquare, 
  Settings, 
  LogOut,
  ChevronDown,
  Menu,
  X
} from 'lucide-react';

interface SidebarProps {
  sidebarOpen: boolean;
  setSidebarOpen: (open: boolean) => void;
}

export default function Sidebar({ sidebarOpen, setSidebarOpen }: SidebarProps) {
  const pathname = usePathname();
  const router = useRouter();
  const [expandedMenu, setExpandedMenu] = useState<string | null>(null);

  const menuItems = [
    { name: 'Dashboard', icon: LayoutDashboard, href: '/dashboard' },
    {
      name: 'Vendas',
      icon: DollarSign,
      href: '/vendas',
      children: [
        { name: 'Documentos', href: '/vendas/documentos' },
        { name: 'POS Frente Caixa', href: '/vendas/pos' },
        { name: 'Exportar SAF-T', href: '/vendas/saft' }
      ]
    },
    {
      name: 'Compras',
      icon: ShoppingCart,
      href: '/compras',
      children: [
        { name: 'Pedidos Internos', href: '/compras/pedidos' },
        { name: 'Encomendas', href: '/compras/encomendas' },
        { name: 'Faturas Fornecedor', href: '/compras/faturas' }
      ]
    },
    {
      name: 'Recursos Humanos',
      icon: Users,
      href: '/rh',
      children: [
        { name: 'Funcionários', href: '/rh/funcionarios' },
        { name: 'Contratos', href: '/rh/contratos' },
        { name: 'Processar Salários', href: '/rh/salarios' }
      ]
    },
    { name: 'Ativos Fixos', icon: HardDrive, href: '/ativos' },
    {
      name: 'Tesouraria',
      icon: Briefcase,
      href: '/tesouraria',
      children: [
        { name: 'Contas de Tesouraria', href: '/tesouraria/contas' },
        { name: 'Contas Correntes', href: '/tesouraria/contas-correntes' },
        { name: 'Reconciliação Bancária', href: '/tesouraria/reconciliacao' }
      ]
    },
    {
      name: 'Contabilidade',
      icon: BookOpen,
      href: '/contabilidade',
      children: [
        { name: 'Plano de Contas', href: '/contabilidade/plano' },
        { name: 'Lançamentos / Diários', href: '/contabilidade/diarios' },
        { name: 'Balancete de Verificação', href: '/contabilidade/balancete' }
      ]
    },
    {
      name: 'Logística',
      icon: Warehouse,
      href: '/logistica',
      children: [
        { name: 'Artigos / Produtos', href: '/logistica/artigos' },
        { name: 'Gestão de Armazéns', href: '/logistica/armazens' },
        { name: 'Inventário Físico', href: '/logistica/inventario' }
      ]
    },
    { name: 'Gestão Documental', icon: FileText, href: '/sgd' },
    { name: 'Assistente IA', icon: MessageSquare, href: '/ai' },
    {
      name: 'Administração',
      icon: Settings,
      href: '/admin',
      children: [
        { name: 'Séries Documentais', href: '/admin/series' },
        { name: 'Utilizadores', href: '/admin/users' },
        { name: 'Configurações', href: '/admin/config' }
      ]
    }
  ];

  const handleLogout = () => {
    localStorage.removeItem('erp_token');
    localStorage.removeItem('erp_user');
    router.push('/login');
  };

  const toggleExpand = (name: string) => {
    if (expandedMenu === name) {
      setExpandedMenu(null);
    } else {
      setExpandedMenu(name);
    }
  };

  return (
    <>
      {/* Mobile Backdrop */}
      {sidebarOpen && (
        <div 
          className="fixed inset-0 z-40 bg-slate-900/60 lg:hidden backdrop-blur-sm"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <aside className={`fixed top-0 bottom-0 left-0 z-50 flex flex-col w-64 bg-slate-900 border-r border-slate-800 shadow-2xl transition-transform duration-300 lg:translate-x-0 ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'}`}>
        {/* Header */}
        <div className="flex items-center justify-between h-16 px-6 bg-slate-950 border-b border-slate-800">
          <div className="flex items-center gap-2">
            <div className="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center border border-blue-400">
              <span className="text-white font-extrabold text-lg">C</span>
            </div>
            <span className="text-white font-bold text-lg tracking-tight">ERP Consulvolt</span>
          </div>
          <button onClick={() => setSidebarOpen(false)} className="text-slate-400 hover:text-white lg:hidden">
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Scrollable Navigation */}
        <nav className="flex-1 px-4 py-6 overflow-y-auto space-y-1">
          {menuItems.map((item) => {
            const Icon = item.icon;
            const hasChildren = !!item.children;
            const isExpanded = expandedMenu === item.name;
            const isActive = pathname === item.href || pathname.startsWith(item.href + '/');

            return (
              <div key={item.name} className="space-y-1">
                {hasChildren ? (
                  <button
                    onClick={() => toggleExpand(item.name)}
                    className={`flex items-center justify-between w-full px-4 py-3 text-sm font-semibold rounded-xl transition-all cursor-pointer ${isActive ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'}`}
                  >
                    <div className="flex items-center gap-3">
                      <Icon className="h-5 w-5 shrink-0" />
                      <span>{item.name}</span>
                    </div>
                    <ChevronDown className={`h-4 w-4 transition-transform duration-200 ${isExpanded ? 'rotate-180' : ''}`} />
                  </button>
                ) : (
                  <Link
                    href={item.href}
                    onClick={() => setSidebarOpen(false)}
                    className={`flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all ${isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/10' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white'}`}
                  >
                    <Icon className="h-5 w-5 shrink-0" />
                    <span>{item.name}</span>
                  </Link>
                )}

                {hasChildren && isExpanded && (
                  <div className="pl-11 pr-2 py-1 space-y-1 border-l border-slate-800 ml-6">
                    {item.children?.map((child) => {
                      const isChildActive = pathname === child.href;
                      return (
                        <Link
                          key={child.name}
                          href={child.href}
                          onClick={() => setSidebarOpen(false)}
                          className={`block py-2 px-3 text-xs font-semibold rounded-lg transition-all ${isChildActive ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white'}`}
                        >
                          {child.name}
                        </Link>
                      );
                    })}
                  </div>
                )}
              </div>
            );
          })}
        </nav>

        {/* Footer info & Logout */}
        <div className="p-4 bg-slate-950 border-t border-slate-800">
          <button 
            onClick={handleLogout}
            className="flex items-center gap-3 w-full px-4 py-3 text-sm font-semibold text-red-400 hover:text-red-300 hover:bg-red-950/20 rounded-xl transition-all cursor-pointer"
          >
            <LogOut className="h-5 w-5 shrink-0" />
            <span>Encerrar Sessão</span>
          </button>
        </div>
      </aside>
    </>
  );
}
