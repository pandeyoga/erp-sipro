import axios from "@/lib/axios";
import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";

export default function FilterLead({
  handleFilter,
}: {
  handleFilter: (status: string, project_id: string, cluster_id: string) => void;
}) {
  const statusOptions = [
    {
      label: "Pondasi",
      value: "pondasi",
    },
    {
      label: "Naik Bata",
      value: "naik_bata",
    },
    {
      label: "Naik Atap",
      value: "naik_atap",
    },
    {
      label: "Plester Aci",
      value: "plester_aci",
    },
    {
      label: "Keramik Cat",
      value: "keramik_cat",
    },
    {
      label: "Finishing",
      value: "finishing",
    },
  ];

  const {
    register,
    watch,
    reset,
    setValue,
    formState: { errors },
  } = useForm();

  const [projects, setProjects] = useState([]);
  const [clusters, setClusters] = useState([]);
  // GET: project list
  useEffect(() => {
    axios
      .get("/property/construction/project-lists")
      .then((res) => setProjects(res.data.data))
      .catch(() => console.log("Gagal mengambil project"));
  }, []);

  
  const project_id = watch("project_id");

  // GET: clusters list
  useEffect(() => {
    if (!project_id) return;
    setValue("cluster_id", null)
    axios
      .get(`/property/construction/cluster-lists/${project_id}`)
      .then((res) => setClusters(res.data.data))
      .catch(() => console.log("Gagal mengambil cluster"));
  }, [project_id]);

  const status = watch("status");
  const cluster_id = watch("cluster_id");

  useEffect(() => {
    handleFilter(status, project_id, cluster_id);
  }, [status, project_id, cluster_id]);

  return (
    <form className="flex items-end gap-4 w-1/2 mb-6">
      <div className="form-control w-1/4">
        <label className="label font-medium">Status</label>
        <select
          {...register("status")}
          className="select select-bordered w-full"
          required
        >
          <option value="">-- All --</option>
          {statusOptions &&
            statusOptions.map((status) => (
              <option key={status.value} value={status.value}>
                {status.label}
              </option>
            ))}
        </select>
      </div>
      <div className="form-control w-1/4">
        <label className="label font-medium">Project</label>
        <select
          {...register("project_id")}
          className="select select-bordered w-full"
          required
        >
          <option value="">-- All --</option>
          {projects &&
            projects.map((val : any) => (
              <option key={val.id} value={val.id}>
                {val.name}
              </option>
            ))}
        </select>
      </div>
      <div className="form-control w-1/4">
        <label className="label font-medium">Cluster</label>
        <select
          {...register("cluster_id")}
          className="select select-bordered w-full"
          required
        >
          <option value="">-- All --</option>
          {clusters &&
            clusters.map((val : any) => (
              <option key={val.id} value={val.id}>
                {val.name}
              </option>
            ))}
        </select>
      </div>
      <button type="button" onClick={()=>{ reset() }}className="btn btn-secondary">
        Reset
      </button>
    </form>
  );
}
