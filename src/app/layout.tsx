import type { Metadata } from 'next'
import './globals.css'

export const metadata: Metadata = {
  title: 'AppsGroup',
  description: 'Site da AppsGroup',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="pt-BR">
      <body>{children}</body>
    </html>
  )
}
