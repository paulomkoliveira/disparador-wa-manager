export const dynamic = 'force-dynamic'

'use client'

import { useState, useEffect } from 'react'
import { createClient } from '@/lib/supabase/client'
import { LayoutGrid, MoreHorizontal, Plus, Search, User, MessageCircle, Calendar, Loader2, X, Hash, Phone, MapPin, Trash2, Edit2 } from 'lucide-react'

export default function KanbanPage() {
  const [columns, setColumns] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState('')
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingLead, setEditingLead] = useState<any>(null)
  const [formData, setFormData] = useState({ name: '', phone: '', email: '', region: '', column_id: '' })
  const [saving, setSaving] = useState(false)

  const supabase = createClient()

  useEffect(() => {
    fetchData()
    const interval = setInterval(fetchData, 10000) // Sincroniza a cada 10s para ver novos leads do whatsapp
    return () => clearInterval(interval)
  }, [])

  const fetchData = async () => {
    const { data: cols } = await supabase.from('kanban_columns').select('*').order('order')
    const { data: leads } = await supabase.from('leads').select('*').order('created_at', { ascending: false })
    
    if (cols) {
      const enrichedCols = cols.map(col => ({
        ...col,
        items: leads?.filter(lead => lead.column_id === col.id) || []
      }))
      setColumns(enrichedCols)
    }
    setLoading(false)
  }

  const handleOpenModal = (columnId: string, lead: any = null) => {
    if (lead) {
      setEditingLead(lead)
      setFormData({ 
        name: lead.name, 
        phone: lead.phone, 
        email: lead.email || '', 
        region: lead.region || '', 
        column_id: lead.column_id 
      })
    } else {
      setEditingLead(null)
      setFormData({ name: '', phone: '', email: '', region: '', column_id: columnId })
    }
    setIsModalOpen(true)
  }

  const handleSave = async () => {
    if (!formData.name || !formData.phone) return alert('Preencha Nome e WhatsApp')
    setSaving(true)

    try {
      const { data: { user } } = await supabase.auth.getUser()
      const payload = {
        name: formData.name,
        phone: formData.phone.replace(/\D/g, ''),
        email: formData.email,
        region: formData.region,
        column_id: formData.column_id,
        user_id: user?.id,
        status: 'active'
      }

      if (editingLead) {
        const { error } = await supabase.from('leads').update(payload).eq('id', editingLead.id)
        if (error) throw error
      } else {
        const { error } = await supabase.from('leads').insert([payload])
        if (error) throw error
      }

      setIsModalOpen(false)
      fetchData()
    } catch (e: any) {
      alert('Erro ao salvar: ' + e.message)
    } finally {
      setSaving(false)
    }
  }

  const handleAddColumn = async () => {
    const name = prompt('Nome da Nova Etapa do Funil:')
    if (!name) return
    const { data: { user } } = await supabase.auth.getUser()
    await supabase.from('kanban_columns').insert([{ 
      name, 
      order: columns.length,
      user_id: user?.id 
    }])
    fetchData()
  }

  const handleRenameColumn = async (col: any) => {
    const name = prompt('Novo nome para a etapa:', col.name)
    if (!name) return
    await supabase.from('kanban_columns').update({ name }).eq('id', col.id)
    fetchData()
  }

  const handleDeleteColumn = async (col: any) => {
    if (col.items.length > 0) return alert('Não é possível excluir uma etapa que possui leads.')
    if (!confirm(`Excluir a etapa "${col.name}"?`)) return
    await supabase.from('kanban_columns').delete().eq('id', col.id)
    fetchData()
  }

  const handleDeleteLead = async (id: string) => {
    if (!confirm('Deseja excluir este lead permanentemente?')) return
    await supabase.from('leads').delete().eq('id', id)
    fetchData()
  }

  return (
    <div className="space-y-12 bg-[#f8fafc] -m-10 p-10 min-h-screen animate-in fade-in duration-700">
      
      {/* ── HEADER ── */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-600/20">
            <LayoutGrid className="text-white w-7 h-7" />
          </div>
          <div>
            <h1 className="text-3xl font-black text-[#0f172a] tracking-tight">CRM / Leads</h1>
            <p className="text-zinc-400 text-sm font-bold uppercase tracking-widest">Gestão de Funil de Vendas</p>
          </div>
        </div>
        
        <div className="flex items-center gap-6">
           <div className="relative group w-80">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400 focus-within:text-emerald-500 transition-colors" />
              <input 
                type="text" 
                placeholder="Buscar lead real..." 
                className="w-full bg-white border border-zinc-200 rounded-2xl py-3.5 pl-12 pr-6 text-sm text-zinc-900 outline-none focus:border-emerald-500/50 transition-all shadow-sm"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
           </div>
           <button 
             onClick={handleAddColumn}
             className="px-6 py-3.5 bg-zinc-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-zinc-800 transition-all shadow-lg active:scale-95 flex items-center gap-2"
           >
             <Plus className="w-4 h-4" /> Nova Etapa
           </button>
        </div>
      </div>

      {/* ── KANBAN BOARD ── */}
      <div className="flex gap-8 pb-10 overflow-x-auto scrollbar-thin h-[calc(100vh-250px)]">
        {loading ? (
             <div className="flex-1 flex items-center justify-center">
                <Loader2 className="w-12 h-12 animate-spin text-emerald-500" />
             </div>
          ) : (
            <>
              {columns.map((column) => (
                <div key={column.id} className="min-w-[340px] flex flex-col gap-6">
                  <div className="flex items-center justify-between px-4">
                      <div className="flex items-center gap-3 group/title cursor-pointer" onClick={() => handleRenameColumn(column)}>
                        <h3 className="text-xs font-black text-zinc-500 uppercase tracking-[0.2em] group-hover/title:text-emerald-600 transition-colors">{column.name}</h3>
                        <span className="w-6 h-6 bg-emerald-600/10 text-emerald-600 rounded-lg flex items-center justify-center text-[10px] font-black">{column.items.length}</span>
                        <Edit2 className="w-3 h-3 text-zinc-300 opacity-0 group-hover/title:opacity-100 transition-all" />
                      </div>
                      <div className="flex items-center gap-1">
                        <button 
                          onClick={() => handleOpenModal(column.id)}
                          className="p-2 hover:bg-emerald-600/10 text-emerald-600 rounded-xl transition-colors"
                        >
                          <Plus className="w-4 h-4" />
                        </button>
                        <button 
                          onClick={() => handleDeleteColumn(column)}
                          className="p-2 hover:bg-red-50 text-red-300 hover:text-red-500 rounded-xl transition-colors"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                  </div>

                  <div className="flex-1 bg-white border border-zinc-200 rounded-[3rem] p-6 space-y-4 shadow-xl shadow-zinc-200/40 overflow-y-auto scrollbar-hidden">
                      {column.items.length === 0 ? (
                        <div className="h-full flex flex-col items-center justify-center py-20 opacity-30">
                          <p className="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Coluna Vazia</p>
                        </div>
                      ) : (
                        column.items
                          .filter((lead: any) => lead.name.toLowerCase().includes(searchTerm.toLowerCase()))
                          .map((item: any) => (
                          <div 
                            key={item.id} 
                            onClick={() => handleOpenModal(column.id, item)}
                            className="bg-zinc-50 border border-zinc-100 rounded-[2.2rem] p-6 space-y-5 hover:border-emerald-500/30 hover:bg-white transition-all cursor-pointer group/card shadow-sm"
                          >
                            <div className="flex items-center justify-between">
                                <span className="px-3 py-1 bg-white text-emerald-600 text-[9px] font-black uppercase tracking-widest border border-zinc-100 rounded-full shadow-sm">{item.status || 'Ativo'}</span>
                                <span className="text-[10px] font-bold text-zinc-300 uppercase">{new Date(item.created_at).toLocaleDateString()}</span>
                            </div>
                            
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 bg-white rounded-2xl border border-zinc-100 flex items-center justify-center shadow-sm">
                                  <User className="w-5 h-5 text-zinc-300 group-hover/card:text-emerald-500 transition-colors" />
                                </div>
                                <div>
                                  <h4 className="font-bold text-zinc-800 tracking-tight">{item.name}</h4>
                                  <p className="text-[11px] font-bold text-zinc-400">{item.phone}</p>
                                </div>
                            </div>

                            <div className="pt-5 border-t border-zinc-100 flex items-center justify-between">
                                <div className="flex gap-4">
                                  <button className="text-zinc-300 hover:text-emerald-600 transition-colors"><MessageCircle className="w-4 h-4" /></button>
                                  <button className="text-zinc-300 hover:text-emerald-600 transition-colors"><Calendar className="w-4 h-4" /></button>
                                </div>
                                <button 
                                  onClick={(e) => { e.stopPropagation(); handleDeleteLead(item.id) }}
                                  className="text-zinc-200 hover:text-red-400 p-1 opacity-0 group-hover/card:opacity-100 transition-all"
                                >
                                  <Trash2 className="w-4 h-4" />
                                </button>
                            </div>
                          </div>
                        ))
                      )}
                  </div>
                </div>
              ))}
              
              {/* Botão de Nova Etapa no final do board */}
              <button 
                onClick={handleAddColumn}
                className="min-w-[340px] h-[calc(100vh-320px)] border-2 border-dashed border-zinc-200 rounded-[3rem] items-center justify-center flex flex-col gap-4 hover:border-emerald-500 group transition-all"
              >
                 <div className="w-12 h-12 bg-zinc-100 rounded-2xl flex items-center justify-center group-hover:bg-emerald-500 transition-all">
                    <Plus className="w-6 h-6 text-zinc-400 group-hover:text-white" />
                 </div>
                 <span className="text-xs font-black text-zinc-400 uppercase tracking-widest group-hover:text-emerald-600">Adicionar Etapa</span>
              </button>
            </>
          )
        }
      </div>

      {/* ── MODAL DE LEAD ── */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/60 backdrop-blur-sm animate-in fade-in duration-300">
           <div className="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300">
              <div className="p-8 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                 <div className="flex items-center gap-4">
                    <div className="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                       {editingLead ? <Edit2 className="text-white w-5 h-5" /> : <Plus className="text-white w-5 h-5" />}
                    </div>
                    <div>
                       <h3 className="text-xl font-black text-zinc-900 uppercase tracking-tighter">{editingLead ? 'Editar Contato' : 'Novo Contato'}</h3>
                       <p className="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Informações do Funil</p>
                    </div>
                 </div>
                 <button onClick={() => setIsModalOpen(false)} className="p-3 hover:bg-zinc-100 rounded-2xl transition-all"><X className="w-5 h-5 text-zinc-400" /></button>
              </div>

              <div className="p-10 space-y-6">
                 <div>
                    <label className="text-[10px] font-black text-zinc-400 uppercase tracking-widest block mb-2 px-1 text-zinc-500">Etapa do Funil</label>
                    <select 
                      value={formData.column_id}
                      onChange={e => setFormData({...formData, column_id: e.target.value})}
                      className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl py-4 px-6 text-sm font-bold text-zinc-900 outline-none focus:border-emerald-500 transition-all appearance-none cursor-pointer"
                    >
                      {columns.map(col => (
                        <option key={col.id} value={col.id}>{col.name}</option>
                      ))}
                    </select>
                 </div>

                 <div>
                    <label className="text-[10px] font-black text-zinc-400 uppercase tracking-widest block mb-2 px-1">Nome Completo</label>
                    <div className="relative">
                       <User className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-300" />
                       <input 
                         type="text" 
                         value={formData.name}
                         onChange={e => setFormData({...formData, name: e.target.value})}
                         className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl py-4 pl-12 pr-6 text-sm font-bold text-zinc-900 outline-none focus:border-emerald-500 transition-all" 
                         placeholder="Ex: João Silva" 
                       />
                    </div>
                 </div>

                 <div>
                    <label className="text-[10px] font-black text-zinc-400 uppercase tracking-widest block mb-2 px-1">WhatsApp</label>
                    <div className="relative">
                       <Phone className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-300" />
                       <input 
                         type="text" 
                         value={formData.phone}
                         onChange={e => setFormData({...formData, phone: e.target.value})}
                         className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl py-4 pl-12 pr-6 text-sm font-bold text-zinc-900 outline-none focus:border-emerald-500 transition-all" 
                         placeholder="Ex: 5573988937137" 
                       />
                    </div>
                 </div>

                 <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className="text-[10px] font-black text-zinc-400 uppercase tracking-widest block mb-2 px-1">E-mail</label>
                        <input 
                          type="email" 
                          value={formData.email}
                          onChange={e => setFormData({...formData, email: e.target.value})}
                          className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl py-4 px-6 text-sm font-bold text-zinc-900 outline-none focus:border-emerald-500 transition-all" 
                          placeholder="opcional@email.com" 
                        />
                    </div>
                    <div>
                        <label className="text-[10px] font-black text-zinc-400 uppercase tracking-widest block mb-1 px-1">Localização</label>
                        <input 
                          type="text" 
                          value={formData.region}
                          onChange={e => setFormData({...formData, region: e.target.value})}
                          className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl py-4 px-6 text-sm font-bold text-zinc-900 outline-none focus:border-emerald-500 transition-all" 
                          placeholder="São Paulo, SP" 
                        />
                    </div>
                 </div>
              </div>

              <div className="p-8 bg-zinc-50 flex items-center justify-end gap-3 mt-4">
                 <button onClick={() => setIsModalOpen(false)} className="px-8 py-4 text-xs font-black text-zinc-400 uppercase tracking-widest hover:text-zinc-600 transition-all">Cancelar</button>
                 <button 
                   onClick={handleSave}
                   disabled={saving}
                   className="px-10 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-emerald-600/20 flex items-center gap-3 transition-all active:scale-95 disabled:opacity-50"
                 >
                   {saving ? <Loader2 className="w-4 h-4 animate-spin text-white" /> : <Check className="w-4 h-4 text-white" />}
                   {saving ? 'Salvando...' : 'Salvar Contato'}
                 </button>
              </div>
           </div>
        </div>
      )}
    </div>
  )
}

function Check({ className }: { className?: string }) {
  return <Plus className={className} />
}

