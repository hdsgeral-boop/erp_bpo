'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  FileBox, 
  Search, 
  Plus, 
  Check, 
  X, 
  Calendar,
  Building2,
  DollarSign,
  Download
} from 'lucide-react';

export default function EncomendasCompraPage() {
  const [orders, setOrders] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  
  // Creation state
  const [showCreate, setShowCreate] = useState(false);
  const [suppliers, setSuppliers] = useState<any[]>([]);
  const [products, setProducts] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    supplier_id: '',
    date: new Date().toISOString().split('T')[0],
    notes: '',
    items: [{ product_id: '', quantity: '1', unit_price: '0', tax_rate: '14' }]
  });

  const fetchOrders = async () => {
    setLoading(true);
    try {
      const response = await api.get('/compras/encomendas');
      setOrders(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar encomendas de compra.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const loadCreateData = async () => {
    try {
      const response = await api.get('/compras/encomendas-data');
      setSuppliers(response.data.suppliers || []);
      setProducts(response.data.products || []);
      if (response.data.suppliers?.length > 0) {
        setFormData(prev => ({ ...prev, supplier_id: response.data.suppliers[0].id.toString() }));
      }
    } catch (err) {
      console.error('Erro ao carregar dados de criação de encomenda.', err);
    }
  };

  useEffect(() => {
    fetchOrders();
    loadCreateData();
  }, []);

  const handleCreateOrder = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    try {
      const payload = {
        ...formData,
        supplier_id: Number(formData.supplier_id),
        items: formData.items.map(item => ({
          product_id: Number(item.product_id),
          quantity: Number(item.quantity),
          unit_price: Number(item.unit_price),
          tax_rate: Number(item.tax_rate)
        }))
      };

      const response = await api.post('/compras/encomendas', payload);
      if (response.data.success) {
        alert(response.data.message);
        setShowCreate(false);
        setFormData({
          supplier_id: suppliers[0]?.id?.toString() || '',
          date: new Date().toISOString().split('T')[0],
          notes: '',
          items: [{ product_id: '', quantity: '1', unit_price: '0', tax_rate: '14' }]
        });
        fetchOrders();
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Falha ao submeter nota de encomenda.');
    }
  };

  const handleApprove = async (id: number) => {
    try {
      const response = await api.post(`/compras/encomendas/${id}/aprovar`);
      alert(response.data.message);
      fetchOrders();
    } catch (err: any) {
      alert(err.response?.data?.message || 'Erro ao aprovar nota de encomenda.');
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Notas de Encomenda</h1>
          <p className="text-sm text-slate-500 font-medium">Ordens de compra enviadas a fornecedores externos para aquisição de artigos ou matérias-primas.</p>
        </div>
        <button 
          onClick={() => setShowCreate(!showCreate)}
          className="enterprise-btn enterprise-btn-primary flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          <span>{showCreate ? 'Cancelar' : 'Nova Encomenda'}</span>
        </button>
      </div>

      {showCreate ? (
        /* Create Order Form */
        <div className="enterprise-card p-6 bg-white border border-slate-200 shadow-xl max-w-3xl">
          <h3 className="font-extrabold text-slate-800 text-lg mb-4">Nova Nota de Encomenda</h3>
          <form onSubmit={handleCreateOrder} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Fornecedor</label>
                <select
                  value={formData.supplier_id}
                  onChange={(e) => setFormData({ ...formData, supplier_id: e.target.value })}
                  className="enterprise-input"
                >
                  {suppliers.map((sup) => (
                    <option key={sup.id} value={sup.id}>{sup.name}</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data de Emissão</label>
                <input 
                  type="date"
                  value={formData.date}
                  onChange={(e) => setFormData({ ...formData, date: e.target.value })}
                  className="enterprise-input"
                />
              </div>
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Notas / Termos de Entrega</label>
              <textarea
                value={formData.notes}
                onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                placeholder="Ex: Entrega na nossa delegação de Viana. Pagamento a 30 dias."
                className="enterprise-input h-20 resize-none"
              />
            </div>

            {/* Items table */}
            <div className="space-y-3">
              <h4 className="font-bold text-slate-700 text-sm">Artigos a Encomendar</h4>
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

                  <div className="w-20">
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

                  <div className="w-32">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Preço Unitário (Kz)</label>
                    <input 
                      type="number" 
                      value={item.unit_price}
                      onChange={(e) => {
                        const newItems = [...formData.items];
                        newItems[index].unit_price = e.target.value;
                        setFormData({ ...formData, items: newItems });
                      }}
                      className="enterprise-input py-1.5 text-xs"
                    />
                  </div>

                  <div className="w-24">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Taxa IVA (%)</label>
                    <select
                      value={item.tax_rate}
                      onChange={(e) => {
                        const newItems = [...formData.items];
                        newItems[index].tax_rate = e.target.value;
                        setFormData({ ...formData, items: newItems });
                      }}
                      className="enterprise-input py-1.5 text-xs"
                    >
                      <option value="14">14%</option>
                      <option value="7">7%</option>
                      <option value="0">0%</option>
                    </select>
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
                onClick={() => setFormData({ ...formData, items: [...formData.items, { product_id: '', quantity: '1', unit_price: '0', tax_rate: '14' }] })}
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
              Confirmar Encomenda
            </button>
          </form>
        </div>
      ) : null}

      {/* Orders list table */}
      <div className="enterprise-table-container">
        {loading ? (
          <div className="flex items-center justify-center py-20 bg-white">
            <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
          </div>
        ) : (
          <table className="enterprise-table">
            <thead>
              <tr>
                <th>Fornecedor</th>
                <th>Data</th>
                <th>Total Mercadoria</th>
                <th>Total Imposto (IVA)</th>
                <th>Total Geral</th>
                <th>Estado</th>
                <th className="text-right">Ações</th>
              </tr>
            </thead>
            <tbody>
              {orders.map((ord: any) => (
                <tr key={ord.id}>
                  <td className="font-bold text-slate-800">{ord.supplier?.name || 'Fornecedor Externo'}</td>
                  <td>{new Date(ord.date).toLocaleDateString('pt-AO')}</td>
                  <td className="font-mono">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(ord.total_amount)}
                  </td>
                  <td className="font-mono text-slate-500">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(ord.total_tax)}
                  </td>
                  <td className="font-mono font-bold text-slate-900">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(ord.total_amount + ord.total_tax)}
                  </td>
                  <td>
                    <span className={`enterprise-badge ${
                      ord.status === 'APPROVED' || ord.status === 'COMPLETED'
                        ? 'badge-success'
                        : ord.status === 'REJECTED'
                        ? 'badge-danger'
                        : 'badge-warning'
                    }`}>
                      {ord.status}
                    </span>
                  </td>
                  <td className="text-right">
                    {ord.status === 'PENDING' ? (
                      <button 
                        onClick={() => handleApprove(ord.id)}
                        className="p-1 hover:bg-green-50 rounded text-green-600 border border-green-200 flex items-center gap-1 text-xs px-2 font-bold cursor-pointer"
                      >
                        <Check className="h-3 w-3" /> Aprovar
                      </button>
                    ) : (
                      <span className="text-xs text-slate-400 font-semibold italic">Enviado</span>
                    )}
                  </td>
                </tr>
              ))}
              {orders.length === 0 && (
                <tr>
                  <td colSpan={7} className="text-center text-slate-400 py-12">
                    Nenhuma nota de encomenda registada até ao momento.
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
