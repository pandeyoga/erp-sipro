import { SitemapProvider } from "@/context/useSitemapContext";

export default function Layout({ children }: { children: React.ReactNode }) {
    return (
        <SitemapProvider>
            {children}
        </SitemapProvider>
    )
}