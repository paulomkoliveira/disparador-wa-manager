'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { 
  LayoutDashboard, 
  Send, 
  Mail, 
  Users, 
  MessageSquare, 
  Settings, 
  LogOut 
} from 'lucide-react'
import { createClientComponentClient } from '@supabase/auth-helpers-nextjs'
import { useRouter } from 'next/navigation'

const menuItems = [
  { name: 'Dashboard', icon: LayoutDashboard, href: '/' },
  { name: 'Disparador WA', icon: Send, href: '/disparador' },
  { name: 'Disparador E-mail', icon: Mail, href: '/email' },
  { name: 'CRM Kanban', icon: Users, href: '/crm' },
  { name: 'Chat App', icon: MessageSquare, href: '/chat' },
]

export default function Sidebar({ user }: { user: any }) {
  const pathname = usePathname()
  const router = useRouter()
  const supabase = createClientComponentClient()

  const handleLogout = async () => {
    await supabase.auth.signOut()
    router.push('/login')
    router.refresh()
  }

  return (
    <aside className="fixed left-0 top-0 h-full w-64 bg-slate-900 border-r border-slate-800 flex flex-col z-50">
      <div className="p-6 flex items-center gap-3">
        <div className="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
          <MessageSquare className="text-white w-6 h-6" />
        </div>
        <div>
          <h1 className="text-white font-bold text-lg leading-none">WA Manager</h1>
          <p className="text-emerald-500 text-[10px] font-bold uppercase tracking-wider mt-1">Premium Auth</p>
        </div>
      </div>

      <nav className="flex-1 px-4 py-4 space-y-1">
        {menuItems.map((item) => {
          const isActive = pathname === item.href
          return (
            <Link
              key={item.href}
              href={item.href}
              className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group ${
                isActive 
                  ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' 
                  : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200'
              }`}
            >
              <item.icon className={`w-5 h-5 ${isActive ? 'text-emerald-400' : 'group-hover:text-emerald-400'}`} />
              <span className="font-medium text-sm">{item.name}</span>
            </Link>
          )
        })}
      </nav>

      <div className="p-4 border-t border-slate-800">
        <div className="flex items-center gap-3 mb-4 px-2">
          <div className="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-xs font-bold text-white uppercase">
            {user?.email?.substring(0, 2) || 'U'}
          </div>
          <div className="min-w-0">
            <p className="text-xs font-semibold text-slate-200 truncate">
              {user?.user_metadata?.full_name || user?.email?.split('@')[0] || 'Usuário'}
            </p>
            <p className="text-[10px] text-slate-500 truncate">{user?.email}</p>
          </div>
        </div>
        
        <button 
          onClick={handleLogout}
          className="flex items-center justify-center w-full gap-2 px-4 py-2.5 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-400 rounded-xl transition-all text-sm font-medium"
        >
          <LogOut className="w-4 h-4" />
          <span>Sair da conta</span>
        </button>
      </div>
    </aside>
  )
}
