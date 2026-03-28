"use client";

import { useTranslations } from "next-intl";

interface ModalDeleteConfirmProps {
  id: string;
  onClose: () => void;
  onConfirm: () => void;
  message?: string;
  loading?: boolean;
}

export default function ModalDeleteConfirm({
  id,
  onClose,
  onConfirm,
  message,
  loading = false
}: ModalDeleteConfirmProps) {
  const t = useTranslations('common');
  
  return (
    <dialog id={id} className="modal modal-middle">
      <div className="modal-box w-11/12 max-w-lg flex flex-col gap-5">
        <h1 className="text-lg font-semibold">{t('delete_confirm')}</h1>
        <p>{message ?? t("delete_confirm_message")}</p>
        <div className="flex gap-2 ml-auto mt-4">
          <button type="button" className="btn btn-outline" onClick={onClose}>
            {t('cancel')}
          </button>
          <button type="button" className="btn btn-primary" disabled={loading} onClick={onConfirm}>
            { loading ? <span className="loading loading-spinner"></span> : null}
            {t('yes')}, {t('delete')}
          </button>
        </div>
      </div>
      <form method="dialog" className="modal-backdrop">
        <button>close</button>
      </form>
    </dialog>
  );
}
