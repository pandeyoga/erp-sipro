"use client";

import { useForm } from "react-hook-form";
import { useEffect, useState } from "react";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import { PageLoading } from "@/components/loading/loading";
import FileDropzone from "@/components/dropzone";
import NumberFormat from "react-number-format";
import { NumericFormat } from "react-number-format";
import Select from "react-select";

type FormValues = {
  contact: string;
  name: string;
  phone: string;
  email: string;
  surveyDate: string;
  survey_location: string;
  marketing_id: string;
  marketing_agent: string;
  survey_date: string;
  lead_id: string;
  property_id: string;
  reservation_date: string;
  reservation_fee: number;
  hook_additional_fee: number;
  additional_land_area_fee: number;
  additional_building_specifications_fee: number;
  all_in_fee: number;
  unit_price: number;
  notes: string;
  construction_notes: string;
  status?: string;
  reservation_proof?: FileList;
  reservation_letter?: FileList;
};

type ReservationFormProps = {
  id?: number | string;
  onSubmit: (data: {
    lead_id: string;
    property_id: string;
    reservation_date: string;
    reservation_fee: number;
    hook_additional_fee: number;
    additional_land_area_fee: number;
    additional_building_specifications_fee: number;
    all_in_fee: number;
    unit_price: number;
    notes: string;
    status?: string;
    reservation_proof?: FileList;
    reservation_letter?: FileList;
  } | any) => void;
  mode?: "create" | "edit";
  loading: boolean;
};

type ProspectOption = {
  id: string;
  name: string;
  phone: string;
  email: string;
  marketing_agent: string;
  survey_date: string;
};

type PropertyOption = {
  value: string;
  label: string;
};

