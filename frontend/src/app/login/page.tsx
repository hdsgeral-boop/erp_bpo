'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { Mail, Lock, Shield, ArrowRight, Building, CheckCircle2 } from 'lucide-react';
import api from '@/lib/api';

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const response = await api.post('/login', { email, password });
      if (response.data.success) {
        localStorage.setItem('erp_token', response.data.access_token);
        localStorage.setItem('erp_user', JSON.stringify(response.data.user));
        router.push('/dashboard');
      } else {
        setError(response.data.message || 'Falha no login');
      }
    } catch (err: any) {
      setError(
        err.response?.data?.message || 'As credenciais fornecidas estão incorretas.'
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <main className="min-h-screen flex bg-slate-950 font-sans selection:bg-blue-600 selection:text-white overflow-hidden relative">
      {/* Decorative blurred backgrounds */}
      <div className="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-indigo-600/10 rounded-full blur-[120px] pointer-events-none" />

      {/* Split Layout */}
      <div className="flex-1 flex max-w-7xl mx-auto w-full z-10">
        
        {/* Left Panel: Enterprise Branding (Hidden on Mobile) */}
        <div className="hidden lg:flex flex-col justify-between w-[45%] p-12 relative overflow-hidden border-r border-slate-900 bg-slate-950/40 backdrop-blur-md">
          <div className="flex items-center gap-3">
            <div className="h-10 w-10 rounded-lg bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 border border-blue-400">
              <span className="text-white font-black text-xl tracking-tighter">C</span>
            </div>
            <span className="text-white font-bold text-lg tracking-tight">Consulvolt ERP</span>
          </div>

          <div className="space-y-8 my-auto pr-8">
            <div className="space-y-4">
              <span className="text-xs font-semibold tracking-wider text-blue-400 uppercase">SaaS Moderno</span>
              <h1 className="text-4xl font-extrabold text-white leading-tight tracking-tight">
                A próxima geração da gestão empresarial portuguesa.
              </h1>
              <p className="text-slate-400 leading-relaxed text-sm">
                Uma solução multi-tenant robusta, segura e moderna para gerir compras, ativos, tesouraria, contabilidade e recursos humanos num único local.
              </p>
            </div>

            <div className="space-y-4 pt-4 border-t border-slate-900">
              <div className="flex items-start gap-3">
                <CheckCircle2 className="h-5 w-5 text-blue-500 shrink-0 mt-0.5" />
                <div>
                  <h4 className="text-sm font-semibold text-slate-200">Arquitetura de Alta Performance</h4>
                  <p className="text-xs text-slate-400 mt-0.5">Baseada em Laravel 12 API & Next.js 15 para velocidade incrível.</p>
                </div>
              </div>
              
              <div className="flex items-start gap-3">
                <CheckCircle2 className="h-5 w-5 text-blue-500 shrink-0 mt-0.5" />
                <div>
                  <h4 className="text-sm font-semibold text-slate-200">Totalmente Multi-tenant</h4>
                  <p className="text-xs text-slate-400 mt-0.5">Privacidade e integridade de dados garantidos por isolamento de empresa.</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <CheckCircle2 className="h-5 w-5 text-blue-500 shrink-0 mt-0.5" />
                <div>
                  <h4 className="text-sm font-semibold text-slate-200">Design System Premium</h4>
                  <p className="text-xs text-slate-400 mt-0.5">Interface enterprise otimizada para produtividade diária.</p>
                </div>
              </div>
            </div>
          </div>

          <div className="text-xs text-slate-500 flex justify-between items-center">
            <span>© {new Date().getFullYear()} Consulvolt Lda.</span>
            <div className="flex gap-4">
              <a href="#" className="hover:text-slate-400 transition-colors">Termos</a>
              <a href="#" className="hover:text-slate-400 transition-colors">Privacidade</a>
            </div>
          </div>
        </div>

        {/* Right Panel: Login Form */}
        <div className="flex-1 flex flex-col justify-center items-center px-6 py-12 lg:px-8 bg-slate-950/20">
          <div className="w-full max-w-md space-y-8">
            
            {/* Mobile Header Branding */}
            <div className="flex flex-col items-center lg:hidden mb-8">
              <div className="h-12 w-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 border border-blue-400">
                <span className="text-white font-extrabold text-2xl tracking-tighter">C</span>
              </div>
              <h2 className="mt-4 text-3xl font-extrabold text-white tracking-tight">
                Consulvolt ERP
              </h2>
              <p className="mt-1 text-sm text-slate-400">
                Introduza as suas credenciais para aceder ao portal
              </p>
            </div>

            <div className="lg:mb-6">
              <h3 className="hidden lg:block text-2xl font-bold text-white tracking-tight">
                Iniciar Sessão
              </h3>
              <p className="hidden lg:block text-slate-400 text-xs mt-1.5">
                Bem-vindo de volta! Insira os seus dados de conta para aceder ao ERP.
              </p>
            </div>

            {/* Glassmorphic Login Card */}
            <div className="enterprise-card bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 p-8 shadow-2xl rounded-2xl relative overflow-hidden">
              <div className="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-blue-500/80 to-transparent" />
              
              <form onSubmit={handleSubmit} className="space-y-6">
                
                {/* Alert message */}
                {error && (
                  <div className="p-3 bg-red-950/50 border border-red-500/50 text-red-200 text-xs rounded-xl flex items-start gap-2.5 transition-all">
                    <Shield className="h-4.5 w-4.5 text-red-400 shrink-0 mt-0.5" />
                    <span>{error}</span>
                  </div>
                )}

                {/* Email input field */}
                <div className="space-y-2">
                  <label htmlFor="email" className="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Endereço de E-mail
                  </label>
                  <div className="relative group">
                    <span className="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 group-focus-within:text-blue-500 transition-colors">
                      <Mail className="h-4.5 w-4.5" />
                    </span>
                    <input
                      id="email"
                      type="email"
                      required
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      className="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-600/50 focus:border-blue-500 transition-all shadow-inner"
                      placeholder="exemplo@consulvolt.ao"
                    />
                  </div>
                </div>

                {/* Password input field */}
                <div className="space-y-2">
                  <div className="flex justify-between items-center">
                    <label htmlFor="password" className="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                      Palavra-passe
                    </label>
                    <a href="#" className="text-xs font-medium text-blue-500 hover:text-blue-400 transition-colors">
                      Esqueceu-se?
                    </a>
                  </div>
                  <div className="relative group">
                    <span className="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 group-focus-within:text-blue-500 transition-colors">
                      <Lock className="h-4.5 w-4.5" />
                    </span>
                    <input
                      id="password"
                      type="password"
                      required
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      className="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-600/50 focus:border-blue-500 transition-all shadow-inner"
                      placeholder="••••••••"
                    />
                  </div>
                </div>

                {/* Remember me checkbox */}
                <div className="flex items-center justify-between">
                  <label className="flex items-center cursor-pointer select-none">
                    <input
                      id="remember-me"
                      name="remember-me"
                      type="checkbox"
                      className="h-4 w-4 rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-blue-500/50 accent-blue-600 mr-2.5 cursor-pointer"
                    />
                    <span className="text-xs text-slate-400 hover:text-slate-300 transition-colors">
                      Lembrar sessão neste dispositivo
                    </span>
                  </label>
                </div>

                {/* Submit button */}
                <button
                  type="submit"
                  disabled={loading}
                  className="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-950 focus:ring-blue-500 transition-all shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2 border border-blue-500 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                >
                  {loading ? (
                    <>
                      <svg className="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                      </svg>
                      <span>A entrar...</span>
                    </>
                  ) : (
                    <>
                      <span>Iniciar Sessão</span>
                      <ArrowRight className="h-4 w-4" />
                    </>
                  )}
                </button>
              </form>
            </div>
            
            {/* Footer help note */}
            <p className="text-center text-xs text-slate-500">
              Precisa de ajuda ou acesso? Contate o{' '}
              <a href="mailto:suporte@consulvolt.ao" className="text-blue-500 hover:text-blue-400 font-medium transition-colors">
                Administrador de TI
              </a>
            </p>
          </div>
        </div>
        
      </div>
    </main>
  );
}
