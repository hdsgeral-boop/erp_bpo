'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  FileText, 
  Search, 
  Plus, 
  Check, 
  X, 
  Calendar,
  User,
  Building
} from 'lucide-react';

export default function PedidosCompraPage() {
  const [requests, setRequests] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  
  // Creation state
  const [showCreate, setShowCreate] = useState(false);
  const [departments, setDepartments] = useState<any[]>([]);
  const [products, setProducts] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    requester_name: '',
    department_id: '',
    date: new Date().toISOString().split('T')[0],
    notes: '',
    items: [{ product_id: '', quantity: '1', notes: '' }]
  });

  const fetchRequests = async () => {
    setLoading(true);
    try {
      const response = await api.get('/compras/pedidos');
      setRequests(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar pedidos de compra.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const loadCreateData = async () => {
    try {
      const response = await api.get('/compras/pedidos-data');
      setDepartments(response.data.departments || []);
      setProducts(response.data.products || []);
      if (response.data.departments?.length > 0) {
        setFormData(prev => ({ ...prev, department_id: response.data.departments[0].id.toString() }));
      }
    } catch (err) {
      console.error('Erro ao carregar dados auxiliares.', err);
    }
  };

  useEffect(() => {
    fetchRequests();
    loadCreateData();
  }, []);

  const handleCreateRequest = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    
    // Validations
    if (!formData.requester_name) {
      setError('Por favor, indique o nome do requisitante.');
      return;
    }

    try {
      const payload = {
        ...formData,
        department_id: Number(formData.department_id),
        items: formData.items.map(item => ({
          product_id: Number(item.product_id),
          quantity: Number(item.quantity),
          notes: item.notes
        }))
      };

      const response = await api.post('/compras/pedidos', payload);
      if (response.data.success) {
        alert(response.data.message);
        setShowCreate(false);
        setFormData({
          requester_name: '',
          department_id: departments[0]?.id?.toString() || '',
          date: new Date().toISOString().split('T')[0],
          notes: '',
          items: [{ product_id: '', quantity: '1', notes: '' }]
        });
        fetchRequests();
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Falha ao submeter o pedido.');
    }
  };

  const handleApprove = async (id: number) => {
    try {
      const response = await api.post(`/compras/pedidos/${id}/aprovar`);
      alert(response.data.message);
      fetchRequests();
    } catch (err: any) {
      alert(err.response?.data?.message || 'Erro ao aprovar pedido.');
    }
  };

  const handleReject = async (id: number) => {
    try {
      const response = await api.post(`/compras/pedidos/${id}/rejeitar`);
      alert(response.data.message);
      fetchRequests();
    } catch (err: any) {
      alert(err.response?.data?.message || 'Erro ao rejeitar pedido.');
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Pedidos Internos de Compra</h1>
          <p className="text-sm text-slate-500 font-medium">Controlo interno de requisições de material submetidas por departamentos.</p>
        </div>
        <button 
          onClick={() => setShowCreate(!showCreate)}
          className="enterprise-btn enterprise-btn-primary flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          <span>{showCreate ? 'Cancelar' : 'Nova Requisição'}</span>
        </button>
      </div>

      {showCreate ? (
        /* Create Request form */
        <div className="enterprise-card p-6 bg-white border border-slate-200 shadow-xl max-w-3xl">
          <h3 className="font-extrabold text-slate-800 text-lg mb-4">Nova Requisição de Material</h3>
          <form onSubmit={handleCreateRequest} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Requisitante</label>
                <input 
                  type="text"
                  value={formData.requester_name}
                  onChange={(e) => setFormData({ ...formData, requester_name: e.target.value })}
                  placeholder="Nome do colaborador"
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Departamento</label>
                <select
                  value={formData.department_id}
                  onChange={(e) => setFormData({ ...formData, department_id: e.target.value })}
                  className="enterprise-input"
                >
                  {departments.map((dept) => (
                    <option key={dept.id} value={dept.id}>{dept.name}</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data da Requisição</label>
                <input 
                  type="date"
                  value={formData.date}
                  onChange={(e) => setFormData({ ...formData, date: e.target.value })}
                  className="enterprise-input"
                />
              </div>
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Notas / Justificação</label>
              <textarea
                value={formData.notes}
                onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                placeholder="Descreva a finalidade ou urgência da requisição..."
                className="enterprise-input h-20 resize-none"
              />
            </div>

            {/* Items table */}
            <div className="space-y-3">
              <h4 className="font-bold text-slate-700 text-sm">Artigos Solicitados</h4>
              {formData.items.map((item, index) => (
                <div key={index} className="flex gap-4 items-end">
                  <div className="flex-1">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Artigo</label>
                    <select
                      value={item.product_id}
                      onChange={(e) => {
                        const newItems = [...formData.items];
                        newItems[index].product_id = e.target.value;
                        setFormData({ ...formData, items: newItems });
                      }}
                      className="enterprise-input py-1.5 text-xs"
                    >
                      <option value="">Selecione um artigo...</option>
                      {products.map((p) => (
                        <option key={p.id} value={p.id}>{p.name}</option>
                      ))}
                    </select>
                  </div>

                  <div className="w-24">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Qtd</label>
                    <input 
                      type="number" 
                      value={item.quantity}
                      onChange={(e) => {
                        const newItems = [...formData.items];
                        newItems[index].quantity = e.target.value;
                        setFormData({ ...formData, items: newItems });
                      }}
                      className="enterprise-input py-1.5 text-xs"
                    />
                  </div>

                  <div className="flex-1">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Observações</label>
                    <input 
                      type="text" 
                      value={item.notes}
                      onChange={(e) => {
                        const newItems = [...formData.items];
                        newItems[index].notes = e.target.value;
                        setFormData({ ...formData, items: newItems });
                      }}
                      placeholder="Ex: Marca preferencial, tamanho"
                      className="enterprise-input py-1.5 text-xs"
                    />
                  </div>

                  {formData.items.length > 1 && (
                    <button 
                      type="button" 
                      onClick={() => {
                        setFormData({ ...formData, items: formData.items.filter((_, idx) => idx !== index) });
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
                onClick={() => setFormData({ ...formData, items: [...formData.items, { product_id: '', quantity: '1', notes: '' }] })}
                className="text-xs font-bold text-blue-600 hover:text-blue-500"
              >
                + Adicionar Outro Artigo
              </button>
            </div>

            {error && (
              <div className="p-3 bg-red-50 border border-red-200 text-red-750 text-xs rounded-lg font-medium">
                {error}
              </div>
            )}

            <button type="submit" className="enterprise-btn enterprise-btn-primary px-6 py-2.5">
              Submeter Requisição
            </button>
          </form>
        </div>
      ) : null}

      {/* Requests table */}
      <div className="enterprise-table-container">
        {loading ? (
          <div className="flex items-center justify-center py-20 bg-white">
            <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
          </div>
        ) : (
          <table className="enterprise-table">
            <thead>
              <tr>
                <th>Requisitante</th>
                <th>Departamento</th>
                <th>Data</th>
                <th>Notas</th>
                <th>Estado</th>
                <th className="text-right">Ações de Aprovação</th>
              </tr>
            </thead>
            <tbody>
              {requests.map((req: any) => (
                <tr key={req.id}>
                  <td className="font-bold text-slate-800">{req.requester_name}</td>
                  <td className="font-semibold text-slate-600">
                    {req.department?.name || 'Não atribuído'}
                  </td>
                  <td>{new Date(req.date).toLocaleDateString('pt-AO')}</td>
                  <td className="truncate max-w-[200px]">{req.notes || '—'}</td>
                  <td>
                    <span className={`enterprise-badge ${
                      req.status === 'APPROVED' || req.status === 'CONVERTED'
                        ? 'badge-success'
                        : req.status === 'REJECTED'
                        ? 'badge-danger'
                        : 'badge-warning'
                    }`}>
                      {req.status}
                    </span>
                  </td>
                  <td className="text-right">
                    {req.status === 'PENDING' ? (
                      <div className="inline-flex gap-2">
                        <button 
                          onClick={() => handleApprove(req.id)}
                          className="p-1 hover:bg-green-50 rounded text-green-600 border border-green-200 flex items-center gap-1 text-xs px-2 font-bold cursor-pointer"
                        >
                          <Check className="h-3 w-3" /> Aprovar
                        </button>
                        <button 
                          onClick={() => handleReject(req.id)}
                          className="p-1 hover:bg-red-50 rounded text-red-500 border border-red-200 flex items-center gap-1 text-xs px-2 font-bold cursor-pointer"
                        >
                          <X className="h-3 w-3" /> Rejeitar
                        </button>
                      </div>
                    ) : (
                      <span className="text-xs text-slate-400 font-semibold italic">Decidido</span>
                    )}
                  </td>
                </tr>
              ))}
              {requests.length === 0 && (
                <tr>
                  <td colSpan={6} className="text-center text-slate-400 py-12">
                    Nenhuma requisição de material submetida até ao momento.
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
