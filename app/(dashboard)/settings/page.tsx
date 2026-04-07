'use client'

import { useState, useEffect } from 'react'
import { createClient } from '@/lib/supabase/client'
import { 
  Settings, 
  Plus, 
  Trash2, 
  Globe, 
  Key, 
  Loader2, 
  QrCode, 
  RefreshCcw, 
  CheckCircle2, 
  AlertCircle,
  Mail,
  ShieldCheck,
  Server,
  Lock,
  Smartphone
} from 'lucide-react'
import { evolutionApi } from '@/lib/evolution'

export default function SettingsPage() {
  const [instances, setInstances] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [savingSmtp, setSavingSmtp] = useState(false)
  const [activeQr, setActiveQr] = useState<{ id: string, code: string } | null>(null)
  
  const [formData, setFormData] = useState({
    name: '',
    url: 'https://noiton-evolution-api.fc9pdo.easypanel.host',
    apikey: '',
    token: ''
  })

  const [smtpData, setSmtpData] = useState({
    host: 'smtp.gmail.com',
    port: 465,
    secure: true,
    user_name: '',
    password: '',
    from_email: ''
  })

  const supabase = createClient()

  useEffect(() => { 
    fetchInstances()
    fetchSmtp()
  }, [])

  const fetchInstances = async () => {
    setLoading(true)
    const { data } = await supabase.from('instances').select('*')
    if (data) {
      const enriched = await Promise.all(data.map(async (inst) => {
        try {
          const api = evolutionApi(inst.url, inst.apikey)
          const status = await api.getInstanceStatus(inst.name)
          return { ...inst, connectionStatus: status?.instance?.state || 'unknown' }
        } catch (e) {
          return { ...inst, connectionStatus: 'error' }
        }
      }))
      setInstances(enriched)
    }
    setLoading(false)
  }

  const fetchSmtp = async () => {
    const { data } = await supabase.from('smtp_settings').select('*').single()
    if (data) {
      setSmtpData(data)
    }
  }

  const handleSaveSmtp = async (e: React.FormEvent) => {
    e.preventDefault()
    setSavingSmtp(true)
    const { data: { user } } = await supabase.auth.getUser()
    
    // Verificar se já existe
    const { data: existing } = await supabase.from('smtp_settings').select('id').single()
    
    let res
    if (existing) {
      res = await supabase.from('smtp_settings').update(smtpData).eq('id', existing.id)
    } else {
      res = await supabase.from('smtp_settings').insert([{ ...smtpData, user_id: user?.id }])
    }

    if (!res.error) {
      alert('Configurações de e-mail salvas com sucesso!')
    } else {
      alert('Erro ao salvar: ' + res.error.message)
    }
    setSavingSmtp(false)
  }

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault()
    setSaving(true)
    const { data: { user } } = await supabase.auth.getUser()
    const { error } = await supabase.from('instances').insert([{ ...formData, user_id: user?.id }])
    if (!error) {
      setFormData({ name: '', url: 'https://noiton-evolution-api.fc9pdo.easypanel.host', apikey: '', token: '' })
      fetchInstances()
    }
    setSaving(false)
  }

  const handleConnect = async (inst: any) => {
    setActiveQr({ id: inst.id, code: 'loading' })
    try {
      const api = evolutionApi(inst.url, inst.apikey)
      const data = await api.connectInstance(inst.name)
      if (data.code) {
        setActiveQr({ id: inst.id, code: data.code })
      } else {
        alert('Instância já está conectada!')
        setActiveQr(null)
        fetchInstances()
      }
    } catch (e) {
      alert('Erro ao buscar QR Code.')
      setActiveQr(null)
    }
  }

  const handleDelete = async (id: string, name: string, url: string, apikey: string) => {
    if (!confirm('Remover instância permanentemente?')) return
    try {
      const api = evolutionApi(url, apikey)
      await api.deleteInstance(name)
    } catch (e) {}
    await supabase.from('instances').delete().eq('id', id)
    fetchInstances()
  }

  const inputClass = "w-full bg-white border border-zinc-200 rounded-xl p-3.5 text-sm font-bold text-zinc-800 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none transition-all placeholder:text-zinc-300"
  const labelClass = "text-[11px] font-black text-zinc-500 uppercase tracking-widest block mb-1.5 ml-0.5"

  return (
    <div className="space-y-8 animate-in fade-in duration-700 bg-[#f4f6f8] -m-10 p-10 min-h-screen">
      {/* Header */}
      <div className="flex items-center gap-4">
        <div className="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/20">
          <Settings className="text-white w-7 h-7" />
        </div>
        <div>
          <h1 className="text-3xl font-black text-zinc-900 tracking-tight leading-none">Configurações</h1>
          <p className="text-zinc-500 text-base font-bold uppercase tracking-widest mt-2">Gestão de Conexões e APIs</p>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {/* Lado Esquerdo: Formulários */}
        <div className="lg:col-span-4 space-y-8">
          
          {/* WhatsApp Config */}
          <div className="bg-white border border-zinc-200 rounded-[2.5rem] p-10 shadow-sm border-b-4 border-emerald-500/20">
            <h2 className="text-lg font-black text-zinc-900 mb-8 flex items-center gap-3">
              <QrCode className="w-6 h-6 text-emerald-500" /> WhatsApp API
            </h2>
            <form onSubmit={handleCreate} className="space-y-5">
              <div>
                <label className={labelClass}>Nome Identificador</label>
                <input required placeholder="Ex: comercial-01" className={inputClass}
                  value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} />
              </div>
              <div>
                <label className={labelClass}>URL da Evolution API</label>
                <input required placeholder="https://api.seuserver.com" className={inputClass}
                  value={formData.url} onChange={e => setFormData({ ...formData, url: e.target.value })} />
              </div>
              <div>
                <label className={labelClass}>Global API Key</label>
                <input required type="password" placeholder="Chave de autenticação" className={inputClass}
                  value={formData.apikey} onChange={e => setFormData({ ...formData, apikey: e.target.value })} />
              </div>
              <button
                disabled={saving}
                className="w-full bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-black text-xs uppercase tracking-widest py-5 rounded-2xl transition-all shadow-lg active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2 mt-4"
              >
                {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <Plus className="w-4 h-4" />}
                Registrar WhatsApp
              </button>
            </form>
          </div>

          {/* E-mail Config (NOVO) */}
          <div className="bg-white border border-zinc-200 rounded-[2.5rem] p-10 shadow-sm border-b-4 border-blue-500/20">
            <h2 className="text-lg font-black text-zinc-900 mb-8 flex items-center gap-3">
              <Mail className="w-6 h-6 text-blue-500" /> E-mail (SMTP)
            </h2>
            <form onSubmit={handleSaveSmtp} className="space-y-5">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Servidor Host</label>
                  <input required placeholder="smtp.gmail.com" className={inputClass}
                    value={smtpData.host} onChange={e => setSmtpData({ ...smtpData, host: e.target.value })} />
                </div>
                <div>
                  <label className={labelClass}>Porta</label>
                  <input required type="number" placeholder="465" className={inputClass}
                    value={smtpData.port} onChange={e => setSmtpData({ ...smtpData, port: parseInt(e.target.value) })} />
                </div>
              </div>
              <div>
                <label className={labelClass}>E-mail de Usuário</label>
                <input required placeholder="seuemail@gmail.com" className={inputClass}
                  value={smtpData.user_name} onChange={e => setSmtpData({ ...smtpData, user_name: e.target.value })} />
              </div>
              <div>
                <label className={labelClass}>Senha de App (16 digitos)</label>
                <input required type="password" placeholder="•••• •••• •••• ••••" className={inputClass}
                  value={smtpData.password} onChange={e => setSmtpData({ ...smtpData, password: e.target.value })} />
              </div>
              <div>
                <label className={labelClass}>E-mail de Envio (Remetente)</label>
                <input required placeholder="contato@empresa.com" className={inputClass}
                  value={smtpData.from_email} onChange={e => setSmtpData({ ...smtpData, from_email: e.target.value })} />
              </div>
              <button
                disabled={savingSmtp}
                className="w-full bg-blue-500 hover:bg-blue-400 text-white font-black text-xs uppercase tracking-widest py-5 rounded-2xl transition-all shadow-lg active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2 mt-4"
              >
                {savingSmtp ? <Loader2 className="w-4 h-4 animate-spin" /> : <Lock className="w-4 h-4" />}
                Salvar SMTP
              </button>
            </form>
          </div>

        </div>

        {/* Lado Direito: Listagem de Instâncias */}
        <section className="lg:col-span-8 space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-black text-zinc-900 flex items-center gap-3">
              <Globe className="w-6 h-6 text-emerald-500" /> Dispositivos Online
            </h2>
            <button onClick={fetchInstances} className="p-3 bg-white hover:bg-zinc-50 text-zinc-500 rounded-2xl border border-zinc-200 transition-all shadow-sm active:scale-95">
              <RefreshCcw className={`w-5 h-5 ${loading ? 'animate-spin' : ''}`} />
            </button>
          </div>

          {loading ? (
            <div className="py-40 flex flex-col items-center justify-center bg-white border border-zinc-200 rounded-[3rem] shadow-sm">
              <Loader2 className="w-12 h-12 animate-spin text-emerald-500/20" />
              <p className="mt-4 text-xs font-black text-zinc-400 uppercase tracking-widest">Sincronizando dados...</p>
            </div>
          ) : instances.length === 0 ? (
            <div className="py-40 bg-white border border-zinc-200 rounded-[3rem] shadow-xl shadow-zinc-200/50 flex flex-col items-center gap-6 text-zinc-400">
              <div className="w-20 h-20 bg-zinc-50 rounded-full flex items-center justify-center">
                <AlertCircle className="w-10 h-10 text-zinc-200" />
              </div>
              <div className="text-center space-y-1">
                <p className="text-base font-black text-zinc-800 uppercase tracking-widest">Nenhuma instância cadastrada</p>
                <p className="text-sm font-bold text-zinc-400">Comece registrando uma instância ao lado.</p>
              </div>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 pb-20">
              {instances.map(inst => (
                <div key={inst.id} className="bg-white border border-zinc-200 rounded-[2.5rem] p-10 shadow-sm border-b-4 border-zinc-200 hover:border-emerald-500 transition-all group relative overflow-hidden">
                  
                  {/* Status Indicator */}
                  <div className={`absolute top-0 right-0 w-32 h-32 -mr-16 -mt-16 rounded-full opacity-10 ${inst.connectionStatus === 'open' ? 'bg-emerald-500' : 'bg-red-400'}`} />

                  <div className="flex items-start justify-between mb-8 relative z-10">
                    <div className="flex items-center gap-5">
                      <div className={`w-14 h-14 rounded-2xl flex items-center justify-center border-2 ${inst.connectionStatus === 'open' ? 'bg-emerald-50 border-emerald-100' : 'bg-red-50 border-red-100'}`}>
                        <Smartphone className={`${inst.connectionStatus === 'open' ? 'text-emerald-600' : 'text-red-500'} w-7 h-7`} />
                      </div>
                      <div>
                        <h3 className="text-xl font-black text-zinc-900 uppercase tracking-tight">{inst.name}</h3>
                        <div className="flex items-center gap-2 mt-1">
                          <div className={`w-2 h-2 rounded-full ${inst.connectionStatus === 'open' ? 'bg-emerald-500 animate-pulse' : 'bg-red-400'}`} />
                          <span className="text-xs font-black text-zinc-500 uppercase tracking-widest">
                            {inst.connectionStatus === 'open' ? 'WhatsApp Ativo' : 'Desconectado'}
                          </span>
                        </div>
                      </div>
                    </div>
                    <button
                      onClick={() => handleDelete(inst.id, inst.name, inst.url, inst.apikey)}
                      className="p-3 bg-zinc-50 text-zinc-400 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all opacity-0 group-hover:opacity-100 border border-zinc-100"
                    >
                      <Trash2 className="w-5 h-5" />
                    </button>
                  </div>

                  <div className="space-y-4 mb-10 relative z-10">
                    <div className="space-y-1">
                      <label className="text-[10px] font-black text-zinc-300 uppercase tracking-[0.2em]">Servidor de Destino</label>
                      <div className="flex items-center gap-3 text-sm font-bold text-zinc-600 bg-zinc-50 px-5 py-4 rounded-2xl border border-zinc-100">
                        <Globe className="w-4 h-4 text-zinc-400" />
                        <span className="truncate">{inst.url}</span>
                      </div>
                    </div>
                  </div>

                  {activeQr?.id === inst.id && activeQr && (
                    <div className="p-8 bg-zinc-900 border border-zinc-800 rounded-3xl flex flex-col items-center gap-6 mb-8 relative z-10 transition-all">
                      {activeQr.code === 'loading' ? (
                        <div className="w-[180px] h-[180px] flex items-center justify-center">
                          <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
                        </div>
                      ) : (
                        <>
                          <div className="bg-white p-4 rounded-3xl shadow-2xl">
                            <img src={activeQr.code} alt="QR Code" className="w-[180px] h-[180px]" />
                          </div>
                          <div className="text-center">
                            <p className="text-xs font-black text-white uppercase tracking-[0.2em]">Escaneie Agora</p>
                            <p className="text-[10px] text-zinc-500 mt-1 uppercase font-bold">Use o WhatsApp do seu celular</p>
                          </div>
                          <button onClick={() => setActiveQr(null)} className="text-xs font-black text-red-400 hover:text-red-300 uppercase tracking-widest py-2 px-10 border border-red-400/20 rounded-xl">Cancelar</button>
                        </>
                      )}
                    </div>
                  )}

                  <button
                    onClick={() => inst.connectionStatus !== 'open' && handleConnect(inst)}
                    className={`w-full py-5 rounded-2xl flex items-center justify-center gap-3 font-black text-xs uppercase tracking-widest transition-all relative z-10 ${
                      inst.connectionStatus === 'open'
                        ? 'bg-zinc-50 text-zinc-400 border border-zinc-100 cursor-default'
                        : 'bg-emerald-500 text-zinc-900 hover:bg-emerald-400 shadow-xl shadow-emerald-500/20 active:scale-95'
                    }`}
                  >
                    {inst.connectionStatus === 'open'
                      ? <><CheckCircle2 className="w-5 h-5 text-emerald-500" /> Conexão Segura</>
                      : <><QrCode className="w-5 h-5 shadow-sm" /> Iniciar Conexão</>
                    }
                  </button>
                </div>
              ))}
            </div>
          )}
        </section>
      </div>
    </div>
  )
}
