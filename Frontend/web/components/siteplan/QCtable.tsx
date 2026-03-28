"use client";
import axios from "@/lib/axios";
import { getProtectedImageUrl } from "@/lib/protected-image";
import { useEffect, useState } from "react";

import { toast } from "react-hot-toast";
import ProtectedImage from "../protected-image";
import { SaveIcon, Trash } from "lucide-react";
import { useForm } from "react-hook-form";

type QCItem = {
  id?: string;
  name: string;
  is_passed: number;
  comment?: string;
  evidence?: File | null; // file baru
  evidence_url?: string; // file lama
};

const defaultData = [
  {
    id: "",
    name: "Pondasi terpasang kokoh, tidak ada retakan",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
  {
    id: "",
    name: "Bata tersusun rapi, tidak bolong, tidak miring",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
  {
    id: "",
    name: "Plester dan acian halus, tidak ada retak rambut",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
  {
    id: "",
    name: "Kusen pintu dan jendela terpasang lurus dan kokoh",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
  {
    id: "",
    name: "Atap terpasang rapi, tidak ada kebocoran",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
  {
    id: "",
    name: "Lantai keramik terpasang rata, tidak ada yang pecah",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
  {
    id: "",
    name: "Plafon rapi, tidak ada noda atau bolong",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
  {
    id: "",
    name: "Instalasi listrik berfungsi, stop kontak dan saklar aman",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
  {
    id: "",
    name: "Air lancar, keran dan sanitasi berfungsi baik",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
  {
    id: "",
    name: "Cat dinding merata, tidak mengelupas",
    is_passed: -1,
    comment: "",
    evidence: null,
    created_at: "2025-09-14T15:19:59.000000Z",
  },
];

export default function QCTable({ propertyId }: { propertyId: string }) {
  const [rows, setRows] = useState<QCItem[]>([]);
  const [loading, setLoading] = useState(false);
  const [editMode, setEditMode] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [reload,setReload] = useState(0)

  // GET list QC
  useEffect(() => {
    setRows([])
    if (!propertyId) return;
    setLoading(true);
    axios
      .get(`/property/unit-property/${propertyId}/quality-control-item`)
      .then((res) => {
        setRows(
          res.data.data.map((item: any) => ({
            id: item.id,
            name: item.name,
            is_passed: item.is_passed,
            comment: item.comment,
            evidence: null,
            evidence_url: item.evidence,
          }))
        );
      })
      .catch(() => {
        setError("Gagal ambil data QC");
      })
      .finally(() => setLoading(false));
  }, [propertyId, reload]);

  // Tambah baris baru
  const addRow = () => {
    setRows((prev) => [
      ...prev,
      {
        id: undefined,
        name: "",
        is_passed: 0,
        comment: "",
        evidence: null,
      },
    ]);
  };

  // Update state row
  const updateRow = <K extends keyof QCItem>(
    index: number,
    field: K,
    value: QCItem[K]
  ) => {
    const newRows = [...rows];
    newRows[index][field] = value;
    setRows(newRows);
  };

  const [importing, setImporting] = useState(false);

  const {
    register: registerImport,
    handleSubmit: handleSubmitImport,
    reset: resetImport,
    getValues
  } = useForm();

  
  const handleImport = async (data: any) => {
    if (!data.file || data.file.length === 0) {
      toast.error("Mohon pilih file Excel terlebih dahulu.");
      return;
    }

    const formData = new FormData();
    formData.append("file", data.file[0]);

    try {
      setImporting(true);
      await axios.post(`/property/unit-property/${propertyId}/quality-control-item/import`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      setSuccess("QC berhasil diimport!");
      handleReload()
    } catch {
      setError("Gagal mengimpor QC");
    } finally {
      setImporting(false);
      resetImport();
      (document.getElementById("import_modal_property") as HTMLDialogElement)?.close();
    }
  };

  // Save row (create/update)
  const saveRow = async (row: QCItem) => {
    const formData = new FormData();
    formData.append("name", row.name);
    formData.append("is_passed", row.is_passed ? "1" : "0");
    formData.append("comment", row.comment || "");
    if (row.evidence) {
      formData.append("evidence", row.evidence);
    }
    console.log(formData);

    try {
      if (row.id) {
        // update
        await axios.post(
          `/property/unit-property/${propertyId}/quality-control-item/${row.id}`,
          formData,
          { headers: { "Content-Type": "multipart/form-data" } }
        );
        setSuccess("QC berhasil diupdate");
      } else {
        // create
        const res = await axios.post(
          `/property/unit-property/${propertyId}/quality-control-item`,
          formData,
          { headers: { "Content-Type": "multipart/form-data" } }
        );
        row.id = res.data.data.id; // update id baru
        setSuccess("QC berhasil ditambah");
      }
      // refresh
      // atau bisa langsung update state
    } catch (err) {
      setError("Gagal simpan QC");
    }
  };

  useEffect(()=>{
    if(error || success){
      setTimeout(()=>{
        setSuccess("")
        setError("")
      },5000)
    }
  },[error,success])

  const handleDelete = async (id : string) => {
    setLoading(true);
    try {
      await axios.delete(`/property/unit-property/${propertyId}/quality-control-item/${id}`);
      setSuccess("QC berhasil dihapus!");
    } catch (err) {
      setError("Gagal menghapus qc.");
    } finally {
      setLoading(false);
      handleReload()
      // closeDeleteModal();
    }
  };

  const handleReload = () => setReload(Math.random())
  

  return (
    <div className="flex flex-col">
      {success && (
      <div className="alert alert-success shadow-sm my-4">
        <span>{success}</span>
      </div>
      )}
      {error && (
      <div className="alert alert-error shadow-sm my-4">
        <span>{error}</span>
      </div>
      )}

      <div className="flex justify-between items-center mb-2 ">
        <div className="form-control">
          <label className="label cursor-pointer">
            <span className="label-text mr-2">Edit Mode</span>
            <input
              type="checkbox"
              className="toggle toggle-success"
              checked={editMode}
              onChange={(e) => setEditMode(e.target.checked)}
            />
          </label>
        </div>
        <div>
        <button
          className="btn btn-secondary btn-sm mr-2"
          onClick={() => (document.getElementById("import_modal_property") as HTMLDialogElement)?.showModal()}
        >
          Import Excel
        </button>
          {rows.length == 0 && (
            <button
              type="button"
              onClick={async () => {
                setLoading(true);
                await Promise.all(defaultData.map((row) => saveRow(row)));
                setLoading(false);
                setRows(defaultData);
              }}
              className="btn btn-sm btn-warning mr-2"
            >
              Gunakan Template
            </button>
          )}
          {editMode && (
            <button
              type="button"
              onClick={addRow}
              className="btn btn-sm btn-primary"
            >
              + Tambah Item
            </button>
          )}
        </div>
      </div>

      {loading ? (
        <span className="loading loading-spinner"></span>
      ) : (
        <div className="overflow-x-auto">
          <table className="table table-zebra w-full">
            <thead>
              <tr>
                <th>Nama Item</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Bukti</th>
                {editMode && <th>Aksi</th>}
              </tr>
            </thead>
            <tbody>
              {rows.length == 0 && (
                <tr>
                  <td colSpan={5} className="text-center text-base-content/70">
                    Data is empty
                  </td>
                </tr>
              )}
              {rows.map((row, i) => (
                <tr key={row.id ?? i}>
                  <td>
                    {!editMode ? (
                      row.name
                    ) : (
                      <input
                        type="text"
                        className="input input-bordered w-full"
                        value={row.name}
                        onChange={(e) => updateRow(i, "name", e.target.value)}
                      />
                    )}
                  </td>
                  <td>
                    {!editMode ? (
                      row.is_passed == 1 ? (
                        "Lulus"
                      ) : row.is_passed == 0 ? (
                        "Tidak Lulus"
                      ) : (
                        "Belum Diuji"
                      )
                    ) : (
                      <select
                        className="select select-bordered select-success w-full"
                        value={
                          row.is_passed == 1
                            ? "true"
                            : row.is_passed == 0
                            ? "false"
                            : ""
                        }
                        onChange={(e) =>
                          updateRow(
                            i,
                            "is_passed",
                            e.target.value === "true"
                              ? 1
                              : e.target.value === "false"
                              ? 0
                              : -1
                          )
                        }
                      >
                        <option value="">Belum Diuji</option>
                        <option value="true">Lulus</option>
                        <option value="false">Tidak Lulus</option>
                      </select>
                    )}
                  </td>
                  <td>
                    {!editMode ? (
                      row.comment
                    ) : (
                      <input
                        type="text"
                        className="input input-bordered w-full"
                        value={row.comment || ""}
                        onChange={(e) =>
                          updateRow(i, "comment", e.target.value)
                        }
                      />
                    )}
                  </td>
                  <td>
                    {!editMode ? (
                      row.evidence_url && (
                        <ProtectedImage
                          imageUrl={
                            process.env.NEXT_PUBLIC_BE_URL +
                            row.evidence_url
                          }
                          label={"Lihat Bukti"}
                        />
                      )
                    ) : (
                      <input
                        type="file"
                        accept="image/*"
                        className="file-input file-input-bordered file-input-sm w-full mt-1"
                        onChange={(e) =>
                          updateRow(i, "evidence", e.target.files?.[0] || null)
                        }
                      />
                    )}
                  </td>
                  {editMode ? (
                    <td>
                      <button
                        type="button"
                        className="btn btn-sm btn-success"
                        onClick={() => saveRow(row)}
                      >
                          <SaveIcon />
                      </button>
                      <button
                        type="button"
                        className="ml-2 btn-sm btn btn-error"
                        onClick={() => handleDelete(row.id ?? "")}
                        // /property/unit-property/{property_id}/quality-control-item/{item_id}
                      >
                        <Trash />
                      </button>
                    </td>
                  ) : null}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
      <dialog id="import_modal_property" className="modal">
        <div className="modal-box">
          <h3 className="font-bold text-lg">Import QC dari Excel</h3>

          <a
          href={`${process.env.NEXT_PUBLIC_FILES_URL}/static/importable-qc-template.xlsx`}
          target="_blank"
          rel="noopener noreferrer"
          className="text-sm underline text-blue-500"
        >
          Download Template Excel
        </a>

          <form
            onSubmit={(e) => {
              e.stopPropagation(); // mencegah event bubbling
              e.preventDefault()
              handleSubmitImport(handleImport)(e); // jalankan handler asli
            }}
            className="mt-4 space-y-4"
          >
            <input
              type="file"
              accept=".xlsx,.xls"
              {...registerImport("file", { required: true })}
              className="file-input file-input-bordered w-full"
            />
            <div className="flex justify-end gap-2 mt-4">
              <button
                type="button"
                onClick={() => (document.getElementById("import_modal_property") as HTMLDialogElement)?.close()}
                className="btn"
              >
                Batal
              </button>
              <button
                type="button"
                onClick={()=>handleImport(getValues())}
                className="btn btn-primary"
                disabled={importing}
              >
                {importing && <span className="loading loading-spinner"></span>}
                Import
              </button>
            </div>
          </form>
        </div>
      </dialog>
    </div>
  );
}
