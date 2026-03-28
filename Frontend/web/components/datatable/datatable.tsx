'use client'

import { useEffect, useImperativeHandle, useState } from 'react'
import TableHeader from './table-header'
import TableRow from './table-row'
import Pagination from './pagination'
import TableFilters from './table-filters'
import axios from '@/lib/axios'
import toast from "react-hot-toast";
import { Bold } from 'lucide-react'
import { useTranslations } from 'next-intl'

export type Column<T> = {
  key: keyof T
  label: string
  sortable?: boolean
  render?: (item: T) => React.ReactNode
}

type Props<T> = {
  endpoint: string
  columns: Column<T>[]
  onClickCreate ?: () => void
  actions?: (row: T) => React.ReactNode
  tree ?: boolean
  withAction ?: boolean
  withSelect ?: boolean
  ref?: any
  filter ?: {[key: string] : any}
  reportType ?: string,
}

export type DataTableRef = {
  reload: () => void
}

export default function DataTable<T extends { id: string | number }>({
  endpoint,
  columns,
  actions,
  onClickCreate,
  ref,
  tree = false,
  reportType = '',
  withAction = true,
  withSelect = true,
  filter 
}: Props<T>) {
  const t = useTranslations('datatable');
  const [data, setData] = useState<T[]>([])
  const [total, setTotal] = useState(0)
  const [page, setPage] = useState(1)
  const [limit, setLimit] = useState(10)
  const [search, setSearch] = useState('')
  const [sort, setSort] = useState<{ key: keyof T; direction: 'asc' | 'desc' } | null>(null)
  const [selected, setSelected] = useState<Set<string | number>>(new Set())
  const [loading, setLoading] = useState(false) // ✅ loading state

  const [refreshKey, setRefreshKey] = useState(0)
  const reload = () => setRefreshKey((k) => k + 1)
  

  useImperativeHandle(ref, () => ({ reload }))

  const fetchData = async () => {
    setLoading(true)
    const params = new URLSearchParams({
      search,
      page: page.toString(),
      per_page: limit.toString(),
      _:`${Date.now()}`,
      ...(sort ? { sortKey: sort.key as string, sortDir: sort.direction } : {}),
      ...filter
    })
    try{
      const result = await axios.get(`${endpoint}?${params.toString()}`)
      if(result.data.success){
        if(tree){
          if(result.data.data.detail){
            const treeData : any = calculateTotalsWithRow(transformToTree(result.data.data.detail))
            
            const final = [
              ...treeData,
              {
                name : reportType == 'cash-in' ? 'TOTAL CASH IN': 'LABA RUGI',
                bold: true,
                className: ' ml-4',
                value: reportType == 'cash-in' ? result.data.data.total_pendapatan + result.data.data.total_pendapatan_lainnya :  result.data.data.laba_rugi
              }
            ]
            
            setData(final)
          }else{
            const treeData : any = calculateTotalsWithRow(transformToTree(result.data.data))
            console.log('treeData',treeData);
            const sum = treeData.reduce((acc:any, curr:any) => acc + curr.total, 0)
            treeData.push({
              id: "TOTAL_NERACA",
              name: "TOTAL NERACA",
              value: sum,
              bold : true
            })
            setData(treeData)
          }
        }else{
          setData(result.data.data)
          setTotal(result.data.pagination.total)
        }
      }
    }catch(e){
      console.log(e)
      // toast.error("Gagal Memuat Data")
    }
    setLoading(false)
  }

  useEffect(() => {
    fetchData()
  }, [page, limit, search, sort, refreshKey, filter])

  useEffect(() => {
    setPage(1)
  }, [search,refreshKey,sort])

  const toggleSelect = (id: string | number) => {
    const updated = new Set(selected)
    updated.has(id) ? updated.delete(id) : updated.add(id)
    setSelected(updated)
  }

  const toggleSelectAll = () => {
    const allIds = data.map((item) => item.id)
    const allSelected = allIds.every((id) => selected.has(id))
    setSelected(allSelected ? new Set() : new Set(allIds))
  }

  return (
    <div className="space-y-4">
        
      <TableFilters onSearch={setSearch} onLimitChange={setLimit} onClickCreate={onClickCreate} hasSelected={selected.size > 0}/>
      <div className="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table className="table w-full">
          <thead>
            <TableHeader
              columns={columns}
              onSort={setSort}
              sort={sort}
              allSelected={Array.isArray(data) && data.every((item) => selected.has(item.id))}
              toggleSelectAll={toggleSelectAll}
              withAction = {withAction}
              withSelect = {withSelect}
            />
          </thead>
          <tbody>

            {loading ? 
              (
                <tr>
                  <td colSpan={columns.length + 2} className="text-center text-base-content/70">
                  <span className="loading loading-spinner loading-sm mr-2"></span>
                  <span className="text-sm text-gray-500">{t('loading')}</span>
                  </td>
                </tr>
              ) : 
              Array.isArray(data) && data.length > 0 ? data.map((row, index) => (
                <TableRow
                  key={index}
                  row={row}
                  columns={columns}
                  isSelected={selected.has(row.id)}
                  onToggleSelect={() => toggleSelect(row.id)}
                  actions={actions?.(row)}
                  withSelect={withSelect}
                />
              )) : 
              (
                <tr>
                  <td colSpan={columns.length + 2} className="text-center text-base-content/70">
                  {t('not_found')}
                  </td>
                </tr>
              )
            }
          </tbody>
        </table>
      </div>
      
      <Pagination total={total} page={page} limit={limit} onPageChange={setPage} />
    </div>
  )
}

type TreeNode = {
  id: string
  name: string
  value?: number | null
  children?: TreeNode[]
  total?: number
  bold ?: boolean
}

function transformToTree(obj: Record<string, any>): TreeNode[] {
  return Object.entries(obj).map(([key, value]) => {
    if (typeof value === "object" && value !== null) {
      return {
        id: key,
        name: key,
        children: transformToTree(value)
      }
    } else {
      return {
        id: key,
        name: key,
        value
      }
    }
  })
}

function calculateTotalsWithRow(nodes: TreeNode[]): TreeNode[] {
  return nodes.map((node) => {
    if (node.children && node.children.length > 0) {
      // Rekursif hitung anak-anak dulu
      const childrenWithTotals = calculateTotalsWithRow(node.children);

      // Hitung total: sum value + total dari children
      const total = childrenWithTotals.reduce((sum, child) => {
        if(child.value){
          if (/^\d/.test(child.name)) {
              if (typeof child.value === "string") {
                sum += parseFloat(child.value) || 0;
              } else if (typeof child.value === "number") {
                sum += child.value || 0;
              }
          }
        }else{
          sum += child.total || 0;
        }
        return sum;
      }, 0);

      // Tambahkan baris TOTAL di akhir children
      return {
        ...node,
        total,
        children: [
          ...childrenWithTotals,
          {
            id: `TOTAL ${node.id}`,
            name: `TOTAL ${node.name}`,
            value: total,
            bold : true
          },
        ],
      };
    } else {
      // Leaf node → pastikan value numeric
      return {
        ...node,
        value:
          typeof node.value === "string"
            ? parseFloat(node.value) || 0
            : node.value || 0,
      };
    }
  });
}