"use client";

import { usePathname, useRouter } from "next/navigation";
import { useLocale } from "next-intl";
import { useTransition } from "react";
import { toggleLanguageApi } from "@/api/auth/auth";

export default function LanguageToggle() {
  const locale = useLocale();
  const router = useRouter();
  const pathname = usePathname();
  const [isPending, startTransition] = useTransition();

  const toggleLanguage = () => {
    async function toggle(){
        await toggleLanguageApi(locale == "en" ? "id" : "en")
    }
    toggle()
  };

  return (
    <button
      onClick={toggleLanguage}
      disabled={isPending}
      className="btn btn-sm btn-outline flex items-center gap-2 hidden"
    >
      <input
        type="checkbox"
        className="toggle toggle-primary"
        checked={locale === "id"}
        onChange={toggleLanguage}
      />
      <span className="font-semibold">
        {locale === "en" ? "🇮🇩 Indonesia" : "🇺🇸 English"}
      </span>
    </button>
  );
}
