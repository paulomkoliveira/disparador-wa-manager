'use client'

import { Bell, Search, User } from 'lucide-react'

export default function Header({ title }: { title: string }) {
  return (
    <header className="h-16 bg-slate-900 border-b border-slate-800 px-8 flex items-center justify-between sticky top-0 z-40">
      <div className="flex items-center gap-4">
        <h2 className="text-white font-semibold text-lg">{title}</h2>
        <div className="h-4 w-px bg-slate-800 hidden sm:block"></div>
        <div className="relative hidden md:block">
          <Search className="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" />
          <input 
            type="text" 
            placeholder="Buscar..." 
            className="bg-slate-900 border border-slate-800 rounded-lg py-1.5 pl-10 pr-4 text-sm text-slate-300 focus:outline-none focus:border-emerald-500/50 transition-colors"
          />
        </div>
      </div>

      <div className="flex items-center gap-4">
        <button className="p-2 text-slate-400 hover:text-emerald-500 transition-colors relative">
          <Bell className="w-5 h-5" />
          <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-emerald-500 rounded-full border-2 border-slate-900"></span>
        </button>
        <div className="h-4 w-px bg-slate-800"></div>
        <button className="flex items-center gap-2 p-1.5 rounded-lg bg-slate-800/50 border border-slate-800 hover:bg-slate-800 transition-colors group">
          <div className="w-7 h-7 rounded-md bg-emerald-500/20 flex items-center justify-center">
            <User className="w-4 h-4 text-emerald-500" />
          </div>
          <span className="text-xs font-semibold text-slate-300 group-hover:text-white px-1">Minha Conta</span>
        </button>
      </div>
    </header>
  )
}
