import React from 'react'

export default function AppShell({ children }) {
  return (
    <div className="min-h-screen flex flex-col bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100">
      <header className="backdrop-blur bg-white/5 border-b border-white/10 px-6 py-4 flex items-center gap-3 shadow-[0_10px_40px_rgba(0,0,0,0.45)]">
        <div className="w-9 h-9 bg-cyan-400 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/30">
          <span className="text-slate-950 text-sm font-black tracking-tight">R</span>
        </div>
        <div>
          <h1 className="text-xl font-semibold tracking-tight">Dynamic Reporting</h1>
          <p className="text-xs text-slate-400">Live Solr insights • Kafka pipeline</p>
        </div>
        <div className="ml-auto flex items-center gap-3">
          <span className="px-3 py-1 text-xs rounded-full bg-white/5 border border-white/10 text-slate-300">
            Dark Mode
          </span>
          <span className="px-3 py-1 text-xs rounded-full bg-cyan-400/10 text-cyan-200 border border-cyan-500/40">
            v0.2
          </span>
        </div>
      </header>
      <main className="flex-1 p-6">{children}</main>
    </div>
  )
}
