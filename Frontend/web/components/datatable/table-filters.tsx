'use client'

import { useTranslations } from "next-intl"

interface Props {
  onSearch: (val: string) => void
  onLimitChange ?: (val: number) => void
  onClickCreate ?: () => void
  hasSelected : boolean
}

export default function TableFilters({ onSearch, hasSelected, onClickCreate }: Props) {
  const t = useTranslations('datatable');
  return (
    <div className="flex justify-between items-center">
      <input
        type="text"
        placeholder={t('search')}
        onChange={(e) => onSearch(e.target.value)}
        className="input input-outline input-md w-full max-w-xs"
      />
      <div>
        {
            hasSelected ? (
            <button className="btn btn-error" >{t('bulk_delete')}</button>
            ):onClickCreate ? (<button className="btn btn-primary" onClick={onClickCreate}> {t('create_new')}</button>) : null
        }
      </div>
      {/* <select onChange={(e) => onLimitChange(Number(e.target.value))} className="select select-bordered select-md">
        {[5, 10, 20, 50].map((num) => (
          <option key={num} value={num}>{num} / page</option>
        ))}
      </select> */}
    </div>
  )
}