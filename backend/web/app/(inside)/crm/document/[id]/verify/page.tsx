"use client";

import { useEffect, useState } from "react";
import axios from "@/lib/axios";
import { PageLoading } from "@/components/loading/loading";
import { useParams } from "next/navigation";
import ProtectedImage from "@/components/protected-image";
import toast from "react-hot-toast";

type DocumentStatus = "verified" | "unverified" | "";

interface VerifikasiDokumen {
  name: string;
  label: string;
  fileUrl?: string;
  status?: DocumentStatus;
  note?: string;
}
const documentFields: VerifikasiDokumen[] = [
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

export interface LeadDocumentFile {
  type: string; // kode dokumen seperti "ktp_applicant", "npwp", dst.
  is_uploaded: boolean; // apakah file sudah di-upload
  is_validated: boolean; // apakah file sudah divalidasi
  file_url: string | null; // URL file (jika ada), bisa null kalau belum upload
}

export interface LeadDocumentDetail {
  id: string;
  lead_id: string;
  name: string;
  phone: string;
  email: string;
  status: "input" | "verification" | "completed"; // sesuaikan jika ada status lain
  notes: string;
  documents: LeadDocumentFile[];
}

export default function DocumentVerificationPage() {
  const { id } = useParams() as { id: string };
  const [documents, setDocuments] = useState<LeadDocumentFile[]>([]);
  const [lead, setLead] = useState<LeadDocumentDetail>();
  const [verifications, setVerifications] = useState<
    Record<string, DocumentStatus>
  >({});
  const [notes, setNotes] = useState<Record<string, string>>({});
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetch = async () => {
      try {
        const res = await axios.get(`/crm/lead-document/${id}`);
        setLead(res.data.data);
        setDocuments(res.data.data.documents);
      } catch (err) {
        console.error("Gagal mengambil dokumen:", err);
      } finally {
        setLoading(false);
      }
    };

    fetch();
  }, [id]);

  const handleStatusChange = (name: string, status: DocumentStatus) => {
    setVerifications((prev) => ({ ...prev, [name]: status }));
    axios
      .put(`/crm/lead-document/${id}/status`, {
        type: name,
        status: status,
      })
      .then(() => {
        toast.success("Sukses merubah status dokumen");
      })
      .catch((err) => {
        console.error(err);
        toast.error("Gagal merubah status dokumen");
        setVerifications((prev) => ({ ...prev, [name]: "" }));
      });
  };

  const total = documentFields.length;
  const sudahDiverifikasiValid = Object.values(verifications).filter(
    (v) => v === "verified"
  ).length;
  const sudahDiverifikasiInvalid = Object.values(verifications).filter(
    (v) => v === "unverified"
  ).length;
  const belumDiperiksa =
    total - (sudahDiverifikasiValid + sudahDiverifikasiInvalid);

  useEffect(() => {
    const newVerifications: Record<string, DocumentStatus> = {};

    documentFields.forEach((doc) => {
      const matchedDoc = documents.find((d) => `${d.type}` === doc.name);

      if (matchedDoc?.is_validated) {
        newVerifications[doc.name] = "verified";
      } else if (matchedDoc?.is_uploaded && !matchedDoc.is_validated) {
        newVerifications[doc.name] = "unverified";
      }
    });

    setVerifications(newVerifications);
  }, [documents]);

  if (loading) return <PageLoading />;

  return (
    <div className=" mx-auto p-6">
      <h1 className="text-xl font-bold mb-6">Verifikasi Dokumen</h1>
      <div className="bg-white rounded-xl shadow p-6 mb-6">
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 text-sm text-gray-700">
          <div>
            <p className="text-gray-500">Nama Lead</p>
            <p className="font-semibold">{lead?.name ?? "-"}</p>
          </div>
          <div>
            <p className="text-gray-500">Email</p>
            <p className="font-semibold">{lead?.email ?? "-"}</p>
          </div>
          <div>
            <p className="text-gray-500">Phone</p>
            <p className="font-semibold">{lead?.phone ?? "-"}</p>
          </div>
          <div>
            <p className="text-gray-500">Notes</p>
            <p className="font-semibold">{lead?.notes ?? "-"}</p>
          </div>
        </div>
      </div>
      <div className="text-sm text-gray-500 mb-6">
        Total dokumen: <strong>{total}</strong> | Sudah diverifikasi (Valid):{" "}
        <strong>{sudahDiverifikasiValid}</strong> | Sudah diverifikasi (Tidak
        Valid): <strong>{sudahDiverifikasiInvalid}</strong> | Belum diperiksa:{" "}
        <strong>{belumDiperiksa}</strong>
      </div>
      <div className="grid grid-cols-2 gap-6">
        {documentFields.map((doc) => {
          const matchedDoc = documents.find((d) => `${d.type}` === doc.name);

          const file_url = matchedDoc?.file_url;
          const is_uploaded = matchedDoc?.is_uploaded;
          const is_validated = matchedDoc?.is_validated;
          return (
            <div
              key={doc.name}
              className="border p-4 rounded-lg flex flex-col sm:flex-row sm:items-start gap-4"
            >
              {/* Label & Checkbox */}
                <div className="flex-1">
                  <div className="font-semibold ">{doc.label}</div>
              

                  <div className="mt-2">
                    <label className="label cursor-pointer inline-flex items-center gap-2">
                      <input
                        type="checkbox"
                        className="checkbox"
                        name={doc.name}
                        checked={verifications[doc.name] === "verified"}
                        onChange={(e) =>
                          handleStatusChange(
                            doc.name,
                            e.target.checked ? "verified" : "unverified"
                          )
                        }
                        disabled = {!file_url}
                      />
                      <span className="label-text">Verified</span>
                    </label>
                  </div>
                </div>

              {/* File Preview */}
              <div className="w-full sm:w-48">
                {file_url ? (
                  <ProtectedImage
                    imageUrl={file_url}
                    linkOnly={true}
                    label={doc.label}
                  />
                ) : (
                  <p className="text-sm text-gray-400 italic">Tidak ada file</p>
                )}
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
