'use client'

import Sidebar from '@/components/Sidebar'
import Header from '@/components/Header'

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <div className="flex bg-white">
      <Sidebar />
      <main className="flex-1 lg:ml-72 flex flex-col min-h-screen bg-[#f8fafc]">
        <Header />
        <div className="p-10 flex-1 max-w-[1920px] mx-auto w-full animate-in fade-in duration-1000">
          {children}
        </div>
      </main>
    </div>
  )
}
