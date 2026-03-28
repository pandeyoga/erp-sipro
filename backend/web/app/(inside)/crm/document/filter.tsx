import axios from "@/lib/axios";
import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";

export default function FilterLead({ handleFilter } : { handleFilter : (status : string) => void }) {
  const [statusOptions, setStatusOptions] = useState<string[]>([]);

  const {
      register,
      watch,
      reset,
      formState: { errors },
    } = useForm();

  useEffect(() => {
    setStatusOptions(['input', 'verification', 'completed']);
  }, []);

  const status = watch('status')

  useEffect(()=>{
    handleFilter(status)
  },[status])

  return (
    <form className="flex items-end gap-4 w-xs mb-6">
      <div className="form-control w-full">
        <label className="label font-medium">Status</label>
        <select
          {...register("status")}
          className="select select-bordered w-full"
          required
        >
          <option value="">-- All --</option>
          {statusOptions &&
            statusOptions.map((status) => (
              <option key={status} value={status}>
                {status.replace(/_/g, " ")}
              </option>
            ))}
        </select>
      </div>
      <button type="button" onClick={()=>{ reset() }}className="btn btn-secondary">
        Reset
      </button>
    </form>
  );
};

