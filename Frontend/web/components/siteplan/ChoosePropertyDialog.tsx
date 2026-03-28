"use client";

import { useEffect, useState } from "react";
import axios from "@/lib/axios";
import { useForm } from "react-hook-form";
import ProtectedImage from "../protected-image";
import { useSitemapContext } from "@/context/useSitemapContext";

type ProjectListItem = {
  id: string;
  name: string;
  location: string;
};

type ProjectDetail = {
  id: string;
  name: string;
  location: string;
  developer: string;
  area_total_sqm: string;
  start_date: string;
  status: string;
  site_plan_image: string | null;
};

export function ChoosePropertyDialog() {
  const [open, setOpen] = useState(false);
  const { project, setProject } = useSitemapContext();
  const [projects, setProjects] = useState<ProjectListItem[]>([]);
  const [projectDetail, setProjectDetail] = useState<ProjectDetail | null>(null);
  const [loadingList, setLoadingList] = useState(true);
  const [loadingDetail, setLoadingDetail] = useState(false);

  const { register, handleSubmit, watch, reset } = useForm<{ projectId: string }>({
    defaultValues: {
      projectId: "",
    },
  });

  const selectedProjectId = watch("projectId");

  // Fetch list project saat dialog dibuka
  useEffect(() => {
    if (!open) return;

    const fetchProjects = async () => {
      try {
        const res = await axios.get("/property/projects", {
          params: { page: 1, per_page: 100 }
        });
        const projectList = res.data.data.map((p: any) => ({
          id: p.id,
          name: p.name,
          location: p.location,
        }));
        setProjects(projectList);
      } catch (err) {
        console.error("Gagal memuat project", err);
      } finally {
        setLoadingList(false);
      }
    };

    fetchProjects();
  }, [open]);

  // Fetch detail project setelah project dipilih
  useEffect(() => {
    if (!selectedProjectId) {
      setProjectDetail(null);
      return;
    }

    const fetchDetail = async () => {
      setLoadingDetail(true);
      try {
        const res = await axios.get(`/property/projects/${selectedProjectId}`);
        setProjectDetail(res.data.data);
      } catch (err) {
        console.error("Gagal mengambil detail project", err);
      } finally {
        setLoadingDetail(false);
      }
    };

    fetchDetail();
  }, [selectedProjectId]);

  const onSubmit = () => {
    if (projectDetail) {
      console.log("Project terpilih:", projectDetail);
      // TODO: simpan ke context atau lanjut ke langkah selanjutnya
      setProject(projectDetail)
      setOpen(false);
      reset();
    }
  };

  return (
    <>
      <button className="btn btn-primary" onClick={() => setOpen(true)}>
        {project ? 'Ganti Project' : 'Pilih Project'}
      </button>

      {open && (
        <dialog className="modal modal-open">
          <div className="modal-box space-y-4 max-w-3xl">
            <h3 className="font-bold text-lg">{project ? 'Ganti Project' : 'Pilih Project'}</h3>

            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              {loadingList ? (
                <div className="skeleton h-12 w-full rounded"></div>
              ) : (
                <select
                  className="select select-bordered w-full"
                  {...register("projectId", { required: true })}
                >
                  <option value="">-- Pilih Project --</option>
                  {projects.map((p) => (
                    <option key={p.id} value={p.id}>
                      {p.name} - {p.location}
                    </option>
                  ))}
                </select>
              )}

              {/* Preview detail dan siteplan */}
              {loadingDetail && <p>Memuat detail...</p>}
              {projectDetail && (
                <div className="grid grid-cols-1 md:grid-cols-1 gap-4">
                  <div className="space-y-1">
                    <p><strong>Nama:</strong> {projectDetail.name}</p>
                    <p><strong>Lokasi:</strong> {projectDetail.location}</p>
                    <p><strong>Developer:</strong> {projectDetail.developer}</p>
                    <p><strong>Luas Area:</strong> {projectDetail.area_total_sqm} m²</p>
                    <p><strong>Status:</strong> {projectDetail.status}</p>
                  </div>
                  {projectDetail.site_plan_image && (
                    <ProtectedImage imageUrl={projectDetail.site_plan_image}/>
                    // <img
                    //   src={projectDetail.site_plan_image}
                    //   alt="Siteplan"
                    //   className="rounded border max-h-64 object-contain"
                    // />
                  )}
                </div>
              )}

              <div className="modal-action">
                <button type="submit" className="btn btn-primary" disabled={!projectDetail}>
                  Gunakan Project Ini
                </button>
                <button
                  type="button"
                  className="btn"
                  onClick={() => {
                    setOpen(false);
                    reset();
                    setProjectDetail(null);
                  }}
                >
                  Batal
                </button>
              </div>
            </form>
          </div>
        </dialog>
      )}
    </>
  );
}
