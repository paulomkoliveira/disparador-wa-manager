'use client'
export const dynamic = 'force-dynamic'

import { useState, useEffect, useRef } from 'react'
import { createClient } from '@/lib/supabase/client'
import {
  MessageSquare,
  Send,
  User,
  MoreVertical,
  Loader2,
  Wifi,
  RefreshCw,
  Clock,
  CheckCheck,
  Search,
  AlertCircle
} from 'lucide-react'
import { evolutionApi } from '@/lib/evolution'

export default function ChatPage() {
  const [instances, setInstances] = useState<any[]>([])
  const [selectedInstance, setSelectedInstance] = useState<any>(null)
  const [chats, setChats] = useState<any[]>([])
  const [filteredChats, setFilteredChats] = useState<any[]>([])
  const [searchTerm, setSearchTerm] = useState('')
  const [activeChat, setActiveChat] = useState<any>(null)
  const [messages, setMessages] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  const [loadingMessages, setLoadingMessages] = useState(false)
  const [newMessage, setNewMessage] = useState('')
  const [error, setError] = useState<string | null>(null)
  const messagesEndRef = useRef<HTMLDivElement>(null)
  const supabase = createClient()

  useEffect(() => {
    fetchInstances()
  }, [])

  useEffect(() => {
    // Scroll para última mensagem
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' })
  }, [messages])

  useEffect(() => {
    if (!searchTerm) {
      setFilteredChats(chats)
    } else {
      setFilteredChats(
        chats.filter(c =>
          (c.pushName || c.remoteJid || '').toLowerCase().includes(searchTerm.toLowerCase())
        )
      )
    }
  }, [searchTerm, chats])

  const fetchInstances = async () => {
    const { data } = await supabase.from('instances').select('*')
    if (data && data.length > 0) {
      setInstances(data)
      const active = data.find(i => i.connectionStatus === 'open') || data[0]
      setSelectedInstance(active)
      await loadChats(active)
    } else {
      setLoading(false)
    }
  }

  const loadChats = async (inst: any) => {
    if (!inst) return
    setLoading(true)
    setError(null)
    setChats([])
    setFilteredChats([])

    try {
      const api = evolutionApi(inst.url, inst.apikey)
      // ✅ Usar findChats (POST) - endpoint correto descoberto via teste direto
      const data = await api.findChats(inst.name)
      setChats(data)
      setFilteredChats(data)
    } catch (e: any) {
      console.error('Erro ao buscar chats:', e)
      setError(`Erro ao conectar na API: ${e?.message || 'Verifique a URL e a API Key da instância'}`)
    } finally {
      setLoading(false)
    }
  }

  const handleSelectChat = async (chat: any) => {
    setActiveChat(chat)
    setMessages([])
    setLoadingMessages(true)
    try {
      const api = evolutionApi(selectedInstance.url, selectedInstance.apikey)
      // ✅ Usar findMessages (POST) com remoteJid - endpoint correto
      const msgs = await api.findMessages(selectedInstance.name, chat.remoteJid, 100)
      // ✅ Inverter para que a mais nova fique embaixo (estilo WhatsApp)
      setMessages(msgs.reverse())
    } catch (e) {
      console.error('Erro ao buscar mensagens:', e)
      setMessages([])
    } finally {
      setLoadingMessages(false)
    }
  }

  const handleSend = async () => {
    if (!newMessage.trim() || !activeChat || !selectedInstance) return
    const text = newMessage
    setNewMessage('')
    try {
      const api = evolutionApi(selectedInstance.url, selectedInstance.apikey)
      await api.sendMessage(selectedInstance.name, activeChat.remoteJid, text)
      setMessages(prev => [...prev, {
        key: { fromMe: true, remoteJid: activeChat.remoteJid },
        message: { conversation: text },
        messageTimestamp: Math.floor(Date.now() / 1000),
        pushName: 'Você'
      }])
    } catch (e) {
      console.error('Erro ao enviar:', e)
    }
  }

  const formatTime = (ts: number) => {
    if (!ts) return ''
    return new Date(ts * 1000).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
  }

  const getDisplayName = (chat: any) =>
    chat.pushName || chat.remoteJid?.split('@')[0] || 'Desconhecido'

  const getInitial = (chat: any) =>
    (getDisplayName(chat)).charAt(0).toUpperCase()

  return (
    <div className="h-[calc(100vh-160px)] flex gap-6 animate-in fade-in duration-500 -m-10 p-10 bg-[#f4f6f8]">

      {/* ── Sidebar de Conversas ── */}
      <aside className="w-[380px] flex flex-col gap-4">
        {/* Seletor de Instância */}
        <div className="bg-white rounded-2xl border border-zinc-200 p-4 shadow-sm">
          <label className="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block mb-2">
            Número Conectado
          </label>
          <div className="relative">
            <Wifi className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-emerald-500" />
            <select
              className="w-full bg-zinc-50 border border-zinc-200 rounded-xl py-3 pl-10 pr-4 text-sm font-semibold text-zinc-800 outline-none focus:border-emerald-400 transition-all cursor-pointer appearance-none"
              value={selectedInstance?.id || ''}
              onChange={(e) => {
                const inst = instances.find(i => i.id === e.target.value)
                setSelectedInstance(inst)
                loadChats(inst)
              }}
            >
              {instances.map(inst => (
                <option key={inst.id} value={inst.id}>
                  {inst.name}
                </option>
              ))}
            </select>
          </div>
        </div>

        {/* Lista de Chats */}
        <div className="flex-1 bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden flex flex-col">
          {/* Header com busca */}
          <div className="p-4 border-b border-zinc-100">
            <div className="flex items-center justify-between mb-3">
              <div className="flex items-center gap-2">
                <Clock className="w-4 h-4 text-zinc-400" />
                <span className="text-xs font-bold text-zinc-500 uppercase tracking-wider">
                  Conversas ({filteredChats.length})
                </span>
              </div>
              <button
                onClick={() => loadChats(selectedInstance)}
                disabled={loading}
                className="p-2 hover:bg-emerald-50 text-zinc-400 hover:text-emerald-600 rounded-xl transition-all disabled:opacity-40"
              >
                <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
              </button>
            </div>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-300" />
              <input
                type="text"
                placeholder="Buscar conversa..."
                className="w-full bg-zinc-50 border border-zinc-100 rounded-xl py-2.5 pl-9 pr-4 text-sm text-zinc-700 placeholder:text-zinc-300 outline-none focus:border-emerald-300 transition-all"
                value={searchTerm}
                onChange={e => setSearchTerm(e.target.value)}
              />
            </div>
          </div>

          {/* Lista */}
          <div className="flex-1 overflow-y-auto">
            {loading ? (
              <div className="h-full flex flex-col items-center justify-center gap-3 py-20">
                <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
                <p className="text-xs font-semibold text-zinc-400">Carregando conversas...</p>
              </div>
            ) : error ? (
              <div className="p-6 flex flex-col items-center gap-4 text-center">
                <AlertCircle className="w-10 h-10 text-red-400" />
                <p className="text-xs font-semibold text-zinc-500 leading-relaxed">{error}</p>
                <button
                  onClick={() => loadChats(selectedInstance)}
                  className="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-bold hover:bg-emerald-100 transition-all"
                >
                  Tentar novamente
                </button>
              </div>
            ) : filteredChats.length === 0 ? (
              <div className="py-16 flex flex-col items-center gap-3 text-center px-6">
                <MessageSquare className="w-12 h-12 text-zinc-200" />
                <p className="text-xs font-semibold text-zinc-400">
                  Nenhuma conversa encontrada
                </p>
              </div>
            ) : (
              filteredChats.map(chat => {
                const isActive = activeChat?.remoteJid === chat.remoteJid
                return (
                  <button
                    key={chat.id || chat.remoteJid}
                    onClick={() => handleSelectChat(chat)}
                    className={`w-full flex items-center gap-3 px-4 py-3.5 hover:bg-zinc-50 transition-all border-b border-zinc-50 text-left group ${isActive ? 'bg-emerald-50' : ''}`}
                  >
                    {/* Avatar */}
                    {chat.profilePicUrl ? (
                      <img
                        src={chat.profilePicUrl}
                        alt=""
                        className="w-11 h-11 rounded-full object-cover shrink-0 shadow-sm"
                        onError={(e: any) => { e.target.style.display = 'none' }}
                      />
                    ) : (
                      <div className={`w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold shrink-0 shadow-sm ${isActive ? 'bg-emerald-500 text-white' : 'bg-zinc-100 text-zinc-500'}`}>
                        {getInitial(chat)}
                      </div>
                    )}

                    {/* Info */}
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center justify-between">
                        <span className={`text-sm font-bold truncate ${isActive ? 'text-emerald-700' : 'text-zinc-800'}`}>
                          {getDisplayName(chat)}
                        </span>
                        {chat.updatedAt && (
                          <span className="text-[10px] text-zinc-400 shrink-0 ml-2">
                            {new Date(chat.updatedAt).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })}
                          </span>
                        )}
                      </div>
                      <p className="text-xs text-zinc-400 truncate mt-0.5">
                        {chat.lastMessage?.message?.conversation || chat.remoteJid?.split('@')[0]}
                      </p>
                    </div>

                    {/* Badge não lidas */}
                    {(chat.unreadCount ?? 0) > 0 && (
                      <span className="w-5 h-5 bg-emerald-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0">
                        {chat.unreadCount}
                      </span>
                    )}
                  </button>
                )
              })
            )}
          </div>
        </div>
      </aside>

      {/* ── Janela de Chat ── */}
      <main className="flex-1 bg-white rounded-2xl border border-zinc-200 shadow-sm flex flex-col overflow-hidden">
        {activeChat ? (
          <>
            {/* Header do chat */}
            <div className="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-white">
              <div className="flex items-center gap-3">
                {activeChat.profilePicUrl ? (
                  <img src={activeChat.profilePicUrl} className="w-10 h-10 rounded-full object-cover" alt="" />
                ) : (
                  <div className="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                    <User className="w-5 h-5 text-emerald-600" />
                  </div>
                )}
                <div>
                  <h3 className="text-[15px] font-bold text-zinc-900">{getDisplayName(activeChat)}</h3>
                  <div className="flex items-center gap-1.5">
                    <div className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                    <span className="text-[11px] text-zinc-400 font-medium">
                      {activeChat.remoteJid?.split('@')[0]}
                    </span>
                  </div>
                </div>
              </div>
              <button className="p-2 hover:bg-zinc-50 rounded-xl text-zinc-400 transition-all">
                <MoreVertical className="w-5 h-5" />
              </button>
            </div>

            {/* Mensagens */}
            <div className="flex-1 overflow-y-auto p-6 space-y-3 bg-[#efeae2]"
              style={{ backgroundImage: 'url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23d9c9a3\' fill-opacity=\'0.2\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")' }}
            >
              {loadingMessages ? (
                <div className="flex-1 flex flex-col items-center justify-center gap-3 h-full">
                  <Loader2 className="w-8 h-8 animate-spin text-emerald-600 opacity-40" />
                  <p className="text-xs font-semibold text-zinc-500">Carregando mensagens...</p>
                </div>
              ) : messages.length === 0 ? (
                <div className="flex flex-col items-center justify-center h-full gap-3 opacity-40">
                  <MessageSquare className="w-12 h-12 text-zinc-400" />
                  <p className="text-sm font-semibold text-zinc-500">Nenhuma mensagem</p>
                </div>
              ) : (
                messages.map((msg, i) => {
                  const isMe = msg.key?.fromMe
                  const content =
                    msg.message?.conversation ||
                    msg.message?.extendedTextMessage?.text ||
                    msg.message?.imageMessage?.caption ||
                    '📎 Mídia'

                  return (
                    <div key={i} className={`flex ${isMe ? 'justify-end' : 'justify-start'}`}>
                      <div className={`max-w-[72%] px-4 py-2.5 rounded-2xl shadow-sm ${
                        isMe
                          ? 'bg-[#dcf8c6] text-zinc-900 rounded-tr-sm'
                          : 'bg-white text-zinc-900 rounded-tl-sm'
                      }`}>
                        {!isMe && msg.pushName && (
                          <p className="text-[11px] font-bold text-emerald-600 mb-1">{msg.pushName}</p>
                        )}
                        <p className="text-[14px] leading-relaxed">{content}</p>
                        <div className={`flex items-center gap-1 mt-1 ${isMe ? 'justify-end' : 'justify-start'}`}>
                          <span className="text-[10px] text-zinc-400">
                            {formatTime(msg.messageTimestamp)}
                          </span>
                          {isMe && <CheckCheck className="w-3 h-3 text-blue-500" />}
                        </div>
                      </div>
                    </div>
                  )
                })
              )}
              <div ref={messagesEndRef} />
            </div>

            {/* Input */}
            <div className="px-4 py-3 border-t border-zinc-100 bg-white">
              <div className="flex items-center gap-3 bg-zinc-50 border border-zinc-200 rounded-2xl px-4 py-2 focus-within:border-emerald-300 transition-all">
                <input
                  type="text"
                  placeholder="Escreva uma mensagem..."
                  className="flex-1 bg-transparent border-none outline-none text-sm text-zinc-800 placeholder:text-zinc-400"
                  value={newMessage}
                  onChange={e => setNewMessage(e.target.value)}
                  onKeyDown={e => e.key === 'Enter' && handleSend()}
                />
                <button
                  onClick={handleSend}
                  disabled={!newMessage.trim()}
                  className="w-9 h-9 bg-emerald-500 hover:bg-emerald-400 disabled:opacity-40 text-white rounded-xl flex items-center justify-center transition-all active:scale-90 shadow-sm"
                >
                  <Send className="w-4 h-4 fill-current ml-0.5" />
                </button>
              </div>
            </div>
          </>
        ) : (
          <div className="flex-1 flex flex-col items-center justify-center gap-4 text-center">
            <div className="w-20 h-20 bg-zinc-100 rounded-full flex items-center justify-center">
              <MessageSquare className="w-9 h-9 text-zinc-300" />
            </div>
            <div>
              <h3 className="text-lg font-bold text-zinc-700">Selecione uma conversa</h3>
              <p className="text-sm text-zinc-400 mt-1">Escolha um contato na lista para ver as mensagens</p>
            </div>
          </div>
        )}
      </main>
    </div>
  )
}
