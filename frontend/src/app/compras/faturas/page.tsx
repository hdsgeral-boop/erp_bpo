'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  FileText, 
  Search, 
  Plus, 
  X, 
  Calendar,
  Building2,
  DollarSign
} from 'lucide-react';

export default function FaturasCompraPage() {
  const [invoices, setInvoices] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  
  // Creation state
  const [showCreate, setShowCreate] = useState(false);
  const [suppliers, setSuppliers] = useState<any[]>([]);
  const [products, setProducts] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    supplier_id: '',
    invoice_number: '',
    date: new Date().toISOString().split('T')[0],
    items: [{ product_id: '', quantity: '1', unit_price: '0' }]
  });

  const fetchInvoices = async () => {
    setLoading(true);
    try {
      const response = await api.get('/compras/faturas');
      setInvoices(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar faturas de fornecedores.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const loadCreateData = async () => {
    try {
      const response = await api.get('/compras/faturas-data');
      setSuppliers(response.data.suppliers || []);
      setProducts(response.data.products || []);
      if (response.data.suppliers?.length > 0) {
        setFormData(prev => ({ ...prev, supplier_id: response.data.suppliers[0].id.toString() }));
      }
    } catch (err) {
      console.error('Erro ao carregar dados de criação de faturas.', err);
    }
  };

  useEffect(() => {
    fetchInvoices();
    loadCreateData();
  }, []);

  const handleCreateInvoice = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (!formData.invoice_number) {
      setError('Indique o número da fatura de fornecedor.');
      return;
    }

    try {
      const payload = {
        ...formData,
        supplier_id: Number(formData.supplier_id),
        items: formData.items.map(item => ({
          product_id: Number(item.product_id),
          quantity: Number(item.quantity),
          unit_price: Number(item.unit_price)
        }))
      };

      const response = await api.post('/compras/faturas', payload);
      if (response.data.success) {
        alert(response.data.message);
        setShowCreate(false);
        setFormData({
          supplier_id: suppliers[0]?.id?.toString() || '',
          invoice_number: '',
          date: new Date().toISOString().split('T')[0],
          items: [{ product_id: '', quantity: '1', unit_price: '0' }]
        });
        fetchInvoices();
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Falha ao registar a fatura de fornecedor.');
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Faturas de Fornecedores</h1>
          <p className="text-sm text-slate-500 font-medium">Controlo, validação e lançamento contabilístico de despesas e compras externas.</p>
        </div>
        <button 
          onClick={() => setShowCreate(!showCreate)}
          className="enterprise-btn enterprise-btn-primary flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          <span>{showCreate ? 'Cancelar' : 'Registar Fatura'}</span>
        </button>
      </div>

      {showCreate ? (
        /* Create Invoice Form */
        <div className="enterprise-card p-6 bg-white border border-slate-200 shadow-xl max-w-3xl">
          <h3 className="font-extrabold text-slate-800 text-lg mb-4">Registar Nova Fatura de Fornecedor</h3>
          <form onSubmit={handleCreateInvoice} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Número da Fatura</label>
                <input 
                  type="text"
                  value={formData.invoice_number}
                  onChange={(e) => setFormData({ ...formData, invoice_number: e.target.value })}
                  placeholder="Ex: FT-A/12345"
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data da Fatura</label>
                <input 
                  type="date"
                  value={formData.date}
                  onChange={(e) => setFormData({ ...formData, date: e.target.value })}
                  className="enterprise-input"
                />
              </div>
            </div>

            {/* Items table */}
            <div className="space-y-3">
              <h4 className="font-bold text-slate-700 text-sm">Artigos Faturados</h4>
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

                  <div className="w-36">
                    <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1">Custo Unitário (Kz)</label>
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
                onClick={() => setFormData({ ...formData, items: [...formData.items, { product_id: '', quantity: '1', unit_price: '0' }] })}
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
              Confirmar Lançamento
            </button>
          </form>
        </div>
      ) : null}

      {/* Invoices list table */}
      <div className="enterprise-table-container">
        {loading ? (
          <div className="flex items-center justify-center py-20 bg-white">
            <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
          </div>
        ) : (
          <table className="enterprise-table">
            <thead>
              <tr>
                <th>Número Fatura</th>
                <th>Fornecedor</th>
                <th>Data</th>
                <th>Total Lançado</th>
                <th>Lançamento Contabilístico</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              {invoices.map((inv: any) => (
                <tr key={inv.id}>
                  <td className="font-bold text-slate-900">{inv.invoice_number}</td>
                  <td className="font-semibold text-slate-800">{inv.supplier?.name || 'Fornecedor Externo'}</td>
                  <td>{new Date(inv.date).toLocaleDateString('pt-AO')}</td>
                  <td className="font-mono font-bold text-slate-900">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(inv.total_amount)}
                  </td>
                  <td>
                    <span className={`text-[10px] font-bold px-2 py-0.5 rounded ${inv.is_posted ? 'bg-green-50 text-green-700 border border-green-150' : 'bg-slate-100 text-slate-500'}`}>
                      {inv.is_posted ? 'Lançado no Diário' : 'Rascunho'}
                    </span>
                  </td>
                  <td>
                    <span className="enterprise-badge badge-success">
                      {inv.status}
                    </span>
                  </td>
                </tr>
              ))}
              {invoices.length === 0 && (
                <tr>
                  <td colSpan={6} className="text-center text-slate-400 py-12">
                    Nenhuma fatura de compra registada até ao momento.
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
