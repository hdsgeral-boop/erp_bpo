'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  FileText, 
  Search, 
  Plus, 
  X, 
  Calendar,
  DollarSign,
  User,
  Trash2
} from 'lucide-react';

export default function ContratosColaboradoresPage() {
  const [contracts, setContracts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  
  // Creation state
  const [showCreate, setShowCreate] = useState(false);
  const [employees, setEmployees] = useState<any[]>([]);
  const [infotypes, setInfotypes] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    employee_id: '',
    infotype_id: '',
    value: '',
    start_date: new Date().toISOString().split('T')[0],
    end_date: ''
  });

  const fetchContracts = async () => {
    setLoading(true);
    try {
      const response = await api.get('/rh/contratos');
      setContracts(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar contratos de colaboradores.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const loadCreateData = async () => {
    try {
      const response = await api.get('/rh/contratos-data');
      setEmployees(response.data.employees || []);
      setInfotypes(response.data.infotypes || []);
      
      setFormData(prev => ({
        ...prev,
        employee_id: response.data.employees[0]?.id?.toString() || '',
        infotype_id: response.data.infotypes[0]?.id?.toString() || ''
      }));
    } catch (err) {
      console.error('Erro ao carregar dados de contratos.', err);
    }
  };

  useEffect(() => {
    fetchContracts();
    loadCreateData();
  }, []);

  const handleCreateContract = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (!formData.employee_id || !formData.infotype_id || !formData.value) {
      setError('Por favor, preencha todos os campos obrigatórios.');
      return;
    }

    try {
      const payload = {
        ...formData,
        employee_id: Number(formData.employee_id),
        infotype_id: Number(formData.infotype_id),
        value: Number(formData.value),
        end_date: formData.end_date || null
      };

      const response = await api.post('/rh/contratos', payload);
      if (response.data.success) {
        alert(response.data.message);
        setShowCreate(false);
        setFormData({
          employee_id: employees[0]?.id?.toString() || '',
          infotype_id: infotypes[0]?.id?.toString() || '',
          value: '',
          start_date: new Date().toISOString().split('T')[0],
          end_date: ''
        });
        fetchContracts();
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Falha ao registar o contrato.');
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Deseja rescindir/eliminar este contrato de trabalho?')) return;
    try {
      const response = await api.delete(`/rh/contratos/${id}`);
      alert(response.data.message || 'Contrato removido.');
      fetchContracts();
    } catch (err: any) {
      alert(err.response?.data?.message || 'Erro ao remover contrato.');
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Contratos de Trabalho</h1>
          <p className="text-sm text-slate-500 font-medium">Gestão de vínculos contratuais, remunerações base e rubricas de colaboradores.</p>
        </div>
        <button 
          onClick={() => setShowCreate(!showCreate)}
          className="enterprise-btn enterprise-btn-primary flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          <span>{showCreate ? 'Cancelar' : 'Registar Contrato'}</span>
        </button>
      </div>

      {showCreate ? (
        /* Create Contract Form */
        <div className="enterprise-card p-6 bg-white border border-slate-200 shadow-xl max-w-2xl">
          <h3 className="font-extrabold text-slate-800 text-lg mb-4">Registar Novo Contrato / Vínculo</h3>
          <form onSubmit={handleCreateContract} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Colaborador</label>
                <select
                  value={formData.employee_id}
                  onChange={(e) => setFormData({ ...formData, employee_id: e.target.value })}
                  className="enterprise-input"
                >
                  {employees.map((emp) => (
                    <option key={emp.id} value={emp.id}>{emp.first_name} {emp.last_name} ({emp.code})</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Rubrica Salarial (Infotype)</label>
                <select
                  value={formData.infotype_id}
                  onChange={(e) => setFormData({ ...formData, infotype_id: e.target.value })}
                  className="enterprise-input"
                >
                  {infotypes.map((info) => (
                    <option key={info.id} value={info.id}>{info.name} (Cód: {info.code})</option>
                  ))}
                </select>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Valor Salário Base / Subsídio (Kz)</label>
                <input 
                  type="number"
                  value={formData.value}
                  onChange={(e) => setFormData({ ...formData, value: e.target.value })}
                  placeholder="0.00"
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data Início</label>
                <input 
                  type="date"
                  value={formData.start_date}
                  onChange={(e) => setFormData({ ...formData, start_date: e.target.value })}
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data Fim (Opcional)</label>
                <input 
                  type="date"
                  value={formData.end_date}
                  onChange={(e) => setFormData({ ...formData, end_date: e.target.value })}
                  className="enterprise-input"
                />
              </div>
            </div>

            {error && (
              <div className="p-3 bg-red-50 border border-red-200 text-red-750 text-xs rounded-lg font-medium">
                {error}
              </div>
            )}

            <button type="submit" className="enterprise-btn enterprise-btn-primary px-6 py-2.5">
              Confirmar Contrato
            </button>
          </form>
        </div>
      ) : null}

      {/* Contracts table */}
      <div className="enterprise-table-container">
        {loading ? (
          <div className="flex items-center justify-center py-20 bg-white">
            <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
          </div>
        ) : (
          <table className="enterprise-table">
            <thead>
              <tr>
                <th>Colaborador</th>
                <th>Rubrica Salarial</th>
                <th>Valor Remuneração</th>
                <th>Início</th>
                <th>Fim</th>
                <th className="text-right">Ação</th>
              </tr>
            </thead>
            <tbody>
              {contracts.map((con: any) => (
                <tr key={con.id}>
                  <td className="font-bold text-slate-800">
                    {con.employee?.first_name} {con.employee?.last_name}
                  </td>
                  <td className="font-semibold text-slate-600">{con.infotype?.name || 'Vencimento Base'}</td>
                  <td className="font-mono font-bold text-slate-900">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(con.value)}
                  </td>
                  <td>{new Date(con.start_date).toLocaleDateString('pt-AO')}</td>
                  <td>{con.end_date ? new Date(con.end_date).toLocaleDateString('pt-AO') : 'Sem termo'}</td>
                  <td className="text-right">
                    <button 
                      onClick={() => handleDelete(con.id)}
                      className="p-1 hover:bg-red-50 rounded text-red-500 border border-transparent hover:border-red-200 cursor-pointer"
                      title="Rescindir Contrato"
                    >
                      <Trash2 className="h-4 w-4" />
                    </button>
                  </td>
                </tr>
              ))}
              {contracts.length === 0 && (
                <tr>
                  <td colSpan={6} className="text-center text-slate-400 py-12">
                    Nenhum contrato registado.
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
