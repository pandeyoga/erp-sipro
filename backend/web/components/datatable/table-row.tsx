'use client'

import { useState } from 'react'
import { Column } from './datatable'
import { ChevronDown, ChevronRight } from 'lucide-react'

interface TreeRow<T> {
  id: string | number
  [key: string]: any
  children?: T[]
}

interface Props<T extends TreeRow<T>> {
  row: T
  columns: Column<T>[]
  isSelected: boolean
  onToggleSelect: () => void
  actions?: React.ReactNode
  depth?: number
  defaultExpanded?: boolean
  withSelect?: boolean
}

export default function TableRow<T extends TreeRow<T>>({
  row,
  columns,
  isSelected,
  onToggleSelect,
  actions,
  depth = 0,
  defaultExpanded = true, // semua row default expand
  withSelect= true
}: Props<T>) {
  const [expanded, setExpanded] = useState(defaultExpanded)
  const hasChildren = row.children && row.children.length > 0

  return (
    <>
      <tr>
        {withSelect && (
          <td className="border-b border-gray-200">
            <input
              type="checkbox"
              checked={isSelected}
              onChange={onToggleSelect}
              className="checkbox checkbox-sm checkbox-success"
            />
          </td>
        )}
        {columns.map((col, idx) => (
          <td
            key={col.key as string}
            className="border-b border-gray-200"
            style={idx === 0 ? { paddingLeft: depth * 20 } : {}}
          >
            {/* expand/collapse button */}
            {idx === 0 && hasChildren && (
              <button
                className="ml-4 mr-2 text-xs"
                onClick={() => setExpanded(!expanded)}
              >
                {expanded ? <ChevronDown size={16} /> : <ChevronRight size={16} />}
              </button>
            )}
            <span className={((idx === 0 && hasChildren || row.bold) ? 'font-bold' : '')  + ' ' + row.className}>
              {col.render ? col.render(row) : row[col.key] ?? "-"}
            </span>
          </td>
        ))}
        {actions ? (<td className="border-b border-gray-200">{actions}</td>): null}
        
      </tr>

      {/* render children jika expanded */}
      {expanded &&
        hasChildren &&
        row.children!.map((child) => (
          <TableRow
            key={child.id}
            row={child}
            columns={columns}
            isSelected={false}
            onToggleSelect={() => {}}
            actions={actions}
            depth={depth + 1}
            defaultExpanded={defaultExpanded} // wariskan default expand
            withSelect={withSelect}
          />
        ))}
    </>
  )
}
