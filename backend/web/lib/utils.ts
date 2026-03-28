// src/lib/utils.ts

import { useEffect, useState } from "react";

/**
 * Utility untuk menggabungkan className secara kondisional.
 * Berguna saat menggunakan Tailwind CSS.
 */
export function cn(...classes: (string | false | null | undefined)[]): string {
    return classes.filter(Boolean).join(" ");
  }

export function convertToRupiah(amount : number) {
  if(!amount) return "-";
  const formatted = new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0, // hilangkan koma jika tidak mau desimal
  }).format(amount);
  return (formatted)
}

export function useBreakpoint() {
  const [isMobile, setIsMobile] = useState(false);
  const [isDesktop, setIsDesktop] = useState(false);

  useEffect(() => {
    const mobileQuery = window.matchMedia("(max-width: 1279px)"); // < lg
    const desktopQuery = window.matchMedia("(min-width: 1280px)"); // lg+

    const update = () => {
      setIsMobile(mobileQuery.matches);
      setIsDesktop(desktopQuery.matches);
    };

    update(); // initial check

    mobileQuery.addEventListener("change", update);
    desktopQuery.addEventListener("change", update);

    return () => {
      mobileQuery.removeEventListener("change", update);
      desktopQuery.removeEventListener("change", update);
    };
  }, []);

  return { isMobile, isDesktop };
}

export const isAllowed = (permission : string) : boolean => {
  try{
    const permissions =  localStorage.getItem("permissions") ?? ""
    const permissions_data = JSON.parse(permissions)
    const codes = permissions_data.map((p: any) => p.code)
    if(codes[0] == 'all_access') return true
    return codes.includes(permission)
  }catch(e){
    return false
  }
}
  

// ================= HELPER =================

export function getPermissionType(pathname: string): string {
  if (pathname.endsWith('/create')) return 'create'
  if (pathname.endsWith('/edit')) return 'update'
  return 'get_all'
}

export function getPermissionName(pathname: string): string {
  let modules = ''
  let subModules = ''
  const permissionType = getPermissionType(pathname)

  // CRM
  if (pathname.startsWith('/crm')) {
    modules = 'lead'
    if (pathname.startsWith('/crm/document')){
      if (pathname.endsWith('/verify')) return `lead.verify_document`
      return `lead.get_all_documents`
    } 
    

    if (pathname.startsWith('/crm/dashboard')) subModules = 'dashboard'
    else if (pathname.startsWith('/crm/contact')) subModules = 'contact'
    else if (pathname.startsWith('/crm/lead')) subModules = 'lead'
    else if (pathname.startsWith('/crm/reservation')) subModules = 'reservation'
    else if (pathname.startsWith('/crm/payment')) subModules = 'payment'
    else if (pathname.startsWith('/crm/legalitas')) subModules = 'final-legality'

    if (subModules === 'contact') return `contact.${permissionType}`
    if (modules === 'lead' || subModules === 'lead') return `${modules}.${permissionType}`
    if (subModules === 'dashboard') return `marketing.get_marketing_performance`
    

    return `${modules}.${permissionType}_${subModules}`
  }

  // PROPERTY
  if (pathname.startsWith('/property')) {
    modules = 'property'

    if (pathname.startsWith('/property/project')) subModules = 'project'
    else if (pathname.startsWith('/property/cluster')) subModules = 'cluster'
    else if (pathname.startsWith('/property/unit')) subModules = 'unit'
    else if (pathname.startsWith('/property/siteplan')) subModules = 'property'
    else if (pathname.startsWith('/property/sub-contractor')) subModules = 'sub_contractor'
    else if (pathname.startsWith('/property/construction')) subModules = 'construction'
    else if (pathname.startsWith('/property/retention')) subModules = 'retention'

    if(pathname.startsWith('/property/siteplan')) {
      return 'property.manage_site_plan';
    }

    return `${modules}.${permissionType}_${subModules}`
  }

  // FINANCE
  if (pathname.startsWith('/finance')) {
    modules = 'finance'

    if (pathname.startsWith('/finance/cash-in')) subModules = 'cash_in'
    else if (pathname.startsWith('/finance/cash-out')) subModules = 'cash_out'
    else if (pathname.startsWith('/finance/debt')) subModules = 'debt'
    else if (pathname.startsWith('/finance/submission')){
      subModules = permissionType == 'get_all' ? 'submissions' : 'submission' 
      return `${modules}.${permissionType}_${subModules}`
    }else if(pathname.startsWith('/finance/cashflow')){
      return `${modules}.view_cash_flow`
    }else if(pathname.startsWith('/finance/laba-rugi')){
      return `${modules}.view_laba_rugi`
    }else if(pathname.startsWith('/finance/neraca')){
      return `${modules}.view_neraca`
    }
    else if (pathname.startsWith('/finance/bank-account')) subModules = 'bank_accounts'

    return `${modules}.manage_${subModules}`
  }

  // ASSET
  if (pathname.startsWith('/asset')) {
    return 'asset.manage_assets'
  }

  return ''
}