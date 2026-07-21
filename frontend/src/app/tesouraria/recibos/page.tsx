'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  DollarSign, 
  Search, 
  Plus, 
  X, 
  Calendar,
  Layers,
  ArrowUpRight,
  ArrowDownLeft,
  XCircle
} from 'lucide-react';

export default function DocumentosTesourariaPage() {
  const [documents, setDocuments] = useState<any[]>([]);
  const [category, setCategory] = useState<'receipt' | 'payment'>('receipt'); // receipt = Entradas (Recibos), payment = Saídas (Pagamentos)
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  
  // Creation state
  const [showCreate, setShowCreate] = useState(false);
  const [entities, setEntities] = useState<any[]>([]);
  const [bankAccounts, setBankAccounts] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    entity_type: 'customer', // customer, supplier, other
    entity_id: '',
    total_amount: '',
    payment_method: 'CASH',
    payment_reference: '',
    notes: '',
    date: new Date().toISOString().split('T')[0]
  });

  const fetchDocuments = async () => {
    setLoading(true);
    try {
      const response = await api.get(`/tesouraria/documentos/${category}`);
      setDocuments(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar documentos de tesouraria.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const loadCreateData = async () => {
    try {
      // Carrega terceiros para associar ao recibo/pagamento
      const response = await api.get('/vendas/documentos'); // de forma simplificada, ou um endpoint de terceiros se existir. 
      // Vamos tentar carregar terceiros
      const entityRes = await api.get('/rh/funcionarios'); // Exemplo
      setEntities(entityRes.data.data || entityRes.data || []);
    } catch (err) {
      console.error('Erro ao carregar entidades de suporte.', err);
    }
  };

  useEffect(() => {
    fetchDocuments();
  }, [category]);

  useEffect(() => {
    loadCreateData();
  }, []);

  const handleCreateDocument = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (!formData.total_amount || Number(formData.total_amount) <= 0) {
      setError('Indique um valor válido para a transação.');
      return;
    }

    try {
      const payload = {
        ...formData,
        total_amount: Number(formData.total_amount),
        entity_id: formData.entity_id ? Number(formData.entity_id) : 1 // fallback
      };

      const response = await api.post(`/tesouraria/documentos/${category}`, payload);
      if (response.data.success) {
        alert(response.data.message);
        setShowCreate(false);
        setFormData({
          entity_type: 'customer',
          entity_id: '',
          total_amount: '',
          payment_method: 'CASH',
          payment_reference: '',
          notes: '',
          date: new Date().toISOString().split('T')[0]
        });
        fetchDocuments();
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Falha ao lançar documento de tesouraria.');
    }
  };

  const handleCancel = async (id: number) => {
    if (!confirm('Deseja anular este lançamento de tesouraria? O diário contabilístico será estornado.')) return;
    try {
      const response = await api.post(`/tesouraria/documentos/${category}/${id}/anular`);
      alert(response.data.message);
      fetchDocuments();
    } catch (err: any) {
      alert(err.response?.data?.message || 'Erro ao anular documento.');
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Movimentos de Tesouraria</h1>
          <p className="text-sm text-slate-500 font-medium">Controle de fluxos de caixa, emissão de recibos de entrada e registo de pagamentos efetuados.</p>
        </div>
        <button 
          onClick={() => setShowCreate(!showCreate)}
          className="enterprise-btn enterprise-btn-primary flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          <span>{showCreate ? 'Cancelar' : category === 'receipt' ? 'Emitir Recibo' : 'Registar Pagamento'}</span>
        </button>
      </div>

      {/* Category selector Tab */}
      <div className="flex gap-2 border-b border-slate-200 pb-px">
        <button
          onClick={() => setCategory('receipt')}
          className={`pb-3 px-4 text-xs font-bold border-b-2 transition-all cursor-pointer ${category === 'receipt' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600'}`}
        >
          Entradas de Caixa (Recibos)
        </button>
        <button
          onClick={() => setCategory('payment')}
          className={`pb-3 px-4 text-xs font-bold border-b-2 transition-all cursor-pointer ${category === 'payment' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600'}`}
        >
          Saídas de Caixa (Pagamentos)
        </button>
      </div>

      {showCreate ? (
        /* Create Transaction Form */
        <div className="enterprise-card p-6 bg-white border border-slate-200 shadow-xl max-w-2xl">
          <h3 className="font-extrabold text-slate-800 text-lg mb-4">
            {category === 'receipt' ? 'Novo Recibo de Recebimento' : 'Novo Registo de Pagamento'}
          </h3>
          <form onSubmit={handleCreateDocument} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Tipo de Entidade</label>
                <select
                  value={formData.entity_type}
                  onChange={(e) => setFormData({ ...formData, entity_type: e.target.value })}
                  className="enterprise-input"
                >
                  <option value="customer">Cliente</option>
                  <option value="supplier">Fornecedor</option>
                  <option value="employee">Colaborador</option>
                  <option value="other">Outros / Geral</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Entidade Beneficiada/Origem</label>
                <select
                  value={formData.entity_id}
                  onChange={(e) => setFormData({ ...formData, entity_id: e.target.value })}
                  className="enterprise-input"
                >
                  <option value="">Selecione...</option>
                  {entities.map((ent) => (
                    <option key={ent.id} value={ent.id}>{ent.name || `${ent.first_name} ${ent.last_name}`}</option>
                  ))}
                  {entities.length === 0 && (
                    <option value="1">Consumidor Final / Geral</option>
                  )}
                </select>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Valor da Transação (Kz)</label>
                <input 
                  type="number"
                  value={formData.total_amount}
                  onChange={(e) => setFormData({ ...formData, total_amount: e.target.value })}
                  placeholder="0.00"
                  className="enterprise-input"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Método</label>
                <select
                  value={formData.payment_method}
                  onChange={(e) => setFormData({ ...formData, payment_method: e.target.value })}
                  className="enterprise-input"
                >
                  <option value="CASH">Numerário</option>
                  <option value="BANK_TRANSFER">Transferência Bancária</option>
                  <option value="CARD">Cartão Multicaixa</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Data</label>
                <input 
                  type="date"
                  value={formData.date}
                  onChange={(e) => setFormData({ ...formData, date: e.target.value })}
                  className="enterprise-input"
                />
              </div>
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Referência de Pagamento / ID Transação</label>
              <input 
                type="text"
                value={formData.payment_reference}
                onChange={(e) => setFormData({ ...formData, payment_reference: e.target.value })}
                placeholder="Ex: BCI-TRANSF-1283712 ou n.º do talão"
                className="enterprise-input"
              />
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-400 uppercase mb-2">Notas / Descrição do Lançamento</label>
              <textarea
                value={formData.notes}
                onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                placeholder="Ex: Liquidação da fatura de fornecedor FT-2026/12 ou adiantamento por serviços."
                className="enterprise-input h-20 resize-none"
              />
            </div>

            {error && (
              <div className="p-3 bg-red-50 border border-red-200 text-red-750 text-xs rounded-lg font-medium">
                {error}
              </div>
            )}

            <button type="submit" className="enterprise-btn enterprise-btn-primary px-6 py-2.5">
              Confirmar Lançamento de Caixa
            </button>
          </form>
        </div>
      ) : null}

      {/* Table List of documents */}
      <div className="enterprise-table-container">
        {loading ? (
          <div className="flex items-center justify-center py-20 bg-white">
            <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
          </div>
        ) : (
          <table className="enterprise-table">
            <thead>
              <tr>
                <th>Número Documento</th>
                <th>Data</th>
                <th>Origem/Destino</th>
                <th>Forma de Liquidação</th>
                <th>Valor Total</th>
                <th>Estado</th>
                <th className="text-right">Ações</th>
              </tr>
            </thead>
            <tbody>
              {documents.map((doc: any) => (
                <tr key={doc.id}>
                  <td className="font-bold text-slate-900">
                    <div className="flex items-center gap-2">
                      {category === 'receipt' ? (
                        <ArrowDownLeft className="h-4 w-4 text-green-600" />
                      ) : (
                        <ArrowUpRight className="h-4 w-4 text-red-500" />
                      )}
                      <span>{doc.doc_number || `REC-${doc.id}`}</span>
                    </div>
                  </td>
                  <td>{new Date(doc.date || doc.payment_date).toLocaleDateString('pt-AO')}</td>
                  <td className="font-semibold text-slate-700 capitalize">
                    {doc.notes?.includes('Liq. POS') ? 'Consumidor Final (POS)' : (doc.third_party?.name || 'Cliente Final/Geral')}
                  </td>
                  <td>{doc.payment_method}</td>
                  <td className="font-mono font-bold text-slate-900">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(doc.total_amount)}
                  </td>
                  <td>
                    <span className={`enterprise-badge ${
                      doc.status === 'ISSUED' || doc.status === 'COMPLETED'
                        ? 'badge-success'
                        : 'badge-danger'
                    }`}>
                      {doc.status}
                    </span>
                  </td>
                  <td className="text-right">
                    {doc.status !== 'CANCELLED' ? (
                      <button 
                        onClick={() => handleCancel(doc.id)}
                        className="p-1 hover:bg-red-50 rounded text-red-500 border border-transparent hover:border-red-200 cursor-pointer"
                        title="Anular Movimento"
                      >
                        <XCircle className="h-4 w-4" />
                      </button>
                    ) : (
                      <span className="text-xs text-slate-400 font-semibold italic">Anulado</span>
                    )}
                  </td>
                </tr>
              ))}
              {documents.length === 0 && (
                <tr>
                  <td colSpan={7} className="text-center text-slate-400 py-12">
                    Nenhum documento lançado nesta categoria de tesouraria.
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
