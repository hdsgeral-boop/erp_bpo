'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
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
    <main className="min-h-screen flex items-center justify-center bg-slate-900 px-4 py-12 sm:px-6 lg:px-8 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-slate-900">
      <div className="w-full max-w-md space-y-8">
        <div className="flex flex-col items-center">
          <div className="h-12 w-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 border border-blue-400">
            <span className="text-white font-extrabold text-2xl tracking-tighter">C</span>
          </div>
          <h2 className="mt-6 text-center text-3xl font-extrabold text-white tracking-tight">
            ERP Consulvolt
          </h2>
          <p className="mt-2 text-center text-sm text-slate-400">
            Introduza as suas credenciais para aceder ao portal de gestão
          </p>
        </div>

        <div className="enterprise-card bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 p-8 shadow-2xl rounded-2xl">
          <form onSubmit={handleSubmit} className="space-y-6">
            {error && (
              <div className="p-3 bg-red-950/50 border border-red-500/50 text-red-200 text-sm rounded-lg flex items-center gap-2">
                <svg className="h-5 w-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{error}</span>
              </div>
            )}

            <div>
              <label htmlFor="email" className="block text-sm font-semibold text-slate-200 mb-2">
                Endereço de E-mail
              </label>
              <input
                id="email"
                type="email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-inner"
                placeholder="exemplo@consulvolt.ao"
              />
            </div>

            <div>
              <div className="flex justify-between items-center mb-2">
                <label htmlFor="password" className="block text-sm font-semibold text-slate-200">
                  Palavra-passe
                </label>
                <a href="#" className="text-sm font-medium text-blue-400 hover:text-blue-300 transition-colors">
                  Esqueceu a palavra-passe?
                </a>
              </div>
              <input
                id="password"
                type="password"
                required
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-inner"
                placeholder="••••••••"
              />
            </div>

            <div className="flex items-center">
              <input
                id="remember-me"
                name="remember-me"
                type="checkbox"
                className="h-4 w-4 rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500/50"
              />
              <label htmlFor="remember-me" className="ml-2 block text-sm text-slate-300 select-none">
                Lembrar sessão neste dispositivo
              </label>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 border border-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
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
                'Iniciar Sessão'
              )}
            </button>
          </form>
        </div>
      </div>
    </main>
  );
}
