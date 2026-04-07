'use client'

import { Droppable } from '@hello-pangea/dnd'
import LeadCard from './LeadCard'
import { Plus, MoreHorizontal } from 'lucide-react'

interface ColumnProps {
  column: any
  leads: any[]
}

export default function Column({ column, leads }: ColumnProps) {
  return (
    <div className="flex-shrink-0 w-80 h-[100%] flex flex-col p-4 bg-slate-900/10 border border-slate-900 rounded-3xl backdrop-blur-xl">
      <div className="flex items-center justify-between mb-4 px-2">
        <div className="flex items-center gap-3">
          <div className="flex items-center justify-center p-2 rounded-xl bg-slate-950 border border-slate-800 shadow-xl shadow-black/30">
            <h3 className="text-sm font-bold text-white tracking-widest uppercase">{column.name}</h3>
          </div>
          <span className="text-xs font-bold text-slate-500 bg-slate-900/50 px-2 py-0.5 rounded-full border border-slate-800">
            {leads.length}
          </span>
        </div>
        <div className="flex items-center gap-1">
          <button className="p-2 text-slate-600 hover:text-white hover:bg-slate-900 rounded-xl transition-all">
            <Plus className="w-4 h-4" />
          </button>
          <button className="p-2 text-slate-600 hover:text-white hover:bg-slate-900 rounded-xl transition-all">
            <MoreHorizontal className="w-4 h-4" />
          </button>
        </div>
      </div>

      <Droppable droppableId={column.id}>
        {(provided, snapshot) => (
          <div
            ref={provided.innerRef}
            {...provided.droppableProps}
            className={`flex-1 overflow-y-auto px-2 min-h-[50px] transition-colors rounded-2xl ${
              snapshot.isDraggingOver ? 'bg-emerald-500/5 ring-1 ring-emerald-500/20' : ''
            }`}
          >
            {leads.map((lead, index) => (
              <LeadCard key={lead.id} lead={lead} index={index} />
            ))}
            {provided.placeholder}
          </div>
        )}
      </Droppable>
    </div>
  )
}
