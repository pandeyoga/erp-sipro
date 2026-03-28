import ProtectedImage from "@/components/protected-image";

export default function ConstructionProgress({ data, progress, status }: { data: any[], progress: string, status: string }) {
  return (
    <div className="card bg-base-100 shadow-xl mb-4">
      <div className="card-body">
        <h3 className="card-title">Progress Konstruksi</h3>
        <p>Status: {progress ?? '0%'} (<span className="font-bold">{status ?? "-"}</span>)</p>
        
        <progress
          className="progress progress-success w-full my-2"
          value={progress ? parseInt(progress.replace("%", "")) : 0}
          max="100"
        ></progress>

        <div className="grid grid-cols-2 gap-4 mt-4">
          {data.map((item, idx) => (
            <div key={idx} className="text-center">
              <p className="text-sm font-medium">{item.construction_phase}</p>
              <ProtectedImage imageUrl={process.env.NEXT_PUBLIC_BE_URL + item.documentation} label={item.construction_phase}/>
              {/* <img
                src={item.documentation}
                alt={item.construction_phase}
                className="w-full h-28 object-cover rounded-md mt-1"
              /> */}
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}
