'use client'

import { useState } from 'react'
import { 
  Mail, 
  Send, 
  CheckCircle2, 
  Loader2,
} from 'lucide-react'

export default function EmailDisparador() {
  const [loading, setLoading] = useState(false)
  const [status, setStatus] = useState<null | 'success' | 'error'>(null)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    setStatus(null)

    setTimeout(() => {
      setLoading(false)
      setStatus('success')
    }, 2000)
  }

  return (
    <div className="max-w-6xl mx-auto space-y-8 animate-in slide-in-from-bottom-4 duration-700">
      <div className="flex flex-col gap-2">
        <h1 className="text-3xl font-bold text-white tracking-tight">Disparador de E-mail (SMTP)</h1>
        <p className="text-slate-500 text-sm font-medium">Envie campanhas por e-mail com facilidade.</p>
      </div>

      <form onSubmit={handleSubmit} className="bg-slate-900/50 backdrop-blur-sm border border-slate-800 p-8 rounded-3xl space-y-6 shadow-xl">
        <input 
          type="text" 
          placeholder="Assunto do E-mail"
          className="w-full bg-slate-950/50 border border-slate-800 rounded-2xl p-4 text-sm text-slate-200"
          required
        />
        <textarea 
          placeholder="exemplo@gmail.com"
          className="w-full h-32 bg-slate-950/50 border border-slate-800 rounded-2xl p-4 text-sm text-slate-200"
          required
        />
        <textarea 
          placeholder="Sua mensagem HTML ou Texto..."
          className="w-full h-56 bg-slate-950/50 border border-slate-800 rounded-2xl p-4 text-sm text-slate-200"
          required
        />
        <button 
          type="submit" 
          disabled={loading}
          className="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-5 rounded-2xl transition-all"
        >
          {loading ? <Loader2 className="w-6 h-6 animate-spin mx-auto" /> : 'INICIAR ENVIO'}
        </button>
      </form>
    </div>
  )
}
