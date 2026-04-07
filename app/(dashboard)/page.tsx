'use client'
export const dynamic = 'force-dynamic'

import { useState, useEffect } from 'react'
import { createClient } from '@/lib/supabase/client'
import { 
  Users, 
  MessageSquare, 
  Mail, 
  Zap, 
  TrendingUp, 
  CheckCircle2, 
  Clock, 
  ArrowUpRight,
  ShieldCheck,
  Activity,
  Smartphone
} from 'lucide-react'

export default function Dashboard() {
  const [stats, setStats] = useState({
    leads: 0,
    messages: 0,
    emails: 0,
    apiStatus: 'Online'
  })
  const [recentActivity, setRecentActivity] = useState<any[]>([])

  const supabase = createClient()

  useEffect(() => {
    fetchStats()
  }, [])

  const fetchStats = async () => {
    const { count: leadsCount } = await supabase.from('leads').select('*', { count: 'exact', head: true })
    const { count: waCount } = await supabase.from('sent_messages').select('*', { count: 'exact', head: true }).eq('type', 'whatsapp')
    const { count: emailCount } = await supabase.from('sent_messages').select('*', { count: 'exact', head: true }).eq('type', 'email')
    
    setStats({
      leads: leadsCount || 0,
      messages: waCount || 0,
      emails: emailCount || 0,
      apiStatus: 'Online'
    })

    const { data: recent } = await supabase
      .from('sent_messages')
      .select('*')
      .order('created_at', { ascending: false })
      .limit(5)
    
    setRecentActivity(recent || [])
  }

  return (
    <div className="space-y-10 animate-in fade-in duration-1000">
      {/* Welcome Header */}
      <div className="flex flex-col gap-1">
        <div className="flex items-center gap-2 text-emerald-600">
           <Activity className="w-4 h-4" />
           <span className="text-[10px] font-black uppercase tracking-[0.3em]">Monitoramento em Tempo Real</span>
        </div>
        <h1 className="text-5xl font-black text-zinc-900 tracking-tight">Centro de Comando</h1>
        <p className="text-zinc-500 font-medium text-lg">Sua operação de marketing automatizada e sob controle.</p>
      </div>

      {/* Stats Cards - HIGH CONTRAST */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        {[
          { label: 'Total de Leads', value: stats.leads.toLocaleString(), change: '+12%', icon: Users, color: 'bg-emerald-500', text: 'text-emerald-500' },
          { label: 'Mensagens WA', value: stats.messages.toLocaleString(), change: '+8%', icon: MessageSquare, color: 'bg-blue-500', text: 'text-blue-500' },
          { label: 'E-mails Enviados', value: stats.emails.toLocaleString(), change: '+15%', icon: Mail, color: 'bg-indigo-500', text: 'text-indigo-500' },
          { label: 'Status da API', value: stats.apiStatus, change: '100%', icon: Zap, color: 'bg-amber-500', text: 'text-amber-500' }
        ].map((item, i) => (
          <div key={i} className="bg-white border border-zinc-200 rounded-[2.5rem] p-8 shadow-xl shadow-zinc-200/50 hover:scale-[1.02] transition-all cursor-pointer group">
            <div className={`w-14 h-14 ${item.color} rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-${item.color.split('-')[1]}-500/20 group-hover:rotate-12 transition-transform`}>
              <item.icon className="text-white w-7 h-7" />
            </div>
            <p className="text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-1">{item.label}</p>
            <div className="flex items-end justify-between">
              <h3 className="text-4xl font-black text-zinc-900 tracking-tighter">{item.value}</h3>
              <span className={`text-[12px] font-black px-2 py-1 rounded-lg bg-emerald-50 text-emerald-600 flex items-center gap-1`}>
                 <ArrowUpRight className="w-3 h-3" />
                 {item.change}
              </span>
            </div>
          </div>
        ))}
      </div>

      {/* Main Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {/* Performance Graph Placeholder */}
        <section className="lg:col-span-8 bg-white border border-zinc-200 rounded-[3rem] p-10 shadow-xl shadow-zinc-200/30">
          <div className="flex items-center justify-between mb-10">
            <div className="flex items-center gap-3">
               <div className="w-2 h-2 rounded-full bg-emerald-500" />
               <h3 className="text-lg font-black text-zinc-900 uppercase tracking-widest">Performance Semanal</h3>
            </div>
            <div className="flex bg-zinc-100 p-1.5 rounded-2xl gap-2">
               <button className="px-6 py-2 bg-emerald-600 text-white text-xs font-black rounded-xl shadow-lg shadow-emerald-600/20 uppercase tracking-widest transition-all">Semana</button>
               <button className="px-6 py-2 text-zinc-400 text-xs font-black rounded-xl uppercase tracking-widest hover:text-zinc-600">Mês</button>
            </div>
          </div>
          
          <div className="h-80 w-full bg-zinc-50 rounded-[2rem] border border-zinc-100 flex items-center justify-center relative overflow-hidden group">
             <TrendingUp className="w-20 h-20 text-emerald-500 opacity-10 group-hover:scale-125 transition-transform duration-1000" />
             <div className="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent opacity-60" />
             <p className="absolute bottom-10 text-[10px] font-black text-zinc-300 uppercase tracking-[0.5em]">Dados em Sincronização...</p>
          </div>
        </section>

        {/* Recent Activity */}
        <section className="lg:col-span-4 bg-white border border-zinc-200 rounded-[3rem] p-10 shadow-xl shadow-zinc-200/30">
           <div className="flex items-center gap-3 mb-8">
              <Activity className="w-5 h-5 text-emerald-600" />
              <h3 className="text-lg font-black text-zinc-900 uppercase tracking-widest">Atividade Recente</h3>
           </div>

           <div className="space-y-6">
              {recentActivity.length === 0 ? (
                <div className="py-10 text-center space-y-4">
                   <Clock className="w-10 h-10 text-zinc-200 mx-auto" />
                   <p className="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Nenhuma atividade registrada</p>
                </div>
              ) : recentActivity.map((act, i) => (
                <div key={i} className="flex gap-5 group cursor-pointer">
                   <div className={`w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-colors shrink-0 ${act.type === 'email' ? 'bg-indigo-600 group-hover:bg-indigo-500' : 'bg-zinc-900 group-hover:bg-emerald-600'}`}>
                      {act.type === 'email' ? <Mail className="w-5 h-5 text-white" /> : <Smartphone className="w-5 h-5 text-white" />}
                   </div>
                   <div className="border-b border-zinc-100 pb-5 w-full">
                      <p className="text-sm font-bold text-zinc-800 tracking-tight transition-colors">
                        Disparo via {act.type === 'email' ? 'E-mail' : 'WhatsApp'} para {act.phone}
                      </p>
                      <p className="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1">
                        {new Date(act.created_at).toLocaleTimeString()}
                      </p>
                   </div>
                </div>
              ))}
           </div>

           <button className="w-full mt-8 py-4 border border-zinc-100 rounded-2xl text-[10px] font-black text-zinc-400 uppercase tracking-widest hover:bg-zinc-50 transition-all active:scale-95">
              Ver todos os logs
           </button>
        </section>
      </div>
    </div>
  )
}
