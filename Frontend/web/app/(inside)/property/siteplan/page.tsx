"use client";
import {
  SitemapProvider,
  useSitemapContext,
} from "@/context/useSitemapContext";
import { SitemapCanvas } from "@/components/siteplan/SitemapCanvas";
import { AddUnitDialog } from "@/components/siteplan/AddUnitDIalog";
import { ChoosePropertyDialog } from "@/components/siteplan/ChoosePropertyDialog";
import { useRouter, useSearchParams } from "next/navigation";
import { useEffect, useRef, useState } from "react";
import axios from "@/lib/axios";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import DataTable, { Column } from "@/components/datatable/datatable";
import toast from "react-hot-toast";
import { PropertyUnitModal } from "@/components/siteplan/PropertyUnitModal";
import React from "react";
import { UploadSiteplanDialog } from "@/components/siteplan/UploadSiteplanDialog";
import { CheckCircle, Eye, Pencil, Trash2 } from "lucide-react";
import { QCUnitModal } from "@/components/siteplan/QCUnitModal";
import FilterUnitProperty from "./filter";
import { isAllowed } from "@/lib/utils";

export interface PropertyUnit {
  id: string;
  project_id: string;
  project_name: string;
  cluster_id: string;
  cluster_name: string;
  unit_type_id: string;
  unit_type: string;
  unit_number: string;
  price: number | null;
  status: "belum_dibangun" | "dibangun" | "terjual" | "siap_huni" | string; // bisa disesuaikan
  construction_status: string | null;
  notes: string | null;
}

export default function SitemapSetup() {
  
  return (
    <div className="space-y-4 p-4">
      <h1 className="text-2xl font-semibold">Siteplan Editor</h1>
      <SitemapHeader />
      <SitemapBody/>
      
    </div>
  );
}

function SitemapBody(){
  const { project } = useSitemapContext();

  if(!project){
    return null
  }
  return (
    <React.Fragment>
      <SitemapCanvas />
      <PropertyUnitSection project_id={project.id}/>
    </React.Fragment>
  )
}

function PropertyUnitSection({ project_id } : { project_id : string }) {
  const router = useRouter();
  const [selected, setSelected] = useState<PropertyUnit | null>(null);

  const columns: Column<PropertyUnit>[] = [
    { key: "unit_number", label: "Unit Number", sortable: true },
    { key: "unit_type", label: "Type", sortable: true },
    { key: "project_name", label: "Project", sortable: true },
    { key: "cluster_name", label: "Cluster", sortable: true },
    { key: "status", label: "Status", sortable: true },
  ];

  const openDeleteModal = (row: PropertyUnit) => {
    setSelected(row);
    (document.getElementById("delete_modal") as HTMLDialogElement)?.showModal();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const [modalMode, setModalMode] = useState<string>("create");
  const [unit, setUnit] = useState<PropertyUnit>();

  const openFormModal = (unit: PropertyUnit | null = null) => {
    const modal = document.getElementById("modify_unit") as HTMLDialogElement;
    if (unit?.id) {
      setModalMode("edit");
    } else {
      setModalMode("create");
    }
    modal.showModal();
  };

  const openQCModal = (unit: PropertyUnit | null = null) => {
    const modal = document.getElementById("qc_unit") as HTMLDialogElement;
    modal.showModal();
  };

  const handleDelete = async () => {
    if (!selected) return;
    try {
      await axios.delete(`/property/unit-property/${selected.id}`);
      toast.success("Data berhasil dihapus");
    } catch {
      toast.error("Gagal menghapus data");
    } finally {
      closeDeleteModal();
      unitTableRef?.current?.reload()
    }
  };

  const unitTableRef = useRef<{ reload: () => void }>(null);

  const [filter, setFilter] = useState({project_id : project_id});

  const handleFilter = async (cluster : string, unit_type : string) => {
    const filters = { ...filter,cluster,unit_type }

    setFilter(
      Object.entries(filters).reduce((acc, [key, value]) => {
        if (value) (acc as any)[key] = value
        return acc
      }, {} as typeof filters)
    )
  };

  return (
    <div>
      <FilterUnitProperty project_id={project_id} handleFilter={handleFilter}/>
      {/* Data Table */}
      <DataTable
        ref={unitTableRef}
        filter={filter}
        endpoint="/property/unit-property"
        onClickCreate={isAllowed("property.create_property") ? openFormModal : undefined}
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            <button
            className="btn btn-sm btn-info"
            onClick={() =>
              window.open(`/property/siteplan/unit/${row.id}/detail`, "_blank")
            }
          >
            <Eye size={16} className="mr-1" />
          </button>
          <button
            className="btn btn-sm btn-success"
            onClick={async () => {
              const response = await axios.get(`/property/unit-property/${row.id}`);
              setUnit(response.data.data)
              openQCModal(response.data.data)
            }}
          >
            <CheckCircle size={16} className="mr-1" />
          </button>
          {isAllowed("property.update_property") && (<button
            className="btn btn-sm btn-warning"
            onClick={async () => {
              const response = await axios.get(`/property/unit-property/${row.id}`);
              setUnit(response.data.data)
              openFormModal(response.data.data)
            }}
          >
            <Pencil size={16} className="mr-1" />
          </button>)}
          {isAllowed("property.delete_property") &&(<button
            className="btn btn-sm btn-error text-white"
            onClick={() => openDeleteModal(row)}
          >
            <Trash2 size={16} className="mr-1" />
          </button>)}
        </div>
        )}
      />

      {/* Modal Delete */}
      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        message={`Hapus unit property "${selected?.unit_number}"?`}
      />
      <PropertyUnitModal unit={unit} modalMode={modalMode} unitTableRef={unitTableRef}/>
      <QCUnitModal unit={unit} modalMode={modalMode} unitTableRef={unitTableRef}/>
    </div>
  );
}

