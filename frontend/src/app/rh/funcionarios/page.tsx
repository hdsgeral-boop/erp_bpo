'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  UserPlus, 
  Search, 
  Mail, 
  Phone, 
  Building2, 
  MapPin 
} from 'lucide-react';

export default function FuncionariosPage() {
  const [employees, setEmployees] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [search, setSearch] = useState('');

  const fetchEmployees = async () => {
    setLoading(true);
    try {
      const response = await api.get('/rh/funcionarios', {
        params: { search }
      });
      setEmployees(response.data.data || response.data || []);
    } catch (err: any) {
      setError('Erro ao carregar colaboradores.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchEmployees();
  }, []);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    fetchEmployees();
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Colaboradores</h1>
          <p className="text-sm text-slate-500 font-medium font-sans">Gestão de pessoal, cadastros ativos, contratos e salários da empresa.</p>
        </div>
        <button className="enterprise-btn enterprise-btn-primary flex items-center gap-2">
          <UserPlus className="h-4 w-4" />
          <span>Adicionar Colaborador</span>
        </button>
      </div>

      {/* Search Filter bar */}
      <div className="enterprise-card p-6 bg-white">
        <form onSubmit={handleSearch} className="flex gap-4">
          <div className="relative flex-1">
            <Search className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
            <input
              type="text"
              placeholder="Pesquisar por nome, NIF, e-mail..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="enterprise-input pl-9"
            />
          </div>
          <button type="submit" className="enterprise-btn enterprise-btn-secondary px-6">
            Procurar
          </button>
        </form>
      </div>

      {/* Directory Grid View */}
      {loading ? (
        <div className="flex items-center justify-center py-20">
          <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
        </div>
      ) : error ? (
        <div className="p-6 text-center text-red-600 bg-white rounded-xl border border-slate-200">{error}</div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {employees.map((emp: any) => (
            <div key={emp.id} className="enterprise-card p-6 bg-white flex flex-col justify-between">
              <div>
                <div className="flex items-center gap-4 mb-4">
                  <div className="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-lg font-bold text-slate-650 border border-slate-200 shadow-inner">
                    {emp.first_name?.[0] || 'U'}{emp.last_name?.[0] || ''}
                  </div>
                  <div>
                    <h3 className="font-extrabold text-slate-800 text-base">{emp.first_name} {emp.last_name}</h3>
                    <span className="text-xs font-semibold text-slate-400">{emp.position?.name || 'Cargo indefinido'}</span>
                  </div>
                </div>

                <div className="space-y-2.5 pt-4 border-t border-slate-100 text-xs text-slate-600">
                  <div className="flex items-center gap-2">
                    <Mail className="h-3.5 w-3.5 text-slate-400" />
                    <span>{emp.email || 'sem e-mail'}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Phone className="h-3.5 w-3.5 text-slate-400" />
                    <span>{emp.phone || 'sem telefone'}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Building2 className="h-3.5 w-3.5 text-slate-400" />
                    <span>{emp.department?.name || 'sem departamento'}</span>
                  </div>
                </div>
              </div>

              <div className="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                <span className={`enterprise-badge ${emp.is_active ? 'badge-success' : 'badge-danger'}`}>
                  {emp.is_active ? 'Ativo' : 'Inativo'}
                </span>
                <button className="text-xs font-bold text-blue-600 hover:text-blue-500 transition-colors">
                  Ver Ficha Completa →
                </button>
              </div>
            </div>
          ))}
          {employees.length === 0 && (
            <div className="col-span-full enterprise-card p-12 text-center text-slate-400 bg-white">
              Nenhum colaborador encontrado com as condições especificadas.
            </div>
          )}
        </div>
      )}
    </div>
  );
}
