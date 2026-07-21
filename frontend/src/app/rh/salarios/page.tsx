'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  Calculator, 
  Calendar, 
  RefreshCw, 
  CheckCircle2, 
  XCircle, 
  Download, 
  Undo2 
} from 'lucide-react';

export default function SalariosPage() {
  const [runs, setRuns] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [processing, setProcessing] = useState(false);

  // Form states for wizard simulation
  const [month, setMonth] = useState(new Date().getMonth() + 1);
  const [year, setYear] = useState(new Date().getFullYear());

  const fetchRuns = async () => {
    setLoading(true);
    try {
      const response = await api.get('/rh/salarios');
      setRuns(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar histórico de processamentos.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchRuns();
  }, []);

  const handleProcessSimulation = async (e: React.FormEvent) => {
    e.preventDefault();
    setProcessing(true);
    setError('');
    
    try {
      // 1. Correr a simulação
      const simResponse = await api.post('/rh/salarios/processar', {
        month,
        year,
        employee_ids: [1, 2, 3] // Id de exemplo para simulação automática no dev local
      });

      const { results, totals, reference } = simResponse.data;

      // 2. Fechar o processamento de imediato para testar o fluxo de ponta a ponta
      const closeResponse = await api.post('/rh/salarios/close', {
        payroll_data: {
          month,
          year,
          reference,
          totals,
          results
        }
      });

      if (closeResponse.data.success) {
        alert(closeResponse.data.message);
        fetchRuns();
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Falha ao processar folha salarial.');
    } finally {
      setProcessing(false);
    }
  };

  const handleReverse = async (id: number) => {
    if (!confirm('Tem a certeza que deseja estornar este processamento? Esta operação irá cancelar os diários contábeis e recibos de tesouraria associados.')) {
      return;
    }

    try {
      const response = await api.post(`/rh/salarios/${id}/estornar`);
      if (response.data.success) {
        alert('Processamento estornado com sucesso!');
        fetchRuns();
      }
    } catch (err: any) {
      alert(err.response?.data?.message || 'Erro ao estornar processamento.');
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Processamento Salarial</h1>
          <p className="text-sm text-slate-500 font-medium">Gere folhas de pagamento, envie declarações fiscais e efetue o estorno de processamentos.</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Wizard execution container */}
        <div className="enterprise-card p-6 bg-white flex flex-col justify-between lg:col-span-1">
          <div>
            <div className="flex items-center gap-3 mb-6">
              <div className="p-2.5 rounded-lg bg-blue-50 border border-blue-100 text-blue-650">
                <Calculator className="h-5 w-5" />
              </div>
              <h3 className="font-extrabold text-slate-800 text-lg">Novo Processamento</h3>
            </div>

            <form onSubmit={handleProcessSimulation} className="space-y-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Mês de Referência</label>
                <select 
                  value={month} 
                  onChange={(e) => setMonth(Number(e.target.value))}
                  className="enterprise-input"
                >
                  <option value={1}>Janeiro</option>
                  <option value={2}>Fevereiro</option>
                  <option value={3}>Março</option>
                  <option value={4}>Abril</option>
                  <option value={5}>Maio</option>
                  <option value={6}>Junho</option>
                  <option value={7}>Julho</option>
                  <option value={8}>Agosto</option>
                  <option value={9}>Setembro</option>
                  <option value={10}>Outubro</option>
                  <option value={11}>Novembro</option>
                  <option value={12}>Dezembro</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Ano</label>
                <input 
                  type="number" 
                  value={year} 
                  onChange={(e) => setYear(Number(e.target.value))}
                  className="enterprise-input"
                />
              </div>

              {error && (
                <div className="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg font-medium">
                  {error}
                </div>
              )}

              <button 
                type="submit" 
                disabled={processing}
                className="w-full enterprise-btn enterprise-btn-primary flex items-center justify-center gap-2 py-3 disabled:opacity-50"
              >
                {processing ? (
                  <>
                    <RefreshCw className="animate-spin h-4 w-4" />
                    <span>A Processar...</span>
                  </>
                ) : (
                  <>
                    <Calculator className="h-4 w-4" />
                    <span>Simular e Fechar Folha</span>
                  </>
                )}
              </button>
            </form>
          </div>
        </div>

        {/* Runs History */}
        <div className="lg:col-span-2">
          <div className="enterprise-table-container">
            <div className="p-6 border-b border-slate-200/80 bg-white">
              <h3 className="font-extrabold text-slate-850 text-base">Histórico de Processamentos</h3>
            </div>
            {loading ? (
              <div className="flex items-center justify-center py-12">
                <div className="animate-spin h-6 w-6 border-2 border-blue-500 border-t-transparent rounded-full" />
              </div>
            ) : (
              <table className="enterprise-table">
                <thead>
                  <tr>
                    <th>Período</th>
                    <th>Versão</th>
                    <th>Líquido Pago</th>
                    <th>Impostos (IRT+INSS)</th>
                    <th>Estado</th>
                    <th className="text-right">Estorno</th>
                  </tr>
                </thead>
                <tbody>
                  {runs.map((run: any) => (
                    <tr key={run.id}>
                      <td className="font-bold text-slate-900">{run.reference}</td>
                      <td>V{run.version}</td>
                      <td className="font-bold text-slate-900">
                        {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(run.total_net_paid)}
                      </td>
                      <td className="font-medium text-slate-650">
                        {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(run.total_irt + run.total_inss)}
                      </td>
                      <td>
                        <span className={`enterprise-badge ${
                          run.status === 'PROCESSED'
                            ? 'badge-success'
                            : run.status === 'REVERSED'
                            ? 'badge-danger'
                            : 'badge-warning'
                        }`}>
                          {run.status}
                        </span>
                      </td>
                      <td className="text-right">
                        {run.status !== 'REVERSED' ? (
                          <button 
                            onClick={() => handleReverse(run.id)}
                            className="p-1.5 hover:bg-red-50 rounded text-red-500 hover:text-red-700 transition-colors border border-transparent hover:border-red-200 cursor-pointer"
                            title="Estornar Processamento"
                          >
                            <Undo2 className="h-4 w-4" />
                          </button>
                        ) : (
                          <span className="text-xs text-slate-400 font-semibold italic">Estornado</span>
                        )}
                      </td>
                    </tr>
                  ))}
                  {runs.length === 0 && (
                    <tr>
                      <td colSpan={6} className="text-center text-slate-400 py-10">
                        Nenhum processamento salarial executado até ao momento.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
