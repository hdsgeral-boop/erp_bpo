import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import AppLayout from "@/components/layout/AppLayout";

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-sans",
});

export const metadata: Metadata = {
  title: "ERP Consulvolt — Portal de Gestão",
  description: "Sistema Integrado de Gestão Empresarial (ERP)",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="pt" className={`${inter.variable} h-full antialiased`}>
      <body className="h-full bg-slate-100 font-sans text-slate-900">
        <AppLayout>{children}</AppLayout>
      </body>
    </html>
  );
}
