'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { X, Download, FileText, Building, User, Calendar, ShieldCheck, CheckCircle2 } from 'lucide-react';

interface DocumentDetailModalProps {
  documentId: number | null;
  onClose: () => void;
}

export default function DocumentDetailModal({ documentId, onClose }: DocumentDetailModalProps) {
  const [doc, setDoc] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!documentId) return;
    setLoading(true);
    api.get(`/vendas/documentos/detalhes/${documentId}`)
      .then(res => {
        setDoc(res.data.data);
      })
      .catch(err => {
        console.error('Erro ao carregar detalhes:', err);
      })
      .finally(() => {
        setLoading(false);
      });
  }, [documentId]);

  if (!documentId) return null;

  const handleDownloadPdf = () => {
    window.open(`${api.defaults.baseURL}/vendas/documentos/${documentId}/pdf`, '_blank');
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'ISSUED':
        return <span className="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full border border-emerald-300">Emitido</span>;
      case 'CANCELLED':
        return <span className="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full border border-red-300">Cancelado</span>;
      case 'DRAFT':
      default:
        return <span className="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full border border-amber-300">Rascunho</span>;
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
      <div className="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-slate-200">
        {/* Header */}
        <div className="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-600 rounded-lg">
              <FileText className="h-5 w-5 text-white" />
            </div>
            <div>
              <h2 className="text-lg font-bold">Detalhes do Documento Comercial</h2>
              <p className="text-xs text-slate-400">{doc?.doc_number || 'Carregando...'}</p>
            </div>
          </div>
          <button onClick={onClose} className="p-1 text-slate-400 hover:text-white rounded-lg transition-colors cursor-pointer">
            <X className="h-6 w-6" />
          </button>
        </div>

        {/* Content Body */}
        <div className="p-6 overflow-y-auto space-y-6 flex-1">
          {loading ? (
            <div className="flex items-center justify-center py-16">
              <div className="animate-spin h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full" />
            </div>
          ) : !doc ? (
            <div className="text-center py-12 text-slate-500">Documento não encontrado.</div>
          ) : (
            <>
              {/* Document Overview Header Box */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div>
                  <span className="text-xs font-semibold text-slate-400 uppercase">Documento</span>
                  <p className="text-base font-extrabold text-slate-900">{doc.doc_number}</p>
                  <p className="text-xs font-medium text-slate-500">{doc.doc_type === 'FT' ? 'Fatura' : doc.doc_type === 'FR' ? 'Fatura-Recibo' : doc.doc_type}</p>
                </div>
                <div>
                  <span className="text-xs font-semibold text-slate-400 uppercase">Data de Emissão</span>
                  <p className="text-sm font-bold text-slate-800">{new Date(doc.date).toLocaleDateString('pt-AO')}</p>
                  <div className="mt-1">{getStatusBadge(doc.status)}</div>
                </div>
                <div>
                  <span className="text-xs font-semibold text-slate-400 uppercase">Cliente</span>
                  <p className="text-sm font-bold text-slate-800">{doc.customer?.name || 'Cliente Final'}</p>
                  <p className="text-xs text-slate-500">NIF: {doc.customer?.nif || 'Consumidor Final'}</p>
                </div>
              </div>

              {/* Items Table */}
              <div>
                <h3 className="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                  <span>Itens / Linhas do Documento</span>
                </h3>
                <div className="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                  <table className="w-full text-left text-sm">
                    <thead className="bg-slate-100 text-slate-700 font-semibold text-xs border-b border-slate-200">
                      <tr>
                        <th className="p-3">Artigo / Descrição</th>
                        <th className="p-3 text-center">Qtd.</th>
                        <th className="p-3 text-right">Preço Un.</th>
                        <th className="p-3 text-right">Imposto</th>
                        <th className="p-3 text-right">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                      {doc.items && doc.items.length > 0 ? (
                        doc.items.map((item: any, idx: number) => (
                          <tr key={idx} className="hover:bg-slate-50">
                            <td className="p-3 font-semibold text-slate-800">
                              {item.product?.name || `Artigo #${item.product_id}`}
                            </td>
                            <td className="p-3 text-center font-bold text-slate-700">{item.quantity}</td>
                            <td className="p-3 text-right text-slate-700">
                              {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(item.unit_price)}
                            </td>
                            <td className="p-3 text-right text-slate-500">
                              {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(item.tax_amount || 0)}
                            </td>
                            <td className="p-3 text-right font-bold text-slate-900">
                              {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(item.subtotal || (item.quantity * item.unit_price))}
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr>
                          <td colSpan={5} className="text-center p-6 text-slate-400">Sem itens registados.</td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>
              </div>

              {/* Totals & AGT Certification Footer */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 items-end pt-2">
                <div className="p-4 bg-blue-50/60 rounded-xl border border-blue-200/80 text-xs text-blue-900 space-y-1">
                  <div className="flex items-center gap-1.5 font-bold text-blue-700">
                    <ShieldCheck className="h-4 w-4" />
                    <span>Certificação AGT Angola</span>
                  </div>
                  <p className="font-mono text-[11px] text-slate-600 break-all">Hash: {doc.hash || '4a1f-Processado por programa validado n.º 000/AGT/2026'}</p>
                </div>

                <div className="bg-slate-900 text-white p-4 rounded-xl space-y-2 text-right">
                  <div className="flex justify-between text-xs text-slate-300">
                    <span>Incidência / Subtotal:</span>
                    <span>{new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(doc.total_amount - (doc.total_tax || 0))}</span>
                  </div>
                  <div className="flex justify-between text-xs text-slate-300">
                    <span>Imposto (IVA 14%):</span>
                    <span>{new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(doc.total_tax || 0)}</span>
                  </div>
                  <div className="flex justify-between text-base font-extrabold text-white border-t border-slate-800 pt-2">
                    <span>Total a Pagar:</span>
                    <span className="text-emerald-400">{new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(doc.total_amount)}</span>
                  </div>
                </div>
              </div>
            </>
          )}
        </div>

        {/* Footer Actions */}
        <div className="px-6 py-4 bg-slate-100 border-t border-slate-200 flex justify-end gap-3">
          <button
            onClick={onClose}
            className="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer"
          >
            Fechar
          </button>
          <button
            onClick={handleDownloadPdf}
            className="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl flex items-center gap-2 shadow-md transition-colors cursor-pointer"
          >
            <Download className="h-4 w-4" />
            <span>Descarregar PDF</span>
          </button>
        </div>
      </div>
    </div>
  );
}
