import { createRouteHandlerClient } from '@supabase/auth-helpers-nextjs'
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import nodemailer from 'nodemailer'

export async function POST(req: Request) {
  try {
    const supabase = createRouteHandlerClient({ cookies })
    const { data: { user } } = await supabase.auth.getUser()

    if (!user) {
      return NextResponse.json({ error: 'Não autorizado' }, { status: 401 })
    }

    const { to, subject, message } = await req.json()

    // Buscar configurações SMTP do usuário
    const { data: smtp } = await supabase
      .from('smtp_settings')
      .select('*')
      .eq('user_id', user.id)
      .single()

    if (!smtp) {
      return NextResponse.json({ error: 'Configurações SMTP não encontradas. Vá em Configurações e configure seu e-mail.' }, { status: 400 })
    }

    // Configurar o trasnporte
    const transporter = nodemailer.createTransport({
      host: smtp.host,
      port: smtp.port,
      secure: smtp.secure, // true para 465, false para outros
      auth: {
        user: smtp.user_name,
        pass: smtp.password,
      },
    })

    // Enviar o e-mail
    await transporter.sendMail({
      from: `"${user.email}" <${smtp.from_email}>`,
      to,
      subject,
      text: message,
      html: message.replace(/\n/g, '<br>'),
    })

    return NextResponse.json({ success: true })
  } catch (error: any) {
    console.error('Erro no envio de e-mail:', error)
    return NextResponse.json({ error: error.message || 'Erro interno no servidor' }, { status: 500 })
  }
}
