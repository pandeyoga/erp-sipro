'use client'

import { useEffect, useState } from 'react'
import axios from '@/lib/axios'

type SummaryItem = {
  status: string
  total: number
}

export default function ConstructionSummary() {
  const [summary, setSummary] = useState<SummaryItem[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const fetchSummary = async () => {
      setLoading(true)
      try {
        const res = await axios.get('/property/construction/summary')
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
    pondasi: { label: 'Pondasi', color: 'text-gray-500' },
    naik_bata: { label: 'Naik Bata', color: 'text-gray-500' },
    naik_atap: { label: 'Naik Atap', color: 'text-gray-500' },
    plester_aci:  { label: 'Plester Aci', color: 'text-gray-500' },
    keramik_cat: { label: 'Keramik & Cat', color: 'text-gray-500' },
    finishing: { label: 'Finishing', color: 'text-gray-500' },
    done: { label: 'Selesai', color: 'text-gray-500' }
  }

  if (loading) {
    return (
      <div className="grid grid-cols-1 sm:grid-cols-7 gap-4 my-6">
        {Array(6)
          .fill(0)
          .map((_, i) => (
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
    <div className="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-7 gap-4 my-6">
      {summary.map((item) => (
        <div
          key={item.status}
          className="bg-base-100 shadow rounded-xl p-4 flex flex-col items-start gap-2"
        >
          <div className="text-sm font-semibold text-base-content">
            {statusMap[item.status]?.label || item.status}
          </div>
          <div
            className={`text-3xl font-bold ${statusMap[item.status]?.color || 'text-base-content'}`}
          >
            {item.total}
          </div>
        </div>
      ))}
    </div>
  )
}
