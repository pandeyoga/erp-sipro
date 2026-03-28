

"use client";
import { useParams } from "next/navigation";
import ConstructionProgress from "./section/ConstuctionProgress";
import HeaderUnit from "./section/HeaderUnit";
import NotesSection from "./section/Notes";
import PaymentDetail from "./section/PaymentDetail";
import RetentionCases from "./section/RetentionCases";
import { useEffect, useState } from "react";
import { PropertyUnit } from "../../../page";
import axios from "@/lib/axios";

export default function DashboardUnit() {
  const { id } = useParams() as { id: string }

  const [unit, setUnit] = useState<PropertyUnit | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!id) return;
    setLoading(true);
    setError(null)
    axios
      .get(`/property/unit-property/${id}`)
      .then((res) => {
        setUnit(res.data.data);
      })
      .catch((err) => {
        console.error("❌ Gagal fetch unit detail:", err);
        setError("Gagal memuat unit detail")
      })
      .finally(() => {
        setLoading(false);
      });
  }, [id]);
  const data : any = unit;
  if (!data) return null;

  return (
    <div className="container mx-auto p-6 space-y-4">
      <HeaderUnit data={unit} />
      <ConstructionProgress
        data={data.construction_documentation}
        progress={data.construction_progress}
        status={data.construction_status}
      />
      <RetentionCases cases={data.retention_cases} />
      <PaymentDetail payment={data.payment} />
      <NotesSection notes={data.notes} subContractor={data.sub_contractor} />
    </div>
  );
}
