'use client'

import { useState } from 'react'
import { 
  Send, 
  Users, 
  MessageSquare, 
  CheckCircle2, 
  Loader2,
  Phone,
  FileText
} from 'lucide-react'

export default function WhatsAppDisparador() {
  const [loading, setLoading] = useState(false)
  const [numbers, setNumbers] = useState('')
  const [message, setMessage] = useState('')
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
    <div className="max-w-5xl mx-auto space-y-8 animate-in slide-in-from-bottom-4 duration-700">
      <div className="flex items-center justify-between">
        <div className="flex flex-col gap-2">
          <h1 className="text-3xl font-bold text-white tracking-tight">Disparador de WhatsApp</h1>
          <p className="text-slate-500 text-sm font-medium">Envie campanhas em massa usando a Evolution API.</p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="bg-slate-900/50 backdrop-blur-sm border border-slate-800 p-8 rounded-3xl space-y-6 shadow-xl">
        <textarea 
          value={numbers}
          onChange={(e) => setNumbers(e.target.value)}
          placeholder="5511999999999" 
          className="w-full h-32 bg-slate-950/50 border border-slate-800 rounded-2xl p-4 text-sm text-slate-200"
          required
        />
        <textarea 
          value={message}
          onChange={(e) => setMessage(e.target.value)}
          placeholder="Sua mensagem..." 
          className="w-full h-48 bg-slate-950/50 border border-slate-800 rounded-2xl p-4 text-sm text-slate-200"
          required
        />
        <button 
          type="submit" 
          disabled={loading}
          className="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-5 rounded-2xl transition-all"
        >
          {loading ? <Loader2 className="w-6 h-6 animate-spin mx-auto" /> : 'INICIAR DISPARO'}
        </button>
      </form>
    </div>
  )
}
