'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  FileSpreadsheet, 
  Search, 
  Filter, 
  Plus, 
  Trash2, 
  FileText, 
  Download 
} from 'lucide-react';
import CreateDocumentModal from '@/components/vendas/CreateDocumentModal';
import DocumentDetailModal from '@/components/vendas/DocumentDetailModal';

export default function DocumentosPage() {
  const [documents, setDocuments] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [search, setSearch] = useState('');
  const [docType, setDocType] = useState('');
  const [status, setStatus] = useState('');

  // Modals state
  const [isCreateOpen, setIsCreateOpen] = useState(false);
  const [selectedDetailId, setSelectedDetailId] = useState<number | null>(null);

  const fetchDocuments = async () => {
    setLoading(true);
    try {
      const response = await api.get('/vendas/documentos', {
        params: { search, doc_type: docType, status }
      });
      // A API retorna ou paginate ou array direto. Tratamos ambos:
      setDocuments(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar documentos comerciais.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchDocuments();
  }, [docType, status]);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    fetchDocuments();
  };

  const handleDownloadPdf = (docId: number) => {
    window.open(`${api.defaults.baseURL}/vendas/documentos/${docId}/pdf`, '_blank');
  };

  const formatStatus = (st: string) => {
    switch (st) {
      case 'ISSUED':
        return 'Emitido';
      case 'CANCELLED':
        return 'Cancelado';
      case 'DRAFT':
        return 'Rascunho';
      case 'PAID':
        return 'Pago';
      case 'PENDING':
        return 'Pendente';
      default:
        return st;
    }
  };

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Documentos de Vendas</h1>
          <p className="text-sm text-slate-500 font-medium">Consulte e crie faturas, orçamentos, notas de crédito e outros documentos comerciais.</p>
        </div>
        <button
          onClick={() => setIsCreateOpen(true)}
          className="enterprise-btn enterprise-btn-primary flex items-center gap-2 cursor-pointer shadow-md hover:bg-blue-700 transition-colors"
        >
          <Plus className="h-4 w-4" />
          <span>Novo Documento</span>
        </button>
      </div>

      {/* Filter / Search Bar */}
      <div className="enterprise-card p-6 bg-white space-y-4">
        <form onSubmit={handleSearchSubmit} className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div className="relative">
            <Search className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
            <input
              type="text"
              placeholder="Pesquisar número, cliente..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="enterprise-input pl-9"
            />
          </div>

          <div>
            <select
              value={docType}
              onChange={(e) => setDocType(e.target.value)}
              className="enterprise-input"
            >
              <option value="">Todos os Tipos</option>
              <option value="FT">Fatura (FT)</option>
              <option value="FR">Fatura-Recibo (FR)</option>
              <option value="FS">Fatura Simplificada (FS)</option>
              <option value="OR">Orçamento (OR)</option>
              <option value="NC">Nota de Crédito (NC)</option>
              <option value="ND">Nota de Débito (ND)</option>
            </select>
          </div>

          <div>
            <select
              value={status}
              onChange={(e) => setStatus(e.target.value)}
              className="enterprise-input"
            >
              <option value="">Todos os Estados</option>
              <option value="ISSUED">Emitido</option>
              <option value="DRAFT">Rascunho</option>
              <option value="CANCELLED">Cancelado</option>
            </select>
          </div>

          <button type="submit" className="enterprise-btn enterprise-btn-secondary flex items-center justify-center gap-2">
            <Filter className="h-4 w-4" />
            <span>Aplicar Filtros</span>
          </button>
        </form>
      </div>

      {/* Table grid listing */}
      <div className="enterprise-table-container">
        {loading ? (
          <div className="flex items-center justify-center py-20 bg-white">
            <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
          </div>
        ) : error ? (
          <div className="p-6 text-center text-red-650 font-medium bg-white">{error}</div>
        ) : (
          <table className="enterprise-table">
            <thead>
              <tr>
                <th>Número</th>
                <th>Tipo</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Valor Total</th>
                <th>Pago</th>
                <th>Estado</th>
                <th className="text-right">Ações</th>
              </tr>
            </thead>
            <tbody>
              {documents.map((doc: any) => (
                <tr key={doc.id}>
                  <td className="font-bold text-slate-900">{doc.doc_number}</td>
                  <td>
                    <span className="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                      {doc.doc_type}
                    </span>
                  </td>
                  <td className="font-semibold text-slate-700">
                    {doc.customer ? doc.customer.name : 'Cliente Final'}
                  </td>
                  <td>{new Date(doc.date).toLocaleDateString('pt-AO')}</td>
                  <td className="font-bold text-slate-900">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(doc.total_amount)}
                  </td>
                  <td className="font-semibold text-slate-650">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(doc.amount_paid || 0)}
                  </td>
                  <td>
                    <span className={`enterprise-badge ${
                      doc.status === 'ISSUED'
                        ? 'badge-success'
                        : doc.status === 'CANCELLED'
                        ? 'badge-danger'
                        : 'badge-warning'
                    }`}>
                      {formatStatus(doc.status)}
                    </span>
                  </td>
                  <td className="text-right">
                    <div className="inline-flex gap-2">
                      <button
                        onClick={() => setSelectedDetailId(doc.id)}
                        className="p-1.5 hover:bg-slate-100 rounded text-slate-500 hover:text-blue-600 transition-colors cursor-pointer"
                        title="Ver Detalhe"
                      >
                        <FileText className="h-4 w-4" />
                      </button>
                      <button
                        onClick={() => handleDownloadPdf(doc.id)}
                        className="p-1.5 hover:bg-slate-100 rounded text-slate-500 hover:text-blue-600 transition-colors cursor-pointer"
                        title="Descarregar PDF"
                      >
                        <Download className="h-4 w-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              {documents.length === 0 && (
                <tr>
                  <td colSpan={8} className="text-center text-slate-400 py-12">
                    Nenhum documento de venda encontrado com os filtros selecionados.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        )}
      </div>

      {/* Modals */}
      <CreateDocumentModal
        isOpen={isCreateOpen}
        onClose={() => setIsCreateOpen(false)}
        onSuccess={() => fetchDocuments()}
      />

      <DocumentDetailModal
        documentId={selectedDetailId}
        onClose={() => setSelectedDetailId(null)}
      />
    </div>
  );
}
