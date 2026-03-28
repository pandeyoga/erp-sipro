"use client";

import { useEffect, useState } from "react";
import axios from "@/lib/axios";
import { useForm } from "react-hook-form";
import ProtectedImage from "../protected-image";
import { useSitemapContext } from "@/context/useSitemapContext";
import FileDropzone from "../dropzone";
import toast from "react-hot-toast";

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


export function UploadSiteplanDialog() {
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const { project, setProject } = useSitemapContext();

  const { register, handleSubmit, watch, reset, setValue } = useForm();

  const mode = project?.site_plan_image ? 'edit' : 'create';

  
  

  const onSubmit = (data : any) => {
    if(project){

      setLoading(true);
      const formData = new FormData();
      for (const key in data) {
        if (data[key]) formData.append(key, data[key]);
      }

      axios.post(`/property/projects/${project.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      }).then(()=>{
        axios.get(`/property/projects/${project.id}`).then((res)=>{
          const prj = res.data.data;
          setProject({
            ...project,
            site_plan_image : prj.site_plan_image
          })
        })
      }).finally(()=>{
        setOpen(false)
      });
    }
  };

  useEffect(()=>{
    if(project){
      reset(project)
    }
  },[project])

  return (
    <>
      <button className="btn btn-info" onClick={() => setOpen(true)}>
        {mode == 'create' ? 'Upload Gambar Siteplan' : 'Update Gambar Siteplan'}
      </button>

      {(open && project) && (
        <dialog className="modal modal-open">
          <div className="modal-box space-y-4 max-w-3xl">
            <h3 className="font-bold text-lg">{mode == 'create' ? 'Upload Gambar Siteplan' : 'Update Gambar Siteplan'}</h3>

            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              <fieldset className="border border-base-300 rounded-xl p-6">
                <legend className="text-md font-semibold">Siteplan</legend>
                <div className="form-control">
                  <FileDropzone
                    name="site_plan_image"
                    label="Peta Siteplan"
                    setValue={setValue}
                    initialPreviewSrc={
                      ( mode == 'edit' && typeof watch('site_plan_image') === 'string')
                        ? watch('site_plan_image')
                        : ''
                    }
                  />
                </div>
              </fieldset>

              <div className="modal-action">
                <button type="submit" className="btn btn-primary" >
                  Simpan
                </button>
                <button
                  type="button"
                  className="btn"
                  onClick={() => {
                    setOpen(false);
                    reset();
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
