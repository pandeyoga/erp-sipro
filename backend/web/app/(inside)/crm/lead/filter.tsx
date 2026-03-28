import axios from "@/lib/axios";
import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";

export default function FilterLead({ handleFilter } : { handleFilter : (status : string,agent : string,source : string) => void }) {
  const [statusOptions, setStatusOptions] = useState<string[]>([]);
  const [agentOptions, setAgentOptions] = useState<
    { label: string; value: string }[]
  >([]);

  const {
      register,
      watch,
      reset,
      formState: { errors },
    } = useForm();

  useEffect(() => {
    const fetch = async () => {
      const [agentRes, statusRes] = await Promise.all([
        axios.get("/crm/lead/get-marketing-agents"),
        axios.get("/crm/lead/get-available-status"),
      ]);

      setAgentOptions(
        agentRes.data.data.map((a: any) => ({
          label: a.name,
          value: a.id,
        }))
      );

      setStatusOptions(statusRes.data.data);
    };

    fetch();
  }, []);

  const status = watch('status')
  const agent = watch('marketing_id')
  const source = watch('source')

  useEffect(()=>{
    handleFilter(status,source,agent)
  },[status,source,agent])

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
          {statusOptions &&
            statusOptions.map((status) => (
              <option key={status} value={status}>
                {status.replace(/_/g, " ")}
              </option>
            ))}
        </select>
      </div>

      <div className="form-control w-full">
        <label className="label font-medium">Marketing</label>
        <select
          {...register("marketing_id")}
          className="select select-bordered w-full"
          required
        >
          <option value="">-- All --</option>
          {agentOptions.map((opt) => (
            <option key={opt.value} value={opt.value}>
              {opt.label}
            </option>
          ))}
        </select>
      </div>

      <div className="form-control w-full">
        <label className="label font-medium">Source</label>
        <select
          {...register("source")}
          className="select select-bordered w-full"
          required
        >
          <option value="">-- All --</option>
          {[
            { value: "facebook", label: "Facebook" },
            { value: "instagram", label: "Instagram" },
            { value: "tiktok", label: "Tiktok" },
            { value: "ots", label: "On the spot" },
            { value: "event", label: "Event" },
            { value: "agent", label: "Marketing" },
            { value: "ads_facebook", label: "Ads Facebook" },
            { value: "ads_instagram", label: "Ads Instagram" },
            { value: "ads_tiktok", label: "Ads Tiktok" },
          ].map((opt) => (
            <option key={opt.value} value={opt.value}>
              {opt.label}
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

