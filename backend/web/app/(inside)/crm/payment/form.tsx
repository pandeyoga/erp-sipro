"use client";

import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import FileDropzone from "@/components/dropzone";
import { PageLoading } from "@/components/loading/loading";
import { useRouter } from "next/navigation";
import ProtectedImage from "@/components/protected-image";
import { LeadDocumentFile } from "./[id]/verify/page";
import Select from "react-select";

const documentFields = [
  { name: "ktp_applicant", label: "KTP Pemohon" },
  { name: "ktp_partner", label: "KTP Pasangan" },
  { name: "npwp", label: "NPWP" },
  { name: "kk", label: "Kartu Keluarga" },
  {
    name: "marriage_or_divorce_certificate",
    label: "Surat Nikah / Cerai",
  },
  { name: "applicant_photo", label: "Foto Pemohon" },
  { name: "partner_photo", label: "Foto Pasangan" },
  { name: "house_ownership_certificate", label: "Surat Kepemilikan Rumah" },
  { name: "domisili_certificate", label: "Surat Domisili" },
  { name: "spr_bank", label: "Surat Perjanjian Bank" },
];

interface LeadDocumentFormProps {
  id?: string;
  mode?: "create" | "edit";
}

interface LeadDocument {
  [key: string]: any;
}
interface DocumentLead {
  id: string;
  name: string;
  phone: string;
  email: string;
  collection_document_id: string;
}

const documentPekerja = [
  {
    key: "pekerja_materai_60_lembar",
    label: "Materai 60 Lembar",
  },
  {
    key: "pekerja_no_telp_dan_nama_atasan",
    label: "No Telpon & Nama Atasan",
  },
  {
    key: "pekerja_slip_gaji_3_bulan",
    label: "Slip Gaji 3 Bulan Terakhir",
  },
  {
    key: "pekerja_rekening_koran_3_bulan",
    label: "Rekening Koran 3 Bulan Terakhir",
  },
  {
    key: "pekerja_foto_tempat_kerja_dan_serlok",
    label: "Foto Tempat Kerja dan Share Lokasi",
  },
  {
    key: "pekerja_formulir_bank_dan_flpp",
    label: "Formulir Bank Dan Flpp",
  },
];

const documentWirausaha = [
  {
    key: "wirausaha_materai_60_lembar",
    label: "Materai 60 Lembar",
  },
  {
    key: "wirausaha_sk_usaha_atau_nomor_usaha",
    label: "SK Usaha/Nomor Induk Usaha",
  },
  {
    key: "wirausaha_neraca_penghasilan_6_bulan",
    label: "Neraca Penghasilan 6 Bulan Terakhir",
  },
  {
    key: "wirausaha_foto_tempat_usaha",
    label: "Foto Tempat Usaha",
  },
  {
    key: "wirausaha_rekening_koran_6_bulan",
    label: "Rekening Koran 6 Bulan Terakhir",
  },
  {
    key: "wirausaha_foto_tempat_usaha_dan_serlok",
    label: "Foto Tempat Usaha dan Share Lokasi",
  },
  {
    key: "wirausaha_formulir_bank_dan_flpp",
    label: "Formulir Bank Dan Flpp",
  },
];

interface Bank {
  id: string;
  code: string;
  name: string;
}

