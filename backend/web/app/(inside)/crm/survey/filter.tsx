import axios from "@/lib/axios";
import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";

export default function FilterLead({ handleFilter } : { handleFilter : (status : string,agent : string,source : string) => void }) {
  const statusOptions = [
    {
      "label" : "Not Scheduled",
      "value" : "not_scheduled"
    },
    {
      "label" : "Scheduled",
      "value" : "scheduled"
    },
    {
      "label" : "Done",
      "value" : "done"
    },
  ];
  const [agentOptions, setAgentOptions] = useState<
    { label: string; value: string }[]
  >([]);

  const {
      register,
      watch,
      reset,
      formState: { errors },
    } = useForm();


  const status = watch('status')
  const agent = watch('marketing_id')
  const source = watch('source')

  useEffect(()=>{
    handleFilter(status,source,agent)
  },[status,source,agent])

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
      {/* <button type="button" onClick={()=>{ reset() }}className="btn btn-secondary">
        Reset
      </button> */}
    </form>
  );
};

