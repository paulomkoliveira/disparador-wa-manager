import Link from 'next/link'
import { FileSearch, Home, ChevronRight } from 'lucide-react'

export default function NotFound() {
  return (
    <div className="min-h-[80vh] flex flex-col items-center justify-center p-12 bg-slate-900/10 backdrop-blur-3xl border border-slate-900 rounded-[60px] animate-in zoom-in-95 duration-1000 space-y-12">
      <div className="relative group">
        <div className="absolute inset-0 bg-emerald-500/20 rounded-full blur-[80px] animate-pulse"></div>
        <div className="relative w-44 h-44 bg-slate-900 border border-slate-800 rounded-[50px] shadow-2xl flex items-center justify-center group-hover:scale-105 group-hover:rotate-6 transition-all duration-700">
           <FileSearch className="w-20 h-20 text-emerald-500 shadow-2xl" />
        </div>
      </div>

      <div className="text-center space-y-5">
        <h1 className="text-7xl font-black text-white tracking-tighter tabular-nums drop-shadow-2xl">404</h1>
        <div className="space-y-4">
           <h2 className="text-2xl font-bold text-white uppercase tracking-widest leading-none">Módulo Não Encontrado</h2>
           <p className="text-slate-600 max-w-sm mx-auto text-sm font-medium leading-relaxed italic pr-2">
             "Otimismo é a chave, mas esta página não está no nosso roadmap atual."
           </p>
        </div>
      </div>

      <Link 
        href="/"
        className="flex items-center gap-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-10 py-6 rounded-[30px] font-black text-xs uppercase tracking-[5px] transition-all shadow-2xl shadow-emerald-500/20 active:scale-95 group"
      >
        <Home className="w-5 h-5 group-hover:-translate-y-1 transition-transform" />
        Voltar à Base
        <ChevronRight className="w-4 h-4 group-hover:translate-x-2 transition-transform" />
      </Link>
    </div>
  )
}
