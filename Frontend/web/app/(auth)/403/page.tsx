import { ShieldAlert } from "lucide-react" // kalau pakai lucide-react

export default function ForbiddenPage() {
  return (
    
    <div className="flex h-screen bg-primary items-center justify-center bg-base-200 px-4">
      <div className="card w-full max-w-lg bg-primary text-error-content border-rounded-lg">
        <div className="card-body items-center text-center">
          <ShieldAlert className="w-16 h-16" />
          <h1 className="text-[200px] font-extrabold">403</h1>
          <p className="mt-4 text-lg">
            🚫 Forbidden – Kamu tidak punya akses ke halaman ini.
          </p>
          <div className="card-actions justify-center mt-6">
            <a href="/login" className="btn btn-secondary">
              Kembali ke Login
            </a>
          </div>
        </div>
      </div>
    </div>
  )
}
