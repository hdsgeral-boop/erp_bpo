'use client';

import { useState } from 'react';
import api from '@/lib/api';
import { 
  FileSpreadsheet, 
  Download, 
  Calendar, 
  AlertCircle,
  CheckCircle2
} from 'lucide-react';

export default function SaftExportPage() {
  const [month, setMonth] = useState(new Date().getMonth() + 1);
  const [year, setYear] = useState(new Date().getFullYear());
  const [loading, setLoading] = useState(false);
  const [successMsg, setSuccessMsg] = useState('');
  const [errorMsg, setErrorMsg] = useState('');

  const handleExport = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setSuccessMsg('');
    setErrorMsg('');

    try {
      const response = await api.post('/vendas/saft/export', { month, year }, {
        responseType: 'blob' // Esperamos o ficheiro XML como download direto
      });

      // Criar link temporário para descarregar o blob do XML
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `SAFT_AO_${month}_${year}.xml`);
      document.body.appendChild(link);
      link.click();
      link.remove();

      setSuccessMsg('Ficheiro SAF-T (AO) gerado e descarregado com sucesso!');
    } catch (err: any) {
      setErrorMsg('Falha ao exportar SAF-T. Verifique se existem faturas emitidas no período.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-2xl mx-auto space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Exportar SAF-T (AO)</h1>
        <p className="text-sm text-slate-500 font-medium">
          Gere o ficheiro de auditoria fiscal SAF-T para submissão no portal da AGT (Administração Geral Tributária de Angola).
        </p>
      </div>

      {/* Export Panel Card */}
      <div className="enterprise-card p-8 bg-white border border-slate-200 shadow-xl">
        <div className="flex items-center gap-3 mb-6">
          <div className="p-2.5 rounded-lg bg-blue-50 border border-blue-100 text-blue-650">
            <FileSpreadsheet className="h-5 w-5" />
          </div>
          <h3 className="font-extrabold text-slate-800 text-lg">Definições da Declaração</h3>
        </div>

        <form onSubmit={handleExport} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-xs font-bold text-slate-550 uppercase mb-2">Mês do Período</label>
              <select
                value={month}
                onChange={(e) => setMonth(Number(e.target.value))}
                className="enterprise-input"
              >
                <option value={1}>Janeiro</option>
                <option value={2}>Fevereiro</option>
                <option value={3}>Março</option>
                <option value={4}>Abril</option>
                <option value={5}>Maio</option>
                <option value={6}>Junho</option>
                <option value={7}>Julho</option>
                <option value={8}>Agosto</option>
                <option value={9}>Setembro</option>
                <option value={10}>Outubro</option>
                <option value={11}>Novembro</option>
                <option value={12}>Dezembro</option>
              </select>
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-550 uppercase mb-2">Ano Civil</label>
              <input
                type="number"
                value={year}
                onChange={(e) => setYear(Number(e.target.value))}
                className="enterprise-input"
              />
            </div>
          </div>

          <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-500 space-y-1.5">
            <span className="font-bold text-slate-700 block mb-1">Notas importantes:</span>
            <p>• O ficheiro XML gerado inclui todas as faturas, recibos e notas emitidos no período selecionado.</p>
            <p>• Garanta que todas as faturas estão finalizadas e assinadas (com hash válido AGT) antes da exportação.</p>
          </div>

          {successMsg && (
            <div className="p-4 bg-green-50 border border-green-200 text-green-800 text-xs rounded-xl flex items-center gap-2">
              <CheckCircle2 className="h-5 w-5 text-green-500 shrink-0" />
              <span>{successMsg}</span>
            </div>
          )}

          {errorMsg && (
            <div className="p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl flex items-center gap-2">
              <AlertCircle className="h-5 w-5 text-red-500 shrink-0" />
              <span>{errorMsg}</span>
            </div>
          )}

          <button
            type="submit"
            disabled={loading}
            className="w-full enterprise-btn enterprise-btn-primary py-3 flex items-center justify-center gap-2 disabled:opacity-50"
          >
            <Download className="h-5 w-5" />
            <span>{loading ? 'A processar XML...' : 'Gerar Ficheiro Audit SAF-T XML'}</span>
          </button>
        </form>
      </div>
    </div>
  );
}
