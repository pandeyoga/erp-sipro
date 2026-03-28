"use client";
import { createContext, useContext, useEffect, useState } from "react";
import { UnitBoxProps } from "@/types/unit";
import axios from "@/lib/axios";

type ProjectDetail = {
  id: string;
  name: string;
  location: string;
  developer: string;
  area_total_sqm: string;
  start_date: string;
  status: string;
  site_plan_image: string | null;
};

type SitemapContextType = {
  units: UnitBoxProps[];
  updateUnitPosition: (id: string, x: number, y: number, rotate : number) => void;
  updateUnitById: (id: string, partial: Partial<UnitBoxProps>) => void;
  addUnit: (unit: UnitBoxProps) => void;
  project: ProjectDetail | null;
  setProject: (project: ProjectDetail) => void;
  containerSize: { width: number; height: number };
  setContainerSize: (value: { width: number; height: number }) => void;
  convertToPx:(percentValue : number, prop : 'top' | 'left' | 'width' | 'height' ) => number;
  convertToPercent:(pxValue : number, prop : 'top' | 'left' | 'width' | 'height') => number;
  editMode: boolean;
  setEditMode: (value : boolean) => void;
};

const SitemapContext = createContext<SitemapContextType | undefined>(undefined);

export const SitemapProvider = ({
  children,
}: {
  children: React.ReactNode;
}) => {
  const [units, setUnits] = useState<UnitBoxProps[]>([]);
  const [project, setProject] = useState<ProjectDetail | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [containerSize, setContainerSize] = useState({ width: 0, height: 0 });
  const [editMode, setEditMode] = useState(false);

  const convertToPx = (percentValue : number, prop : 'top' | 'left' | 'width' | 'height') => { 
    let y = (prop == 'left' || prop == 'width') ? containerSize.width : containerSize.height
    return percentValue * y
  }

  const convertToPercent = (pxValue : number, prop : 'top' | 'left' | 'width' | 'height') => { 
    let y = (prop == 'left' || prop == 'width') ? containerSize.width : containerSize.height
    return pxValue / y
  }
  

  const updateUnitPosition = (id: string, x: number, y: number, rotate : number) => {
    const previousUnits = [...units];
    const unitBeforeUpdate = previousUnits.find((u) => u.id === id);
    if (!unitBeforeUpdate || !project?.id) return;
  
    // Optimistic update
    setUnits((prev) =>
      prev.map((u) => (u.id === id ? { ...u, left: x, top: y, rotate : rotate } : u))
    );
  
    axios
      .put(`/property/siteplan/${project.id}/${id}`, {
        top: y,
        left: x,
        width: unitBeforeUpdate.width ?? convertToPercent(100,'width'),
        height: unitBeforeUpdate.height ?? convertToPercent(100,'height'),
        rotate: unitBeforeUpdate.rotate ?? 0,
      })
      .then((res) => {
        console.log("✅ Unit position updated successfully:", res.data);
        console.log("TES1",{top: y,
          left: x})
        // toast.success("Posisi unit berhasil diperbarui");
      })
      .catch((err) => {
        setUnits(previousUnits); // rollback
        console.error("❌ Failed to update unit position:", err);
        // toast.error("Gagal memperbarui posisi unit");
      });
  };
  

  const updateUnitById = (id: string, partial: Partial<UnitBoxProps>) => {
    const previousUnits = [...units];
    setUnits((prev) =>
      prev.map((u) => (u.id === id ? { ...u, ...partial } : u))
    );

    const targetUnit = units.find((u) => u.id === id);
    if (!targetUnit || !project?.id || !targetUnit.id) return;

    // Buat data final setelah merge perubahan parsial
    const updated = { ...targetUnit, ...partial };
    console.log({top: updated.top ?? 0,
      left: updated.left ?? 0})

    axios
      .put(`/property/siteplan/${project.id}/${updated.id}`, {
        top: updated.top ?? 0,
        left: updated.left ?? 0,
        width: updated.width ?? convertToPercent(100,'width'),
        height: updated.height ?? convertToPercent(100,'height'),
        rotate: updated.rotate ?? 0,
      })
      .then((res) => {
        
        console.log("TES2",{top: updated.top ?? 0,
          left: updated.left ?? 0})
        // toast.success("Unit berhasil diperbarui");
      })
      .catch((err) => {
        console.error("❌ Failed to update unit via partial:", err);
        setUnits(previousUnits)
        // toast.error("Gagal memperbarui unit");
      });
  };

  const addUnit = (unit: UnitBoxProps) => {
    axios
      .post(`/property/siteplan/${project?.id}`, {
        property_id: unit.property_id,
        top: unit.top ?? convertToPercent(30,'top'),
        left: unit.left ?? convertToPercent(30,'left'),
        width: unit.width??convertToPercent(100,'width'),
        height: unit.height??convertToPercent(100,'height'),
        rotate: unit.rotate??0,
      })
      .then((response) => {
        console.log("✅ Unit berhasil disimpan ke backend:", response.data);
        const unit_id = response.data.data.unit_id

        setUnits((prev) => [
          ...prev, {
            ...unit,
            id : unit_id
          }]);
        // Tambahkan notifikasi sukses (opsional)
        // toast.success("Unit berhasil ditambahkan ke site plan");
      })
      .catch((error) => {
        console.error("❌ Gagal menyimpan unit:", error);
        // Tambahkan notifikasi error (opsional)
        // toast.error("Gagal menyimpan unit. Silakan coba lagi.");
      });
  };

  useEffect(()=>{
    function fetchUnits(){
      axios
      .get(`/property/siteplan/${project?.id}`)
      .then((response) => {
        const data = response.data.data

        const units_data = data.units;

        const targetUnit = units_data.find((u:any) => u.id == "0199b7c4-23c9-70ce-95b5-2f367c1f94da");

        console.log({"top" : targetUnit.top, "left" : targetUnit.left, width : targetUnit.width, height : targetUnit.height})

        const allUnits = units_data.map((unit: any) => ({
          id: unit.id,
          left: parseFloat(unit.left),
          top: parseFloat(unit.top),
          label: unit.unit_number,
          status: unit.status,
          width: parseFloat(unit.width),
          height: parseFloat(unit.height),
          rotate: parseFloat(unit.rotate)
        }));
        
        setUnits(allUnits);
        
      }).finally(()=>setLoaded(true))
    }
    if(project && (!loaded)){
      fetchUnits()
    }
  },[project])

  return (
    <SitemapContext.Provider
      value={{
        units,
        updateUnitPosition,
        updateUnitById,
        addUnit,
        project,
        setProject,
        containerSize, setContainerSize,
        convertToPercent, convertToPx, editMode, setEditMode
      }}
    >
      {children}
    </SitemapContext.Provider>
  );
};

export const useSitemapContext = () => {
  const context = useContext(SitemapContext);
  if (!context) throw new Error("useSitemapContext must be used in Provider");
  return context;
};
