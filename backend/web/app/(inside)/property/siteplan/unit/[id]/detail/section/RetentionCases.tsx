export default function RetentionCases({ cases }: { cases: any[] }) {
    return (
      <div className="card bg-base-100 shadow-xl mb-4">
        <div className="card-body">
          <h3 className="card-title">Retention Cases</h3>
          {cases.length === 0 ? (
            <p className="text-gray-500">Tidak ada kasus</p>
          ) : (
            cases.map((c, idx) => (
              <div key={idx} className="collapse collapse-arrow border border-base-300 bg-base-200 rounded-box mb-2">
                <input type="checkbox" />
                <div className="collapse-title font-medium">
                  {c.description} <div className="badge badge-info ml-2">{c.status}</div>
                </div>
                <div className="collapse-content">
                  <p>Dibuka: {c.opened_at}</p>
                  <p>Estimasi Selesai: {c.estimated_resolved_at}</p>
                  <p>Sub Kontraktor: {c.sub_contractor_name}</p>
                  {c.case_pictures.length > 0 && (
                    <div className="flex gap-2 mt-2">
                      {c.case_pictures.map((pic: string, i: number) => (
                        <img key={i} src={pic} alt="bukti" className="w-20 h-20 object-cover rounded-md" />
                      ))}
                    </div>
                  )}
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    )
  }
  