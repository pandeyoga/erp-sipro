'use client'

import { useEffect, useState } from 'react'
import axios from '@/lib/axios'

type SummaryItem = {
  status: string
  total: number
}

export default function DocumentSummary() {
  const [summary, setSummary] = useState<SummaryItem[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const fetchSummary = async () => {
      setLoading(true)
      try {
        const res = await axios.get('/crm/lead-document/summary')
        setSummary(res.data.data)
      } catch (error) {
        console.error('Failed to fetch summary', error)
      } finally {
        setLoading(false)
      }
    }

    fetchSummary()
  }, [])

  const statusMap: Record<string, { label: string; color: string }> = {
    input: { label: 'Input', color: 'text-gray-500' },
    verification: { label: 'Verification', color: 'text-gray-500' },
    completed: { label: 'Completed', color: 'text-gray-500' }
  }

  if (loading) {
    return (
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 my-6">
        {Array(3).fill(0).map((_, i) => (
          <div key={i} className="bg-base-100 rounded-xl p-6 flex items-start gap-4">
            <div className="text-3xl font-bold">
              <span className="loading loading-spinner"></span>
            </div>
          </div>
        ))}
      </div>
    )
  }

  return (
    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 my-6">
      {summary.map(item => (
        <div key={item.status} className="bg-base-100 rounded-xl p-6 flex items-start gap-4">
          <div>
            <div className="text-sm font-medium text-base-content">
              {statusMap[item.status]?.label || item.status}
            </div>
            <div className={"text-3xl font-bold " + (statusMap[item.status]?.color || '')}>
              {item.total}
            </div>
          </div>
        </div>
      ))}
    </div>
  )
}
