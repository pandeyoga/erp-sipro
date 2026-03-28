import { UnitFromAPI } from "@/components/siteplan/PropertyUnitModal";
import axios from "@/lib/axios";
import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import toast from "react-hot-toast";

export default function FilterUnitProperty({ project_id,handleFilter } : { project_id : string; handleFilter : (cluster : string, unit_type : string) => void }) {
  const [units, setUnits] = useState<UnitFromAPI[]>([]);
  const [clusters, setClusters] = useState<{ id: string; name: string }[]>([]);
  const [loading, setLoading] = useState(true);

  const {
      register,
      watch,
      reset,
      formState: { errors },
    } = useForm();

  useEffect(() => {
    const fetchUnits = async () => {
      try {
        const res = await axios.get("/property/unit-property/unit-types-list");
        setUnits(res.data.data || []);
      } catch (err) {
        console.error("Gagal memuat unit", err);
      } finally {
        setLoading(false);
      }
    };
    const fetchCluster = async () => {
      axios
        .get(`/property/construction/cluster-lists/${project_id}`)
        .then((res) => setClusters(res.data.data))
        .catch(() => toast.error('Gagal mengambil cluster'))
    }
    fetchUnits();
    fetchCluster();
  }, []);

  const cluster = watch('cluster_id')
  const unit_type = watch('unit_type_id')
  

  useEffect(()=>{
    handleFilter(cluster,unit_type)
  },[cluster,unit_type])

  return (
    <form className="flex items-end gap-4 w-1/2 mb-6">
      {/* Pilih Label dari Cluster */}
      <div className="form-control w-full">
        <label className="label font-medium">Cluster</label>
          {loading ? (
            <div className="skeleton h-12 w-full rounded"></div>
          ) : (
            <select
              className="select select-bordered w-full"
              {...register("cluster_id", {
                required: "Cluster wajib dipilih",
              })}
            >
              <option value="">Pilih Cluster</option>
              {clusters.map((cluster) => (
                <option key={cluster.id} value={cluster.id}>
                  {cluster.name}
                </option>
              ))}
            </select>
          )}
        </div>
        {/* Pilih Label dari Type Unit */}
        <div className="form-control w-full">
          <label className="label font-medium">Tipe Unit</label>
          {loading ? (
            <div className="skeleton h-12 w-full rounded"></div>
          ) : (
            <select
              className="select select-bordered w-full"
              {...register("unit_type_id", {
                required: "Tipe unit wajib dipilih",
              })}
            >
              <option value="">Pilih Type Unit</option>
              {units.map((unit) => (
                <option key={unit.id} value={unit.id}>
                  {unit.type}
                </option>
              ))}
            </select>
          )}
        </div>
      <button type="button" onClick={()=>{ reset() }}className="btn btn-secondary">
        Reset
      </button>
    </form>
  );
};

