export default function HeaderUnit({ data, styling = true }: { data: any; styling?: boolean }) {
  return (
    <div className={styling ? "card bg-base-100 shadow-xl mb-4" : ""}>
      <div className={"card-body" + (styling ? "" : "px-0")}>
        {/* Title */}
        <h2 className="card-title">
          {data.project_name} - {data.cluster_name}
        </h2>

        {/* Unit Info */}
        <p className="mt-1">
          <span className="font-semibold">Unit:</span> {data.unit_number} ({data.unit_type})
        </p>

        {/* Status */}
        <div className="flex flex-wrap gap-2 mt-2">
          <div className="badge badge-primary">{data.status}</div>
          <div className="badge badge-accent">{data.construction_status}</div>
          {data.is_booked && (
            <div className="badge badge-secondary">Dibooking oleh {data.customer}</div>
          )}
        </div>

        {/* Harga */}
        <p className="mt-2 text-lg font-bold text-green-600">
          Harga Unit: Rp {Number(data.price).toLocaleString("id-ID")}
        </p>
        {data.payment && (
          <p className="text-sm text-gray-600">
            <span className="font-semibold">Total Payment:</span>{" "}
            Rp {Number(data.payment.total_amount).toLocaleString("id-ID")}
          </p>
        )}

        {/* Progres Konstruksi */}
        {data.construction_progress && (
          <div className="mt-2">
            <p className="text-sm font-semibold">Progress Konstruksi:</p>
            <progress
              className="progress progress-success w-full"
              value={parseInt(data.construction_progress.replace("%", ""))}
              max="100"
            ></progress>
            <p className="text-xs mt-1">{data.construction_progress}</p>
          </div>
        )}

        {/* Sub Kontraktor & Notes */}
        <div className="mt-3">
          <p><span className="font-semibold">Sub Kontraktor:</span> {data.sub_contractor || "-"}</p>
          <p><span className="font-semibold">Catatan:</span> {data.notes ?? "Tidak ada catatan"}</p>
        </div>
      </div>
    </div>
  )
}
