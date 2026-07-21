'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  BookOpen, 
  Search, 
  Plus, 
  X, 
  Check, 
  Info,
  ChevronDown
} from 'lucide-react';

export default function DiariosContabilisticosPage() {
  const [entries, setEntries] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  
  // Creation state
  const [showCreate, setShowCreate] = useState(false);
  const [accounts, setAccounts] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    doc_date: new Date().toISOString().split('T')[0],
    entry_date: new Date().toISOString().split('T')[0],
    doc_number: '',
    description: '',
    lines: [
      { account_code: '', type_dc: 'D', value: '0', description: '' },
      { account_code: '', type_dc: 'C', value: '0', description: '' }
    ]
  });

  const fetchEntries = async () => {
    setLoading(true);
    try {
      const response = await api.get('/contabilidade/diarios');
      setEntries(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar lançamentos de diário.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const loadCreateData = async () => {
    try {
      const response = await api.get('/contabilidade/diarios-data');
      setAccounts(response.data.accounts || []);
    } catch (err) {
      console.error('Erro ao carregar plano de contas.', err);
    }
  };

  useEffect(() => {
    fetchEntries();
    loadCreateData();
  }, []);

  const handleCreateEntry = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    // Validations
    if (!formData.doc_number || !formData.description) {
      setError('Por favor, preencha o número do documento e a descrição geral.');
      return;
    }

    // Verify lines balance
    let dSum = 0;
    let cSum = 0;
    for (const ln of formData.lines) {
      const val = Number(ln.value);
      if (val <= 0) {
        setError('Todas as linhas de lançamento precisam de ter valores positivos.');
        return;
      }
      if (ln.type_dc === 'D') dSum += val;
      else cSum += val;
    }

    if (dSum.toFixed(2) !== cSum.toFixed(2)) {
      setError(`Lançamento desequilibrado! Total Débitos: ${dSum} Kz | Total Créditos: ${cSum} Kz.`);
      return;
    }

    try {
      const payload = {
        ...formData,
        lines: formData.lines.map(ln => ({
          ...ln,
          value: Number(ln.value)
        }))
      };

      const response = await api.post('/contabilidade/diarios', payload);
      if (response.data.success) {
        alert(response.data.message);
        setShowCreate(false);
        setFormData({
          doc_date: new Date().toISOString().split('T')[0],
          entry_date: new Date().toISOString().split('T')[0],
          doc_number: '',
          description: '',
          lines: [
            { account_code: '', type_dc: 'D', value: '0', description: '' },
            { account_code: '', type_dc: 'C', value: '0', description: '' }
          ]
        });
        fetchEntries();
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Falha ao processar lançamento no diário.');
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Diários Contabilísticos</h1>
          <p className="text-sm text-slate-500 font-medium">Registo cronológico de todos os movimentos financeiros e diários gerais.</p>
        </div>
        <button 
          onClick={() => setShowCreate(!showCreate)}
          className="enterprise-btn enterprise-btn-primary flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          <span>{showCreate ? 'Cancelar' : 'Lançamento Manual'}</span>
        </button>
      </div>

      {showCreate ? (
        /* Create Entry Form */
        <div className="enterprise-card p-6 bg-white border border-slate-200 shadow-xl max-w-4xl">
          <h3 className="font-extrabold text-slate-800 text-lg mb-4">Lançamento Contabilístico por Partida Dobrada</h3>
          <form onSubmit={handleCreateEntry} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Número Doc.</label>
                <input 
                  type="text"
                  value={formData.doc_number}
                  onChange={(e) => setFormData({ ...formData, doc_number: e.target.value })}
                  placeholder="Ex: DIARIO-2026/001"
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Descrição Geral</label>
                <input 
                  type="text"
                  value={formData.description}
                  onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                  placeholder="Ex: Regularização de saldos de caixa"
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data do Doc.</label>
                <input 
                  type="date"
                  value={formData.doc_date}
                  onChange={(e) => setFormData({ ...formData, doc_date: e.target.value })}
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data Lançamento</label>
                <input 
                  type="date"
                  value={formData.entry_date}
                  onChange={(e) => setFormData({ ...formData, entry_date: e.target.value })}
                  className="enterprise-input"
                />
              </div>
            </div>

            {/* Account double entry lines */}
            <div className="space-y-3">
              <h4 className="font-bold text-slate-700 text-sm">Linhas de Lançamento (Mínimo 2 Partidas)</h4>
              {formData.lines.map((line, index) => (
                <div key={index} className="flex gap-4 items-end">
                  <div className="flex-1">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Conta de Movimento</label>
                    <select
                      value={line.account_code}
                      onChange={(e) => {
                        const newLines = [...formData.lines];
                        newLines[index].account_code = e.target.value;
                        setFormData({ ...formData, lines: newLines });
                      }}
                      className="enterprise-input py-1.5 text-xs font-mono"
                    >
                      <option value="">Selecione a conta...</option>
                      {accounts.map((acc) => (
                        <option key={acc.id} value={acc.code}>{acc.code} - {acc.name}</option>
                      ))}
                    </select>
                  </div>

                  <div className="w-32">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Tipo Movimento</label>
                    <select
                      value={line.type_dc}
                      onChange={(e) => {
                        const newLines = [...formData.lines];
                        newLines[index].type_dc = e.target.value;
                        setFormData({ ...formData, lines: newLines });
                      }}
                      className="enterprise-input py-1.5 text-xs"
                    >
                      <option value="D">DÉBITO</option>
                      <option value="C">CRÉDITO</option>
                    </select>
                  </div>

                  <div className="w-36">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Valor (Kz)</label>
                    <input 
                      type="number"
                      value={line.value}
                      onChange={(e) => {
                        const newLines = [...formData.lines];
                        newLines[index].value = e.target.value;
                        setFormData({ ...formData, lines: newLines });
                      }}
                      placeholder="0.00"
                      className="enterprise-input py-1.5 text-xs"
                    />
                  </div>

                  <div className="flex-1">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nota da Linha (Opcional)</label>
                    <input 
                      type="text"
                      value={line.description}
                      onChange={(e) => {
                        const newLines = [...formData.lines];
                        newLines[index].description = e.target.value;
                        setFormData({ ...formData, lines: newLines });
                      }}
                      placeholder="Histórico da linha"
                      className="enterprise-input py-1.5 text-xs"
                    />
                  </div>

                  {formData.lines.length > 2 && (
                    <button 
                      type="button" 
                      onClick={() => {
                        setFormData({ ...formData, lines: formData.lines.filter((_, idx) => idx !== index) });
                      }}
                      className="p-2 text-red-500 hover:bg-red-50 border border-slate-200 rounded"
                    >
                      <X className="h-4 w-4" />
                    </button>
                  )}
                </div>
              ))}

              <button
                type="button"
                onClick={() => setFormData({ ...formData, lines: [...formData.lines, { account_code: '', type_dc: 'D', value: '0', description: '' }] })}
                className="text-xs font-bold text-blue-600 hover:text-blue-500"
              >
                + Adicionar Outra Partida
              </button>
            </div>

            {error && (
              <div className="p-3 bg-red-50 border border-red-200 text-red-750 text-xs rounded-lg font-medium">
                {error}
              </div>
            )}

            <button type="submit" className="enterprise-btn enterprise-btn-primary px-6 py-2.5">
              Validar e Lançar no Razão
            </button>
          </form>
        </div>
      ) : null}

      {/* Entries List table */}
      <div className="enterprise-table-container">
        {loading ? (
          <div className="flex items-center justify-center py-20 bg-white">
            <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
          </div>
        ) : (
          <table className="enterprise-table">
            <thead>
              <tr>
                <th>Data</th>
                <th>Doc. Ref</th>
                <th>Histórico Geral</th>
                <th>Conta</th>
                <th>Movimento</th>
                <th className="text-right">Valor Lançado</th>
              </tr>
            </thead>
            <tbody>
              {entries.map((ent: any) => (
                <tr key={ent.id}>
                  <td>{new Date(ent.entry_date).toLocaleDateString('pt-AO')}</td>
                  <td className="font-bold text-slate-900">{ent.doc_number}</td>
                  <td>{ent.description}</td>
                  <td className="font-mono text-xs">{ent.account_code}</td>
                  <td>
                    <span className={`text-[10px] font-bold px-2 py-0.5 rounded ${ent.type_dc === 'D' ? 'bg-blue-50 text-blue-700 border border-blue-150' : 'bg-amber-50 text-amber-700 border border-amber-150'}`}>
                      {ent.type_dc === 'D' ? 'DÉBITO' : 'CRÉDITO'}
                    </span>
                  </td>
                  <td className="text-right font-mono font-bold text-slate-900">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(ent.value)}
                  </td>
                </tr>
              ))}
              {entries.length === 0 && (
                <tr>
                  <td colSpan={6} className="text-center text-slate-400 py-12">
                    Nenhum lançamento contabilístico registado nos diários.
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
