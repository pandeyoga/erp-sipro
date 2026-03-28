"use client";

import { useForm } from "react-hook-form";
import { useRouter } from "next/navigation";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import React, { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import SelectGroup, { OptionData } from "@/components/common/selectGroup";
import { NumericFormat } from "react-number-format";
import UnitSelect from "@/components/siteplan/AddUnitDIalog";
import { boolean } from "zod";
import { isAllowed } from "@/lib/utils";
import FileDropzone from "@/components/dropzone";

interface Props {
  mode: "create" | "edit";
  id?: string;
  handleReload ?: any;
}

type FormValues = {
  category_id: string;
  sub_category_id: string;
  amount: number;
  status:string;
  description: string;
  notes: string;
  project_id : string;
  property_id : string;
  property_name : string;
  category : string;
  sub_category : string;
  file_proof : FileList | string;
};

type FormInfoType = {
  [key : string] : {
    id : string
    name : string
    total_amount : string
    paid_amount : string
  }[]
}

export default function SubmissionForm({ mode, id, handleReload }: Props) {
  const router = useRouter();
  const { register, handleSubmit, watch, reset, setValue } = useForm<FormValues>();
  const [loading, setLoading] = useState(false);
  const [formInfo, setFormInfo] = useState<FormInfoType>();
  const [reload,setReload] = useState(0)

  // Ambil data jika edit
  useEffect(() => {
    if (mode === "edit" && id) {
      axios
        .get(`/finance/submission/${id}`)
        .then((res) => {
          const data = res.data.data;
          setFormInfo(data.child)
          console.log("Submission_data",data);
          reset({
            category_id: data.category_id,
            sub_category_id: data.sub_category_id,
            category: data.category,
            status: data.status,
            sub_category: data.sub_category,
            property_name: data.property_name,
            amount: parseFloat(data.amount),
            description: data.description,
            notes: data.description,
            file_proof : data.file_proof
          });
        })
        .catch(() => toast.error("Gagal mengambil data"));
    }
  }, [id, mode, reset, reload]);

  const handleReloadSubmission = () =>{
    setReload((a)=>a+1)
  }

  const status = watch('status')

  const onSubmit = async (data: FormValues) => {
    try {
      setLoading(true);
      console.log(data)

      const formData = new FormData();
      Object.entries(data).forEach(([key, value]) => {
        if(value){
          if (key == "file_proof" && typeof value != "string") {
            formData.append(key, value as any);
          } else if(!(key == "file_proof") && typeof value == "string" || typeof value == "number") {
            formData.append(key, value as string);
          }
        }
      });

      formData.append("type","submission")

      if (mode === "edit" && id) {
        await axios.put(`/finance/submission/${id}`, formData,
          {
            headers: { 'Content-Type': 'multipart/form-data' },
          });
        toast.success("Submission berhasil diperbarui");
      } else {
        await axios.post("/finance/submission", formData,
          {
            headers: { 'Content-Type': 'multipart/form-data' },
          });
        toast.success("Submission berhasil ditambahkan");
      }

      router.push("/finance/submission");
    } catch (err: any) {
      toast.error("Gagal menyimpan data");
    } finally {
      setLoading(false);
      handleReload()
    }
  };

  const handleReject = async () => {
    if (id) {
      setLoading(true);
      await axios.put(`/finance/submission/${id}/reject`);
      setLoading(false);
      toast.success("Submission berhasil di reject");
    }
  }

  const handleApprove = async () => {
    if (id) {
      setLoading(true);
      await axios.put(`/finance/submission/${id}/approve`);
      setLoading(false);
      toast.success("Submission berhasil di approve");
    }
  }

  const [categories,setCategories] = useState<{id : string; name: string}[]>([]);
  const category_id = watch("category_id")
  useEffect(()=>{
    axiosInstance.get("/finance/submission/categories").then((response)=>{
      setCategories(response.data.data)
    })
  },[])

  const [subCategories,setSubCategories] = useState<{id : string; name: string}[]>([]);
  const sub_category_id = watch("sub_category_id")
  useEffect(()=>{
    if(category_id){
      axiosInstance.get(`/finance/submission/sub-categories/${category_id}`).then((response)=>{
        setSubCategories(response.data.data)
      })
    }
  },[category_id])


  type Unit = {
    id: string;
    unit_number: string;
    status: string;
  };  
  type UnitData = Record<string, Record<string, Unit[]>>;
  const [units, setUnits] = useState<UnitData>();
  const [projects, setProjects] = useState([]);
  const [showProperty, setShowProperty] = useState(false);
  const project_id = watch('project_id');

  // Ambil data unit dari API
  useEffect(() => {
    const fetchUnits = async () => {
      try {
        if(project_id){
          const res = await axios.get(`/property/siteplan/${project_id}/list-option-property`);
          setUnits(res.data.data || []);
        }
      } catch (err) {
        console.error("Gagal memuat unit", err);
      } finally {
        setLoading(false);
      }
    };
    fetchUnits();
  }, [project_id]);

  // Fetch projects for select
  useEffect(() => {
    axios.get('/property/projects').then((res)=>{
      setProjects(res.data.data || []);
    })
  }, []);

  useEffect(()=>{
    if(mode == "create"){
      reset({
        category_id
      })
      setUnits({})
    }
    const penjualan_rumah :any = categories.find((value)=> value.name == 'Penjualan Rumah')
    if(penjualan_rumah){
      setShowProperty(penjualan_rumah.id == category_id ? true : false)
    }
  },[category_id])
  

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <fieldset className="border border-base-300 rounded-xl p-6 space-y-1">
        <legend className={"text-lg font-semibold px-2 capitalize " + (status == 'approved' ? 'text-green-500' : status == 'rejected' ? 'text-red-500' :  '')}> {status == 'pending' || !status  ? "": `( Submission has been ${status} )`}</legend>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="form-control">
            <label className="label">
              <span className="label-text">Category</span>
            </label>
            {
              mode == "create" ?
              (
                <select
                  className="select select-bordered w-full"
                  {...register("category_id", { required: true })}
                >
                  <option value="">-- Pilih Kategori --</option>
                  {categories.map((value)=>(
                    <option key={value.id} value={value.id}>{value.name}</option>
                  ))}
                </select>
              ) :
              (
                <input
                  type="text"
                  readOnly
                  className="input input-bordered w-full"
                  {...register("category")}
                />
              )
            }
            
          </div>
          <div className="form-control">
            <label className="label">
              <span className="label-text">Sub Category</span>
            </label>
            {
              mode == "create" ?
              (
                <select
                  className="select select-bordered w-full"
                  {...register("sub_category_id", { required: true })}
                >
                  <option value="">-- Pilih Sub Kategori --</option>
                  {subCategories.map((value)=>(
                    <option key={value.id} value={value.id}>{value.name}</option>
                  ))}
                </select>
              ):
              (
                <input
                  type="text"
                  readOnly
                  className="input input-bordered w-full"
                  {...register("sub_category")}
                />
              )
            }
          </div>
        </div>
        {
          mode == 'edit' && watch('property_name') ? (
            <div className="form-control">
            <label className="label">
              <span className="label-text">Property</span>
            </label>
            <input
                  type="text"
                  readOnly
                  className="input input-bordered w-full"
                  {...register("property_name")}
                />
                </div>
          ) : null
        }
        {
          mode == 'create' && showProperty ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="form-control">
              <label className="label">
                <span className="label-text">Project</span>
              </label>
              <select
                className="select select-bordered w-full"
                {...register("project_id", { required: true })}
              >
                <option value="">-- Pilih Project --</option>
              {
                projects && projects.map((value : any)=>(
                  <option key={value.id} value={value.id}>{value.name}</option>
                ))
              }
              </select>
            </div>
            <div className="form-control">
              <label className="label">
                <span className="label-text">Type (Property)</span>
              </label>
              {
                <UnitSelect data={units} register={register} />
              } 
            </div>
          </div>
          ) : null
        }
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="form-control">
            <label className="label">
              <span className="label-text">Description</span>
            </label>
            <input
              type="text"
              className="input input-bordered w-full"
              {...register("description", { required: true })}
            />
          </div>
          <div className="form-control">
            <label className="label">
              <span className="label-text">Total Amount</span>
            </label>
            <NumericFormat
              thousandSeparator="."
              decimalSeparator=","
              prefix="Rp "
              value={watch('amount')}
              className="input input-bordered w-full"
              {...register("amount")}
              onValueChange={(values) => {
                const { floatValue } = values;
                setValue("amount", floatValue || 0);
              }}
            />
          </div>
          <FileDropzone
              name="file_proof"
              label="Upload Proof"
              setValue={setValue}
              className="col-span-2"
              initialPreviewSrc={watch("file_proof")}
            />
        </div>
        <div className="grid grid-cols-1 md:grid-cols-1 gap-4">
          
          <div className="form-control">
            <label className="label">
              <span className="label-text">Notes</span>
            </label>
            <input
              type="text"
              className="input input-bordered w-full"
              {...register("notes")}
            />
          </div>
        </div>
      </fieldset>
      <div className="flex gap-4">
        { mode == 'edit' && isAllowed('finance.approval_submission') && 
          (
            <React.Fragment>
            <button
              type="button"
              className="btn btn-error ml-auto"
              disabled={loading || status != 'pending'}
              onClick={handleReject}
            >
              {loading && <span className="loading loading-spinner"></span>}
              Reject
            </button>
            <button
              type="button"
              className="btn btn-success "
              disabled={loading || status != 'pending'}
              onClick={handleApprove}
            >
              {loading && <span className="loading loading-spinner"></span>}
              Approve
            </button>
            </React.Fragment>
          )
        }
        {
          isAllowed('finance.create_submission') && (
            <button
              type="submit"
              className="btn btn-primary ml-auto"
              disabled={loading || (status != 'pending' && status != null )}
            >
              {loading && <span className="loading loading-spinner"></span>}
              {mode === "edit" ? "Update" : "Simpan"}
            </button>
          )
        }
      </div>
    </form>
  );
}