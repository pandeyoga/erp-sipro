'use client'

import { useMemo, useState } from 'react'
import { PendingTaskItem } from '../types'
import Pagination from '@/components/datatable/pagination'

interface PendingTasksTableProps {
  data: PendingTaskItem[]
}

const PendingTasksTable: React.FC<PendingTasksTableProps> = ({ data = [] }) => {
  const [filterText, setFilterText] = useState('')
  const [sortField, setSortField] = useState<keyof PendingTaskItem>('due_date')
  const [sortOrder, setSortOrder] = useState<'asc' | 'desc'>('asc')
  const [page, setPage] = useState(1)
  const limit = 10 // jumlah item per halaman

  const dataArray = useMemo(() => Object.values(data || {}), [data])

  // ======== FILTER DATA ========
  const filteredData = useMemo(() => {
    if (!Array.isArray(dataArray)) return [] 

    return dataArray.filter((task) =>
      task.lead_name?.toLowerCase().includes(filterText.toLowerCase()) ||
      task.task?.toLowerCase().includes(filterText.toLowerCase()) ||
      task.status?.toLowerCase().includes(filterText.toLowerCase())
    )
  }, [dataArray, filterText])

  // ======== SORT DATA ========
  const sortedData = useMemo(() => {
    const sorted = [...filteredData].sort((a, b) => {
      const valA = a[sortField] ?? ''
      const valB = b[sortField] ?? ''
      if (typeof valA === 'number' && typeof valB === 'number') {
        return sortOrder === 'asc' ? valA - valB : valB - valA
      }
      return sortOrder === 'asc'
        ? String(valA).localeCompare(String(valB))
        : String(valB).localeCompare(String(valA))
    })
    return sorted
  }, [filteredData, sortField, sortOrder])

  // ======== PAGINATION ========
  const totalPages = Math.ceil(sortedData.length / limit)
  const paginatedData = useMemo(() => {
    const start = (page - 1) * limit
    return sortedData.slice(start, start + limit)
  }, [sortedData, page])

  // ======== HANDLER ========
  const handleSort = (field: keyof PendingTaskItem) => {
    if (sortField === field) {
      setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc')
    } else {
      setSortField(field)
      setSortOrder('asc')
    }
  }

  if (!data || data.length === 0) {
    return (
      <div className="text-center text-gray-500 p-4">
        No pending tasks found. Good job!
      </div>
    )
  }

  return (
    <div className="space-y-4">
      {/* ===== FILTER ===== */}
      <div className="flex items-center justify-between">
        <input
          type="text"
          placeholder="Filter by name, task, or status..."
          className="input input-bordered input-sm w-full max-w-xs"
          value={filterText}
          onChange={(e) => {
            setFilterText(e.target.value)
            setPage(1)
          }}
        />
        <div className="text-sm text-gray-500">
          Showing {paginatedData.length} of {filteredData.length} tasks
        </div>
      </div>

      {/* ===== TABLE ===== */}
      <div className="overflow-x-auto">
        <table className="table w-full table-zebra">
          <thead>
            <tr>
              <th onClick={() => handleSort('lead_name')} className="cursor-pointer">
                Lead Name {sortField === 'lead_name' && (sortOrder === 'asc' ? '▲' : '▼')}
              </th>
              <th onClick={() => handleSort('task')} className="cursor-pointer">
                Task {sortField === 'task' && (sortOrder === 'asc' ? '▲' : '▼')}
              </th>
              <th onClick={() => handleSort('status')} className="cursor-pointer">
                Status {sortField === 'status' && (sortOrder === 'asc' ? '▲' : '▼')}
              </th>
              <th onClick={() => handleSort('due_date')} className="cursor-pointer">
                Due Date {sortField === 'due_date' && (sortOrder === 'asc' ? '▲' : '▼')}
              </th>
              <th>Remaining Days</th>
              <th>Late</th>
            </tr>
          </thead>
          <tbody>
            {paginatedData.map((task, index) => (
              <tr key={task.lead_id || index}>
                <td>{task.lead_name}</td>
                <td>{task.task}</td>
                <td>
                  <span className="badge badge-sm badge-info">{task.status}</span>
                </td>
                <td>{task.due_date || '-'}</td>
                <td
                  className={`${
                    task.is_late ? 'text-error' : 'text-success'
                  }`}
                >
                  {task.remaining_days !== null
                    ? `${task.is_late ? '-' : ''}${task.remaining_days} days`
                    : '-'}
                </td>
                <td>
                  <span
                    className={`badge ${
                      task.is_late ? 'badge-error' : 'badge-success'
                    }`}
                  >
                    {task.is_late ? 'YES' : 'NO'}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Pagination page={page} onPageChange={setPage} limit={limit} total={sortedData.length} />
    </div>
  )
}

export default PendingTasksTable
