'use client'

import { Bell, Search, User, ChevronDown, Menu } from 'lucide-react'
import { useState } from 'react'

export default function Header() {
  const [active, setActive] = useState(false)

  return (
    <header className="h-24 sticky top-0 z-40 flex items-center justify-between px-10 bg-white border-b border-zinc-100 shadow-sm shadow-zinc-200/20">
      {/* Search Bar - Minimalist (Light) */}
      <div className="relative group max-w-lg w-full">
        <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400 group-focus-within:text-emerald-500 transition-colors" />
        <input 
          type="text" 
          placeholder="Pesquisar nos módulos..." 
          className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl py-3 pl-12 pr-6 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:border-emerald-500/30 focus:bg-white transition-all focus:ring-4 focus:ring-emerald-500/5 shadow-inner"
        />
      </div>

      <div className="flex items-center gap-6">
        {/* Notifications */}
        <button className="relative p-3 bg-white border border-zinc-100 rounded-2xl hover:bg-zinc-50 transition-all group">
          <Bell className="w-5 h-5 text-zinc-400 group-hover:text-emerald-500" />
          <span className="absolute top-3.5 right-3.5 w-2 h-2 bg-emerald-500 border-2 border-white rounded-full shadow-lg" />
        </button>

        {/* User Profile */}
        <div className="flex items-center gap-4 pl-6 border-l border-zinc-100">
          <div className="flex flex-col items-end">
            <span className="text-xs font-black text-zinc-900 uppercase tracking-widest">Admin Manager</span>
            <span className="text-[9px] font-black text-emerald-600 uppercase tracking-tighter">Sua Empresa LTDA</span>
          </div>
          <div className="w-12 h-12 bg-white rounded-2xl border border-zinc-100 flex items-center justify-center group cursor-pointer hover:border-emerald-500/30 transition-all shadow-xl shadow-zinc-200/20 active:scale-95">
             <User className="w-5 h-5 text-zinc-400 group-hover:text-emerald-500" />
          </div>
        </div>
      </div>
    </header>
  )
}
