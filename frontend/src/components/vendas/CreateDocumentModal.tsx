'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { X, Plus, Trash2, Save, FilePlus, Building, User, Calendar, CheckCircle2, AlertCircle } from 'lucide-react';

interface CreateDocumentModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess: () => void;
}

export default function CreateDocumentModal({ isOpen, onClose, onSuccess }: CreateDocumentModalProps) {
  const [loadingOptions, setLoadingOptions] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  // Options from API
  const [customers, setCustomers] = useState<any[]>([]);
  const [warehouses, setWarehouses] = useState<any[]>([]);
  const [products, setProducts] = useState<any[]>([]);
  const [docTypes, setDocTypes] = useState<any[]>([]);

  // Form State
  const [docType, setDocType] = useState('FT');
  const [customerId, setCustomerId] = useState('');
  const [warehouseId, setWarehouseId] = useState('');
  const [date, setDate] = useState(new Date().toISOString().split('T')[0]);
  const [notes, setNotes] = useState('');
  const [items, setItems] = useState<any[]>([
    { product_id: '', quantity: 1, unit_price: 0, tax_rate: 14, subtotal: 0 }
  ]);

  useEffect(() => {
    if (!isOpen) return;
    setLoadingOptions(true);
    setError('');

    api.get('/vendas/documentos-options')
      .then(res => {
        if (res.data.success) {
          setCustomers(res.data.customers || []);
          setWarehouses(res.data.warehouses || []);
          setProducts(res.data.products || []);
          setDocTypes(res.data.doc_types || []);

          if (res.data.customers?.length > 0) setCustomerId(res.data.customers[0].id.toString());
          if (res.data.warehouses?.length > 0) setWarehouseId(res.data.warehouses[0].id.toString());
        }
      })
      .catch(err => {
        console.error('Erro ao carregar opções:', err);
        setError('Falha ao carregar clientes e produtos.');
      })
      .finally(() => {
        setLoadingOptions(false);
      });
  }, [isOpen]);

  if (!isOpen) return null;

  const handleProductChange = (index: number, productIdStr: string) => {
    const pId = parseInt(productIdStr);
    const prod = products.find(p => p.id === pId);
    
    const newItems = [...items];
    newItems[index].product_id = pId;
    if (prod) {
      newItems[index].unit_price = parseFloat(prod.unit_price) || 0;
      newItems[index].subtotal = newItems[index].quantity * newItems[index].unit_price;
    }
    setItems(newItems);
  };

  const handleQtyChange = (index: number, qtyVal: number) => {
    const newItems = [...items];
    newItems[index].quantity = qtyVal;
    newItems[index].subtotal = qtyVal * (newItems[index].unit_price || 0);
    setItems(newItems);
  };

  const handlePriceChange = (index: number, priceVal: number) => {
    const newItems = [...items];
    newItems[index].unit_price = priceVal;
    newItems[index].subtotal = (newItems[index].quantity || 1) * priceVal;
    setItems(newItems);
  };

  const addItemLine = () => {
    setItems([...items, { product_id: '', quantity: 1, unit_price: 0, tax_rate: 14, subtotal: 0 }]);
  };

  const removeItemLine = (index: number) => {
    if (items.length <= 1) return;
    setItems(items.filter((_, idx) => idx !== index));
  };

  const subtotalSum = items.reduce((acc, item) => acc + (item.subtotal || 0), 0);
  const taxSum = subtotalSum * 0.14;
  const totalAmountSum = subtotalSum + taxSum;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!customerId) {
      setError('Por favor selecione um cliente.');
      return;
    }
    if (items.some(i => !i.product_id || i.quantity <= 0)) {
      setError('Por favor preencha todos os artigos e quantidades válidas.');
      return;
    }

    setSubmitting(true);
    setError('');

    try {
      const payload = {
        doc_type: docType,
        customer_id: parseInt(customerId),
        warehouse_id: warehouseId ? parseInt(warehouseId) : null,
        date,
        notes,
        items: items.map(i => ({
          product_id: parseInt(i.product_id),
          quantity: parseFloat(i.quantity),
          unit_price: parseFloat(i.unit_price),
          tax_rate: 14
        }))
      };

      const res = await api.post('/vendas/documentos', payload);
      if (res.data.success) {
        onSuccess();
        onClose();
      } else {
        setError(res.data.message || 'Erro ao emitir documento.');
      }
    } catch (err: any) {
      console.error(err);
      setError(err.response?.data?.message || 'Falha ao comunicar com o servidor.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
      <div className="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden border border-slate-200">
        
        {/* Header */}
        <div className="px-6 py-4 bg-blue-600 text-white flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-700 rounded-lg">
              <FilePlus className="h-5 w-5 text-white" />
            </div>
            <div>
              <h2 className="text-lg font-bold">Emitir Novo Documento Comercial</h2>
              <p className="text-xs text-blue-100">Selecione o tipo de documento, cliente e adicione os artigos.</p>
            </div>
          </div>
          <button onClick={onClose} className="p-1 text-blue-200 hover:text-white rounded-lg transition-colors cursor-pointer">
            <X className="h-6 w-6" />
          </button>
        </div>

        {/* Form Body */}
        <form onSubmit={handleSubmit} className="flex flex-col flex-1 overflow-hidden">
          <div className="p-6 overflow-y-auto space-y-6 flex-1">
            
            {error && (
              <div className="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold flex items-center gap-2">
                <AlertCircle className="h-5 w-5 text-red-500 shrink-0" />
                <span>{error}</span>
              </div>
            )}

            {/* Document Header Controls */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Tipo de Documento</label>
                <select
                  value={docType}
                  onChange={(e) => setDocType(e.target.value)}
                  className="w-full rounded-xl border-slate-300 border p-2.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none"
                >
                  <option value="FT">Fatura (FT)</option>
                  <option value="FR">Fatura-Recibo (FR)</option>
                  <option value="OR">Orçamento (OR)</option>
                  <option value="PP">Fatura Pró-Forma (PP)</option>
                  <option value="NC">Nota de Crédito (NC)</option>
                  <option value="ND">Nota de Débito (ND)</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Cliente</label>
                <select
                  value={customerId}
                  onChange={(e) => setCustomerId(e.target.value)}
                  className="w-full rounded-xl border-slate-300 border p-2.5 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none"
                >
                  <option value="">Selecione um Cliente...</option>
                  {customers.map(c => (
                    <option key={c.id} value={c.id}>{c.name} (NIF: {c.nif || 'Consumidor Final'})</option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 uppercase mb-1">Data de Emissão</label>
                <input
                  type="date"
                  value={date}
                  onChange={(e) => setDate(e.target.value)}
                  className="w-full rounded-xl border-slate-300 border p-2.5 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none"
                />
              </div>
            </div>

            {/* Line Items Table */}
            <div>
              <div className="flex items-center justify-between mb-3">
                <h3 className="text-sm font-bold text-slate-800">Linhas / Artigos do Documento</h3>
                <button
                  type="button"
                  onClick={addItemLine}
                  className="text-xs font-bold bg-slate-100 hover:bg-slate-200 text-blue-700 px-3 py-1.5 rounded-lg flex items-center gap-1 transition-colors cursor-pointer"
                >
                  <Plus className="h-3.5 w-3.5" />
                  <span>Adicionar Linha</span>
                </button>
              </div>

              <div className="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <table className="w-full text-left text-sm">
                  <thead className="bg-slate-100 text-slate-700 font-semibold text-xs border-b border-slate-200">
                    <tr>
                      <th className="p-3">Artigo</th>
                      <th className="p-3 w-24 text-center">Qtd.</th>
                      <th className="p-3 w-36 text-right">Preço Unit. (Kz)</th>
                      <th className="p-3 w-28 text-center">Imposto</th>
                      <th className="p-3 w-36 text-right">Total (Kz)</th>
                      <th className="p-3 w-12"></th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {items.map((item, idx) => (
                      <tr key={idx} className="hover:bg-slate-50">
                        <td className="p-2">
                          <select
                            value={item.product_id}
                            onChange={(e) => handleProductChange(idx, e.target.value)}
                            className="w-full rounded-lg border-slate-300 border p-2 text-sm text-slate-800 font-medium outline-none"
                          >
                            <option value="">Selecione um Produto...</option>
                            {products.map(p => (
                              <option key={p.id} value={p.id}>
                                {p.code ? `[${p.code}] ` : ''}{p.name} (Stock: {p.stock_qty})
                              </option>
                            ))}
                          </select>
                        </td>
                        <td className="p-2">
                          <input
                            type="number"
                            min="1"
                            value={item.quantity}
                            onChange={(e) => handleQtyChange(idx, parseFloat(e.target.value) || 0)}
                            className="w-full text-center rounded-lg border-slate-300 border p-2 text-sm font-bold text-slate-800 outline-none"
                          />
                        </td>
                        <td className="p-2">
                          <input
                            type="number"
                            step="100"
                            value={item.unit_price}
                            onChange={(e) => handlePriceChange(idx, parseFloat(e.target.value) || 0)}
                            className="w-full text-right rounded-lg border-slate-300 border p-2 text-sm font-bold text-slate-800 outline-none"
                          />
                        </td>
                        <td className="p-2 text-center text-xs font-bold text-slate-500">
                          IVA 14%
                        </td>
                        <td className="p-2 text-right font-bold text-slate-900">
                          {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(item.subtotal || 0)}
                        </td>
                        <td className="p-2 text-center">
                          <button
                            type="button"
                            onClick={() => removeItemLine(idx)}
                            className="p-1 text-slate-400 hover:text-red-600 rounded transition-colors cursor-pointer"
                          >
                            <Trash2 className="h-4 w-4" />
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            {/* Subtotal and Total Summary */}
            <div className="flex justify-end">
              <div className="w-full sm:w-72 bg-slate-900 text-white p-4 rounded-xl space-y-2">
                <div className="flex justify-between text-xs text-slate-300">
                  <span>Incidência:</span>
                  <span>{new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(subtotalSum)}</span>
                </div>
                <div className="flex justify-between text-xs text-slate-300">
                  <span>Total IVA (14%):</span>
                  <span>{new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(taxSum)}</span>
                </div>
                <div className="flex justify-between text-base font-extrabold text-white border-t border-slate-800 pt-2">
                  <span>Total Documento:</span>
                  <span className="text-emerald-400">{new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(totalAmountSum)}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Footer Actions */}
          <div className="px-6 py-4 bg-slate-100 border-t border-slate-200 flex justify-end gap-3">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer"
            >
              Cancelar
            </button>
            <button
              type="submit"
              disabled={submitting}
              className="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-bold rounded-xl flex items-center gap-2 shadow-md transition-colors cursor-pointer"
            >
              <Save className="h-4 w-4" />
              <span>{submitting ? 'A Emitir...' : 'Emitir Documento'}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
