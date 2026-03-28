"use client";

import { useForm } from "react-hook-form";
import { useEffect, useState } from "react";
import axios from "@/lib/axios";
import { PageLoading } from "@/components/loading/loading";
import toast from "react-hot-toast";
import Select from "react-select";

type FormValues = {
  contact: string;
  name: string;
  phone: string;
  email: string;
  surveyDate: string;
  leadSource: string;
  surveyLocation: string;
  marketingAgent: string;
  actual_survey_date : string;
  survey_documentation : any;
  unit_preference_id : any;
  note: string;
  pic: string;
  status?: string; // hanya digunakan saat edit
};

type LeadFormProps = {
  id?: string;
  onSubmit: (data: FormValues) => void;
  mode?: "create" | "edit";
  loading: boolean;
};

type ContactOption = {
  label: string;
  value: string;
  phone: string;
  email: string | null;
};

type AgentOption = {
  label: string;
  value: string;
};

export default function LeadForm({
  id,
  onSubmit,
  mode = "create",
  loading,
}: LeadFormProps) {
  const [initialData, setInitialData] = useState<any>(null);
  const {
    register,
    handleSubmit,
    reset,
    setValue,
    watch,
    getValues,
    formState: { errors },
  } = useForm<FormValues>({
    defaultValues: {
      contact: "",
      name: "",
      phone: "",
      email: "",
      surveyDate: "",
      leadSource: "",
      surveyLocation: "",
      marketingAgent: "",
      note: "",
      status: "",
      ...initialData,
    },
  });

  const [contactOptions, setContactOptions] = useState<ContactOption[]>([]);
  const [agentOptions, setAgentOptions] = useState<AgentOption[]>([]);
  const [statusOptions, setStatusOptions] = useState<string[]>([]);
  const [unitOptions, setUnitOptions] = useState<any[]>([]);
  const [surveyLocationOptions, setSurveyLocationOptions] = useState<any[]>([]);
  const [fetchLoading, setFetchLoading] = useState<boolean>(true);
  const [searchTermContact, setSearchTermContact] = useState("");
 

  useEffect(() => {
    if (!searchTermContact) return;
  
    const delay = setTimeout(() => {
      axios
        .get(`/crm/lead/get-non-lead-contacts?search=${searchTermContact}`)
        .then((res) => {
          const options = res.data.data.map((c: any) => ({
            label: c.name,
            value: c.id,
            phone: c.phone,
            email: c.email,
          }))
          setContactOptions(options)
        })
        .catch(() => console.error("Gagal ambil data"));
    }, 400);
  
    return () => clearTimeout(delay);
  }, [searchTermContact]);
  

  useEffect(() => {
    const fetchData = async () => {
      try {
        let leadData = null;

        if (mode === "edit") {
          
          const res = await axios.get(`/crm/lead/${id}`);
          const lead = res.data.data;

          leadData = {
            name: lead.name,
            contact: lead.contact_id,
            phone: lead.phone,
            email: lead.email,
            surveyDate: lead.survey_date?.substring(0, 10) || "",
            surveyLocation: lead.survey_location_id || "",
            pic: lead.pic || "",
            leadSource: lead.source || "",
            marketingAgent: lead.marketing_agent_id,
            note: lead.notes || "",
            status: lead.status,
          };

          setInitialData(leadData);
        }

        const [contactRes, agentRes, surveyRes] = await Promise.all([
          axios.get("/crm/lead/get-non-lead-contacts"),
          axios.get("/crm/lead/get-marketing-agents"),
          axios.get('/crm/lead/get-survey-location')
        ]);

        setSurveyLocationOptions(surveyRes.data.data)

        if(mode == 'edit'){
          const [statusRes, propertyUnitRes] = await Promise.all([
            axios.get("/crm/lead/get-available-status"),
            axios.get("/crm/lead/get-property-units"),
            Promise.resolve({ data: { data: [] } }),
          ]);
          setStatusOptions(statusRes.data.data);
          setUnitOptions(propertyUnitRes.data.data)
        }
        

        setContactOptions(
          contactRes.data.data.map((c: any) => ({
            label: c.name,
            value: c.id,
            phone: c.phone,
            email: c.email,
          }))
        );

        setAgentOptions(
          agentRes.data.data.map((a: any) => ({
            label: a.name,
            value: a.id,
          }))
        );

        // Set nilai awal form setelah semua opsi tersedia
        if (initialData) {
          reset(initialData);
        } else if (leadData) {
          reset(leadData);
        }
      } catch (err) {
        console.error("Gagal memuat data lead atau opsi:", err);
        toast.error("Gagal memuat data.");
      } finally {
        setFetchLoading(false);
      }
    };

    fetchData();
  }, [mode, id]);

  const onFinalSubmit = (data: FormValues) => {
    onSubmit(data);
  };

  if (fetchLoading) {
    return <PageLoading />;
  }

  const housingOptions = [
    { id: "3f17a3b8-1f5b-4c94-88c1-aec3b3c1a4b7", name: "Green Valley" },
    { id: "a5e92a12-728b-4d41-9dc4-d2186a546f92", name: "Taman Surya" },
    { id: "e7f3d9c1-9bd0-49c2-bf93-278c0a7f92f6", name: "Bukit Indah" },
  ];

  return (
    <form
      onSubmit={handleSubmit(onFinalSubmit)}
      className="space-y-6 w-full mx-auto"
    >
      {/* LEAD INFORMATION */}
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Lead Information</legend>
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {/* Contact Selector */}
          {mode === "edit" ? (
            <div className="form-control mt-4">
              <label className="label">
                <span className="label-text">Contact</span>
              </label>
              <input
                type="text"
                {...register("name")}
                className="input input-bordered w-full"
                readOnly
              />
            </div>
          ) : (
            <div className="form-control mt-4">
              <label className="label">
                <span className="label-text">Contact</span>
              </label>
              <Select
                options={contactOptions}
                placeholder="Select or search contact"
                onChange={(value) => {
                  const selected = contactOptions.find(
                    (opt) => opt.value === value?.value
                  );
                  setValue("contact", (value as any).value);
                  setValue("phone", selected?.phone || "");
                  setValue("email", selected?.email || "");
                }}
                onInputChange={(value, { action }) => {
                  if (action === "input-change") {
                    setSearchTermContact(value); // update state setiap user ketik
                    console.log("User sedang mencari:", value);
                  }
                }}
                className="w-full"
                styles={{
                  control: (base) => ({
                    ...base,
                    borderColor: "#d1d5db", // tailwind: border-gray-300
                    borderRadius: "0.5rem",
                    padding: "2px",
                  }),
                }}
              />
              {errors.contact && (
                <p className="text-sm text-red-500">Wajib pilih kontak</p>
              )}
            </div>
          )}

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Phone</span>
            </label>
            <input
              type="text"
              {...register("phone")}
              className="input input-bordered w-full"
              readOnly
            />
          </div>

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Email</span>
            </label>
            <input
              type="text"
              {...register("email")}
              className="input input-bordered w-full"
              readOnly
            />
          </div>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Lead Source</span>
            </label>
            <select
              {...register("leadSource", { required: true })}
              className="select select-bordered w-full"
            >
              <option value="">Choose source</option>
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
            {errors.leadSource && (
              <p className="text-sm text-red-500">Wajib pilih lead source</p>
            )}
          </div>

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Select Agent</span>
            </label>
            <select
              {...register("marketingAgent", {
                required: watch("leadSource") == "agent",
              })}
              className="select select-bordered w-full"
            >
              <option value="">Choose agent</option>
              {agentOptions.map((opt) => (
                <option key={opt.value} value={opt.value}>
                  {opt.label}
                </option>
              ))}
            </select>
            {errors.marketingAgent && (
              <p className="text-sm text-red-500">Wajib pilih agent</p>
            )}
          </div>
        </div>
      </fieldset>

      {/* STATUS - ONLY IN EDIT MODE */}
      {mode === "edit" && (
        <fieldset className="border border-base-300 rounded-xl p-6">
          <legend className="text-lg font-semibold px-2">Lead Status</legend>
          <div className="form-control mt-4 max-w-sm">
            <label className="label">
              <span className="label-text">Status</span>
            </label>
            <select
              {...register("status", { required: true })}
              className="select select-bordered w-full"
              disabled={getValues('status') != 'new'}
            >
              <option value="">Choose status</option>
              {statusOptions.map((status) => (
                <option key={status} value={status} disabled={status != 'prospect'}>
                  {status.replace(/_/g, " ")}
                </option>
              ))}
            </select>
            {errors.status && (
              <p className="text-sm text-red-500">Wajib pilih status</p>
            )}
          </div>
        </fieldset>
      )}

      {/* NOTES */}
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Notes</legend>
        <div className="form-control">
          <label className="label">
            <span className="label-text">Note</span>
          </label>
          <textarea
            {...register("note")}
            className="textarea textarea-bordered w-full"
          />
        </div>
      </fieldset>

      <div className="flex justify-end">
        <button className="btn btn-primary" type="submit" disabled={loading}>
          {loading ? <span className="loading loading-spinner"></span> : null}
          {mode === "edit" ? "Update Lead" : "Save Lead"}
        </button>
      </div>
    </form>
  );
}
