'use client'

import { useState } from 'react'
import { Mail, Send, TrendingUp, AlertCircle, CheckCircle2, List, ShieldCheck, MailPlus, Loader2, Users } from 'lucide-react'

export default function EmailPage() {
  const [emails, setEmails] = useState('')
  const [subject, setSubject] = useState('')
  const [content, setContent] = useState('')
  const [loading, setLoading] = useState(false)
  const [status, setStatus] = useState<any>(null)

  const stats = [
    { label: 'Emails Enviados', value: '45.210', icon: Mail, color: 'text-indigo-500' },
    { label: 'Taxa de Abertura', value: '28.4%', icon: TrendingUp, color: 'text-emerald-500' },
    { label: 'Falhas de Envio', value: '1.2%', icon: AlertCircle, color: 'text-red-500' },
  ]

  const getEmailCount = () => {
    if (!emails.trim()) return 0
    return emails.split(',').map(e => e.trim()).filter(e => e.includes('@') && e.includes('.')).length
  }

  const handleSend = async () => {
     if(!emails || !subject || !content) return
     setLoading(true)
     setStatus({ type: 'loading', msg: 'Preparando envio em massa...' })
     
     // Simulação de envio para demonstração visual rápida
     setTimeout(() => {
        setStatus({ type: 'success', msg: `Sucesso! Campanha agendada para ${getEmailCount()} destinatários.` })
        setLoading(false)
        setEmails('')
        setSubject('')
        setContent('')
     }, 2000)
  }

  return (
    <div className="space-y-12 pb-20 animate-in fade-in duration-1000">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="w-14 h-14 bg-indigo-500 rounded-3xl flex items-center justify-center shadow-xl shadow-indigo-500/20">
            <Mail className="text-white w-7 h-7" />
          </div>
          <div>
            <h1 className="text-3xl font-black text-white tracking-tight">E-mail Marketing</h1>
            <p className="text-zinc-500 text-sm font-bold uppercase tracking-widest">Campanhas via SMTP</p>
          </div>
        </div>
        
        <button className="flex items-center gap-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 px-8 py-3.5 rounded-full border border-indigo-500/30 transition-all font-black text-[10px] uppercase tracking-widest group shadow-2xl">
           <MailPlus className="w-4 h-4 group-hover:scale-110 transition-transform" />
           Configurar Servidor
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {stats.map((stat, i) => (
          <div key={i} className="bg-zinc-900/20 border border-zinc-800/40 p-8 rounded-[3rem] shadow-xl group hover:border-indigo-500/20 transition-all duration-700">
             <div className="flex items-center justify-between mb-4">
                <div className={`p-4 rounded-2xl bg-zinc-950 border border-zinc-900 ${stat.color} shadow-inner`}>
                   <stat.icon className="w-6 h-6" />
                </div>
                <TrendingUp className="text-zinc-800 w-6 h-6" />
             </div>
             <h3 className="text-4xl font-black text-white tracking-tighter mb-2">{stat.value}</h3>
             <p className="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{stat.label}</p>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <section className="lg:col-span-8 bg-zinc-900/20 border border-zinc-800/40 p-10 rounded-[4rem] shadow-2xl backdrop-blur-3xl space-y-8">
           <div className="flex items-center justify-between border-b border-zinc-800/50 pb-6 mb-6">
              <h2 className="text-xl font-bold text-white">Nova Campanha</h2>
              <div className="flex items-center gap-2 text-[10px] font-black text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-full border border-indigo-500/20 animate-pulse">
                 <Users className="w-3 h-3" /> {getEmailCount()} Destinatários
              </div>
           </div>

           <div className="space-y-6">
              <div className="space-y-4 px-4">
                 <label className="text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">E-mails (Separados por vírgula)</label>
                 <textarea 
                    rows={2} 
                    className="w-full bg-transparent border-b-2 border-zinc-800 py-4 font-bold text-zinc-100 placeholder:text-zinc-800 focus:border-indigo-500 outline-none transition-all scrollbar-thin overflow-hidden" 
                    placeholder="email1@teste.com, email2@teste.com..."
                    value={emails}
                    onChange={(e) => setEmails(e.target.value)}
                 />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div className="space-y-4 px-4">
                    <label className="text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Assunto do E-mail</label>
                    <input 
                      type="text" 
                      placeholder="Assunto impactante..." 
                      className="w-full bg-transparent border-b-2 border-zinc-800 py-4 font-bold text-zinc-100 placeholder:text-zinc-800 focus:border-indigo-500 outline-none transition-all" 
                      value={subject}
                      onChange={(e) => setSubject(e.target.value)}
                    />
                 </div>
                 <div className="space-y-4 px-4">
                    <label className="text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Servidor de Envio</label>
                    <select className="w-full bg-transparent border-b-2 border-zinc-800 py-4 font-bold text-zinc-100 focus:border-indigo-500 outline-none transition-all cursor-pointer appearance-none">
                       <option value="">Selecione SMTP...</option>
                       <option value="smtp1">Empresa Principal</option>
                    </select>
                 </div>
              </div>
              
              <div className="space-y-4">
                <div className="flex items-center justify-between px-4">
                   <label className="text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Corpo do E-mail (HTML)</label>
                   <div className="flex gap-4">
                      <button className="text-[9px] font-black text-zinc-500 uppercase flex items-center gap-1 hover:text-white transition-colors">Visualizar</button>
                      <button className="text-[9px] font-black text-zinc-500 uppercase flex items-center gap-1 hover:text-white transition-colors">Limpar</button>
                   </div>
                </div>
                <textarea 
                  rows={10} 
                  className="w-full bg-zinc-950/50 border border-zinc-900 rounded-[2.5rem] p-10 text-sm font-mono text-zinc-300 focus:border-indigo-500 outline-none transition-all shadow-inner scrollbar-thin placeholder:text-zinc-800" 
                  placeholder="Escreva seu código HTML aqui..." 
                  value={content}
                  onChange={(e) => setContent(e.target.value)}
                />
              </div>

              {status && (
                 <div className={`p-6 rounded-3xl border flex items-center gap-4 animate-in slide-in-from-top-4 duration-500 ${
                    status.type === 'success' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' :
                    status.type === 'error' ? 'bg-red-500/10 border-red-500/20 text-red-400' :
                    'bg-indigo-500/10 border-indigo-500/20 text-indigo-400'
                 }`}>
                    {status.type === 'success' ? <CheckCircle2 className="w-6 h-6 flex-shrink-0" /> : <AlertCircle className="w-6 h-6 flex-shrink-0" />}
                    <p className="text-xs font-bold leading-relaxed">{status.msg}</p>
                 </div>
              )}

              <div className="pt-6">
                 <button 
                  onClick={handleSend}
                  disabled={loading}
                  className="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-black text-[12px] uppercase tracking-[0.3em] py-6 rounded-[2rem] shadow-2xl shadow-indigo-500/20 active:scale-95 transition-all flex items-center justify-center gap-3 disabled:opacity-50"
                 >
                    {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : <Send className="w-5 h-5 fill-current" />}
                    {loading ? 'Agendando...' : 'Agendar Campanha'}
                 </button>
              </div>
           </div>
        </section>

        <section className="lg:col-span-4 flex flex-col gap-6 font-bold">
           <div className="p-8 bg-zinc-900/20 border border-zinc-800/40 rounded-[3rem] shadow-xl space-y-6">
              <h2 className="text-lg font-bold text-white flex items-center gap-3">
                 <List className="text-indigo-500" /> Listas Ativas
              </h2>
              <div className="space-y-4">
                 {[
                   { name: 'Leads Outbound', count: '1.200', date: 'Há 2d' },
                   { name: 'Newsletter Semanal', count: '45.000', date: 'Há 1h' },
                   { name: 'Recuperação Carrinho', count: '850', date: 'Hoje' }
                 ].map((list, i) => (
                   <div key={i} className="p-5 bg-zinc-950/30 border border-zinc-900 rounded-3xl hover:border-indigo-500/20 transition-all group flex items-center justify-between cursor-pointer">
                      <div>
                         <h4 className="font-bold text-sm text-zinc-100">{list.name}</h4>
                         <p className="text-[9px] font-black text-zinc-700 uppercase tracking-widest mt-1">{list.count} Inscritos</p>
                      </div>
                      <ShieldCheck className="w-4 h-4 text-zinc-800 group-hover:text-emerald-500 transition-colors" />
                   </div>
                 ))}
                 <button className="w-full py-4 border border-zinc-800 text-zinc-600 hover:text-white hover:border-indigo-500/30 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                    Importar Nova Lista
                 </button>
              </div>
           </div>
        </section>
      </div>
    </div>
  )
}
