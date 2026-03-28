"use client";
import Sidebar from "./sidebar";
import { useEffect, useState } from "react";
import { usePathname, useRouter } from "next/navigation";
import { getPermissionName, isAllowed, useBreakpoint } from "@/lib/utils";

function formatSegment(segment: string) {
  return segment
    .replace(/-/g, " ")
    .replace(/\b\w/g, (char) => char.toUpperCase());
}

function BreadcrumbHeader() {
  const pathname = usePathname();
  const router = useRouter();

  const segments = pathname
    .split("/")
    .filter(Boolean) // hapus empty string dari split
    .map((seg) => formatSegment(seg));

  return (
    <div className="card bg-base-100 border border-base-200 p-4 mb-6">
      <div className="flex items-center justify-between flex-wrap gap-2">
        {/* Breadcrumb */}
        <div className="text-sm text-gray-500">
          {segments.map((seg, i) => (
            <span key={i}>
              <span
                className={
                  i === segments.length - 1
                    ? "text-gray-700 font-medium"
                    : "text-gray-400"
                }
              >
                {seg.length > 20 ? "-" : seg}
              </span>
              {i < segments.length - 1 && (
                <span className="mx-1 text-gray-400">/</span>
              )}
            </span>
          ))}
        </div>

        {/* Back button */}
        
        {(segments.length > 2 && segments[0] != 'asset') && (<button
          onClick={() => router.push(`/${segments[0].toLowerCase()}/${segments[1]?.toLowerCase() ?? ""}`)}
          className="text-sm text-gray-500 hover:text-gray-700 underline"
        >
          ← Back
        </button>)}
      </div>
    </div>
  );
}

export default function Layout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const pathname = usePathname();

  const { isMobile, isDesktop } = useBreakpoint();

  const [isReady, setReady] = useState(false);

  useEffect(() => {
    const token = localStorage.getItem("access_token");
    if (!token) {
      router.replace("/login");
    }
    if(pathname != '/dashboard'){
      const permissionName = getPermissionName(pathname)
      if(!isAllowed(permissionName)){
        router.replace("/403");
      }
    }
    setTimeout(() => setReady(true), 500);
  }, []);
  const isSiteFull = pathname == '/property/siteplan/full'
  if (!isReady) {
    return null;
  }
  return (
    <div className="flex min-h-screen bg-base-200">
      
      {/* Sidebar (selalu muncul di lg ke atas) */}
      {!isSiteFull && isDesktop && (
        <div className="hidden lg:block w-64 bg-base-100 shadow">
          <Sidebar />
        </div>
      )}
      {/* Drawer untuk sidebar */}
      
      {isMobile && <div className="drawer">
        <input id="sidebar-drawer" type="checkbox" className="drawer-toggle" />
        <div className="drawer-content flex flex-col">
          {/* Navbar untuk mobile */}
          <div className="w-full navbar bg-base-100 ">
            <label
              htmlFor="sidebar-drawer"
              className="btn btn-square btn-ghost"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                className="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
            </label>
          </div>

            <div className="p-4 ">
              <BreadcrumbHeader />
              {children}
            </div>
        </div>

        <div className="drawer-side">
          <label htmlFor="sidebar-drawer" className="drawer-overlay"></label>
          <Sidebar />
        </div>
      </div>}
      {isDesktop && (
        <div className={`flex-1 hidden lg:block ${!isSiteFull ? 'p-16' : ''}`}>
          {!isSiteFull && <BreadcrumbHeader />}
          {children}
        </div>
      )}
    </div>
  );
}
