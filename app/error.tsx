'use client'

import { useEffect } from 'react'
import { AlertCircle, RotateCcw } from 'lucide-react'

export default function Error({
  error,
  reset,
}: {
  error: Error & { digest?: string }
  reset: () => void
}) {
  useEffect(() => {
    console.error(error)
  }, [error])

  return (
    <div className="min-h-[70vh] flex flex-col items-center justify-center p-10 bg-slate-950/20 backdrop-blur-xl border border-slate-800 rounded-[50px] space-y-10 animate-in zoom-in-95">
      <div className="w-32 h-32 bg-red-500/10 rounded-full flex items-center justify-center shadow-2xl shadow-red-500/10 animate-pulse">
        <AlertCircle className="w-16 h-16 text-red-500" />
      </div>
      
      <div className="text-center space-y-4">
        <h2 className="text-4xl font-bold text-white tracking-tight">Ops! Algo deu errado.</h2>
        <p className="text-slate-500 max-w-md mx-auto text-sm font-bold uppercase tracking-[2px] leading-relaxed">
          Ocorreu uma falha no processamento deste módulo. Por favor, tente recarregar a interface.
        </p>
      </div>

      <button 
        onClick={() => reset()}
        className="flex items-center gap-3 bg-slate-900 border border-slate-800 text-white px-10 py-5 rounded-[25px] font-bold text-xs uppercase tracking-[3px] hover:bg-slate-800 hover:border-emerald-500/50 transition-all active:scale-95 group"
      >
        <RotateCcw className="w-5 h-5 group-hover:rotate-180 transition-all duration-700" />
        Tentar Novamente
      </button>
    </div>
  )
}
