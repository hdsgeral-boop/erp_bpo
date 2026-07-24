'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  Monitor, 
  Plus, 
  Printer, 
  CheckCircle2, 
  XCircle, 
  Trash2, 
  Edit3, 
  X, 
  Save, 
  Building,
  RefreshCw
} from 'lucide-react';

export default function PosRegistersPage() {
  const [registers, setRegisters] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  // Modal State
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [formError, setFormError] = useState('');

  // Form Fields
  const [name, setName] = useState('');
  const [terminalId, setTerminalId] = useState('');
  const [printerType, setPrinterType] = useState('browser');
  const [printerAddress, setPrinterAddress] = useState('Localhost Thermal 80mm');

  const fetchRegisters = async () => {
    setLoading(true);
    setError('');
    try {
      const response = await api.get('/vendas/pos-registers');
      setRegisters(response.data.data || response.data || []);
    } catch (err: any) {
      console.error(err);
      setError('Erro ao carregar os terminais POS.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchRegisters();
  }, []);

  const handleCreateTerminal = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!name) {
      setFormError('Por favor introduza o nome do terminal.');
      return;
    }

    setSubmitting(true);
    setFormError('');

    try {
      const payload = {
        name,
        terminal_id: terminalId || `POS-${Math.floor(100 + Math.random() * 900)}`,
        printer_type: printerType,
        printer_address: printerAddress,
        is_active: true
      };

      const res = await api.post('/vendas/pos-registers', payload);
      if (res.data.success || res.status === 200 || res.status === 201) {
        setIsModalOpen(false);
        setName('');
        setTerminalId('');
        fetchRegisters();
      } else {
        setFormError(res.data.message || 'Erro ao criar terminal.');
      }
    } catch (err: any) {
      console.error(err);
      setFormError(err.response?.data?.message || 'Falha ao guardar terminal POS.');
    } finally {
      setSubmitting(false);
    }
  };

  const handleDeleteTerminal = async (id: number) => {
    if (!confirm('Tem a certeza que pretende eliminar este terminal POS?')) return;
    try {
      await api.delete(`/vendas/pos-registers/${id}`);
      fetchRegisters();
    } catch (err: any) {
      alert(err.response?.data?.message || 'Erro ao eliminar terminal.');
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
            <Monitor className="h-6 w-6 text-blue-600" />
            <span>Configuração de Terminais POS (Caixas)</span>
          </h1>
          <p className="text-sm text-slate-500 font-medium">
            Gerencie as caixas de atendimento e impressoras térmicas associadas à empresa ativa.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={fetchRegisters}
            className="p-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl transition-colors cursor-pointer"
            title="Atualizar"
          >
            <RefreshCw className="h-4 w-4" />
          </button>
          <button
            onClick={() => setIsModalOpen(true)}
            className="enterprise-btn enterprise-btn-primary flex items-center gap-2 cursor-pointer shadow-md"
          >
            <Plus className="h-4 w-4" />
            <span>Novo Terminal POS</span>
          </button>
        </div>
      </div>

      {/* Grid listing */}
      {loading ? (
        <div className="flex items-center justify-center py-20 bg-white rounded-2xl border border-slate-200 shadow-sm">
          <div className="animate-spin h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full" />
        </div>
      ) : error ? (
        <div className="p-6 text-center text-red-650 font-semibold bg-white rounded-2xl border border-red-200">
          {error}
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {registers.map((reg: any) => (
            <div key={reg.id} className="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm hover:shadow-md transition-shadow space-y-4">
              <div className="flex items-start justify-between">
                <div className="flex items-center gap-3">
                  <div className="p-3 bg-blue-50 text-blue-600 rounded-xl border border-blue-100">
                    <Monitor className="h-6 w-6" />
                  </div>
                  <div>
                    <h3 className="font-extrabold text-slate-900 text-base">{reg.name}</h3>
                    <span className="text-xs font-mono text-slate-500 font-semibold">{reg.terminal_id || `POS-${reg.id}`}</span>
                  </div>
                </div>

                <button
                  onClick={() => handleDeleteTerminal(reg.id)}
                  className="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
                  title="Eliminar Terminal"
                >
                  <Trash2 className="h-4 w-4" />
                </button>
              </div>

              <div className="space-y-2 pt-2 border-t border-slate-100 text-xs">
                <div className="flex justify-between items-center text-slate-600">
                  <span className="font-medium flex items-center gap-1.5">
                    <Printer className="h-3.5 w-3.5 text-slate-400" />
                    <span>Impressora:</span>
                  </span>
                  <span className="font-bold text-slate-800 uppercase">{reg.printer_type || 'browser'}</span>
                </div>
                <div className="flex justify-between items-center text-slate-600">
                  <span className="font-medium">Endereço:</span>
                  <span className="font-semibold text-slate-700 truncate max-w-[150px]">{reg.printer_address || 'Padrão Sistema'}</span>
                </div>
              </div>

              <div className="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold ${
                  reg.status === 'OPEN' 
                    ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' 
                    : 'bg-slate-100 text-slate-700 border border-slate-300'
                }`}>
                  {reg.status === 'OPEN' ? <CheckCircle2 className="h-3.5 w-3.5" /> : <XCircle className="h-3.5 w-3.5" />}
                  <span>{reg.status === 'OPEN' ? 'Caixa Aberta' : 'Caixa Fechada'}</span>
                </span>

                <span className="text-[11px] font-bold text-slate-400 uppercase">
                  {reg.is_active ? 'Ativo' : 'Inativo'}
                </span>
              </div>
            </div>
          ))}

          {registers.length === 0 && (
            <div className="col-span-full p-12 text-center bg-white rounded-2xl border border-slate-200 space-y-3">
              <Monitor className="h-12 w-12 text-slate-300 mx-auto" />
              <p className="text-slate-500 font-semibold text-sm">Nenhum terminal POS configurado para esta empresa.</p>
              <button
                onClick={() => setIsModalOpen(true)}
                className="enterprise-btn enterprise-btn-primary inline-flex items-center gap-2 cursor-pointer"
              >
                <Plus className="h-4 w-4" />
                <span>Criar Primeiro Terminal</span>
              </button>
            </div>
          )}
        </div>
      )}

      {/* Create Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
          <div className="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
            <div className="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
              <div className="flex items-center gap-2.5">
                <Monitor className="h-5 w-5 text-blue-400" />
                <h2 className="font-bold text-base">Novo Terminal POS (Caixa)</h2>
              </div>
              <button onClick={() => setIsModalOpen(false)} className="p-1 text-slate-400 hover:text-white rounded cursor-pointer">
                <X className="h-5 w-5" />
              </button>
            </div>

            <form onSubmit={handleCreateTerminal} className="p-6 space-y-4">
              {formError && (
                <div className="p-3 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl">
                  {formError}
                </div>
              )}

              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Nome da Caixa / Terminal</label>
                <input
                  type="text"
                  placeholder="Ex: Caixa Principal Balcão"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  className="w-full rounded-xl border-slate-300 border p-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500 outline-none"
                  required
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Código do Terminal (ID)</label>
                <input
                  type="text"
                  placeholder="Ex: POS-01"
                  value={terminalId}
                  onChange={(e) => setTerminalId(e.target.value)}
                  className="w-full rounded-xl border-slate-300 border p-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500 outline-none"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Tipo de Impressora</label>
                  <select
                    value={printerType}
                    onChange={(e) => setPrinterType(e.target.value)}
                    className="w-full rounded-xl border-slate-300 border p-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500 outline-none"
                  >
                    <option value="browser">Navegador (Padrão)</option>
                    <option value="network">Rede (IP / Socket)</option>
                    <option value="usb">USB Térmica</option>
                    <option value="bluetooth">Bluetooth</option>
                  </select>
                </div>

                <div>
                  <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Endereço Impressora</label>
                  <input
                    type="text"
                    placeholder="Ex: 192.168.1.100 ou USB001"
                    value={printerAddress}
                    onChange={(e) => setPrinterAddress(e.target.value)}
                    className="w-full rounded-xl border-slate-300 border p-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500 outline-none"
                  />
                </div>
              </div>

              <div className="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  disabled={submitting}
                  className="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl flex items-center gap-2 shadow-md transition-colors cursor-pointer"
                >
                  <Save className="h-4 w-4" />
                  <span>{submitting ? 'A Guardar...' : 'Guardar Terminal'}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
