"use client";

import { useForm } from "react-hook-form";
import { useRouter } from "next/navigation";
import axios from "@/lib/axios";
import toast from "react-hot-toast";
import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import SelectGroup, { OptionData } from "@/components/common/selectGroup";
import { NumericFormat } from "react-number-format";
import UnitSelect from "@/components/siteplan/AddUnitDIalog";

interface Props {
  mode: "create" | "edit";
  id?: string;
  handleReload ?: any;
}

type FormValues = {
  category_id: string;
  sub_category_id: string;
  bank_account_id: string;
  bank_account: string;
  total_amount: number;
  description: string;
  notes: string;
  project_id : string;
  property_id : string;
  property_name : string;
  category : string;
  sub_category : string;
};

type FormInfoType = {
  [key : string] : {
    id : string
    name : string
    total_amount : string
    paid_amount : string
    bank_account_id : string
    bank_account : string
  }[]
}

export default function CashOutForm({ mode, id, handleReload }: Props) {
  const router = useRouter();
  const { register, handleSubmit, watch, reset, setValue } = useForm<FormValues>();
  const [loading, setLoading] = useState(false);
  const [formInfo, setFormInfo] = useState<FormInfoType>();
  const [reload,setReload] = useState(0)

  // Ambil data jika edit
  useEffect(() => {
    if (mode === "edit" && id) {
      axios
        .get(`/finance/cash-out/${id}`)
        .then((res) => {
          const data = res.data.data;
          setFormInfo({
            "general": [
              {
                "id": "01984532-0642-7282-b12e-e2e55289115a",
                "name": "general",
                "total_amount": parseFloat(data.total_amount).toString(),
                "paid_amount": "0.00",
                "bank_account_id": "0198f9bb-33e1-72ac-8a98-63ae21771896",
                "bank_account": "bank BJB"
              }
            ],
          })
          console.log("cashin_data",data);
          reset({
            category_id: data.category_id,
            sub_category_id: data.sub_category_id,
            category: data.category,
            bank_account_id : data.bank_account_id,
            bank_account : data.bank_account,
            sub_category: data.sub_category,
            property_name: data.property_name,
            total_amount: parseFloat(data.total_amount),
            description: data.description,
            notes: data.description,
          });
        })
        .catch(() => toast.error("Gagal mengambil data"));
    }
  }, [id, mode, reset, reload]);

  const handleReloadCashin = () =>{
    setReload((a)=>a+1)
  }

  const onSubmit = async (data: FormValues) => {
    try {
      setLoading(true);

      if (mode === "edit" && id) {
        await axios.put(`/finance/cash-out/${id}`, data);
        toast.success("Cash Out berhasil diperbarui");
      } else {
        await axios.post("/finance/cash-out", data);
        toast.success("Cash Out berhasil ditambahkan");
      }

      router.push("/finance/cash-out");
    } catch (err: any) {
      toast.error("Gagal menyimpan data");
    } finally {
      setLoading(false);
      handleReload()
    }
  };

  const [bank_list, setBankLists] = useState<{ id: string; name: string }[]>(
    []
  );
  useEffect(() => {
    axiosInstance.get("/finance/cash-in/bank-list").then((response) => {
      setBankLists(response.data.data);
    });
  }, []);

  const [categories,setCategories] = useState<{id : string; name: string}[]>([]);
  const category_id = watch("category_id")
  useEffect(()=>{
    axiosInstance.get("/finance/cash-out/categories").then((response)=>{
      setCategories(response.data.data)
    })
  },[])

  const [subCategories,setSubCategories] = useState<{id : string; name: string}[]>([]);
  const sub_category_id = watch("sub_category_id")
  useEffect(()=>{
    if(category_id){
      axiosInstance.get(`/finance/cash-out/sub-categories/${category_id}`).then((response)=>{
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
        <legend className="text-lg font-semibold px-2">Cash Out info</legend>
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
        <div className="form-control">
          <label className="label">
            <span className="label-text">Bank</span>
          </label>
          {mode == "create" ? (
            <select
              className="select select-bordered w-full"
              {...register("bank_account_id", { required: true })}
            >
              <option value="">-- Pilih Bank --</option>
              {bank_list.map((value) => (
                <option key={value.id} value={value.id}>
                  {value.name}
                </option>
              ))}
            </select>
          ) : (
            <input
              type="text"
              readOnly
              className="input input-bordered w-full"
              {...register("bank_account")}
            />
          )}
        </div>
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
              value={watch('total_amount')}
              className="input input-bordered w-full"
              {...register("total_amount")}
              onValueChange={(values) => {
                const { floatValue } = values;
                setValue("total_amount", floatValue || 0);
              }}
            />
          </div>
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
      <div className="flex justify-between">
        <button
          type="submit"
          className="btn btn-primary ml-auto"
          disabled={loading}
        >
          {loading && <span className="loading loading-spinner"></span>}
          {mode === "edit" ? "Update" : "Simpan"}
        </button>
      </div>
      {mode == "edit" && formInfo && <PaymentInfoParent bank_list={bank_list} bank_account_id_default={watch("bank_account_id")} id={id} formInfo={formInfo} handleReload={handleReload} handleReloadCashin={handleReloadCashin}/>}
    </form>
  );
}

function PaymentInfoParent({
  formInfo,
  handleReload,
  handleReloadCashin,
  id,
  bank_list,
  bank_account_id_default
}: {
  formInfo: FormInfoType;
  id: string | undefined;
  handleReload: any;
  handleReloadCashin: any;
  bank_list: any;
  bank_account_id_default : string;
}) {
  return (
    <fieldset className="border border-base-300 rounded-xl p-6 space-y-1">
      
      <legend className="text-lg font-semibold px-2 capitalize">
        Payment Info
      </legend>
      {Object.entries(formInfo).flatMap(([key, items]) => (
        <fieldset
          key={key}
          className="border border-base-300 rounded-xl p-6 space-y-1"
        >
          <legend className="text-lg font-semibold px-2 capitalize">
            {key}
          </legend>
          <div className="flex flex-col gap-4">
            {items.map((child_form) => (
              <PaymentInfo
                handleReload={handleReload}
                key={child_form.id}
                child_form={child_form}
                cash_in_id={id}
                handleReloadCashin={handleReloadCashin}
                bank_list={bank_list}
                bank_account_id_default={bank_account_id_default}
              />
            ))}
          </div>
        </fieldset>
      ))}
    </fieldset>
  );
}

function PaymentInfo({
  child_form,
  cash_in_id,
  handleReload,
  handleReloadCashin,
  bank_list,
  bank_account_id_default
}: {
  cash_in_id: string | undefined;
  child_form: {
    id: string;
    name: string;
    total_amount: number | string;
    paid_amount: number | string;
  };
  handleReload: any;
  handleReloadCashin: any;
  bank_list: any;
  bank_account_id_default: string;
}) {
  const { register, reset, setValue, getValues, watch } = useForm<any>({
    defaultValues: {
      total_amount: parseFloat(child_form.total_amount as string),
      paid_amount: parseFloat(child_form.paid_amount as string),
      amount: null
    },
  });

  useEffect(() => {
    reset({
      ...getValues(),
      paid_amount: parseFloat(child_form.paid_amount as string),
      bank_account_id : bank_account_id_default
    });
  }, [child_form.paid_amount]);

  const [loading, setLoading] = useState(false);

  const onSubmit = async () => {
    const data = getValues();
    try {
      setLoading(true);
      await axios.put(`/finance/cash-out/${cash_in_id}/transaction`, {
        cash_in_id: child_form.id,
        total_amount: data.total_amount,
        amount: data.amount,
        date: data.date,
        bank_account_id: data.bank_account_id,
        notes: "",
      });
      handleReload();
      handleReloadCashin();
      toast.success("transaksi berhasil ditambahkan");
    } catch (err: any) {
      toast.error("Gagal menyimpan data");
    } finally {
      setLoading(false);
    }
  };
  return (
    <div className="grid grid-cols-6 gap-4 border-1 rounded-lg p-4 border-gray-200">
      <div className="grid grid-cols-1 md:grid-cols-3 col-span-5 gap-4">
        {/* Total Paid */}
        <div className="form-control">
          <label className="label">
            <span className="label-text capitalize">
              Total Amount {child_form.name}
            </span>
          </label>
          <NumericFormat
            thousandSeparator="."
            decimalSeparator=","
            prefix="Rp "
            value={watch("total_amount")}
            className="input input-bordered w-full"
            {...register("total_amount")}
            onValueChange={(values) => {
              const { floatValue } = values;
              setValue("total_amount", floatValue || 0);
            }}
          />
        </div>

        {/* Total Paid */}
        <div className="form-control">
          <label className="label">
            <span className="label-text capitalize">
              Paid Amount {child_form.name}
            </span>
          </label>
          <NumericFormat
            thousandSeparator="."
            decimalSeparator=","
            prefix="Rp "
            readOnly
            value={watch("paid_amount")}
            className="input input-bordered w-full"
            {...register("paid_amount")}
            onValueChange={(values) => {
              const { floatValue } = values;
              setValue("paid_amount", floatValue || 0);
            }}
          />
        </div>

        <div className="form-control">
          <label className="label">
            <span className="label-text capitalize">
              Remaining Amount {child_form.name}
            </span>
          </label>
          <NumericFormat
            thousandSeparator="."
            decimalSeparator=","
            prefix="Rp "
            readOnly
            value={watch("total_amount") - watch("paid_amount")}
            className="input input-bordered w-full"
          />
        </div>

        <div className="form-control">
          <label className="label">
            <span className="label-text capitalize">
              Total Paid {child_form.name}
            </span>
          </label>
          <NumericFormat
            thousandSeparator="."
            decimalSeparator=","
            prefix="Rp "
            className="input input-bordered w-full"
            {...register("amount")}
            onValueChange={(values) => {
              const { floatValue } = values;
              setValue("amount", floatValue ?? 0);
            }}
          />
        </div>

        <div className="form-control ">
          <label className="label">
            <span className="label-text">Bank</span>
          </label>
          <select
            className="select select-bordered w-full"
            {...register("bank_account_id", { required: true })}
          >
            <option value="">-- Pilih Bank --</option>
            {bank_list.map((value: any) => (
              <option key={value.id} value={value.id}>
                {value.name}
              </option>
            ))}
          </select>
        </div>

        <div className="form-control ">
          <label className="label">
            <span className="label-text">Date</span>
          </label>
          <input
            type="date"
            {...register("date")}
            className="input input-bordered w-full"
          />
        </div>
        
      </div>
      <div className="grid">
          {/* Submit Button */}
          <div className="form-control flex justify-end items-end col-span-2">
            <button
              type="button"
              className="btn btn-primary w-full block"
              disabled={loading}
              onClick={onSubmit}
            >
              Confirm
            </button>
          </div>
        </div>
    </div>
  );
}
