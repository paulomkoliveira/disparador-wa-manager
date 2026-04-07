'use client'

import { Search, Filter, Globe } from 'lucide-react'

interface ChatSidebarProps {
  leads: any[]
  activeLeadId: string | null
  onSelectLead: (id: string) => void
  instances: any[]
  filterInstance: string
  setFilterInstance: (id: string) => void
}

export default function ChatSidebar({ 
  leads, 
  activeLeadId, 
  onSelectLead, 
  instances,
  filterInstance,
  setFilterInstance
}: ChatSidebarProps) {
  return (
    <div className="w-80 h-full bg-slate-900/50 border-r border-slate-800 flex flex-col">
      <div className="p-4 space-y-4">
        <h2 className="text-xl font-bold text-white px-2">Conversas</h2>
        
        <div className="relative group">
          <Search className="absolute left-3 top-3 w-4 h-4 text-slate-500 group-focus-within:text-emerald-500 transition-colors" />
          <input 
            type="text" 
            placeholder="Buscar contato..."
            className="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 pl-10 text-sm text-white focus:ring-1 focus:ring-emerald-500/50 outline-none"
          />
        </div>

        <div className="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
          <button 
            onClick={() => setFilterInstance('all')}
            className={`px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-widest uppercase transition-all whitespace-nowrap border ${
              filterInstance === 'all' 
                ? 'bg-emerald-500 text-slate-950 border-emerald-500' 
                : 'text-slate-500 hover:text-white border-slate-800 hover:bg-slate-800'
            }`}
          >
            Todas
          </button>
          {instances.map(inst => (
            <button 
              key={inst.id}
              onClick={() => setFilterInstance(inst.id)}
              className={`px-3 py-1.5 rounded-lg text-[10px] font-bold tracking-widest uppercase transition-all whitespace-nowrap border flex items-center gap-1.5 ${
                filterInstance === inst.id 
                  ? 'bg-emerald-500 text-slate-950 border-emerald-500' 
                  : 'text-slate-500 hover:text-white border-slate-800 hover:bg-slate-800'
              }`}
            >
              <Globe className="w-3 h-3" />
              {inst.name}
            </button>
          ))}
        </div>
      </div>

      <div className="flex-1 overflow-y-auto">
        {leads.length === 0 ? (
          <div className="p-8 text-center text-slate-600">
            <p className="text-sm">Nenhum contato encontrado.</p>
          </div>
        ) : (
          leads.map(lead => (
            <button
              key={lead.id}
              onClick={() => onSelectLead(lead.id)}
              className={`w-full p-4 flex items-center gap-3 transition-all hover:bg-slate-800/50 border-b border-slate-800/50 ${
                activeLeadId === lead.id ? 'bg-emerald-500/10 border-l-4 border-l-emerald-500' : ''
              }`}
            >
              <div className="relative">
                {lead.photo_url ? (
                  <img 
                    src={lead.photo_url} 
                    alt="" 
                    className="w-12 h-12 rounded-xl object-cover border border-slate-700" 
                  />
                ) : (
                  <div className="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-slate-500 font-bold">
                    {lead.name.substring(0, 2).toUpperCase()}
                  </div>
                )}
                <div className="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-slate-950 rounded-full"></div>
              </div>
              
              <div className="flex-1 text-left">
                <div className="flex items-center justify-between">
                  <h4 className="text-sm font-semibold text-white truncate w-40">{lead.name}</h4>
                  <span className="text-[10px] text-slate-500">12:45</span>
                </div>
                <p className="text-xs text-slate-500 truncate w-48">
                  {lead.last_message || 'Nenhuma mensagem recente.'}
                </p>
              </div>
            </button>
          ))
        )}
      </div>
    </div>
  )
}
