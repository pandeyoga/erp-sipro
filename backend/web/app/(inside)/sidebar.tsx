"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import {
  LayoutDashboard,
  ShieldCheck,
  UserPlus,
  FileText,
  FileSignature,
  Handshake,
  LogOut,
  Users,
  Contact,
  HomeIcon,
  Banknote,
  ChartArea,
  PiggyBank,
  ChartLineIcon,
  CoinsIcon,
  CreditCard,
  Building2,
  Layers3Icon,
  Map,
  Wrench,
  HardHat,
  ArrowUpCircleIcon,
  ArrowDownCircleIcon,
  FileTextIcon,
  BanknoteIcon,
  Building,
  PieChart,
  Scale,
  NotebookText,
} from "lucide-react";
import { isAllowed } from "@/lib/utils";
import { useTranslations } from "next-intl";
import LanguageToggle from "@/components/common/languageToggle";

export default function Sidebar() {
  const pathname = usePathname();
  const [role, setRole] = useState<string | null>(null);
  const [fullname, setFullname] = useState<string | null>(null);
  const [isLoaded, setLoaded] = useState(false)
  const t = useTranslations('menu');

  const menu = [
    {
      label: t("access_management"),
      items: [
        { href: "/access/user", label: "User", icon: <Users size={18} />, permission:"user.get_all" },
        {
          href: "/access/role",
          label: "Role",
          icon: <ShieldCheck size={18} />,
          permission:"role.get_all"
        },
      ],
    },
    {
      label: "CRM",
      items: [
        {
          href: "/crm/dashboard",
          label: "Dashboard",
          icon: <LayoutDashboard size={18} />,
          permission:"marketing.get_marketing_performance"
        },
        { href: "/crm/contact", label: "Contact", icon: <Contact size={18} />, permission:"contact.get_all" },
        { href: "/crm/lead", label: "Lead", icon: <UserPlus size={18} />, permission:"lead.get_all" },
        { href: "/crm/survey", label: "Survey", icon: <UserPlus size={18} />, permission:"lead.get_all" },
        {
          href: "/crm/reservation",
          label: "Reservation",
          icon: <FileText size={18} />,
          permission:"lead.get_all_reservation"
        },
        {
          href: "/crm/document",
          label: "Document & Legal",
          icon: <FileSignature size={18} />,
          permission:"lead.get_all_documents"
        },
        {
          href: "/crm/payment",
          label: "Payment",
          icon: <CreditCard size={18} />,
          permission:"lead.get_all_payment"
        },
        {
          href: "/crm/legalitas",
          label: "Legalitas Akhir",
          icon: <FileSignature size={18} />,
          permission:"lead.get_all_final_legality"
        },
      ],
    },
    {
      label: t("property"),
      items: [
        {
          href: "/property/project",
          label: "Project",
          icon: <Building2 size={18} />,
          permission:"property.get_all_project"
        },
        {
          href: "/property/cluster",
          label: "Cluster",
          icon: <Layers3Icon size={18} />,
          permission:"property.get_all_cluster"
        },
        { href: "/property/unit", label: "Unit", icon: <HomeIcon size={18} />, permission:"property.get_all_unit" },
        {
          href: "/property/siteplan",
          label: "Siteplan",
          icon: <Map size={18} />,
          permission:"property.manage_site_plan" 
        },
        {
          href: "/property/construction",
          label: "Construction Progress",
          icon: <Wrench size={18} />,
          permission:"property.get_all_construction"
        },
        {
          href: "/property/retention",
          label: "Retention",
          icon: <ShieldCheck size={18} />,
          permission:"property.get_all_retention"
        },
        {
          href: "/property/sub-contractor",
          label: "Sub Contractor",
          icon: <HardHat size={18} />,
          permission:"property.get_all_contractor"
        },
      ],
    },
    {
      label: t("asset_management"),
      items: [
        {
          href: "/asset",
          label: "Asset Management",
          icon: <Building size={18} />,
          permission:"asset.manage_assets"
          
        },
      ],
    },
    {
      label: t("finance"),
      items: [
        {
          href: "/finance/cash-in",
          label: "Cash In",
          icon: <ArrowDownCircleIcon size={18} />,
          permission:"finance.manage_cash_in"
        },
        {
          href: "/finance/cash-out",
          label: "Cash Out",
          icon: <ArrowUpCircleIcon size={18} />,
          permission:"finance.manage_cash_out"
        },
        {
          href: "/finance/submission",
          label: "Submisson",
          icon: <FileTextIcon size={18} />,
          permission:"finance.get_all_submissions"
        },
        {
          href: "/finance/bank-account",
          label: "Bank Account",
          icon: <BanknoteIcon size={18} />,
          permission:"finance.manage_bank_accounts"
        },
        {
          href: "/finance/debt",
          label: "Hutang",
          icon: <CreditCard size={18} />,
          permission:"finance.manage_debt"
        },
        {
          href: "#",
          label: "Report",
          icon: <NotebookText size={18}  />,
          permission:"finance.view_cash_flow",
          children: [
            {
              href: "/finance/report/cash-in",
              label: "Cash In",
              icon: <ArrowDownCircleIcon size={18} />,
              permission:"finance.view_cash_flow",
            },
            {
              href: "/finance/cashflow",
              label: "Cashflow",
              icon: <BanknoteIcon size={18} />,
              permission:"finance.view_cash_flow",
            },
            {
              href: "/finance/laba-rugi",
              label: "Laba Rugi",
              icon: <PieChart size={18} />,
              permission:"finance.view_laba_rugi",
            },
            {
              href: "/finance/neraca",
              label: "Neraca",
              icon: <Scale size={18} />,
              permission:"finance.view_neraca",
            },
          ],
        },
      ],
    }
  ];

  useEffect(() => {
    if(!isLoaded){
      setLoaded(true)
      const storedRole = localStorage.getItem("role")?.replace(/"/g, "");
      setRole(storedRole ?? null);
      const fn = localStorage.getItem("fullname")?.replace(/"/g, "");
      setFullname(fn ?? null);
    }
  }, []);

  return (
    <aside className="w-72 h-screen bg-base-100 p-6 fixed flex flex-col justify-between border-r border-gray-200 overflow-y-scroll z-10" >
      <div>
        <h1 className="text-2xl font-bold mb-8 text-primary flex items-center gap-2">
        <img
            src="/assets/logo.png"
            alt="Login Illustration"
            className="w-full mx-auto"
          />
        </h1>
        <nav className="flex flex-col gap-6">
          {menu.map((group) => {
            const menu_item = group.items.filter(({permission} : any) => { 
              const isAllows = isAllowed(permission)
              return isAllows
            }).map(({ href, label, icon, children }: any) => {
              const isActive = pathname.startsWith(href);
              return (
                <li key={href}>
                  <Link href={href}>
                  <button
                    className={
                      `btn ${isActive ? 'btn-secondary' : 'btn-ghost'} btn-block justify-start gap-3` 
                    }
                  >
                    {icon}
                    {label}
                  </button>
                  </Link>

                  {children && (
                    <ul className="ml-6 mt-1 space-y-1 border-l border-base-200 pl-3">
                      {children.filter(({permission} : any) => { 
                        const isAllows = isAllowed(permission)
                        return isAllows
                      }).map((child: any) => {
                        const isChildActive = pathname.startsWith(
                          child.href
                        );
                        return (
                          <li key={child.href}>
                            <Link href={child.href}>
                              <button
                                className={`btn btn-ghost btn-block justify-start gap-2 text-sm ${
                                  isChildActive
                                    ? "bg-base-200 font-semibold"
                                    : ""
                                }`}
                              >
                                {child.icon}
                                {child.label}
                              </button>
                            </Link>
                          </li>
                        );
                      })}
                    </ul>
                  )}
                </li>
              );
            })
            if(menu_item.length == 0) return null

            return (
              <div key={group.label}>
                
              <p className="text-gray-500 text-sm mb-2 uppercase">
                {group.label}
              </p>
              <ul className="space-y-1">
                {menu_item}
              </ul>
              </div>
            )

          })}
        </nav>
      </div>
      <LanguageToggle/>

      {/* User & Logout */}
      <div className="mt-6 pt-4 border-t border-gray-200">
        <div className="dropdown dropdown-top dropdown-end w-full">
          <div
            tabIndex={0}
            role="button"
            className="btn btn-ghost w-full flex justify-start items-center gap-3"
          >
            <div className="avatar">
              <div className="w-8 rounded-full">
                <img
                  src={`https://api.dicebear.com/6.x/initials/svg?seed=${fullname}`}
                  alt="avatar"
                />
              </div>
            </div>
            <span className="truncate">{fullname}</span>
          </div>
          <ul
            tabIndex={0}
            className="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52"
          >
            <li>
              <a
                onClick={() => {
                  localStorage.clear();
                  window.location.href = "/";
                }}
                className="flex items-center gap-2"
              >
                <LogOut size={16} />
                Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </aside>
  );
}
