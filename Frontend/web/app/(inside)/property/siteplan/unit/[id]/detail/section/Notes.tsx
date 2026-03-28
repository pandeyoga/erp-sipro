export default function NotesSection({ notes, subContractor }: { notes: string | null, subContractor: string }) {
    return (
      <div className="card bg-base-100 shadow-xl mb-4">
        <div className="card-body">
          <h3 className="card-title">Catatan & Sub Kontraktor</h3>
          <p><span className="font-semibold">Sub Kontraktor:</span> {subContractor}</p>
          <p><span className="font-semibold">Catatan:</span> {notes ?? "Tidak ada catatan"}</p>
        </div>
      </div>
    )
  }
  