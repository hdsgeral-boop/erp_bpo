'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  Building2, 
  Search, 
  Plus, 
  X, 
  Calendar,
  ShieldAlert,
  Trash2,
  DollarSign,
  TrendingDown
} from 'lucide-react';

export default function AtivosImobilizadosPage() {
  const [assets, setAssets] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  
  // Creation state
  const [showCreate, setShowCreate] = useState(false);
  const [categories, setCategories] = useState<any[]>([]);
  const [vendors, setVendors] = useState<any[]>([]);
  const [departments, setDepartments] = useState<any[]>([]);
  const [employees, setEmployees] = useState<any[]>([]);
  
  const [formData, setFormData] = useState({
    code: '',
    name: '',
    description: '',
    category_id: '',
    vendor_id: '',
    department_id: '',
    employee_id: '',
    acquisition_date: new Date().toISOString().split('T')[0],
    acquisition_cost: '',
    salvage_value: '0',
    useful_life_years: '5',
    depreciation_method: 'straight_line',
    serial_number: '',
    location: '',
    status: 'active'
  });

  const fetchAssets = async () => {
    setLoading(true);
    try {
      const response = await api.get('/ativos');
      setAssets(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar ativos imobilizados.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const loadCreateData = async () => {
    try {
      const response = await api.get('/ativos/create-data');
      setCategories(response.data.categories || []);
      setVendors(response.data.vendors || []);
      setDepartments(response.data.departments || []);
      setEmployees(response.data.employees || []);
      
      setFormData(prev => ({
        ...prev,
        category_id: response.data.categories[0]?.id?.toString() || '',
        vendor_id: response.data.vendors[0]?.id?.toString() || '',
        department_id: response.data.departments[0]?.id?.toString() || '',
        employee_id: response.data.employees[0]?.id?.toString() || ''
      }));
    } catch (err) {
      console.error('Erro ao carregar dados auxiliares de ativos.', err);
    }
  };

  useEffect(() => {
    fetchAssets();
    loadCreateData();
  }, []);

  const handleCreateAsset = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (!formData.code || !formData.name || !formData.acquisition_cost) {
      setError('Por favor, preencha os campos obrigatórios (Código, Nome, Custo).');
      return;
    }

    try {
      const payload = {
        ...formData,
        category_id: Number(formData.category_id),
        vendor_id: formData.vendor_id ? Number(formData.vendor_id) : null,
        department_id: formData.department_id ? Number(formData.department_id) : null,
        employee_id: formData.employee_id ? Number(formData.employee_id) : null,
        acquisition_cost: Number(formData.acquisition_cost),
        salvage_value: Number(formData.salvage_value),
        useful_life_years: Number(formData.useful_life_years)
      };

      const response = await api.post('/ativos', payload);
      if (response.data.success) {
        alert(response.data.message);
        setShowCreate(false);
        setFormData({
          code: '',
          name: '',
          description: '',
          category_id: categories[0]?.id?.toString() || '',
          vendor_id: vendors[0]?.id?.toString() || '',
          department_id: departments[0]?.id?.toString() || '',
          employee_id: employees[0]?.id?.toString() || '',
          acquisition_date: new Date().toISOString().split('T')[0],
          acquisition_cost: '',
          salvage_value: '0',
          useful_life_years: '5',
          depreciation_method: 'straight_line',
          serial_number: '',
          location: '',
          status: 'active'
        });
        fetchAssets();
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Falha ao registar o ativo imobilizado.');
    }
  };

  const handleWriteOff = async (id: number) => {
    if (!confirm('Deseja abater este ativo imobilizado do balanço?')) return;
    try {
      const response = await api.delete(`/ativos/${id}`);
      alert(response.data.message || 'Ativo abatido com sucesso.');
      fetchAssets();
    } catch (err: any) {
      alert(err.response?.data?.message || 'Erro ao abater ativo.');
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Ativos Imobilizados</h1>
          <p className="text-sm text-slate-500 font-medium">Controlo, amortizações e registo de património e bens de longa duração.</p>
        </div>
        <button 
          onClick={() => setShowCreate(!showCreate)}
          className="enterprise-btn enterprise-btn-primary flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          <span>{showCreate ? 'Cancelar' : 'Registar Ativo'}</span>
        </button>
      </div>

      {showCreate ? (
        /* Create Fixed Asset Form */
        <div className="enterprise-card p-6 bg-white border border-slate-200 shadow-xl max-w-4xl">
          <h3 className="font-extrabold text-slate-800 text-lg mb-4">Registar Novo Ativo Imobilizado</h3>
          <form onSubmit={handleCreateAsset} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Código Ativo *</label>
                <input 
                  type="text"
                  value={formData.code}
                  onChange={(e) => setFormData({ ...formData, code: e.target.value })}
                  placeholder="Ex: IMOB-2026-001"
                  className="enterprise-input"
                />
              </div>

              <div className="md:col-span-2">
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Designação do Bem *</label>
                <input 
                  type="text"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  placeholder="Ex: Computador MacBook Pro 16"
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Categoria</label>
                <select
                  value={formData.category_id}
                  onChange={(e) => setFormData({ ...formData, category_id: e.target.value })}
                  className="enterprise-input"
                >
                  {categories.map((cat) => (
                    <option key={cat.id} value={cat.id}>{cat.name}</option>
                  ))}
                </select>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Custo Aquisição * (Kz)</label>
                <input 
                  type="number"
                  value={formData.acquisition_cost}
                  onChange={(e) => setFormData({ ...formData, acquisition_cost: e.target.value })}
                  placeholder="0.00"
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Valor Residual (Kz)</label>
                <input 
                  type="number"
                  value={formData.salvage_value}
                  onChange={(e) => setFormData({ ...formData, salvage_value: e.target.value })}
                  placeholder="0.00"
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Vida Útil (Anos)</label>
                <input 
                  type="number"
                  value={formData.useful_life_years}
                  onChange={(e) => setFormData({ ...formData, useful_life_years: e.target.value })}
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data Aquisição</label>
                <input 
                  type="date"
                  value={formData.acquisition_date}
                  onChange={(e) => setFormData({ ...formData, acquisition_date: e.target.value })}
                  className="enterprise-input"
                />
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Fornecedor</label>
                <select
                  value={formData.vendor_id}
                  onChange={(e) => setFormData({ ...formData, vendor_id: e.target.value })}
                  className="enterprise-input"
                >
                  <option value="">Selecione...</option>
                  {vendors.map((v) => (
                    <option key={v.id} value={v.id}>{v.name}</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Departamento</label>
                <select
                  value={formData.department_id}
                  onChange={(e) => setFormData({ ...formData, department_id: e.target.value })}
                  className="enterprise-input"
                >
                  <option value="">Selecione...</option>
                  {departments.map((d) => (
                    <option key={d.id} value={d.id}>{d.name}</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Colaborador Responsável</label>
                <select
                  value={formData.employee_id}
                  onChange={(e) => setFormData({ ...formData, employee_id: e.target.value })}
                  className="enterprise-input"
                >
                  <option value="">Selecione...</option>
                  {employees.map((emp) => (
                    <option key={emp.id} value={emp.id}>{emp.first_name} {emp.last_name}</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">N.º de Série / Tag</label>
                <input 
                  type="text"
                  value={formData.serial_number}
                  onChange={(e) => setFormData({ ...formData, serial_number: e.target.value })}
                  placeholder="Ex: SN-821731"
                  className="enterprise-input"
                />
              </div>
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Descrição / Detalhes</label>
              <textarea
                value={formData.description}
                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                placeholder="Ex: Localizado na sala de reuniões principal..."
                className="enterprise-input h-20 resize-none"
              />
            </div>

            {error && (
              <div className="p-3 bg-red-50 border border-red-200 text-red-750 text-xs rounded-lg font-medium">
                {error}
              </div>
            )}

            <button type="submit" className="enterprise-btn enterprise-btn-primary px-6 py-2.5">
              Confirmar Registo Patrimonial
            </button>
          </form>
        </div>
      ) : null}

      {/* Assets table list */}
      <div className="enterprise-table-container">
        {loading ? (
          <div className="flex items-center justify-center py-20 bg-white">
            <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
          </div>
        ) : (
          <table className="enterprise-table">
            <thead>
              <tr>
                <th>Código</th>
                <th>Designação</th>
                <th>Categoria</th>
                <th>Data Aquisição</th>
                <th>Custo Aquisição</th>
                <th>Vida Útil</th>
                <th>Estado</th>
                <th className="text-right">Ação</th>
              </tr>
            </thead>
            <tbody>
              {assets.map((ast: any) => (
                <tr key={ast.id}>
                  <td className="font-mono text-xs font-bold text-slate-905">{ast.code}</td>
                  <td>
                    <div>
                      <div className="font-bold text-slate-800">{ast.name}</div>
                      <div className="text-[10px] text-slate-400 font-semibold">{ast.serial_number || 'Sem serial'}</div>
                    </div>
                  </td>
                  <td className="font-semibold text-slate-600">{ast.category?.name || 'Geral'}</td>
                  <td>{new Date(ast.acquisition_date).toLocaleDateString('pt-AO')}</td>
                  <td className="font-mono font-bold text-slate-900">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(ast.acquisition_cost)}
                  </td>
                  <td>{ast.useful_life_years} anos</td>
                  <td>
                    <span className={`enterprise-badge ${
                      ast.status === 'active'
                        ? 'badge-success'
                        : ast.status === 'written_off'
                        ? 'badge-danger'
                        : 'badge-warning'
                    }`}>
                      {ast.status}
                    </span>
                  </td>
                  <td className="text-right">
                    {ast.status !== 'written_off' ? (
                      <button 
                        onClick={() => handleWriteOff(ast.id)}
                        className="p-1 hover:bg-red-50 rounded text-red-500 border border-transparent hover:border-red-200 cursor-pointer"
                        title="Abater Ativo"
                      >
                        <Trash2 className="h-4 w-4" />
                      </button>
                    ) : (
                      <span className="text-xs text-slate-400 font-semibold italic">Abatido</span>
                    )}
                  </td>
                </tr>
              ))}
              {assets.length === 0 && (
                <tr>
                  <td colSpan={8} className="text-center text-slate-400 py-12">
                    Nenhum ativo imobilizado registado.
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
