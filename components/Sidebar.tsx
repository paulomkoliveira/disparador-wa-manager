'use client'

import Link from 'next/link'
import { usePathname, useRouter } from 'next/navigation'
import { createClient } from '@/lib/supabase/client'
import { 
  LayoutDashboard, 
  MessageSquare, 
  TrendingUp,
  LayoutGrid,
  Settings, 
  LogOut,
  ChevronRight,
  Fingerprint
} from 'lucide-react'

const navItems = [
  { href: '/', label: 'Dashboard', icon: LayoutDashboard },
  { href: '/chat', label: 'Chat Multi-Instância', icon: MessageSquare },
  { href: '/disparador', label: 'Disparador em Massa', icon: TrendingUp },
  { href: '/kanban', label: 'CRM / Leads', icon: LayoutGrid },
  { href: '/settings', label: 'Configurações', icon: Settings },
]

export default function Sidebar() {
  const pathname = usePathname()
  const router = useRouter()
  const supabase = createClient()

  const handleLogout = async () => {
    await supabase.auth.signOut()
    router.refresh()
  }

  return (
    <aside className="fixed left-0 top-0 bottom-0 w-72 bg-[#0a1a14] p-8 flex flex-col h-screen z-50 border-r border-emerald-900/10">
      {/* Brand Logo conforme screenshot */}
      <div className="flex items-center gap-2 mb-12">
        <Fingerprint className="text-emerald-500 w-8 h-8" />
        <h1 className="text-2xl font-bold tracking-tight text-white">WA<span className="text-white opacity-80 font-normal">Manager</span></h1>
      </div>

      <nav className="flex flex-col gap-3 flex-grow">
        {navItems.map((item) => {
          const isActive = pathname === item.href
          return (
            <Link 
              key={item.href} 
              href={item.href}
              className={`group flex items-center justify-between px-6 py-4 rounded-xl transition-all duration-300 ${
                isActive 
                ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-600/20' 
                : 'text-zinc-400 hover:text-white hover:bg-emerald-800/20'
              }`}
            >
              <div className="flex items-center gap-4">
                <item.icon className={`w-5 h-5 transition-colors ${isActive ? 'text-white' : 'group-hover:text-emerald-400'}`} />
                <span className="text-[14px] font-medium">
                  {item.label}
                </span>
              </div>
            </Link>
          )
        })}
      </nav>

      <div className="mt-auto">
        <button 
          onClick={handleLogout}
          className="w-full flex items-center gap-4 px-6 py-4 bg-red-500 hover:bg-red-600 text-white rounded-xl transition-all shadow-lg shadow-red-500/10 group group-active:scale-95"
        >
          <LogOut className="w-5 h-5" />
          <span className="text-[14px] font-bold uppercase tracking-widest">Sair</span>
        </button>
      </div>
    </aside>
  )
}
