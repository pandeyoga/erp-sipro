"use client";

import { useTranslations } from "next-intl";
import { ReactNode } from "react";

interface ModalFormProps {
  id: string;
  title: string;
  onClose: () => void;
  onSubmit: (e: React.FormEvent<HTMLFormElement>) => void;
  children: ReactNode;
  loading?: boolean;
  width ?: string; 
  disableAction ?: boolean
}

export default function ModalForm({
  id,
  title,
  onClose,
  onSubmit,
  children,
  loading = false,
  width = 'lg',
  disableAction = false
}: ModalFormProps) {
  const t = useTranslations('common');
  return (
    <dialog id={id} className="modal modal-middle">
      <div className={"modal-box max-w-3/4 flex flex-col gap-5 " + "w-" + width}>
        <h1 className="text-lg font-semibold">{title}</h1>
        <form onSubmit={onSubmit} className="flex flex-col gap-4">
          {children}
          {disableAction ? null : (
            <div className="flex gap-2 ml-auto mt-4">
              <button type="button" className="btn btn-outline" onClick={onClose}>
                {t('cancel')}
              </button>
              <button className="btn btn-primary" disabled={loading }>
                { loading ? <span className="loading loading-spinner"></span> : null}
                {t('save')}
              </button>
            </div>
          )}
        </form>
      </div>
      <form method="dialog" className="modal-backdrop">
        <button>close</button>
      </form>
    </dialog>
  );
}