export default function ReservationForm({
  id,
  onSubmit,
  mode = "create",
  loading,
}: ReservationFormProps) {
  const [prospectOptions, setProspectOptions] = useState<ProspectOption[] | any[]>([]);
  const [propertyOptions, setPropertyOptions] = useState<PropertyOption[]>([]);
  const [statusOptions, setStatusOptions] = useState<string[]>([]);
  const [initialData, setInitialData] = useState<any>(null);
  const [fetchloading, setLoading] = useState(true);

  const {
    register,
    handleSubmit,
    setValue,
    getValues,
    reset,
    watch,
    formState: { errors },
  } = useForm();

  const [searchTermLead, setSearchTermLead] = useState("");
  const [searchTermProperty, setSearchTermProperty] = useState("");
 

  useEffect(() => {
    if (!searchTermLead) return;
  
    const delay = setTimeout(() => {
      axios
        .get(`/crm/lead/get-non-lead-contacts?search=${searchTermLead}`)
        .then((res) => {
          const options = res.data.data.map((p: any) => ({label : p.name, value: p.id, ...p}))
          setProspectOptions(options)
        })
        .catch(() => console.error("Gagal ambil data"));
    }, 400);
  
    return () => clearTimeout(delay);
  }, [searchTermLead]);

  useEffect(() => {
    if (!searchTermProperty) return;
  
    const delay = setTimeout(() => {
      axios
        .get(`/crm/reservation/get-properties?search=${searchTermLead}`)
        .then((res) => {
          const options = res.data.data.map((p: any) => ({label : p.name, value: p.id, ...p}))
          setProspectOptions(options)
        })
        .catch(() => console.error("Gagal ambil data"));
    }, 400);
  
    return () => clearTimeout(delay);
  }, [searchTermProperty]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        let [prospectsRes, propertiesRes]: any = [];

        if (mode === "create") {
          [prospectsRes, propertiesRes] = await Promise.all([
            axios.get("/crm/reservation/get-prospects"),
            axios.get("/crm/reservation/get-properties"),
          ]);
        } else {
          [prospectsRes, propertiesRes] = await Promise.all([
            axios.get("/crm/reservation/get-prospects"),
            axios.get("/crm/reservation/" + id + "/properties"),
          ]);
        }

        setProspectOptions(prospectsRes.data.data.map((p: any) => ({label:p.name, value :p.id, ...p })));
        setPropertyOptions(
          propertiesRes.data.data.map((p: any) => ({
            value: p.id,
            label: p.name,
          }))
        );
        
        if (mode === "edit") {
          const res = await axios.get(`/crm/reservation/${id}`);
          const reservation = res.data.data;

          const newInitialData = {
            name: reservation.name,
            contact: reservation.contact_id,
            phone: reservation.phone,
            email: reservation.email,
            construction_notes : reservation.construction_notes, 
            survey_date: reservation.survey_date?.substring(0, 10) || "",
            survey_location: reservation.survey_location || "",
            marketing_agent: reservation.marketing_agent,
            notes: reservation.notes || "",
            status: reservation.status,
            reservation_fee: parseFloat(reservation.reservation_fee),
            unit_price: parseFloat(reservation.unit_price),
            property_id: reservation.property_id,
            property: reservation.property,
            hook_additional_fee: parseFloat(reservation.hook_additional_fee),
            additional_land_area_fee: parseFloat(reservation.additional_land_area_fee),
            additional_building_specifications_fee: parseFloat(reservation.additional_building_specifications_fee),
            all_in_fee: parseFloat(reservation.all_in_fee),
            reservation_date: reservation.reservation_date,
            reservation_proof: reservation.reservation_proof,
            reservation_letter: reservation.reservation_letter,
          };

          setInitialData(newInitialData);
          reset(newInitialData);
          setStatusOptions(["pending", "confirmed", "canceled", "expired"]);
          // setSearchTermLead(reservation.property);
        }

        

        
      } catch (err) {
        console.error("Error during data fetching:", err);
        toast.error("Gagal memuat data reservation atau opsi form.");
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [mode, id, reset]);

  const onFinalSubmit = (data: any) => {
    const formData = new FormData();
    Object.entries(data).forEach(([key, value]) => {
      if (key === "reservation_proof" || key == "reservation_letter") {
        if (value instanceof File || value instanceof Blob) {
          formData.append(key, value);
        }
      } else if (value !== undefined && value !== null) {
        formData.append(key, value as any);
      }
    });
    onSubmit(formData);
  };



  if (fetchloading) {
    return <PageLoading />;
  }

  return (
    <form
      onSubmit={handleSubmit(onFinalSubmit)}
      className="space-y-6 w-full mx-auto"
    >
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {/* LEAD INFORMATION */}
        <fieldset className="border border-base-300 rounded-xl p-6">
          <legend className="text-lg font-semibold px-2">
            Lead Information
          </legend>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {/* Contact Selector */}
            {mode === "edit" ? (
              <div className="form-control mt-4">
                <label className="label">
                  <span className="label-text">Prospect Lead</span>
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
                  <span className="label-text">Prospect Lead</span>
                </label>
                <Select
                options={prospectOptions}
                placeholder="Select or search Prospect Lead"
                onChange={(value) => {
                  const selected = prospectOptions.find(
                    (opt : any) => opt.value === value?.value
                  );
                  setValue("lead_id", value.value);
                  setValue("phone", selected?.phone || "");
                  setValue("email", selected?.email || "");
                  setValue("surveyDate", selected?.survey_date || "");
                  setValue("marketing_id", selected?.marketing_agent || "");
                }}
                onInputChange={(value, { action }) => {
                  if (action === "input-change") {
                    setSearchTermLead(value); 
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
        </fieldset>

        <fieldset className="border border-base-300 rounded-xl p-6">
          <legend className="text-lg font-semibold px-2">
            Reservation Information
          </legend>

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Reservation Date</span>
            </label>
            <input
              type="date"
              {...register("reservation_date")}
              className="input input-bordered w-full"
            />
          </div>
          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Property</span>
            </label>
            <Select
                options={propertyOptions}
                placeholder="Select or search property"
                onChange={(value) => {
                  setValue("property_id", value?.value || "");
                }}
                onInputChange={(value, { action }) => {
                  if (action === "input-change") {
                    setSearchTermLead(value);
                  }
                }}
                defaultValue={{label : getValues('property'), value : getValues('property_id')}}
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
            {errors.property_id && (
              <p className="text-sm text-red-500">Wajib pilih properti</p>
            )}
          </div>
        </fieldset>

        <fieldset className="border border-base-300 rounded-xl p-6">
          <legend className="text-lg font-semibold px-2">General Fee</legend>
          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Reservation Fee</span>
            </label>
            <NumericFormat
              thousandSeparator="."
              decimalSeparator=","
              prefix="Rp "
              value={watch("reservation_fee")}
              className="input input-bordered w-full"
              {...register("reservation_fee", { required: true })}
              onValueChange={(values) => {
                const { floatValue } = values;
                setValue("reservation_fee", floatValue || 0);
              }}
            />
            {errors.reservation_fee && (
              <p className="text-sm text-red-500">Wajib diisi</p>
            )}
          </div>

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Base Fee</span>
            </label>
            <NumericFormat
              thousandSeparator="."
              decimalSeparator=","
              prefix="Rp "
              className="input input-bordered w-full"
              value={watch("unit_price")}
              {...register("unit_price", { required: true })}
              onValueChange={(values) => {
                const { floatValue } = values;
                // update value manually ke react-hook-form jika perlu
                setValue("unit_price", floatValue || 0);
              }}
            />
            {errors.unit_price && (
              <p className="text-sm text-red-500">Wajib diisi</p>
            )}
          </div>

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">All in Fee</span>
            </label>
            <NumericFormat
              thousandSeparator="."
              decimalSeparator=","
              prefix="Rp "
              
              className="input input-bordered w-full"
              {...register("all_in_fee", { required: true })}
              value={watch("all_in_fee")}
              onValueChange={(values) => {
                const { floatValue } = values;
                // update value manually ke react-hook-form jika perlu
                setValue("all_in_fee", floatValue || 0);
              }}
              
            />
            {errors.reservation_fee && (
              <p className="text-sm text-red-500">Wajib diisi</p>
            )}
          </div>
        </fieldset>

        <fieldset className="border border-base-300 rounded-xl p-6">
          <legend className="text-lg font-semibold px-2">Additional Fee</legend>
          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Hook Fee</span>
            </label>
            <NumericFormat
              thousandSeparator="."
              decimalSeparator=","
              prefix="Rp "
              className="input input-bordered w-full"
              {...register("hook_additional_fee")}
              value={watch("hook_additional_fee")}
              onValueChange={(values) => {
                const { floatValue } = values;
                // update value manually ke react-hook-form jika perlu
                setValue("hook_additional_fee", floatValue || 0);
              }}
            />
          </div>

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Penambahan Luas Tanah</span>
            </label>
            <NumericFormat
              thousandSeparator="."
              decimalSeparator=","
              prefix="Rp "
              className="input input-bordered w-full"
              {...register("additional_land_area_fee")}
              value={watch("additional_land_area_fee")}
              onValueChange={(values) => {
                const { floatValue } = values;
                // update value manually ke react-hook-form jika perlu
                setValue("additional_land_area_fee", floatValue || 0);
              }}
            />
          </div>

          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">
                Penambahan Spesifikasi Bangunan
              </span>
            </label>
            <NumericFormat
              thousandSeparator="."
              decimalSeparator=","
              prefix="Rp "
              className="input input-bordered w-full"
              {...register("additional_building_specifications_fee")}
              value={watch("additional_building_specifications_fee")}
              onValueChange={(values) => {
                const { floatValue } = values;
                // update value manually ke react-hook-form jika perlu
                setValue("additional_building_specifications_fee", floatValue || 0);
              }}
            />
          </div>
        </fieldset>
      </div>

      {mode === "edit" && (
        <>
          <fieldset className="border border-base-300 rounded-xl p-6">
            <legend className="text-lg font-semibold px-2">
              Status & Documents
            </legend>

            <div className="form-control mt-4">
              <label className="label">
                <span className="label-text">Status</span>
              </label>
              <select
                {...register("status", { required: true })}
                className="select select-bordered w-full"
              >
                <option value="">Select status</option>
                {["Pending", "Confirmed", "Canceled", "Expired"].map(
                  (status) => (
                    <option key={status} value={status.toLowerCase()}>
                      {status}
                    </option>
                  )
                )}
              </select>
              {errors.status && (
                <p className="text-sm text-red-500">Wajib pilih status</p>
              )}
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="form-control mt-4">
                <FileDropzone
                  label="Reservation Proof (image/pdf)"
                  name="reservation_proof"
                  setValue={setValue}
                  initialPreviewSrc={getValues("reservation_proof")}
                />
              </div>

              <div className="form-control mt-4">
                <FileDropzone
                  label="Reservation Letter (pdf)"
                  name="reservation_letter"
                  setValue={setValue}
                  initialPreviewSrc={getValues("reservation_letter")}
                />
              </div>
            </div>
          </fieldset>
        </>
      )}

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Notes</legend>
        <div className="form-control mt-4">
          <label className="label">
            <span className="label-text">Notes</span>
          </label>
          <textarea
            {...register("notes")}
            className="textarea textarea-bordered w-full"
          />
        </div>
        <div className="form-control mt-4">
          <label className="label">
            <span className="label-text">Construction Notes</span>
          </label>
          <textarea
            {...register("construction_notes")}
            className="textarea textarea-bordered w-full"
          />
        </div>
      </fieldset>

      <div className="flex justify-end">
        <button type="submit" className="btn btn-primary" disabled={loading}>
          {loading && <span className="loading loading-spinner"></span>}
          {mode === "edit" ? "Update Reservation" : "Save Reservation"}
        </button>
      </div>
    </form>
  );
}