function SitemapHeader() {
  const { project, setProject, editMode, setEditMode } = useSitemapContext();

  const searchParams = useSearchParams();

  const [default_project_id,setDefaultProjectId] = useState()

  

  // GET: project list
  useEffect(() => {
    axios
      .get('/property/construction/project-lists')
      .then((res) => {
        let list = res.data.data;
        if(list){
          setDefaultProjectId(list[0]?.id)
        }
      })
      .catch(() => toast.error('Gagal mengambil project'))
  }, [])

  useEffect(() => {
    let projectId = searchParams.get("project-id");
    if (!projectId) {
      if(!default_project_id) return;
      projectId = default_project_id
    };

    const fetchProject = async () => {
      try {
        const res = await axios.get(`/property/projects/${projectId}`);
        setProject(res.data.data); // asumsi respons: { success: true, data: {...} }
      } catch (error) {
        console.error("Error loading project:", error);
      }
    };

    fetchProject();
  }, [searchParams, setProject,default_project_id]);

  return (
    <div className="space-y-4">
      {/* Informasi Project */}
      {project ? (
        <div className="p-4 border border-gray-300 bg-gray-50 rounded-xl shadow-sm space-y-1">
          <h2 className="text-lg font-semibold">{project.name}</h2>
          <p className="text-sm text-gray-700">
            <strong>Lokasi:</strong> {project.location}
          </p>
          <p className="text-sm text-gray-700">
            <strong>Developer:</strong> {project.developer}
          </p>
          <p className="text-sm text-gray-700">
            <strong>Luas Area:</strong> {project.area_total_sqm} m²
          </p>
          <p className="text-sm text-gray-700">
            <strong>Status:</strong> {project.status}
          </p>
        </div>
      ) : (
        <p className="italic text-gray-500">Belum ada project yang dipilih.</p>
      )}

      {/* Tombol Aksi */}
      {isAllowed('property.modify_site_plan') && (
        <div className="form-control">
          <label className="label cursor-pointer">
            <span className="label-text mr-2">Edit Mode</span>
            <input
              type="checkbox"
              className="toggle toggle-success"
              checked={editMode}
              onChange={(e) => setEditMode(e.target.checked)}
            />
          </label>
        </div>
      )}

      <div className="flex gap-4">
        {
          editMode && (
            <React.Fragment>
              <ChoosePropertyDialog />
              {
                project ? (
                  <React.Fragment>
                    <AddUnitDialog />
                    <UploadSiteplanDialog />
                  </React.Fragment>
                ) : null
              }
            </React.Fragment>
          )
        }
      </div>
    </div>
  );
}
