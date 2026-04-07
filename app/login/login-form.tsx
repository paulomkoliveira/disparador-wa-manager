'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import { createClient } from '@/lib/supabase'
import { Mail, Lock, Loader2, MessageSquare, ArrowRight, AlertTriangle } from 'lucide-react'

export default function LoginForm() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const router = useRouter()
  const supabase = createClient()

  const [isSignUp, setIsSignUp] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    setError(null)

    try {
      const { data, error } = isSignUp 
        ? await supabase.auth.signUp({ email, password })
        : await supabase.auth.signInWithPassword({ email, password })

      if (error) {
        setError(error.message)
        setLoading(false)
        return
      }

      if (isSignUp) {
        alert('Conta criada com sucesso! Você já pode fazer o login (certifique-se de que a confirmação de e-mail está desativada no Supabase ou use o login direto se aparecer logado).')
        setIsSignUp(false)
        setLoading(false)
      } else if (data.session) {
        router.push('/')
        router.refresh()
      }
    } catch (err) {
      setError('Ocorreu um erro inesperado. Tente novamente.')
      setLoading(false)
    }
  }

  return (
    <div className="w-full max-w-md animate-in zoom-in-95 duration-700">
      <div className="bg-slate-900/40 backdrop-blur-3xl border border-slate-800 p-12 rounded-[40px] shadow-2xl relative overflow-hidden group">
        {/* Logo Section */}
        <div className="flex flex-col items-center mb-12">
          <div className="w-20 h-20 bg-emerald-500 rounded-3xl flex items-center justify-center shadow-2xl shadow-emerald-500/20 transform hover:rotate-12 transition-all duration-500 mb-6 group-hover:scale-110">
            <MessageSquare className="text-white w-10 h-10" />
          </div>
          <h1 className="text-4xl font-bold text-white mb-2 tracking-tight">
            {isSignUp ? 'Criar Conta' : 'Login Portal'}
          </h1>
          <p className="text-slate-500 text-xs font-bold uppercase tracking-[3px]">WA Manager Premium</p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          {error && (
            <div className="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-2xl text-xs flex items-center gap-3 animate-head-shake">
              <AlertTriangle className="w-4 h-4 flex-shrink-0" />
              <span className="font-medium text-center">{error}</span>
            </div>
          )}

          <div className="space-y-4">
            <div className="relative group/field">
              <div className="absolute left-5 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center text-slate-500 group-focus-within/field:text-emerald-500 transition-colors">
                <Mail className="w-5 h-5" />
              </div>
              <input 
                type="email" 
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                placeholder="E-mail profissional" 
                className="w-full bg-slate-950/40 border border-slate-800 rounded-2xl py-5 pl-14 pr-4 text-sm text-slate-200 placeholder:text-slate-600 focus:outline-none focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-300"
              />
            </div>

            <div className="relative group/field">
              <div className="absolute left-5 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center text-slate-500 group-focus-within/field:text-emerald-500 transition-colors">
                <Lock className="w-5 h-5" />
              </div>
              <input 
                type="password" 
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                placeholder="Sua senha de acesso" 
                className="w-full bg-slate-950/40 border border-slate-800 rounded-2xl py-5 pl-14 pr-4 text-sm text-slate-200 placeholder:text-slate-600 focus:outline-none focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-300"
              />
            </div>
          </div>

          <button 
            type="submit" 
            disabled={loading}
            className="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs uppercase tracking-[2px] py-6 rounded-2xl transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.97] disabled:opacity-50 disabled:cursor-not-allowed group/btn overflow-hidden relative mt-4"
          >
            <div className="relative z-10 flex items-center justify-center gap-3">
              {loading ? (
                <Loader2 className="w-5 h-5 animate-spin" />
              ) : (
                <>
                  <span>{isSignUp ? 'Criar minha conta' : 'Autenticar Sistema'}</span>
                  <ArrowRight className="w-5 h-5 group-hover/btn:translate-x-2 transition-transform" />
                </>
              )}
            </div>
          </button>

          <div className="text-center">
            <button 
              type="button"
              onClick={() => setIsSignUp(!isSignUp)}
              className="text-[10px] font-bold text-slate-500 hover:text-emerald-500 uppercase tracking-widest transition-colors"
            >
              {isSignUp ? 'Já tenho uma conta. Fazer Login' : 'Não tem conta? Criar conta agora'}
            </button>
          </div>
        </form>

        <p className="text-center mt-10 text-[10px] text-slate-600 font-bold uppercase tracking-widest">
          © 2026 WA Manager • Todos os direitos reservados
        </p>
      </div>
    </div>
  )
}
