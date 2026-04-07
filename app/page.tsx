import { 
  Users, 
  MessageSquare, 
  Mail, 
  TrendingUp, 
  Clock, 
  CheckCircle2, 
  AlertCircle 
} from 'lucide-react'

export default function Dashboard() {
  const stats = [
    { label: 'Total de Leads', value: '1,284', icon: Users, color: 'emerald', trend: '+12%' },
    { label: 'Mensagens WA', value: '8,432', icon: MessageSquare, color: 'blue', trend: '+5%' },
    { label: 'E-mails Enviados', value: '45,210', icon: Mail, color: 'purple', trend: '+18%' },
    { label: 'Status da API', value: 'Online', icon: TrendingUp, color: 'emerald', trend: '100%' },
  ]

  return (
    <div className="space-y-8 animate-in fade-in duration-700">
      <div className="flex flex-col gap-2">
        <h1 className="text-3xl font-bold text-white tracking-tight">Status do Dia</h1>
        <p className="text-slate-500 text-sm font-medium">Bem-vindo de volta! Veja como estão suas automações hoje.</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat) => (
          <div key={stat.label} className="bg-slate-900/50 backdrop-blur-sm border border-slate-800 p-6 rounded-3xl group hover:border-emerald-500/30 transition-all duration-300">
            <div className="flex justify-between items-start mb-4">
              <div className="p-3 rounded-2xl bg-emerald-500/10 text-emerald-500 group-hover:scale-110 transition-transform">
                <stat.icon className="w-6 h-6" />
              </div>
              <span className="text-[10px] font-bold px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded-lg">
                {stat.trend}
              </span>
            </div>
            <p className="text-slate-500 text-xs font-bold uppercase tracking-wider mb-1">{stat.label}</p>
            <h3 className="text-2xl font-bold text-white tabular-nums">{stat.value}</h3>
          </div>
        ))}
      </div>
    </div>
  )
}
