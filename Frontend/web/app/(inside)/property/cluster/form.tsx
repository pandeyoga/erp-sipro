'use client';

import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { useRouter } from 'next/navigation';
import toast from 'react-hot-toast';
import axios from '@/lib/axios';

interface Project {
  id: string;
  name: string;
}

export default function ClusterForm({ mode, id }: { mode: 'create' | 'edit'; id?: string }) {
  const router = useRouter();
  const { register, handleSubmit, setValue, reset } = useForm();
  const [loading, setLoading] = useState(false);
  const [projects, setProjects] = useState<Project[]>([]);
  const [loadingProjects, setLoadingProjects] = useState(true);

  // Fetch data if edit mode
  useEffect(() => {
    if (mode === 'edit' && id) {
      const fetchData = async () => {
        try {
          const res = await axios.get(`/property/cluster/${id}`);
          const data = res.data.data;
          reset({
            name: data.name,
            project_id: data.project_id,
            block_code: data.block_code,
          });
        } catch (err) {
          toast.error('Gagal memuat data cluster');
        }
      };
      fetchData();
    }
  }, [id, mode, reset]);

  // Fetch projects for select
  useEffect(() => {
    const fetchProjects = async () => {
      try {
        const res = await axios.get('/property/projects');
        setProjects(res.data.data || []);
      } catch (err) {
        toast.error('Gagal memuat data project');
      } finally {
        setLoadingProjects(false);
      }
    };
    fetchProjects();
  }, []);

  const onSubmit = async (data: any) => {
    setLoading(true);
    try {
      if (mode === 'edit' && id) {
        await axios.put(`/property/cluster/${id}`, data);
        toast.success('Cluster berhasil diperbarui');
      } else {
        await axios.post('/property/cluster', data);
        toast.success('Cluster berhasil ditambahkan');
      }
      router.push('/property/cluster');
    } catch (error) {
      toast.error('Gagal menyimpan cluster');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">

      {/* Project Select */}
      <div className="form-control">
        <label className="label">
          <span className="label-text">Project</span>
        </label>
        {loadingProjects ? (
          <div className="skeleton h-12 w-full rounded"></div>
        ) : (
          <select {...register('project', { required: true })} className="select select-bordered w-full">
            <option value="">Pilih Project</option>
            {projects.map((project) => (
              <option key={project.id} value={project.id}>
                {project.name}
              </option>
            ))}
          </select>
        )}
      </div>

      {/* Name */}
      <div className="form-control">
        <label className="label">
          <span className="label-text">Nama Cluster</span>
        </label>
        <input {...register('name', { required: true })} className="input input-bordered w-full" placeholder="Contoh: Cluster Mawar" />
      </div>

      {/* Block Code */}
      <div className="form-control">
        <label className="label">
          <span className="label-text">Kode Blok</span>
        </label>
        <input {...register('block_code', { required: true })} className="input input-bordered w-full" placeholder="Contoh: A, B1, etc" />
      </div>

      {/* Notes */}
      <div className="form-control">
        <label className="label">
          <span className="label-text">Notes</span>
        </label>
        <textarea {...register("notes")} placeholder="Input text" className="textarea textarea-bordered w-full" />
      </div>

      {/* Action Buttons */}
      <div className="flex justify-between mt-6">
        <button type="button" onClick={() => router.back()} className="btn">
          Kembali
        </button>
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading && <span className="loading loading-spinner"></span>}
          {mode === 'edit' ? 'Update' : 'Tambah'}
        </button>
      </div>
    </form>
  );
}
