"use client";

import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useSitemapContext } from "@/context/useSitemapContext";
import { UnitBoxProps } from "@/types/unit";
import axios from "@/lib/axios"; // pastikan axios-nya sudah setup pakai token Bearer
import { useSearchParams } from "next/navigation";
import { PropertyUnit } from "@/app/(inside)/property/siteplan/page";
import ModalForm from "../datatable/modal/ModalCreateEdit";
import toast from "react-hot-toast";
import { Cluster } from "@/app/(inside)/property/cluster/page";
import QcTable from "./QCtable";

interface UnitFormValues extends PropertyUnit {
  top: number;
  left: number;
}

interface UnitFromAPI {
  id: string;
  type: string;
}
export type Row = {
  id: string
  name: string
  lulus: boolean
  tidak: boolean
}

export function QCUnitModal({ modalMode, unit, unitTableRef }: { modalMode: string, unit : any, unitTableRef : any }) {
  const [units, setUnits] = useState<UnitFromAPI[]>([]);
  const [clusters, setClusters] = useState<{ id: string; name: string }[]>([]);
  const [loading, setLoading] = useState(true);

  const { project } = useSitemapContext();

  const {
    register,
    handleSubmit,
    reset,
    setValue,
    formState: { errors },
  } = useForm<UnitFormValues>({
    defaultValues: {
      unit_number: "",
      unit_type_id: "",
      cluster_id: "",
      cluster_name: "",
      top: 50,
      left: 50,
    },
  });

  // Ambil data unit dari API
  useEffect(() => {
    const fetchUnits = async () => {
      try {
        const res = await axios.get("/property/unit-property/unit-types-list");
        setUnits(res.data.data || []);
      } catch (err) {
        console.error("Gagal memuat unit", err);
      } finally {
        setLoading(false);
      }
    };
    const fetchCluster = async () => {
      axios
        .get(`/property/construction/cluster-lists/${project?.id}`)
        .then((res) => setClusters(res.data.data))
        .catch(() => toast.error('Gagal mengambil cluster'))
    }
    fetchUnits();
    fetchCluster();
    if(unit){
      reset(unit)
    }else{
      reset()
    }
  }, [unit]);

  const onSubmit = async () => {
   
  }
  

  const closeModal = () => {
    (document.getElementById("modify_unit") as HTMLDialogElement)?.close();
  };

  function resetLulusTidak(data: any): any {
    return data.map((item: any) => ({
      ...item,
      lulus: false,
      tidak: false,
    }));
  }

  return (
    <ModalForm
      id="qc_unit"
      title="Quality Control"
      onClose={closeModal}
      onSubmit={handleSubmit(onSubmit)}
      loading={loading}
      width="3xl"
      disableAction = {true}
    >
      {unit && (
        <div className="w-full space-y-4">
          <QcTable propertyId={unit.id}/>
        </div>
      )}
    </ModalForm>
  );
}
