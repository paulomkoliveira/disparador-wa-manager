'use client'

import { Draggable } from '@hello-pangea/dnd'
import { MessageSquare, Phone, MoreVertical, Globe } from 'lucide-react'

interface LeadCardProps {
  lead: any
  index: number
}

export default function LeadCard({ lead, index }: LeadCardProps) {
  return (
    <Draggable draggableId={lead.id} index={index}>
      {(provided, snapshot) => (
        <div
          ref={provided.innerRef}
          {...provided.draggableProps}
          {...provided.dragHandleProps}
          className={`p-4 mb-3 bg-slate-900 border border-slate-800 rounded-2xl shadow-lg transition-all hover:border-emerald-500/30 group ${
            snapshot.isDragging ? 'rotate-2 scale-105 border-emerald-500 shadow-emerald-500/20' : ''
          }`}
        >
          <div className="flex items-start justify-between mb-3">
            <div className="flex items-center gap-3">
              {lead.photo_url ? (
                <img 
                  src={lead.photo_url} 
                  alt={lead.name} 
                  className="w-10 h-10 rounded-xl object-cover border border-slate-700" 
                />
              ) : (
                <div className="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-500">
                  <span className="text-sm font-bold">{lead.name.substring(0, 2).toUpperCase()}</span>
                </div>
              )}
              <div>
                <h4 className="text-white font-semibold text-sm line-clamp-1">{lead.name}</h4>
                <div className="flex items-center gap-1 text-[10px] text-slate-500">
                  <Phone className="w-3 h-3" />
                  {lead.phone}
                </div>
              </div>
            </div>
            <button className="p-1 text-slate-600 hover:text-white transition-colors">
              <MoreVertical className="w-4 h-4" />
            </button>
          </div>

          {lead.last_message && (
            <p className="text-xs text-slate-400 line-clamp-2 mb-3 bg-slate-950/50 p-2 rounded-lg italic">
              "{lead.last_message}"
            </p>
          )}

          <div className="flex items-center justify-between mt-auto">
            <div className="flex items-center gap-1.5 px-2 py-1 bg-emerald-500/10 rounded-full border border-emerald-500/20">
              <Globe className="w-3 h-3 text-emerald-500" />
              <span className="text-[10px] font-medium text-emerald-500 uppercase tracking-widest">
                {lead.instances?.name || 'Sem Instância'}
              </span>
            </div>
            <div className="p-1.5 bg-slate-800 rounded-lg text-slate-500 group-hover:text-emerald-500 group-hover:bg-emerald-500/10 transition-all">
              <MessageSquare className="w-4 h-4" />
            </div>
          </div>
        </div>
      )}
    </Draggable>
  )
}
