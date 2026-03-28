'use client';

import { useForm } from 'react-hook-form';
import FileDropzone from '@/components/dropzone';
import { useRouter } from 'next/navigation';
import axios from '@/lib/axios';
import toast from 'react-hot-toast';
import { useEffect, useState } from 'react';

export default function ProjectForm({ mode, id }: { mode: 'create' | 'edit'; id?: string }) {
  const router = useRouter();
  const { register, handleSubmit, setValue, reset, watch } = useForm();
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (mode === 'edit' && id) {
      const fetchData = async () => {
        try {
          const res = await axios.get(`/property/projects/${id}`);
          const data = res.data.data;
          reset({
            name: data.name,
            location: data.location,
            developer: data.developer,
            status: data.status,
            start_date: data.start_date,
            area_total_sqm: data.area_total_sqm,
            site_plan_image: data.site_plan_image_url,
          });
        } catch (err) {
          toast.error('Gagal mengambil data project');
        }
      };
      fetchData();
    }
  }, [id, mode, reset]);

  const onSubmit = async (data: any) => {
    try {
      setLoading(true);
      const formData = new FormData();
      for (const key in data) {
        if (data[key]) formData.append(key, data[key]);
      }

      if (mode === 'edit' && id) {
        await axios.post(`/property/projects/${id}`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        toast.success('Project berhasil diperbarui');
      } else {
        await axios.post('/property/projects', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
        toast.success('Project berhasil ditambahkan');
      }

      router.push('/property/project');
    } catch (error) {
      toast.error('Gagal menyimpan data');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Project Information</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="form-control">
            <label className="label"><span className="label-text">Nama Project</span></label>
            <input {...register('name')} className="input input-bordered w-full" />
          </div>
          <div className="form-control">
            <label className="label"><span className="label-text">Lokasi</span></label>
            <input {...register('location')} className="input input-bordered w-full" />
          </div>
          <div className="form-control">
            <label className="label"><span className="label-text">Developer</span></label>
            <input {...register('developer')} className="input input-bordered w-full" />
          </div>
          <div className="form-control">
            <label className="label"><span className="label-text">Total Area (m²)</span></label>
            <input type="number" {...register('area_total_sqm')} className="input input-bordered w-full" />
          </div>
          <div className="form-control">
            <label className="label"><span className="label-text">Tanggal Mulai</span></label>
            <input type="date" {...register('start_date')} className="input input-bordered w-full" />
          </div>
          <div className="form-control">
            <label className="label"><span className="label-text">Status</span></label>
            <select {...register('status')} className="select select-bordered w-full">
              <option value="">Pilih Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-md font-semibold">Siteplan</legend>
        <div className="form-control">
          <FileDropzone
            name="site_plan_image"
            label="Peta Siteplan"
            setValue={setValue}
            initialPreviewSrc={
              mode === 'edit' && typeof watch('site_plan_image') === 'string'
                ? watch('site_plan_image')
                : ''
            }
          />
        </div>
      </fieldset>

      {/* Notes */}
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-md font-semibold">Notes</legend>
        <textarea {...register("notes")} placeholder="Input text" className="textarea textarea-bordered w-full" />
      </fieldset>

      <div className="flex justify-between">
        <button type="button" onClick={() => router.back()} className="btn">
          Back
        </button>
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading && <span className="loading loading-spinner"></span>}
          {mode === 'edit' ? 'Update' : 'Add'}
        </button>
      </div>
    </form>
  );
}
