import { createClient } from '@supabase/supabase-js'
import { NextResponse } from 'next/server'

// Configuração do Supabase (Server Side)
const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL!
const supabaseServiceKey = process.env.SUPABASE_SERVICE_ROLE_KEY! // Necessário Service Role para bypass RLS se necessário
const supabase = createClient(supabaseUrl, supabaseServiceKey)

export async function POST(req: Request) {
  try {
    const body = await req.json()
    const { event, data } = body

    // Evento de nova mensagem recebida
    if (event === 'messages.upsert') {
      const message = data.message
      if (!message || message.key.fromMe) return NextResponse.json({ ok: true })

      const remoteJid = message.key.remoteJid
      const phone = remoteJid.split('@')[0]
      const pushName = data.pushName || 'Novo Contato'
      const content = message.message?.conversation || message.message?.extendedTextMessage?.text || 'Mídia'

      // 1. Verificar se o lead já existe
      const { data: existingLead } = await supabase
        .from('leads')
        .select('*')
        .eq('phone', phone)
        .single()

      if (!existingLead) {
        // 2. Buscar a primeira coluna do Kanban (Novos Leads)
        const { data: firstColumn } = await supabase
          .from('kanban_columns')
          .select('id')
          .order('order', { ascending: true })
          .limit(1)
          .single()

        if (firstColumn) {
          // 3. Criar o novo Lead automaticamente
          await supabase.from('leads').insert([{
            name: pushName,
            phone: phone,
            column_id: firstColumn.id,
            last_message: content,
            status: 'active'
          }])
          
          console.log(`[Webhook] Novo lead criado: ${pushName} (${phone})`)
        }
      } else {
        // Atualizar última mensagem do lead existente
        await supabase
          .from('leads')
          .update({ last_message: content })
          .eq('id', existingLead.id)
      }
    }

    return NextResponse.json({ ok: true })
  } catch (error) {
    console.error('[Webhook Error]', error)
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}