export default function PaymentForm({
  id,
  mode = "create",
}: LeadDocumentFormProps) {
  const [loading, setLoading] = useState<boolean>(true);
  const {
    register,
    handleSubmit,
    setValue,
    watch,
    reset,
    formState: { errors },
  } = useForm<LeadDocument>();
  const [documentLeads, setDocumentLeads] = useState<DocumentLead[]>([]);

  const [searchTermLead, setSearchTermLead] = useState("");
 
  useEffect(() => {
    if (!searchTermLead) return;
  
    const delay = setTimeout(() => {
      axios
        .get(`/crm/lead-payment/get-completed-document-lead?search=${searchTermLead}`)
        .then((res) => {
          const options = res.data.data.map((p: any) => ({label : p.name, value: p.id, ...p}))
          setDocumentLeads(options)
        })
        .catch(() => console.error("Gagal ambil data"));
    }, 400);
  
    return () => clearTimeout(delay);
  }, [searchTermLead]);

  useEffect(() => {
    const fetchData = async () => {
      if (mode === "edit" && id) {
        try {
          const res = await axios.get(`/crm/lead-document/${id}`);
          const data = res.data.data;
          const values = {
            ...data,
            ...data.documents.reduce((acc: any, curr: any) => {
              acc[curr.type] = curr.file_url;
              return acc;
            }, {}),
            ...data.checklist.reduce((acc: any, curr: any) => {
              acc[curr.key] = curr.checked;
              return acc;
            }, {}),
          };
          reset(values);
        } catch (err) {
          toast.error("Gagal memuat data dokumen");
        }
      }

      const docLeads = await axios.get(
        "/crm/lead-payment/get-completed-document-lead?search="
      );
      setDocumentLeads(docLeads.data.data?.map((p: any) => ({label : p.name, value: p.id, ...p})) || []);

      setLoading(false);
    };
    fetchData();
  }, [id, mode, reset]);

  const router = useRouter();
  const [loadingSubmit, setLoadingSubmit] = useState<boolean>(false);

  const onSubmit = async (data: LeadDocument) => {
    const selected_banks = Object.entries(data.bankSelections || {})
      .filter(([_, isChecked]) => isChecked)
      .map(([id]) => id);

    try {
      setLoadingSubmit(true);
      const url = "/crm/lead-payment";
      const res = await axios.post(url, {
        selected_banks,
        payment_type: data.payment_type,
        notes: data.notes,
        lead_id: data.lead_id,
        proposed_name_1: data.proposed_name_1,
        proposed_name_2: data.proposed_name_2,
      });
      toast.success(res.data.message);
      router.push("/crm/payment");
    } catch (err) {
      toast.error("Gagal menyimpan pembayaran");
    } finally {
      setLoadingSubmit(false);
    }
  };

  const [documents, setDocuments] = useState<LeadDocumentFile[] | null>(null);

  useEffect(() => {
    const fetch = async (collection_document_id: string) => {
      try {
        const res = await axios.get(
          `/crm/lead-document/${collection_document_id}`
        );
        const data = res.data?.data;

        if (data?.documents?.length) {
          data.documents.forEach((doc: any) => {
            if (doc?.type && doc?.file_url) {
              setValue(doc.type, doc.file_url);
            }
          });
          setDocuments(data.documents);
        }

        if (data?.checklist?.length) {
          data.checklist.forEach((item: any) => {
            if (item?.key) {
              setValue(item.key, item.checked ?? false); // fallback ke false jika `checked` undefined
            }
          });
        }
        // reset(values)
      } catch (err) {
        console.error("Gagal mengambil dokumen:", err);
        setDocuments(null);
      } finally {
        setLoading(false);
      }
    };

    const selectedLead = documentLeads.find(
      (lead) => lead.id === watch("lead_id")
    );
    const email = selectedLead?.email;
    const phone = selectedLead?.phone;
    const collection_document_id = selectedLead?.collection_document_id;
    setValue("email", email);
    setValue("phone", phone);
    fetch(collection_document_id ?? "");
  }, [watch("lead_id")]);

  const [listBank, setListBank] = useState<Bank[]>([]);

  useEffect(() => {
    const fetchBanks = async () => {
      try {
        const res = await axios.get("/finance/bank-account");
        if (res.data?.success) {
          setListBank(res.data.data);
        }
      } catch (error) {
        console.error("Gagal mengambil daftar bank:", error);
      }
    };

    fetchBanks();
  }, []);

  // if (loading) return <PageLoading />

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Lead Information</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Lead</span>
            </label>
            {mode === "edit" ? (
              <input
                {...register("name")}
                readOnly
                className="input input-bordered w-full"
              />
            ) : (
              <Select
                options={documentLeads}
                placeholder="Select or search Prospect Lead"
                onChange={(value : any) => {
                  setValue("lead_id", value.value);
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
            )}
          </div>
          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">No. Telepon </span>
            </label>
            <input
              {...register("phone")}
              placeholder="No. Telepon"
              className="input input-bordered w-full"
              readOnly
            />
          </div>
          <div className="form-control mt-4">
            <label className="label">
              <span className="label-text">Email </span>
            </label>
            <input
              {...register("email")}
              placeholder="Email"
              className="input input-bordered w-full"
              readOnly
            />
          </div>
        </div>
      </fieldset>
      {documents ? (
        <div>
          <fieldset className="border border-base-300 rounded-xl p-6">
            <legend className="text-lg font-semibold px-2">
              Dokumen Konsumen
            </legend>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {documents &&
                documentFields.map((doc) => {
                  const current_doc = documents.find(
                    (obj: any) => `${obj.type}` === doc.name
                  );
                  const file_url = current_doc?.file_url;

                  return (
                    <div className="form-control mt-4" key={doc.name}>
                      <label className="label">
                        <span className="label-text">{doc.label}</span>
                      </label>
                      <ProtectedImage
                        imageUrl={file_url ?? ""}
                        linkOnly={false}
                        label={doc.label}
                      />
                    </div>
                  );
                })}
            </div>
          </fieldset>
          <fieldset className="border border-base-300 rounded-xl p-6">
            <legend className="text-lg font-semibold px-2">
              Document Konsumen (Pekerja)
            </legend>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {documentPekerja.map((item) => (
                <label key={item.key} className="flex items-center gap-2">
                  <input
                    type="checkbox"
                    {...register(`${item.key}`)}
                    className="checkbox checkbox-sm"
                    disabled
                  />
                  <span>{item.label}</span>
                </label>
              ))}
            </div>
          </fieldset>

          <fieldset className="border border-base-300 rounded-xl p-6">
            <legend className="text-lg font-semibold px-2">
              Document Konsumen (Wirausaha)
            </legend>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {documentWirausaha.map((item) => (
                <label key={item.key} className="flex items-center gap-2">
                  <input
                    type="checkbox"
                    {...register(`${item.key}`)}
                    className="checkbox checkbox-sm"
                    disabled
                  />
                  <span>{item.label}</span>
                </label>
              ))}
            </div>
          </fieldset>

          <fieldset className="border border-base-300 rounded-xl p-6">
            <legend className="text-lg font-semibold px-2">
              Dokumen Developer (SPR Bank)
            </legend>
            <ProtectedImage imageUrl={watch("spr_bank")} />
          </fieldset>
        </div>
      ) : null}

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Bank KPR</legend>
        <div className="grid grid-cols-2 md:grid-cols-2 gap-4">
          {listBank.map((bank) => (
            <label key={bank.code} className="flex items-center gap-2">
              <input
                type="checkbox"
                {...register(`bankSelections.${bank.id}`)}
                className="checkbox checkbox-sm"
              />
              <span>{bank.name}</span>
            </label>
          ))}
        </div>
      </fieldset>

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Pengajuan Nama</legend>
        <div className="form-control mb-4">
          <label>Pengajuan Nama 1</label>
          <input
            {...register("proposed_name_1")}
            placeholder="Pengajuan Nama 1"
            className="input input-bordered w-full"
          />
        </div>
        <div className="form-control">
          <label>Pengajuan Nama 2</label>
          <input
            {...register("proposed_name_2")}
            placeholder="Pengajuan Nama 2"
            className="input input-bordered w-full"
          />
        </div>
      </fieldset>

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Payment Type</legend>
        <div className="form-control">
          <select
            {...register("payment_type")}
            className="select select-bordered w-full"
          >
            <option value="">Pilih Pembayaran</option>
            <option value="kpr">KPR</option>
            <option value="cash_keras">Cash Keras</option>
            <option value="cash_bertahap">Cash Bertahap</option>
          </select>
        </div>
      </fieldset>

      <fieldset className="border border-base-300 rounded-xl p-6">
        <legend className="text-lg font-semibold px-2">Catatan</legend>
        <textarea
          {...register("notes")}
          placeholder="Catatan Tambahan"
          className="textarea textarea-bordered w-full"
        />
      </fieldset>

      <div className="flex justify-end">
        <button
          type="submit"
          className="btn btn-primary"
          disabled={loadingSubmit}
        >
          {loadingSubmit && <span className="loading loading-spinner"></span>}
          {mode === "edit" ? "Update Document" : "Create Document"}
        </button>
      </div>
    </form>
  );
}
