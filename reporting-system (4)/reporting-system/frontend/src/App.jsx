import React, { Suspense, lazy } from 'react'
import AppShell from './components/Layout/AppShell'

const ReportPage = lazy(() => import('./pages/ReportPage'))

export default function App() {
  return (
    <AppShell>
      <Suspense fallback={<div className="p-8 text-gray-400">Loading...</div>}>
        <ReportPage />
      </Suspense>
    </AppShell>
  )
}
