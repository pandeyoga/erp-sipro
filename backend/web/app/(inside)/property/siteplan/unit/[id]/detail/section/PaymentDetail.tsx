export default function PaymentDetail({ payment }: { payment: any }) {
    return (
      <div className="card bg-base-100 shadow-xl mb-4">
        <div className="card-body">
          <h3 className="card-title">Payment Detail</h3>
  
          <div className="stats shadow mb-4">
            <div className="stat">
              <div className="stat-title">Total</div>
              <div className="stat-value">Rp {Number(payment.total_amount).toLocaleString("id-ID")}</div>
            </div>
            <div className="stat">
              <div className="stat-title">Dibayar</div>
              <div className="stat-value">Rp {Number(payment.paid_amount).toLocaleString("id-ID")}</div>
            </div>
            <div className="stat">
              <div className="stat-title">Sisa</div>
              <div className="stat-value">Rp {Number(payment.remaining_amount).toLocaleString("id-ID")}</div>
            </div>
          </div>
  
          <div className="overflow-x-auto">
            <table className="table table-zebra">
              <thead>
                <tr>
                  <th>Deskripsi</th>
                  <th>Total</th>
                  <th>Dibayar</th>
                  <th>Sisa</th>
                </tr>
              </thead>
              <tbody>
                {payment.details.map((d: any, idx: number) => (
                  <tr key={idx}>
                    <td>{d.description}</td>
                    <td className="text-right">{d.total_amount}</td>
                    <td className="text-right">{d.paid_amount}</td>
                    <td className="text-right">{d.remaining_amount}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    )
  }
  