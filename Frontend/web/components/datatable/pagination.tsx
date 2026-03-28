'use client'

interface Props {
  total: number
  page: number
  limit: number
  onPageChange: (val: number) => void
  maxVisible?: number // opsional: jumlah tombol maksimal yang terlihat (default 5)
}

export default function Pagination({
  total,
  page,
  limit,
  onPageChange,
  maxVisible = 10,
}: Props) {
  const totalPages = Math.ceil(total / limit)

  if (totalPages <= 1) return null

  // Hitung rentang halaman yang akan ditampilkan
  const half = Math.floor(maxVisible / 2)
  let start = Math.max(1, page - half)
  let end = Math.min(totalPages, start + maxVisible - 1)

  // Jika di akhir, geser ke kiri supaya tetap menampilkan maxVisible halaman
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  const pages = Array.from({ length: end - start + 1 }, (_, i) => start + i)

  return (
    <div className="join">
      {/* Tombol Previous */}
      <button
        className="join-item btn btn-sm"
        disabled={page === 1}
        onClick={() => onPageChange(page - 1)}
      >
        «
      </button>

      {/* Tombol Halaman */}
      {pages.map((p) => (
        <button
          key={p}
          className={`join-item btn btn-sm ${page === p ? 'btn-primary' : 'btn-ghost'}`}
          onClick={() => onPageChange(p)}
        >
          {p}
        </button>
      ))}

      {/* Tombol Next */}
      <button
        className="join-item btn btn-sm"
        disabled={page === totalPages}
        onClick={() => onPageChange(page + 1)}
      >
        »
      </button>
    </div>
  )
}
