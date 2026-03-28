"use client";

import DataTable, { Column } from "@/components/datatable/datatable";
import { useForm } from "react-hook-form";
import axios from "@/lib/axios";
import { useEffect, useRef, useState } from "react";
import ModalForm from "@/components/datatable/modal/ModalCreateEdit";
import ModalDeleteConfirm from "@/components/datatable/modal/ModalDelete";
import toast from "react-hot-toast";
import Link from "next/link";
import axiosInstance from "@/lib/axios";
import { NumericFormat } from "react-number-format";
import { convertToRupiah, isAllowed } from "@/lib/utils";
import { Pencil, Trash2 } from "lucide-react";

interface Asset {
  id: string;
  acquisition_date: string; // format: YYYY-MM-DD
  registration_number: string;
  category_id: string;
  category_name: string;
  sub_category_id : string;
  sub_category_name: string;
  name: string;
  description: string;
  quantity: number;
  price: number;
  remaining_price: number;
  useful_life: number;
  remaining_useful_life: number;
}

export default function AssetPage() {
  const columns: Column<Asset>[] = [
    { key: "name", label: "Name", sortable: true },
    {
      key: "registration_number",
      label: "Registration Number",
      sortable: true,
    },
    { key: "category_name", label: "Category", sortable: true },
    { key: "sub_category_name", label: "Sub Category", sortable: true },
    { key: "acquisition_date", label: "Acquisition Date", sortable: true },
    { key: "quantity", label: "Quantity", sortable: true },
    
    { key: 'price', label: "Price", sortable: true, render : (item)=>{
          if(!item.price ) return "-";
          const formatted = convertToRupiah(item.price);
          return (<div>{formatted}</div>)
        } },
    { key: 'remaining_price', label: "Remaining Price", sortable: true, render : (item)=>{
      if(!item.remaining_price ) return "-";
      const formatted = convertToRupiah(item.remaining_price);
      return (<div>{formatted}</div>)
    } },
    { key: "useful_life", label: "Useful Life", sortable: true },
    {
      key: "remaining_useful_life",
      label: "Remaining Useful Life",
      sortable: true,
    },
  ];

  const {
    register,
    handleSubmit,
    watch,
    reset,
    setValue,
    formState: { errors },
  } = useForm<Asset>();

  const [modalMode, setModalMode] = useState<"create" | "edit">("create");
  const [selectedAsset, setSelectedAsset] = useState<Asset | null>(null);
  const [loading, setLoading] = useState(false);

  const openModal = (asset: Asset | null = null) => {
    const modal = document.getElementById("modify") as HTMLDialogElement;
    if (asset) {
      setModalMode("edit");
      reset(asset);
      setSelectedAsset(asset);
    } else {
      setModalMode("create");
      reset();
      setSelectedAsset(null);
    }
    modal.showModal();
  };

  const openDeleteModal = (asset: Asset) => {
    const modal = document.getElementById("delete_modal") as HTMLDialogElement;
    setSelectedAsset(asset);
    modal.showModal();
  };

  const closeModal = () => {
    (document.getElementById("modify") as HTMLDialogElement)?.close();
  };

  const closeDeleteModal = () => {
    (document.getElementById("delete_modal") as HTMLDialogElement)?.close();
  };

  const onSubmit = async (data: Asset) => {
    setLoading(true);
    try {
      if (selectedAsset) {
        await axios.put(`/asset/${selectedAsset.id}`, data);
      } else {
        await axios.post(`/asset`, data);
      }
      toast.success("Asset berhasil disimpan!");
      handleReload();
    } catch (err: any) {
      toast.error("Gagal menyimpan asset.");
    } finally {
      setLoading(false);
      closeModal();
    }
  };

  const handleDelete = async () => {
    setLoading(true);
    if (!selectedAsset) return;
    try {
      await axios.delete(`/asset/${selectedAsset.id}`);
      toast.success("Asset berhasil dihapus!");
      handleReload();
    } catch (err) {
      toast.error("Gagal menghapus asset .");
    } finally {
      setLoading(false);
      closeDeleteModal();
    }
  };

  const [categories,setCategories] = useState<{id : string; name: string}[]>([]);
  const category_id = watch("category_id")
  useEffect(()=>{
    axiosInstance.get("/asset/categories").then((response)=>{
      setCategories(response.data.data)
    })
  },[])

  const [subCategories,setSubCategories] = useState<{id : string; name: string}[]>([]);
  useEffect(()=>{
    if(category_id){
      axiosInstance.get(`/asset/sub-categories/${category_id}`).then((response)=>{
        setSubCategories(response.data.data)
      })
    }
  },[category_id])

  const handleReload = async () => {
    TableRef?.current?.reload();
  };

  const TableRef = useRef<{ reload: () => void }>(null);

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-600 mb-6">Asset Management</h1>

      <DataTable
        ref={TableRef}
        endpoint="/asset"
        onClickCreate={
          isAllowed("asset.manage_assets")?
          () => openModal() : undefined
        }
        columns={columns}
        actions={(row) => (
          <div className="flex gap-2">
            {isAllowed("asset.manage_assets") && (<button
              className="btn btn-sm btn-warning"
              onClick={() => openModal(row)}
            >
              <Pencil size={16} className="mr-1" />
            </button>)}

            {isAllowed("asset.manage_assets") && (<button
              className="btn btn-sm btn-error"
              onClick={() => openDeleteModal(row)}
            >
              <Trash2 size={16} className="mr-1" />
            </button>)}
          </div>
        )}
      />

      <ModalForm
        id="modify"
        title={modalMode === "create" ? "Create Asset" : "Edit Asset"}
        onClose={closeModal}
        onSubmit={handleSubmit(onSubmit)}
        loading={loading}
      >
        {/* Category */}
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Category</legend>
          <select
            className="input w-full"
            {...register("category_id", { required: true })}
          >
            <option value="">-- Pilih Category --</option>
            {/* mapping kategori dari API */}
            {categories.map((cat) => (
              <option key={cat.id} value={cat.id}>
                {cat.name}
              </option>
            ))}
          </select>
          {errors.category_id && (
            <p className="text-red-500 text-sm">Wajib dipilih</p>
          )}
        </fieldset>

        {/* Sub Category */}
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Sub Category</legend>
          <select
            className="input w-full"
            {...register("sub_category_id", { required: true })}
          >
            <option value="">-- Pilih Sub Category --</option>
            {/* mapping sub kategori dari API */}
            {subCategories.map((sub) => (
              <option key={sub.id} value={sub.id}>
                {sub.name}
              </option>
            ))}
          </select>
          {errors.sub_category_id && (
            <p className="text-red-500 text-sm">Wajib dipilih</p>
          )}
        </fieldset>

        {/* Name */}
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Name</legend>
          <input
            type="text"
            className="input w-full"
            {...register("name", { required: true })}
          />
          {errors.name && <p className="text-red-500 text-sm">Wajib diisi</p>}
        </fieldset>

        {/* Description */}
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Description</legend>
          <textarea className="input w-full" {...register("description")} />
        </fieldset>

        {/* Quantity */}
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Quantity</legend>
          <input
            type="number"
            className="input w-full"
            {...register("quantity", { required: true, valueAsNumber: true })}
          />
          {errors.quantity && (
            <p className="text-red-500 text-sm">Wajib diisi</p>
          )}
        </fieldset>

        {/* Price */}
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Price</legend>
          <NumericFormat
                        thousandSeparator="."
                        decimalSeparator=","
                        prefix="Rp "
                        value={watch("price")}
                        className="input input-bordered w-full"
                        {...register("price")}
                        onValueChange={(values) => {
                          const { floatValue } = values;
                          setValue("price", floatValue || 0);
                        }}
                      />
          {errors.price && (<p className="text-red-500 text-sm">Wajib diisi</p>)}
        </fieldset>

        {/* Acquisition Date */}
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Acquisition Date</legend>
          <input
            type="date"
            className="input w-full"
            {...register("acquisition_date", { required: true })}
          />
          {errors.acquisition_date && (
            <p className="text-red-500 text-sm">Wajib diisi</p>
          )}
        </fieldset>

        {/* Useful Life */}
        <fieldset className="fieldset w-full mt-4">
          <legend className="fieldset-legend">Useful Life (bulan)</legend>
          <input
            type="number"
            className="input w-full"
            {...register("useful_life", {
              required: true,
              valueAsNumber: true,
            })}
          />
          {errors.useful_life && (
            <p className="text-red-500 text-sm">Wajib diisi</p>
          )}
        </fieldset>
      </ModalForm>

      <ModalDeleteConfirm
        id="delete_modal"
        onClose={closeDeleteModal}
        onConfirm={handleDelete}
        loading={loading}
        message={`Are you sure you want to delete asset "${selectedAsset?.name}"?`}
      />
    </div>
  );
}
