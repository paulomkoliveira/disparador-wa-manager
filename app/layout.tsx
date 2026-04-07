import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'
import Sidebar from '@/components/Sidebar'
import Header from '@/components/Header'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'WA Manager | Premium Dashboard',
  description: 'Gestão Inteligente de WhatsApp e E-mail Marketing',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="pt-BR">
      <body className={`${inter.className} bg-black text-white selection:bg-emerald-500/20 selection:text-emerald-500 scrollbar-thin`}>
        {children}
      </body>
    </html>
  )
}
