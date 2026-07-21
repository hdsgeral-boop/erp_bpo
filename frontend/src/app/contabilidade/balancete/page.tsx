'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  BookOpen, 
  Calendar, 
  Search, 
  Printer, 
  Download 
} from 'lucide-react';

export default function BalancetePage() {
  const [accounts, setAccounts] = useState<any[]>([]);
  const [year, setYear] = useState(new Date().getFullYear());
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const fetchTrialBalance = async () => {
    setLoading(true);
    try {
      const response = await api.get('/contabilidade/trial-balance', {
        params: { year }
      });
      setAccounts(response.data.accounts || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar o balancete de verificação.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchTrialBalance();
  }, [year]);

  const filteredAccounts = accounts.filter(acc => 
    acc.code.includes(search) || acc.name.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Balancete de Verificação</h1>
          <p className="text-sm text-slate-500 font-medium">Balancete consolidado acumulado por conta de razão.</p>
        </div>
        <div className="flex gap-2">
          <button className="enterprise-btn enterprise-btn-secondary flex items-center gap-2">
            <Printer className="h-4 w-4" />
            <span>Imprimir</span>
          </button>
          <button className="enterprise-btn enterprise-btn-secondary flex items-center gap-2">
            <Download className="h-4 w-4" />
            <span>Exportar PDF</span>
          </button>
        </div>
      </div>

      {/* Filter toolbar */}
      <div className="enterprise-card p-6 bg-white flex flex-col md:flex-row gap-4 items-center justify-between shadow-sm">
        <div className="relative flex-1 w-full">
          <Search className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
          <input
            type="text"
            placeholder="Filtrar por conta ou descrição..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="enterprise-input pl-9"
          />
        </div>

        <div className="flex items-center gap-2 w-full md:w-auto shrink-0">
          <Calendar className="h-4 w-4 text-slate-400" />
          <select
            value={year}
            onChange={(e) => setYear(Number(e.target.value))}
            className="enterprise-input py-1.5 text-xs w-28"
          >
            <option value={2026}>Exercício 2026</option>
            <option value={2025}>Exercício 2025</option>
            <option value={2024}>Exercício 2024</option>
          </select>
        </div>
      </div>

      {/* Trial Balance Table */}
      <div className="enterprise-table-container">
        {loading ? (
          <div className="flex items-center justify-center py-20 bg-white">
            <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
          </div>
        ) : error ? (
          <div className="p-6 text-center text-red-650 bg-white">{error}</div>
        ) : (
          <table className="enterprise-table">
            <thead>
              <tr>
                <th>Código Conta</th>
                <th>Descrição / Classe</th>
                <th>Tipo</th>
                <th className="text-right">Débitos Acum.</th>
                <th className="text-right">Créditos Acum.</th>
                <th className="text-right">Saldo do Período</th>
              </tr>
            </thead>
            <tbody>
              {filteredAccounts.map((acc: any) => (
                <tr 
                  key={acc.id}
                  className={acc.type === 'I' ? 'bg-slate-50 font-bold border-t border-slate-205' : ''}
                >
                  <td className="font-mono text-xs">{acc.code}</td>
                  <td className="font-semibold">{acc.name}</td>
                  <td>
                    <span className={`text-[10px] font-bold px-2 py-0.5 rounded ${acc.type === 'I' ? 'bg-blue-150 text-blue-800' : 'bg-slate-100 text-slate-650'}`}>
                      {acc.type === 'I' ? 'INTEGRADORA' : 'MOVIMENTO'}
                    </span>
                  </td>
                  <td className="text-right font-mono font-bold text-slate-900">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(acc.total_debit || 0)}
                  </td>
                  <td className="text-right font-mono font-bold text-slate-900">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(acc.total_credit || 0)}
                  </td>
                  <td className={`text-right font-mono font-extrabold ${acc.balance >= 0 ? 'text-green-650' : 'text-red-655'}`}>
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(acc.balance || 0)}
                  </td>
                </tr>
              ))}
              {filteredAccounts.length === 0 && (
                <tr>
                  <td colSpan={6} className="text-center text-slate-400 py-12">
                    Nenhuma conta encontrada com o código especificado.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
