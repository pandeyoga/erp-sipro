'use client'

import { useTranslations } from 'next-intl';
import { Column } from './datatable'

interface Props<T> {
  columns: Column<T>[]
  sort: { key: keyof T; direction: 'asc' | 'desc' } | null
  onSort: (val: { key: keyof T; direction: 'asc' | 'desc' }) => void
  allSelected: boolean
  toggleSelectAll: () => void
  withAction ?: boolean
  withSelect ?: boolean
}

export default function TableHeader<T>({ columns, sort, onSort, allSelected, toggleSelectAll, withAction, withSelect }: Props<T>) {
  const t = useTranslations('datatable');
  const handleSort = (key: keyof T) => {
    if (sort?.key === key) {
      onSort({ key, direction: sort.direction === 'asc' ? 'desc' : 'asc' })
    } else {
      onSort({ key, direction: 'asc' })
    }
  }

  return (
    <tr className='bg-gray-200 border-0'>
      {withSelect && (
        <th>
          <input type="checkbox" checked={allSelected} onChange={toggleSelectAll} className="checkbox checkbox-sm checkbox-success" />
        </th>
      )}
      {columns.map((col) => (
        <th
          key={col.key as string}
          onClick={() => col.sortable && handleSort(col.key)}
          className={col.sortable ? 'cursor-pointer select-none' : ''}
        >
          {col.label}
          {sort?.key === col.key && (
            <span className="ml-1">{sort.direction === 'asc' ? '▲' : '▼'}</span>
          )}
        </th>
      ))}
      {withAction && (<th>{t('action')}</th>)}
    </tr>
  )
}