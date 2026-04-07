'use client'
export const dynamic = 'force-dynamic'

import { useState, useEffect, useRef } from 'react'
import { createClient } from '@/lib/supabase/client'
import { 
  MessageSquare, 
  Send, 
  Upload, 
  ShieldCheck, 
  CheckCircle2, 
  AlertCircle, 
  Loader2,
  Trash2,
  FileText,
  Mail,
  Smartphone,
  Check,
  History,
  Info,
  ExternalLink,
  Download,
  Clock,
  Ban,
  Filter,
  Shield
} from 'lucide-react'
import { evolutionApi } from '@/lib/evolution'

export default function DisparadorPage() {
  const [activeTab, setActiveTab] = useState<'whatsapp' | 'email'>('whatsapp')
  const [instances, setInstances] = useState<any[]>([])
  const [selectedInstance, setSelectedInstance] = useState<any>(null)
  const [campaignName, setCampaignName] = useState('')
  const [numbers, setNumbers] = useState('')
  const [emails, setEmails] = useState('')
  const [emailSubject, setEmailSubject] = useState('')
  const [message, setMessage] = useState('')
  const [loading, setLoading] = useState(false)
  const [progress, setProgress] = useState(0)
  const [stats, setStats] = useState({ sent: 0, dups: 0, errors: 0 })
  const [campaignLog, setCampaignLog] = useState<string[]>([])
  const [pastCampaigns, setPastCampaigns] = useState<any[]>([])
  const [totalSummary, setTotalSummary] = useState({ attempted: 0, sent: 0, pending: 0, skipped: 0, unavailable: 0 })
  
  const fileInputRef = useRef<HTMLInputElement>(null)
  const supabase = createClient()

  useEffect(() => {
    fetchInstances()
    fetchCampaigns()
  }, [])

  const fetchInstances = async () => {
    const { data } = await supabase.from('instances').select('*')
    if (data) {
      setInstances(data)
      const openOne = data.find(i => i.connectionStatus === 'open') || data[0]
      setSelectedInstance(openOne)
    }
  }

  const fetchCampaigns = async () => {
    const { data } = await supabase.from('campaigns').select('*').order('created_at', { ascending: false })
    if (data) {
      setPastCampaigns(data)
      // Calcular resumo total
      const summary = data.reduce((acc, curr) => ({
        attempted: acc.attempted + curr.total,
        sent: acc.sent + curr.sent,
        pending: acc.pending + (curr.total - curr.sent - curr.failed),
        skipped: acc.skipped + 0, // Placeholder
        unavailable: acc.unavailable + curr.failed
      }), { attempted: 0, sent: 0, pending: 0, skipped: 0, unavailable: 0 })
      setTotalSummary(summary)
    }
  }

  const handleFileUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (!file) return

    const reader = new FileReader()
    reader.onload = (event) => {
      const text = event.target?.result as string
      const lines = text.split('\n')
      const phoneList: string[] = []
      const emailList: string[] = []
      
      lines.forEach(line => {
        const parts = line.split(',')
        const cleanNum = parts.find(p => p.replace(/\D/g, '').length >= 8)
        const cleanEmail = parts.find(p => p.includes('@'))
        if (cleanNum) phoneList.push(cleanNum.trim())
        if (cleanEmail) emailList.push(cleanEmail.trim())
      })

      if (activeTab === 'whatsapp' && phoneList.length > 0) 
        setNumbers(prev => prev ? `${prev}, ${phoneList.join(', ')}` : phoneList.join(', '))
      if (activeTab === 'email' && emailList.length > 0) 
        setEmails(prev => prev ? `${prev}, ${emailList.join(', ')}` : emailList.join(', '))
      
      setCampaignLog(prev => [`Arquivo CSV processado: ${phoneList.length} números e ${emailList.length} e-mails.`, ...prev])
    }
    reader.readAsText(file)
  }

  const handleStartCampaign = async () => {
    if (!campaignName) {
      alert('Por favor, dê um nome para a campanha.')
      return
    }
    if (activeTab === 'whatsapp' && (!selectedInstance || !numbers)) return
    if (activeTab === 'email' && !emails) return
    if (!message) return

    setLoading(true)
    setProgress(0)
    setStats({ sent: 0, dups: 0, errors: 0 })
    setCampaignLog([`Iniciando campanha: ${campaignName}...`])

    const list = activeTab === 'whatsapp' 
      ? numbers.split(',').map(n => n.trim()).filter(n => n !== '')
      : emails.split(',').map(e => e.trim()).filter(e => e !== '')
    
    const total = list.length
    let sentCount = 0
    let errorCount = 0

    // Criar registro da campanha
    const { data: { user } } = await supabase.auth.getUser()
    const { data: campaign } = await supabase.from('campaigns').insert([{
      user_id: user?.id,
      name: campaignName,
      type: activeTab,
      total: total,
      status: 'running'
    }]).select().single()

    for (let i = 0; i < total; i++) {
        try {
            if (activeTab === 'whatsapp') {
                const api = evolutionApi(selectedInstance.url, selectedInstance.apikey)
                let phone = list[i].replace(/\D/g, '')
                if (phone.length >= 10 && !phone.startsWith('55')) phone = '55' + phone
                const personalizedMessage = message.replace(/\[nome\]/g, 'Cliente')
                await api.sendMessage(selectedInstance.name, phone, personalizedMessage)
                
                await supabase.from('sent_messages').insert([{
                   user_id: user?.id,
                   instance_id: selectedInstance.id,
                   campaign_id: campaign.id,
                   phone: phone,
                   content: personalizedMessage,
                   status: 'sent'
                }])
            } else {
                // Envio Real de E-mail via API
                const res = await fetch('/api/disparo/email', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({
                    to: list[i],
                    subject: emailSubject,
                    message: message.replace(/\[nome\]/g, 'Cliente')
                  })
                })

                if (!res.ok) throw new Error('Falha no envio')
                
                await supabase.from('sent_messages').insert([{
                   user_id: user?.id,
                   campaign_id: campaign.id,
                   phone: list[i], // Guardamos o email no campo phone por simplicidade ou poderiamos ter um campo email
                   content: message,
                   status: 'sent'
                }])
            }

            sentCount++
            setStats(prev => ({ ...prev, sent: sentCount }))
            setCampaignLog(prev => [`[${activeTab.toUpperCase()}] Enviado para ${list[i]}`, ...prev])
            setProgress(Math.round(((i + 1) / total) * 100))
            
            if (activeTab === 'whatsapp' && i < total - 1) {
              const delay = 5000 + Math.random() * 10000 
              await new Promise(resolve => setTimeout(resolve, delay))
            }
        } catch (e) {
            errorCount++
            setStats(prev => ({ ...prev, errors: errorCount }))
            setCampaignLog(prev => [`Erro em ${list[i]}`, ...prev])
        }
    }

    // Atualizar campanha finalizada
    await supabase.from('campaigns').update({
      sent: sentCount,
      failed: errorCount,
      status: 'completed'
    }).eq('id', campaign.id)

    setLoading(false)
    setCampaignLog(prev => ['Campanha concluída!', ...prev])
    fetchCampaigns()
  }

  const handleDeleteCampaign = async (id: string) => {
    if (!confirm('Deseja excluir este registro de campanha permanentemente?')) return
    await supabase.from('campaigns').delete().eq('id', id)
    fetchCampaigns()
  }

  const labelClass = "text-[11px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-2 flex items-center justify-between"
  const counterClass = "text-[10px] font-black px-2 py-0.5 rounded-lg bg-zinc-100 text-zinc-500"

  return (
    <div className="bg-[#f4f6f8] -m-10 p-10 min-h-screen animate-in fade-in duration-700 space-y-10">
      
      {/* ─── HEADER ─── */}
      <div className="flex items-center justify-between">
         <div>
          <h1 className="text-4xl font-black text-zinc-900 tracking-tight">Centro de Disparos</h1>
          <p className="text-zinc-500 font-medium text-lg mt-1">Gestão inteligente de campanhas multi-canal</p>
         </div>
         <div className="flex bg-white p-1 rounded-2xl border border-zinc-200 shadow-sm">
            <button 
              onClick={() => setActiveTab('whatsapp')}
              className={`px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all ${activeTab === 'whatsapp' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-zinc-400 hover:text-zinc-600'}`}
            >
              WhatsApp
            </button>
            <button 
              onClick={() => setActiveTab('email')}
              className={`px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all ${activeTab === 'email' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20' : 'text-zinc-400 hover:text-zinc-600'}`}
            >
              E-mail
            </button>
         </div>
      </div>

      {/* ─── SUMMARY CARDS (CONFORME PRINT) ─── */}
      <section className="grid grid-cols-1 md:grid-cols-5 gap-4">
         {[
           { label: 'Tentativas', value: totalSummary.attempted, icon: History, color: 'text-zinc-600', bg: 'bg-white' },
           { label: 'Enviados', value: totalSummary.sent, icon: CheckCircle2, color: 'text-emerald-500', bg: 'bg-white' },
           { label: 'Pendentes', value: totalSummary.pending, icon: Clock, color: 'text-zinc-400', bg: 'bg-white' },
           { label: 'Pulados (Opt-out)', value: totalSummary.skipped, icon: Ban, color: 'text-red-500', bg: 'bg-white' },
           { label: 'Indisponíveis', value: totalSummary.unavailable, icon: AlertCircle, color: 'text-amber-500', bg: 'bg-white' },
         ].map((card, i) => (
           <div key={i} className={`${card.bg} border border-zinc-200 rounded-2xl p-5 shadow-sm`}>
              <div className="flex items-center gap-2 mb-2">
                 <card.icon className={`w-4 h-4 ${card.color}`} />
                 <span className="text-[10px] font-black text-zinc-400 uppercase tracking-widest">{card.label}</span>
              </div>
              <p className={`text-2xl font-black ${card.color}`}>{card.value}</p>
           </div>
         ))}
      </section>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {/* ─── LADO ESQUERDO: CONFIGURAÇÃO ─── */}
        <section className="lg:col-span-8 space-y-6">
           
           {/* Nome da Campanha */}
           <div className="bg-white border border-zinc-100 rounded-[2rem] p-8 shadow-sm">
              <label className={labelClass}>Nome da Campanha</label>
              <input 
                type="text"
                placeholder="Ex: Campanha de Vendas de Abril"
                value={campaignName}
                onChange={e => setCampaignName(e.target.value)}
                className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl p-5 text-sm font-bold text-zinc-900 outline-none focus:border-emerald-500 transition-all placeholder:text-zinc-300"
              />
           </div>

           {activeTab === 'whatsapp' ? (
             <>
               {/* WhatsApp Config */}
               <div className="bg-white border border-zinc-100 rounded-[2rem] p-8 shadow-sm">
                  <label className={labelClass}>Instância de Envio</label>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {instances.map(inst => (
                      <button 
                        key={inst.id}
                        onClick={() => setSelectedInstance(inst)}
                        className={`p-4 rounded-2xl border-2 transition-all text-left flex items-center gap-3 ${
                          selectedInstance?.id === inst.id ? 'border-emerald-500 bg-emerald-50/50' : 'border-zinc-50 bg-zinc-50 hover:border-zinc-200'
                        }`}
                      >
                        <div className={`w-3 h-3 rounded-full ${inst.connectionStatus === 'open' ? 'bg-emerald-500' : 'bg-red-400'}`} />
                        <span className="text-sm font-bold text-zinc-700">{inst.name}</span>
                        {selectedInstance?.id === inst.id && <Check className="w-4 h-4 text-emerald-600 ml-auto" />}
                      </button>
                    ))}
                  </div>
               </div>

               <div className="bg-white border border-zinc-100 rounded-[2rem] p-8 shadow-sm space-y-4">
                  <div className={labelClass}>
                     <span className="flex items-center gap-2 font-black"><Smartphone className="w-4 h-4" /> Números WhatsApp</span>
                     <button onClick={() => fileInputRef.current?.click()} className="text-[10px] text-emerald-600 hover:underline flex items-center gap-1">
                        <Upload className="w-3 h-3" /> Importar CSV
                     </button>
                  </div>
                  <textarea 
                    rows={4}
                    value={numbers}
                    onChange={e => setNumbers(e.target.value)}
                    className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl p-5 text-sm font-bold text-zinc-800 outline-none focus:border-emerald-500 transition-all placeholder:text-zinc-300"
                    placeholder="5573988937137, 5573..."
                  />
               </div>
             </>
           ) : (
             <>
               {/* Email Config & Tutorial */}
               <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div className="bg-white border border-zinc-100 rounded-[2rem] p-8 shadow-sm space-y-4">
                    <label className={labelClass}>Assunto do E-mail</label>
                    <input 
                      type="text"
                      placeholder="Qual o assunto da mensagem?"
                      value={emailSubject}
                      onChange={e => setEmailSubject(e.target.value)}
                      className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl p-5 text-sm font-bold text-zinc-900 outline-none focus:border-blue-500 transition-all placeholder:text-zinc-300"
                    />
                  </div>

                  <div className="bg-white border border-zinc-100 rounded-[2rem] p-8 shadow-sm space-y-4">
                    <div className={labelClass}>
                       <span className="flex items-center gap-2 font-black"><Mail className="w-4 h-4" /> Destinatários (E-mail)</span>
                       <button onClick={() => fileInputRef.current?.click()} className="text-[10px] text-blue-600 hover:underline flex items-center gap-1">
                          <Upload className="w-3 h-3" /> Importar CSV
                       </button>
                    </div>
                    <textarea 
                      rows={1}
                      value={emails}
                      onChange={e => setEmails(e.target.value)}
                      className="w-full bg-zinc-50 border border-zinc-100 rounded-2xl p-5 text-sm font-bold text-zinc-800 outline-none focus:border-blue-500 transition-all placeholder:text-zinc-300"
                      placeholder="email1@teste.com, email2@teste.com..."
                    />
                  </div>
               </div>

               <div className="bg-white border border-zinc-100 rounded-[2rem] p-8 shadow-sm">
                  <div className="flex items-center justify-between mb-4">
                     <h4 className="text-xs font-black text-zinc-800 uppercase tracking-widest flex items-center gap-2">
                        <Shield className="w-4 h-4 text-blue-500" /> Remetente SMTP
                     </h4>
                     <a href="/settings" className="text-[10px] font-black text-blue-600 hover:underline uppercase tracking-widest">Configurar Conta de Envio</a>
                  </div>
                  <div className="p-5 bg-blue-50 border border-blue-100 rounded-2xl">
                     <p className="text-[10px] text-blue-800 font-bold leading-relaxed uppercase tracking-tight">
                        Este módulo envia e-mails através da conta configurada em <b>Ajustes</b>. 
                        Preencha o <b>Assunto</b> e os <b>Destinatários</b> abaixo para iniciar.
                     </p>
                  </div>
               </div>
             </>
           )}

           <div className="bg-white border border-zinc-100 rounded-[2rem] p-10 shadow-sm space-y-6">
              <div className={labelClass}>
                 <span className="flex items-center gap-2 font-black"><FileText className="w-4 h-4" /> Conteúdo</span>
                 <span className="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">Macro [nome] ativa</span>
              </div>
              <textarea 
                rows={6}
                value={message}
                onChange={e => setMessage(e.target.value)}
                className="w-full bg-zinc-50 border border-zinc-200 rounded-[2.5rem] p-8 text-lg font-medium text-zinc-900 outline-none focus:border-emerald-500 transition-all placeholder:text-zinc-300"
                placeholder="Olá [nome], como podemos ajudar hoje?"
              />
           </div>
        </section>

        {/* ─── LADO DIREITO: CONTROLE & HISTÓRICO ─── */}
        <section className="lg:col-span-4 space-y-6">
           <div className="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-10 shadow-2xl space-y-10">
              <h2 className="text-xl font-black text-white uppercase tracking-wider text-center">Controle</h2>
              
              <div className="flex flex-col items-center">
                 <div className="w-44 h-44 rounded-full border-[10px] border-zinc-800 relative flex items-center justify-center">
                    <div className="text-center z-10">
                       <span className="text-5xl font-black text-white leading-none">{progress}%</span>
                       <p className="text-[10px] font-black text-zinc-500 uppercase tracking-widest mt-2">{loading ? 'Enviando...' : 'Aguardando'}</p>
                    </div>
                    <svg className="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 176 176">
                      <circle cx="88" cy="88" r="78" fill="none" stroke={activeTab === 'whatsapp' ? '#10b981' : '#3b82f6'} strokeWidth="12" strokeDasharray={490} strokeDashoffset={490 - (490 * progress / 100)} className="transition-all duration-700 ease-in-out" strokeLinecap="round" />
                    </svg>
                 </div>
              </div>

              <button 
                onClick={handleStartCampaign}
                disabled={loading || !campaignName}
                className={`w-full py-6 rounded-2xl text-zinc-900 font-black text-xs uppercase tracking-widest shadow-xl transition-all flex items-center justify-center gap-3 active:scale-95 disabled:opacity-30 ${activeTab === 'whatsapp' ? 'bg-emerald-500 hover:bg-emerald-400 shadow-emerald-500/10' : 'bg-blue-500 hover:bg-blue-400 shadow-blue-500/10'}`}
              >
                {loading ? <Loader2 className="w-5 h-5 animate-spin text-zinc-900" /> : <Send className="w-5 h-5 text-zinc-900" />}
                {loading ? 'Disparando...' : 'Iniciar Campanha'}
              </button>

              <div className="space-y-4">
                 <label className="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Log de Atividade</label>
                 <div className="h-48 overflow-y-auto space-y-2 pr-2 scrollbar-thin">
                    {campaignLog.length === 0 && <p className="text-[10px] text-zinc-600 italic">Pronto para disparar...</p>}
                    {campaignLog.map((log, i) => (
                      <div key={i} className="text-[10px] font-bold text-zinc-400 bg-zinc-800/30 p-2.5 rounded-xl border border-zinc-800/50 flex items-center gap-2">
                         <div className={`w-1 h-1 rounded-full ${log.includes('Erro') ? 'bg-red-500' : 'bg-emerald-500'}`} />
                         {log}
                      </div>
                    ))}
                 </div>
              </div>
           </div>

           {/* Campanhas Passadas (Conforme Print) */}
           <div className="bg-white border border-zinc-200 rounded-[2.5rem] p-8 shadow-sm">
             <div className="flex items-center gap-2 mb-6">
                <History className="w-5 h-5 text-zinc-400" />
                <h3 className="text-lg font-black text-zinc-800 uppercase tracking-widest">Campanhas Passadas</h3>
             </div>

             <div className="space-y-4">
                {pastCampaigns.length === 0 ? (
                  <p className="text-xs text-zinc-400 text-center py-4 uppercase font-black tracking-widest opacity-50">Nenhum registro</p>
                ) : pastCampaigns.slice(0, 10).map((cp) => (
                  <div key={cp.id} className="p-4 bg-zinc-50 rounded-2xl border border-zinc-100 flex items-center justify-between group animate-in slide-in-from-right duration-300">
                    <div className="flex items-start gap-3">
                       <div className={`w-8 h-8 rounded-lg flex items-center justify-center ${cp.type === 'whatsapp' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600'}`}>
                          {cp.type === 'whatsapp' ? <MessageSquare className="w-4 h-4" /> : <Mail className="w-4 h-4" />}
                       </div>
                       <div>
                          <p className="text-xs font-black text-zinc-800 uppercase tracking-tight truncate max-w-[120px]">{cp.name}</p>
                          <p className="text-[9px] font-bold text-zinc-400 uppercase">{cp.total} destinatários</p>
                       </div>
                    </div>
                    <div className="flex items-center gap-2">
                       <div className="text-right mr-2">
                          <p className="text-xs font-black text-emerald-600">{cp.sent}</p>
                          <p className="text-[8px] font-bold text-zinc-400 uppercase">Enviados</p>
                       </div>
                       <div className="flex gap-1 opacity-0 group-hover:opacity-100 transition-all">
                          <button 
                            onClick={() => handleDeleteCampaign(cp.id)}
                            className="p-1.5 hover:bg-red-50 text-zinc-300 hover:text-red-500 rounded-lg transition-all"
                          >
                             <Trash2 className="w-3.5 h-3.5" />
                          </button>
                       </div>
                    </div>
                  </div>
                ))}
             </div>
           </div>
        </section>
      </div>

      <input type="file" accept=".csv" ref={fileInputRef} onChange={handleFileUpload} className="hidden" />
    </div>
  )
}
