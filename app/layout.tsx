import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'
import Sidebar from '@/components/Sidebar'
import Header from '@/components/Header'
import { createClient } from '@/lib/supabase'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'WA Manager - Automação Premium',
  description: 'Plataforma de gerenciamento e automação de mensagens.',
}

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  const supabase = createClient()
  const { data: { user } } = await supabase.auth.getUser()

  return (
    <html lang="pt-BR" className="dark">
      <body className={`${inter.className} bg-slate-950 text-slate-200 antialiased`}>
        {user ? (
          <div className="flex min-h-screen">
            <Sidebar user={user} />
            <div className="flex-1 ml-64 flex flex-col">
              <Header title="WA Manager" />
              <main className="p-8 flex-1 overflow-auto">
                {children}
              </main>
            </div>
          </div>
        ) : (
          <main className="min-h-screen">
            {children}
          </main>
        )}
      </body>
    </html>
  )
}
