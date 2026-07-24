'use client';

import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { useState, useEffect } from 'react';
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
  ChevronLeft,
  ChevronRight,
  X,
  Sparkles
} from 'lucide-react';

interface SidebarProps {
  sidebarExpanded: boolean;
  setSidebarExpanded: (expanded: boolean) => void;
  mobileSidebarOpen: boolean;
  setMobileSidebarOpen: (open: boolean) => void;
}

export default function Sidebar({ 
  sidebarExpanded, 
  setSidebarExpanded, 
  mobileSidebarOpen, 
  setMobileSidebarOpen 
}: SidebarProps) {
  const pathname = usePathname();
  const router = useRouter();

  // Determine active section based on path
  const getSectionFromPath = (path: string) => {
    if (path.startsWith('/vendas')) return 'vendas';
    if (path.startsWith('/compras')) return 'compras';
    if (path.startsWith('/rh')) return 'rh';
    if (path.startsWith('/ativos')) return 'ativos';
    if (path.startsWith('/tesouraria')) return 'tesouraria';
    if (path.startsWith('/contabilidade')) return 'contabilidade';
    if (path.startsWith('/logistica')) return 'logistica';
    if (path.startsWith('/admin')) return 'admin';
    if (path.startsWith('/sgd')) return 'sgd';
    if (path.startsWith('/ai')) return 'ai';
    return 'dashboard';
  };

  const [activeSection, setActiveSection] = useState<string>(getSectionFromPath(pathname));

  useEffect(() => {
    setActiveSection(getSectionFromPath(pathname));
  }, [pathname]);

  const menuItems = [
    { id: 'dashboard', name: 'Dashboard', icon: LayoutDashboard, href: '/dashboard' },
    {
      id: 'vendas',
      name: 'Vendas',
      icon: DollarSign,
      children: [
        { name: 'Documentos', href: '/vendas/documentos' },
        { name: 'POS Frente Caixa', href: '/vendas/pos' },
        { name: 'Exportar SAF-T', href: '/vendas/saft' }
      ]
    },
    {
      id: 'compras',
      name: 'Compras',
      icon: ShoppingCart,
      children: [
        { name: 'Pedidos Internos', href: '/compras/pedidos' },
        { name: 'Encomendas', href: '/compras/encomendas' },
        { name: 'Faturas Fornecedor', href: '/compras/faturas' }
      ]
    },
    {
      id: 'rh',
      name: 'Recursos Humanos',
      icon: Users,
      children: [
        { name: 'Funcionários', href: '/rh/funcionarios' },
        { name: 'Contratos', href: '/rh/contratos' },
        { name: 'Processar Salários', href: '/rh/salarios' }
      ]
    },
    { id: 'ativos', name: 'Ativos Fixos', icon: HardDrive, href: '/ativos' },
    {
      id: 'tesouraria',
      name: 'Tesouraria',
      icon: Briefcase,
      children: [
        { name: 'Contas de Tesouraria', href: '/tesouraria/contas' },
        { name: 'Contas Correntes', href: '/tesouraria/contas-correntes' },
        { name: 'Reconciliação Bancária', href: '/tesouraria/reconciliacao' }
      ]
    },
    {
      id: 'contabilidade',
      name: 'Contabilidade',
      icon: BookOpen,
      children: [
        { name: 'Plano de Contas', href: '/contabilidade/plano' },
        { name: 'Lançamentos / Diários', href: '/contabilidade/diarios' },
        { name: 'Balancete de Verificação', href: '/contabilidade/balancete' }
      ]
    },
    {
      id: 'logistica',
      name: 'Logística',
      icon: Warehouse,
      children: [
        { name: 'Artigos / Produtos', href: '/logistica/artigos' },
        { name: 'Gestão de Armazéns', href: '/logistica/armazens' },
        { name: 'Inventário Físico', href: '/logistica/inventario' }
      ]
    },
    { id: 'sgd', name: 'Gestão Doc.', icon: FileText, href: '/sgd' },
    { id: 'ai', name: 'Assistente IA', icon: MessageSquare, href: '/ai' },
    {
      id: 'admin',
      name: 'Administração',
      icon: Settings,
      children: [
        { name: 'Terminais POS (Caixas)', href: '/admin/pos-registers' },
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

  const handleMainMenuClick = (item: any) => {
    setActiveSection(item.id);
    if (item.href) {
      // Direct navigation link, collapse submenu panel
      setSidebarExpanded(false);
      setMobileSidebarOpen(false);
      router.push(item.href);
    } else {
      // Has children, auto-expand the submenu panel
      setSidebarExpanded(true);
    }
  };

  const currentSectionItem = menuItems.find(item => item.id === activeSection);
  const hasSubmenus = !!currentSectionItem?.children;

  const sidebarContent = (
    <div className="flex h-full">
      {/* COLUMN 1: Main Module Icons */}
      <div className="flex flex-col w-20 bg-slate-950 border-r border-slate-900/80 items-center py-4 justify-between h-full z-20 shadow-lg">
        {/* Top: Logo */}
        <div className="flex flex-col items-center gap-1 mb-6">
          <div className="h-11 w-11 rounded-xl bg-slate-900 flex items-center justify-center p-1.5 border border-slate-800 shadow-inner">
            <img 
              src="/img/logo_erp.png" 
              alt="ERP Logo" 
              className="h-full w-full object-contain filter brightness-110" 
            />
          </div>
          <span className="text-[9px] text-slate-500 font-extrabold uppercase tracking-widest">Consul</span>
        </div>

        {/* Middle: Icon stack */}
        <div className="flex-1 w-full px-2 space-y-1.5 overflow-y-auto scrollbar-none flex flex-col items-center">
          {menuItems.map((item) => {
            const Icon = item.icon;
            const isSelected = activeSection === item.id;
            return (
              <button
                key={item.id}
                onClick={() => handleMainMenuClick(item)}
                title={item.name}
                className={`relative flex flex-col items-center justify-center w-14 h-14 rounded-xl transition-all duration-200 group cursor-pointer ${
                  isSelected 
                    ? 'bg-blue-600/95 text-white shadow-lg shadow-blue-600/20 border border-blue-500/50' 
                    : 'text-slate-400 hover:bg-slate-900 hover:text-white border border-transparent'
                }`}
              >
                <Icon className={`h-5.5 w-5.5 ${isSelected ? 'scale-110' : 'group-hover:scale-105'} transition-transform duration-200`} />
                <span className="text-[9px] font-bold mt-1 text-center truncate max-w-full px-0.5">
                  {item.id === 'rh' ? 'RH' : item.id === 'dashboard' ? 'Dash' : item.name.split(' ')[0]}
                </span>
                
                {/* Active left indicator bar */}
                {isSelected && (
                  <div className="absolute left-0 top-3 bottom-3 w-1 bg-white rounded-r-md" />
                )}
              </button>
            );
          })}
        </div>

        {/* Bottom actions */}
        <div className="w-full px-2 pt-4 border-t border-slate-900 flex flex-col items-center gap-3">
          {/* Collapse sidebar button */}
          <button
            onClick={() => setSidebarExpanded(!sidebarExpanded)}
            className="flex items-center justify-center w-10 h-10 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900 border border-slate-900/60 cursor-pointer transition-colors"
            title={sidebarExpanded ? "Recolher Menu" : "Expandir Menu"}
          >
            {sidebarExpanded ? <ChevronLeft className="h-5 w-5" /> : <ChevronRight className="h-5 w-5" />}
          </button>

          {/* Logout */}
          <button
            onClick={handleLogout}
            className="flex items-center justify-center w-10 h-10 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-950/20 cursor-pointer transition-colors"
            title="Encerrar Sessão"
          >
            <LogOut className="h-5 w-5" />
          </button>
        </div>
      </div>

      {/* COLUMN 2: Submenus Panel */}
      <div 
        className={`flex flex-col bg-slate-900 border-r border-slate-800 transition-all duration-300 ease-in-out shadow-inner z-10 ${
          sidebarExpanded && hasSubmenus 
            ? 'w-56 opacity-100 visible' 
            : 'w-0 opacity-0 invisible overflow-hidden'
        }`}
      >
        {/* Submenu Title */}
        <div className="h-16 px-6 flex items-center justify-between border-b border-slate-800 bg-slate-950/40">
          <div className="flex flex-col">
            <span className="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Painel de Controlo</span>
            <span className="text-sm font-bold text-white tracking-tight mt-0.5 capitalize">
              {currentSectionItem?.name}
            </span>
          </div>
          {currentSectionItem?.id === 'ai' && (
            <Sparkles className="h-4 w-4 text-purple-400 animate-pulse" />
          )}
        </div>

        {/* Submenu links */}
        <nav className="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto">
          {hasSubmenus && currentSectionItem?.children?.map((child) => {
            const isChildActive = pathname === child.href;
            return (
              <Link
                key={child.name}
                href={child.href}
                onClick={() => setMobileSidebarOpen(false)}
                className={`flex items-center px-4 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 ${
                  isChildActive 
                    ? 'bg-slate-800 text-white border-l-2 border-blue-500 pl-3.5 shadow-md shadow-slate-950/20' 
                    : 'text-slate-400 hover:bg-slate-800/40 hover:text-white border-l-2 border-transparent'
                }`}
              >
                <span>{child.name}</span>
              </Link>
            );
          })}
        </nav>

        {/* Bottom panel indicator */}
        <div className="p-4 bg-slate-950/20 border-t border-slate-800/60 text-center">
          <span className="text-[9px] text-slate-500 font-semibold uppercase tracking-wider">Módulo Licenciado</span>
        </div>
      </div>
    </div>
  );

  return (
    <>
      {/* MOBILE BACKDROP */}
      {mobileSidebarOpen && (
        <div 
          className="fixed inset-0 z-40 bg-slate-950/65 lg:hidden backdrop-blur-sm transition-opacity duration-300"
          onClick={() => setMobileSidebarOpen(false)}
        />
      )}

      {/* SIDEBAR FOR DESKTOP */}
      <aside 
        className={`fixed top-0 bottom-0 left-0 z-35 flex flex-col h-full bg-slate-900 transition-all duration-300 ease-in-out hidden lg:block ${
          sidebarExpanded ? 'w-[304px]' : 'w-20'
        }`}
      >
        {sidebarContent}
      </aside>

      {/* SIDEBAR FOR MOBILE (Drawer) */}
      <aside 
        className={`fixed top-0 bottom-0 left-0 z-50 flex flex-col h-full bg-slate-900 shadow-2xl transition-transform duration-300 ease-in-out lg:hidden ${
          mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
        style={{ width: hasSubmenus ? '304px' : '80px' }}
      >
        {/* Mobile close button */}
        <button 
          onClick={() => setMobileSidebarOpen(false)} 
          className="absolute top-4 right-4 text-slate-400 hover:text-white bg-slate-950/50 p-1.5 rounded-lg border border-slate-800 z-30 lg:hidden cursor-pointer"
        >
          <X className="h-4 w-4" />
        </button>
        {sidebarContent}
      </aside>
    </>
  );
}
