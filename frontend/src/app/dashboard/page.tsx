'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  Users, 
  Package, 
  TrendingUp, 
  CreditCard,
  ArrowRight,
  TrendingDown,
  Clock
} from 'lucide-react';

export default function DashboardPage() {
  const [data, setData] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    async function fetchDashboard() {
      try {
        const response = await api.get('/dashboard');
        setData(response.data);
      } catch (err: any) {
        setError('Falha ao carregar estatísticas do dashboard.');
        console.error(err);
      } finally {
        setLoading(false);
      }
    }
    fetchDashboard();
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center py-20">
        <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
      </div>
    );
  }

  if (error) {
    return (
      <div className="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
        {error}
      </div>
    );
  }

  const kpis = data?.kpis || {
    employees: 0,
    products: 0,
    monthly_sales: 0,
    treasury_balance: 0
  };

  const recentSales = data?.recentSales || [];

  return (
    <div className="space-y-8">
      {/* Page Header */}
      <div>
        <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Painel de Controlo</h1>
        <p className="text-sm text-slate-500 font-medium">Bem-vindo de volta! Aqui está o resumo operacional da sua empresa para hoje.</p>
      </div>

      {/* KPI Grid */}
      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {/* KPI 1 */}
        <div className="enterprise-card p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Colaboradores</p>
              <h3 className="text-3xl font-extrabold text-slate-800 mt-2">{kpis.employees}</h3>
            </div>
            <div className="p-3 rounded-xl bg-blue-50 border border-blue-100 text-blue-600">
              <Users className="h-6 w-6" />
            </div>
          </div>
          <div className="mt-4 flex items-center gap-1.5 text-xs text-green-650 font-bold">
            <TrendingUp className="h-3.5 w-3.5" />
            <span>+2 este mês</span>
          </div>
        </div>

        {/* KPI 2 */}
        <div className="enterprise-card p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Artigos em Stock</p>
              <h3 className="text-3xl font-extrabold text-slate-800 mt-2">{kpis.products}</h3>
            </div>
            <div className="p-3 rounded-xl bg-amber-50 border border-amber-100 text-amber-600">
              <Package className="h-6 w-6" />
            </div>
          </div>
          <div className="mt-4 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
            <Clock className="h-3.5 w-3.5 text-slate-400" />
            <span>Atualizado há 1h</span>
          </div>
        </div>

        {/* KPI 3 */}
        <div className="enterprise-card p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Faturação do Mês</p>
              <h3 className="text-3xl font-extrabold text-slate-800 mt-2">
                {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(kpis.monthly_sales)}
              </h3>
            </div>
            <div className="p-3 rounded-xl bg-green-50 border border-green-100 text-green-600">
              <TrendingUp className="h-6 w-6" />
            </div>
          </div>
          <div className="mt-4 flex items-center gap-1.5 text-xs text-green-650 font-bold">
            <TrendingUp className="h-3.5 w-3.5" />
            <span>+14.2% vs mês anterior</span>
          </div>
        </div>

        {/* KPI 4 */}
        <div className="enterprise-card p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-xs font-semibold text-slate-400 uppercase tracking-wider">Saldo de Tesouraria</p>
              <h3 className="text-3xl font-extrabold text-slate-800 mt-2">
                {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(kpis.treasury_balance)}
              </h3>
            </div>
            <div className="p-3 rounded-xl bg-purple-50 border border-purple-100 text-purple-600">
              <CreditCard className="h-6 w-6" />
            </div>
          </div>
          <div className="mt-4 flex items-center gap-1.5 text-xs text-red-500 font-bold">
            <TrendingDown className="h-3.5 w-3.5" />
            <span>-3.1% saídas de caixa</span>
          </div>
        </div>
      </div>

      {/* Main Charts & Tables */}
      <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
        {/* Sales Chart Container (Placeholder visual premium para esta fase) */}
        <div className="enterprise-card p-6 lg:col-span-2 flex flex-col justify-between">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-extrabold text-slate-800 text-lg">Evolução de Faturação</h3>
            <span className="text-xs font-bold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full uppercase">Anual</span>
          </div>
          {/* Visual Chart Simulation */}
          <div className="h-64 flex items-end justify-between gap-2 pt-6">
            {data?.salesChartData?.map((val: number, idx: number) => {
              const heightPercentage = Math.max(10, Math.min(100, (val / (Math.max(...data.salesChartData) || 1)) * 100));
              return (
                <div key={idx} className="flex-1 flex flex-col items-center gap-2 group cursor-pointer">
                  <div className="w-full bg-slate-100 rounded-t-lg h-full flex items-end overflow-hidden">
                    <div 
                      style={{ height: `${heightPercentage}%` }} 
                      className="w-full bg-blue-600 group-hover:bg-blue-500 transition-all duration-300 rounded-t-lg shadow-[0_-4px_12px_rgba(37,99,235,0.2)]"
                    />
                  </div>
                  <span className="text-[10px] font-bold text-slate-400 group-hover:text-slate-800 transition-colors">
                    {data.months[idx]}
                  </span>
                </div>
              );
            })}
          </div>
        </div>

        {/* Despesas por Fornecedor */}
        <div className="enterprise-card p-6 flex flex-col justify-between">
          <div>
            <h3 className="font-extrabold text-slate-800 text-lg mb-4">Despesas por Credor</h3>
            <div className="space-y-4">
              {data?.expenseLabels?.slice(0, 5).map((label: string, idx: number) => {
                const total = data.expenseData.reduce((a: number, b: number) => a + b, 0) || 1;
                const percentage = ((data.expenseData[idx] / total) * 100).toFixed(1);
                return (
                  <div key={idx} className="space-y-1.5">
                    <div className="flex justify-between text-xs font-bold">
                      <span className="text-slate-700 truncate max-w-[150px]">{label}</span>
                      <span className="text-slate-500">{percentage}%</span>
                    </div>
                    <div className="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-205">
                      <div 
                        style={{ width: `${percentage}%` }} 
                        className="bg-slate-800 h-full rounded-full"
                      />
                    </div>
                  </div>
                );
              })}
              {(!data?.expenseLabels || data.expenseLabels.length === 0) && (
                <p className="text-xs text-slate-400 py-6 text-center">Nenhum registo de despesa para este ano.</p>
              )}
            </div>
          </div>
          <div className="pt-4 border-t border-slate-100 flex items-center justify-between">
            <span className="text-xs text-slate-400 font-semibold">Atualização automática</span>
            <button className="text-xs text-slate-500 hover:text-slate-800 font-bold flex items-center gap-1">
              Ver Relatório <ArrowRight className="h-3 w-3" />
            </button>
          </div>
        </div>
      </div>

      {/* Recent Sales Table */}
      <div className="enterprise-table-container">
        <div className="p-6 border-b border-slate-200/80 flex items-center justify-between bg-white">
          <h3 className="font-extrabold text-slate-850 text-lg">Últimas Transações de Vendas</h3>
          <button className="enterprise-btn enterprise-btn-secondary py-1.5 px-3">
            Ver Todas
          </button>
        </div>
        <table className="enterprise-table">
          <thead>
            <tr>
              <th>Documento</th>
              <th>Cliente</th>
              <th>Data Emissão</th>
              <th>Valor Total</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            {recentSales.map((sale: any) => (
              <tr key={sale.id}>
                <td className="font-bold text-slate-900">
                  {sale.doc_type} {sale.doc_number}
                </td>
                <td className="font-semibold text-slate-700">
                  {sale.customer ? sale.customer.name : 'Cliente Final'}
                </td>
                <td>{new Date(sale.date).toLocaleDateString('pt-AO')}</td>
                <td className="font-bold text-slate-900">
                  {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(sale.total_amount)}
                </td>
                <td>
                  <span className={`enterprise-badge ${
                    sale.status === 'ISSUED' || sale.status === 'PROCESSED'
                      ? 'badge-success'
                      : sale.status === 'CANCELLED' || sale.status === 'REVERSED'
                      ? 'badge-danger'
                      : 'badge-warning'
                  }`}>
                    {sale.status}
                  </span>
                </td>
              </tr>
            ))}
            {recentSales.length === 0 && (
              <tr>
                <td colSpan={5} className="text-center text-slate-400 py-8">
                  Nenhuma transação recente encontrada.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
