export const dynamic = 'force-dynamic'

import LoginForm from './login-form'

export default function LoginPage() {
  return (
    <div className="min-h-screen bg-slate-950 flex items-center justify-center p-6 relative overflow-hidden">
      {/* Background Effects */}
      <div className="absolute top-0 -left-20 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-3xl animate-pulse"></div>
      <div className="absolute -bottom-20 -right-20 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-3xl animate-pulse delay-700"></div>
      
      <div className="relative z-10 w-full flex justify-center">
        <LoginForm />
      </div>
    </div>
  )
}
