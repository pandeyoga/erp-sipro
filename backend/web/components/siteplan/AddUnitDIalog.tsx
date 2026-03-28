"use client";

import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useSitemapContext } from "@/context/useSitemapContext";
import { UnitBoxProps } from "@/types/unit";
import axios from "@/lib/axios"; // pastikan axios-nya sudah setup pakai token Bearer
import { useSearchParams } from "next/navigation";
import { PropertyUnit } from "@/app/(inside)/property/siteplan/page";

type UnitFormValues = {
  label: string;
  property_id : string;
  unit_number: string;
  status: string;
  top: number;
  left: number;
  width: number;
  height: number;
};

type UnitData = Record<string, Record<string, Unit[]>>;

export function AddUnitDialog() {
  const [open, setOpen] = useState(false);
  const { addUnit, project, containerSize, convertToPercent } = useSitemapContext();

  const [units, setUnits] = useState<UnitData>();
  const [loading, setLoading] = useState(true);

  const {
    register,
    handleSubmit,
    reset,
    watch,
    setValue,
    formState: { errors },
  } = useForm<UnitFormValues >({
    defaultValues: {
      label: "",
      status: "available",
      
    },
  });

  const property_id = watch('property_id');

  // Ambil data unit dari API
  useEffect(() => {
    const fetchUnits = async () => {
      try {
        if(project?.id){
          const res = await axios.get(`/property/siteplan/${project?.id}/list-option-property`);
          setUnits(res.data.data || []);
        }
      } catch (err) {
        console.error("Gagal memuat unit", err);
      } finally {
        setLoading(false);
      }
    };
    fetchUnits();
  }, [project]);

  const onSubmit = (data: UnitFormValues) => {
    const newUnit: UnitBoxProps = {
      ...data,
      id: Math.floor(Math.random() * 1000000).toString(),
      top: convertToPercent(50,'top'),
      left: convertToPercent(50,'left'),
      width: convertToPercent(100,'width'),
      height: convertToPercent(50,'height'),
      
    };
    console.log('newUnit',newUnit)
    addUnit(newUnit);
    reset();
    setOpen(false);
  };

  function findUnitById(data: UnitData, property_id: string): Unit | undefined {
    for (const cluster of Object.values(data)) {
      for (const tipeUnits of Object.values(cluster)) {
        const found = tipeUnits.find((unit) => unit.id === property_id);
        if (found) return found;
      }
    }
    return undefined;
  }

  useEffect(()=>{
    if(property_id && units){
      const unit = findUnitById(units, property_id);
      setValue('status',unit?.status ?? 'available')
      setValue('unit_number', unit?.unit_number ?? '')
      setValue('label', unit?.unit_number ?? '')
    }
  },[property_id])

  return (
    <>
      <button className="btn btn-success" onClick={() => setOpen(true)}>
        Tambah Unit Baru
      </button>

      {open && (
        <dialog className="modal modal-open">
          <div className="modal-box space-y-4">
            <h3 className="font-bold text-lg">Tambah Unit Baru</h3>

            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              <div className="form-control w-full space-y-4">
                {/* Pilih Label dari Type Unit */}
                <div>
                  <label className="label">
                    <span className="label-text">Unit Property</span>
                  </label>
                  {loading ? (
                    <div className="skeleton h-12 w-full rounded"></div>
                  ) : (
                    <UnitSelect register={register} data={units ?? {}}/>
                  )}
                  {errors.label && (
                    <p className="text-error text-sm mt-1">
                      {errors.label.message}
                    </p>
                  )}
                </div>
              </div>

              <div className="modal-action">
                <button type="submit" className="btn btn-primary">
                  Tambah
                </button>
                <button
                  type="button"
                  className="btn"
                  onClick={() => {
                    reset();
                    setOpen(false);
                  }}
                >
                  Batal
                </button>
              </div>
            </form>
          </div>
        </dialog>
      )}
    </>
  );
}


type Unit = {
  id: string;
  unit_number: string;
  status: string;
};



interface UnitSelectProps {
  data: UnitData | undefined;
  register: any;
}

export const UnitSelect: React.FC<UnitSelectProps> = ({ data, register }) => {
  return (
    <select
      name="unit_id"
      className="select select-bordered w-full"
      {...register("property_id", {
            required: "Nama unit wajib dipilih",
          })}
    >
      <option value="" disabled>
        Pilih Unit
      </option>
      {data && Object.entries(data).map(([cluster, tipeMap]) =>
        Object.entries(tipeMap).map(([tipe, units]) => (
          <optgroup key={`${cluster}-${tipe}`} label={`${cluster} - ${tipe}`}>
            {units.map((unit) => (
              <option key={unit.id} value={unit.id}>
                Unit {unit.unit_number} ({unit.status})
              </option>
            ))}
          </optgroup>
        ))
      )}
    </select>
  );
};

export default UnitSelect;

