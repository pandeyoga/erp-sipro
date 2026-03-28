"use client";

import { useForm } from "react-hook-form";
import { useEffect, useState } from "react";
import axios from "@/lib/axios";
import { PageLoading } from "@/components/loading/loading";
import toast from "react-hot-toast";
import FileDropzone from "@/components/dropzone";
import Select from "react-select";

type FormValues = {
  lead_id: string;
  name: string;
  phone: string;
  email: string;
  surveyDate: string;
  surveyLocation: string;
  actual_survey_date: string;
  survey_documentation: any;
  unit_preference_id: any;
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
      lead_id: "",
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
  const statusOptions = [
    {
      "label" : "Unscheduled",
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
  const [unitOptions, setUnitOptions] = useState<any[]>([]);
  const [surveyLocationOptions, setSurveyLocationOptions] = useState<any[]>([]);
  const [fetchLoading, setFetchLoading] = useState<boolean>(true);
  const [searchTermContact, setSearchTermContact] = useState("");

  useEffect(() => {
    if (!searchTermContact) return;

    const delay = setTimeout(() => {
      axios
        .get(`/crm/survey/get-non-survey-lead?search=${searchTermContact}`)
        .then((res) => {
          const options = res.data.data.map((c: any) => ({
            label: c.contact_name,
            value: c.id,
            phone: c.contact_phone,
            email: c.contact_email,
            source: c.contact_source,
            project_name: c.project_name
          }));
          setContactOptions(options);
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
          const res = await axios.get(`/crm/survey/${id}`);
          const lead = res.data.data;

          leadData = {
            name: lead.name,
            lead_id: lead.lead_id,
            phone: lead.phone,
            surveyDate: lead.survey_date?.substring(0, 10) || "",
            surveyLocation: lead.survey_location_id || "",
            pic: lead.pic || "",
            note: lead.notes || "",
            status: lead.status,
            actual_survey_date : lead.actual_survey_date,
            unit_preference_id: lead.unit_preference_id
          };

          setInitialData(leadData);
        }

        const [contactRes, agentRes, surveyRes] = await Promise.all([
          axios.get("/crm/survey/get-non-survey-lead"),
          axios.get("/crm/lead/get-marketing-agents"),
          axios.get("/crm/lead/get-survey-location"),
        ]);

        setSurveyLocationOptions(surveyRes.data.data);

        // if (mode == "edit") {
          const [propertyUnitRes] = await Promise.all([
            axios.get("/crm/lead/get-property-units"),
            Promise.resolve({ data: { data: [] } }),
          ]);
          setUnitOptions(propertyUnitRes.data.data);
        // }

        setContactOptions(
          contactRes.data.data.map((c: any) => ({
            label: c.contact_name,
            value: c.id,
            phone: c.contact_phone,
            email: c.contact_email,
            source: c.contact_source,
            project_name: c.project_name
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
                <span className="label-text">Lead</span>
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
                <span className="label-text">Lead</span>
              </label>
              <Select
                options={contactOptions}
                placeholder="Select or search contact"
                onChange={(value) => {
                  const selected = contactOptions.find(
                    (opt) => opt.value === (value as any).value
                  );
                  setValue("lead_id", (value as any).value);
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
              {errors.lead_id && (
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
        
      </fieldset>

      {/* STATUS - ONLY IN EDIT MODE */}
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Survey Information</legend>
          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Survey Location</span>
            </label>
            <select
              {...register("surveyLocation", { required: true })}
              className="select select-bordered w-full"
            >
              <option value="">Choose location</option>
              {surveyLocationOptions.map((opt) => (
                <option key={opt.id} value={opt.id}>
                  {opt.name}
                </option>
              ))}
            </select>
          </div>

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Survey Date</span>
            </label>
            <input
              type="date"
              {...register("surveyDate")}
              className="input input-bordered w-full"
            />
          </div>

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">PIC</span>
            </label>
            <input
              type="text"
              {...register("pic")}
              className="input input-bordered w-full"
            />
          </div>
        <div className="form-control mt-4 ">
          <label className="label">
            <span className="label-text">Actual Survey Date</span>
          </label>
          <input
            type="date"
            {...register("actual_survey_date")}
            className="input input-bordered w-full"
          />
        </div>
        <div className="form-control mt-4 ">
          <FileDropzone
            name="survey_documentation"
            label="Survey Documentation"
            setValue={setValue}
          />
        </div>
        <div className="form-control mt-4 ">
          <label className="label">
            <span className="label-text">Unit Preferences</span>
          </label>
          <select
            {...register("unit_preference_id")}
            className="select select-bordered w-full"
          >
            <option value="">Choose unit</option>
            {unitOptions.map((unit) => (
              <option key={unit.id} value={unit.id}>
                {unit.type}
              </option>
            ))}
          </select>
        </div>
      </fieldset>

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
