import type { Metadata } from "next";
import Script from "next/script";
import { UIProvider } from "./UIContext";
import Navigation from "@/components/Navigation";
import BookingSidebar from "@/components/BookingSidebar";
import Footer from "@/components/Footer";
import ClientScripts from "@/components/ClientScripts";
import "./globals.css";

export const metadata: Metadata = {
  title: "Opera Hotels",
  description: "Opera Grand Hotel - Luxury accommodation in Dubai",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" suppressHydrationWarning data-scroll-behavior="smooth">
      <head>
        {/* Bootstrap 5 CSS */}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
        
        {/* FontAwesome for Icons */}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
        
        {/* Owl Carousel 2 CSS */}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />
        
        {/* Custom CSS */}
        <link rel="stylesheet" href="/css/style.css" />
      </head>
      <body suppressHydrationWarning>
        <UIProvider>
          <Navigation />
          <BookingSidebar />
          {children}
          <Footer />
        </UIProvider>

        {/* Scripts */}
        <Script src="https://code.jquery.com/jquery-3.7.1.min.js" strategy="beforeInteractive" />
        <Script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" strategy="afterInteractive" />
        <Script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" strategy="afterInteractive" />
        <ClientScripts />
      </body>
    </html>
  );
}
