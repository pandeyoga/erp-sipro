import axiosInstance from "@/lib/axios";
import axios from "@/lib/axios";
import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";

export default function FilterLead({
  handleFilter,
}: {
  handleFilter: (status: string, category_id: string, sub_category_id: string) => void;
}) {
  const [statusOptions, setStatusOptions] = useState<string[]>([]);

  const {
    register,
    watch,
    reset,
    formState: { errors },
  } = useForm();

  const [categories, setCategories] = useState<{ id: string; name: string }[]>(
    []
  );
  const category_id = watch("category_id");
  useEffect(() => {
    axiosInstance.get("/finance/cash-in/categories").then((response) => {
      setCategories(response.data.data);
    });
  }, []);

  const [subCategories, setSubCategories] = useState<
    { id: string; name: string }[]
  >([]);
  const sub_category_id = watch("sub_category_id");
  const status = watch("status");

  useEffect(() => {
    if (category_id) {
      reset({'status':status,'category_id' : category_id, 'sub_category_id' : null})
      axiosInstance
      .get(`/finance/cash-in/sub-categories/${category_id}`)
      .then((response) => {
        setSubCategories(response.data.data);
      });
    }
  }, [category_id]);

  

  useEffect(() => {
    handleFilter(status, category_id, sub_category_id);
  }, [status, category_id, sub_category_id]);

  return (
    <form className="flex items-end gap-4 w-1/2 mb-6">
      <div className="form-control w-full">
        <label className="label font-medium">Status</label>
        <select
          {...register("status")}
          className="select select-bordered w-full"
          required
        >
          <option value="">-- All --</option>
          <option value="lunas">Lunas</option>
          <option value="belum-lunas">Belum Lunas</option>
        </select>
      </div>

      <div className="form-control w-full">
        <label className="label font-medium">Category</label>
        <select
          {...register("category_id")}
          className="select select-bordered w-full"
          required
        >
          <option value="">-- All --</option>
          {categories.map((opt) => (
            <option key={opt.id} value={opt.id}>
              {opt.name}
            </option>
          ))}
        </select>
      </div>

      <div className="form-control w-full">
        <label className="label font-medium">Sub Category</label>
        <select
          {...register("sub_category_id")}
          className="select select-bordered w-full"
          required
        >
          <option value="">-- All --</option>
          {subCategories.map((opt) => (
            <option key={opt.id} value={opt.id}>
              {opt.name}
            </option>
          ))}
        </select>
      </div>
      <button
        type="button"
        onClick={() => {
          reset({'category_id' : null, 'sub_category_id' : null});
        }}
        className="btn btn-secondary"
      >
        Reset
      </button>
    </form>
  );
}
