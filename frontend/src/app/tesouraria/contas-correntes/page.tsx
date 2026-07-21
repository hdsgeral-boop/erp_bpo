'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  Building, 
  Search, 
  ArrowLeft, 
  Calendar, 
  Printer, 
  TrendingUp, 
  TrendingDown, 
  Layers 
} from 'lucide-react';

export default function ContasCorrentesPage() {
  const [partners, setPartners] = useState<any[]>([]);
  const [search, setSearch] = useState('');
  const [partnerType, setPartnerType] = useState<'customer' | 'supplier'>('customer');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  // Selected account statement state
  const [selectedPartner, setSelectedPartner] = useState<any | null>(null);
  const [statement, setStatement] = useState<any[]>([]);
  const [startDate, setStartDate] = useState('2026-01-01');
  const [endDate, setEndDate] = useState('2026-12-31');
  const [statementLoading, setStatementLoading] = useState(false);

  const fetchCurrentAccounts = async () => {
    setLoading(true);
    try {
      const response = await api.get('/tesouraria/contas-correntes', {
        params: { search, type: partnerType }
      });
      setPartners(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar saldos das contas correntes.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const fetchStatement = async (partnerId: number) => {
    setStatementLoading(true);
    try {
      const response = await api.get(`/tesouraria/contas-correntes/${partnerId}`, {
        params: { start_date: startDate, end_date: endDate }
      });
      setStatement(response.data.movements || response.data || []);
    } catch (err) {
      console.error('Erro ao carregar extrato de conta corrente.', err);
    } finally {
      setStatementLoading(false);
    }
  };

  useEffect(() => {
    fetchCurrentAccounts();
  }, [partnerType]);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    fetchCurrentAccounts();
  };

  const selectPartnerAccount = (partner: any) => {
    setSelectedPartner(partner);
    fetchStatement(partner.id);
  };

  const handleUpdateStatementDates = (e: React.FormEvent) => {
    e.preventDefault();
    if (selectedPartner) {
      fetchStatement(selectedPartner.id);
    }
  };

  return (
    <div className="space-y-6">
      {/* If a partner account is selected, show statement details */}
      {selectedPartner ? (
        <div className="space-y-6">
          {/* Header */}
          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <button 
              onClick={() => setSelectedPartner(null)}
              className="flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm cursor-pointer w-fit"
            >
              <ArrowLeft className="h-4 w-4" />
              <span>Voltar aos Saldos</span>
            </button>
            <div className="flex gap-2">
              <button className="enterprise-btn enterprise-btn-secondary flex items-center gap-2">
                <Printer className="h-4 w-4" />
                <span>Imprimir Extrato</span>
              </button>
            </div>
          </div>

          {/* Account Card details */}
          <div className="enterprise-card p-6 bg-white border border-slate-200 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
              <span className="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded border border-slate-200 uppercase">
                Ficha de Conta Corrente
              </span>
              <h3 className="font-extrabold text-slate-850 text-xl mt-1.5">{selectedPartner.name}</h3>
              <p className="text-xs text-slate-400 font-semibold mt-1">NIF: {selectedPartner.nif || 'Consumidor Final'}</p>
            </div>

            <div className="flex items-center gap-6">
              <div className="text-right">
                <span className="text-[10px] font-bold text-slate-400 uppercase">Saldo Pendente</span>
                <h4 className={`text-2xl font-black mt-0.5 ${selectedPartner.balance >= 0 ? 'text-green-650' : 'text-red-655'}`}>
                  {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(selectedPartner.balance || 0)}
                </h4>
              </div>
            </div>
          </div>

          {/* Statement date range parameters */}
          <div className="enterprise-card p-6 bg-white shadow-sm">
            <form onSubmit={handleUpdateStatementDates} className="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data Inicial</label>
                <input 
                  type="date"
                  value={startDate}
                  onChange={(e) => setStartDate(e.target.value)}
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data Final</label>
                <input 
                  type="date"
                  value={endDate}
                  onChange={(e) => setEndDate(e.target.value)}
                  className="enterprise-input"
                />
              </div>

              <button type="submit" className="enterprise-btn enterprise-btn-primary flex items-center justify-center gap-2 py-3">
                <Calendar className="h-4 w-4" />
                <span>Atualizar Extrato</span>
              </button>
            </form>
          </div>

          {/* Movements Timeline Table */}
          <div className="enterprise-table-container">
            {statementLoading ? (
              <div className="flex items-center justify-center py-20 bg-white">
                <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
              </div>
            ) : (
              <table className="enterprise-table">
                <thead>
                  <tr>
                    <th>Data Lançamento</th>
                    <th>Documento</th>
                    <th>Descrição / Histórico</th>
                    <th className="text-right">Débitos (AOA)</th>
                    <th className="text-right">Créditos (AOA)</th>
                    <th className="text-right">Saldo Acumulado</th>
                  </tr>
                </thead>
                <tbody>
                  {statement.map((move: any, idx: number) => (
                    <tr key={idx}>
                      <td>{new Date(move.date).toLocaleDateString('pt-AO')}</td>
                      <td className="font-bold text-slate-900">{move.document || move.reference || '—'}</td>
                      <td>{move.description || move.notes || 'Lançamento de conta corrente'}</td>
                      <td className="text-right font-mono font-bold text-slate-900">
                        {move.debit > 0 ? new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(move.debit) : '—'}
                      </td>
                      <td className="text-right font-mono font-bold text-slate-900">
                        {move.credit > 0 ? new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(move.credit) : '—'}
                      </td>
                      <td className={`text-right font-mono font-extrabold ${move.balance >= 0 ? 'text-green-650' : 'text-red-655'}`}>
                        {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(move.balance)}
                      </td>
                    </tr>
                  ))}
                  {statement.length === 0 && (
                    <tr>
                      <td colSpan={6} className="text-center text-slate-400 py-12">
                        Nenhum lançamento registado no período de datas selecionado.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            )}
          </div>
        </div>
      ) : (
        /* Balance Listing page */
        <div className="space-y-6">
          {/* Header */}
          <div>
            <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Contas Correntes</h1>
            <p className="text-sm text-slate-500 font-medium">Consulte e extraia extratos de movimentos de terceiros (Clientes e Fornecedores).</p>
          </div>

          {/* Search bar & Type tab filter */}
          <div className="enterprise-card p-6 bg-white flex flex-col md:flex-row gap-4 items-center justify-between shadow-sm">
            <div className="flex gap-2 w-full md:w-auto">
              <button
                onClick={() => setPartnerType('customer')}
                className={`px-4 py-2 rounded-lg text-xs font-bold transition-all border ${partnerType === 'customer' ? 'bg-blue-600 text-white border-blue-500 shadow' : 'bg-slate-50 text-slate-600 border-slate-205'}`}
              >
                Clientes
              </button>
              <button
                onClick={() => setPartnerType('supplier')}
                className={`px-4 py-2 rounded-lg text-xs font-bold transition-all border ${partnerType === 'supplier' ? 'bg-blue-600 text-white border-blue-500 shadow' : 'bg-slate-50 text-slate-600 border-slate-205'}`}
              >
                Fornecedores
              </button>
            </div>

            <form onSubmit={handleSearchSubmit} className="flex-1 w-full md:max-w-md flex gap-2">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                <input
                  type="text"
                  placeholder="Pesquisar por nome ou NIF..."
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="enterprise-input pl-9"
                />
              </div>
              <button type="submit" className="enterprise-btn enterprise-btn-secondary px-6">
                Filtrar
              </button>
            </form>
          </div>

          {/* Table list of partners and current balances */}
          <div className="enterprise-table-container">
            {loading ? (
              <div className="flex items-center justify-center py-20 bg-white">
                <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
              </div>
            ) : (
              <table className="enterprise-table">
                <thead>
                  <tr>
                    <th>Entidade</th>
                    <th>NIF</th>
                    <th className="text-right">Total Faturado (Débito)</th>
                    <th className="text-right">Total Pago (Crédito)</th>
                    <th className="text-right">Saldo Corrente</th>
                    <th className="text-right">Ação</th>
                  </tr>
                </thead>
                <tbody>
                  {partners.map((partner: any) => (
                    <tr key={partner.id}>
                      <td className="font-bold text-slate-800">{partner.name}</td>
                      <td>{partner.nif || '999999999'}</td>
                      <td className="text-right font-mono font-bold text-slate-900">
                        {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(partner.total_debit || 0)}
                      </td>
                      <td className="text-right font-mono font-bold text-slate-950">
                        {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(partner.total_credit || 0)}
                      </td>
                      <td className={`text-right font-mono font-extrabold ${partner.balance >= 0 ? 'text-green-650' : 'text-red-655'}`}>
                        {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(partner.balance || 0)}
                      </td>
                      <td className="text-right">
                        <button 
                          onClick={() => selectPartnerAccount(partner)}
                          className="text-xs font-bold text-blue-650 hover:text-blue-550 transition-colors"
                        >
                          Ver Extrato →
                        </button>
                      </td>
                    </tr>
                  ))}
                  {partners.length === 0 && (
                    <tr>
                      <td colSpan={6} className="text-center text-slate-400 py-12">
                        Nenhum parceiro comercial registado.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
