"use client";
import { useSitemapContext } from "@/context/useSitemapContext";
import { UnitBox } from "./UnitBox";
import { useEffect, useRef, useState } from "react";
import { UnitBoxProps } from "@/types/unit";
import { useForm } from "react-hook-form";
import { getProtectedImageUrl } from "@/lib/protected-image";
import { Maximize, Minimize, ZoomIn, ZoomOut } from "lucide-react";
import { PropertyUnit } from "@/app/(inside)/property/siteplan/page";
import axios from "@/lib/axios";
import HeaderUnit from "@/app/(inside)/property/siteplan/unit/[id]/detail/section/HeaderUnit";
import { usePathname, useRouter } from "next/navigation";
import { isUtf8 } from "buffer";
import React from "react";

export function SitemapCanvas() {
  const {
    units,
    updateUnitPosition,
    updateUnitById,
    project,
    containerSize,
    setContainerSize,
    editMode
  } = useSitemapContext();

  const handleDragEnd = (id: string, left: number, top: number) => {
    updateUnitPosition(id, left, top, 0);
  };

  const pathname = usePathname();
  const isSiteFull = pathname == "/property/siteplan/full";

  const [unitid, setUnitid] = useState<string | null>();

  const handleClick = (id: string) => {
    setUnitid(id);
  };
  const { register, reset, watch } = useForm();

  const [activeUnit, setActiveUnit] = useState<UnitBoxProps>();
  const [selectedUnitId, setSelectedUnitId] = useState<string | null>(null);

  const rotateVal = watch("rotate");
  const widthVal = watch("width");
  const heightVal = watch("height");

  useEffect(() => {
    if (rotateVal <= 360) {
      const newRotate = ((activeUnit?.rotate ?? 0) + rotateVal) % 360;
      updateUnitById(activeUnit?.id ?? "", { rotate: newRotate });
    }
  }, [rotateVal]);

  useEffect(() => {
    const newWidth = (activeUnit?.width ?? 80) + parseInt(widthVal);
    updateUnitById(activeUnit?.id ?? "", { width: newWidth });
  }, [widthVal]);

  useEffect(() => {
    const newH = (activeUnit?.width ?? 80) + parseInt(heightVal);
    updateUnitById(activeUnit?.id ?? "", { height: newH });
  }, [heightVal]);

  const [blobUrl, setBlobUrl] = useState<string | null>(null);
  const [blobUrlLoading, setBlobUrlLoading] = useState<boolean>(false);

  useEffect(() => {
    const getUrl = async () => {
      setBlobUrl("");
      setBlobUrlLoading(true);
      const potectedUrl = await getProtectedImageUrl(
        project?.site_plan_image ?? ""
      );
      setBlobUrlLoading(false);
      setBlobUrl(potectedUrl ?? "");
    };
    getUrl();
  }, [project?.site_plan_image]);
  const [zoom, setZoom] = useState(1);
  const [offset, setOffset] = useState({ x: 0, y: 0 });
  const [isPanning, setIsPanning] = useState(false);
  const [startPoint, setStartPoint] = useState<{ x: number; y: number } | null>(
    null
  );
  const [mode, setMode] = useState<"pan" | "drag">("pan");

  const handleMouseDown = (e: React.MouseEvent) => {
    if (mode == "drag") return;
    setIsPanning(true);
    setStartPoint({ x: e.clientX, y: e.clientY });
  };

  const handleMouseMove = (e: React.MouseEvent) => {
    if (mode == "drag") return;
    if (!isPanning || !startPoint) return;

    const dx = e.clientX - startPoint.x;
    const dy = e.clientY - startPoint.y;

    setOffset((prev) => ({ x: prev.x + dx, y: prev.y + dy }));
    setStartPoint({ x: e.clientX, y: e.clientY });
  };

  const handleMouseUp = () => {
    if (mode == "drag") return;
    setIsPanning(false);
    setStartPoint(null);
  };

  const containerRef = useRef<HTMLImageElement>(null);

  const router = useRouter();

  useEffect(() => {
    const resize = () => {
      if (containerRef.current) {
        setContainerSize({
          width: containerRef.current.offsetWidth,
          height: containerRef.current.offsetHeight,
        });
      }
    };

    resize(); // initial
    window.addEventListener("resize", resize);
    return () => window.removeEventListener("resize", resize);
  }, []);

  useEffect(()=>{
    if(unitid){
      setMode("drag")
    }else{
      setMode("pan")
    }
  },[unitid])

  useEffect(()=>{
    setMode("pan")
  },[editMode])

  if (blobUrlLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  return (
    <div className="h-full ">
      <div className={`flex items-center gap-3 mb-3 ${isSiteFull ? 'p-4' : ''}`}>
        {!isSiteFull && editMode && (
          <React.Fragment>
            <button
              onClick={() => setMode("pan")}
              className={`btn btn-sm ${
                mode === "pan" ? "btn-primary" : "btn-outline"
              }`}
            >
              🖐️ Pan Mode
            </button>
            <button
              onClick={() => setMode("drag")}
              className={`btn btn-sm  ${
                mode === "drag" ? "btn-primary" : "btn-outline"
              }`}
            >
              🧱 Drag Mode
            </button>
          </React.Fragment>
        )}
        <button
          className="btn btn-sm btn-outline p-2"
          onClick={() => setZoom((z) => Math.max(z - 0.1, 1))}
        >
          <ZoomOut className="w-4 h-4" />
        </button>

        <input
          type="range"
          min={0.5}
          max={10}
          step={0.1}
          value={zoom}
          onChange={(e) => setZoom(parseFloat(e.target.value))}
          className="w-40"
        />

        <button
          className="btn btn-sm btn-outline p-2"
          onClick={() => setZoom((z) => Math.min(z + 0.1, 10))}
        >
          <ZoomIn className="w-4 h-4" />
        </button>

        <span className="text-sm w-14 text-right">
          {(zoom * 100).toFixed(0)}%
        </span>

        {isSiteFull ? (
          <button
            onClick={() => router.push("/property/siteplan")}
            className={`btn btn-sm btn-outline ml-auto`}
          >
            <Minimize />
          </button>
        ) : (
          <button
            onClick={() => router.push("/property/siteplan/full")}
            className={`btn btn-sm btn-outline ml-auto`}
          >
            <Maximize />
          </button>
        )}
      </div>

      <div
        className={`overflow-hidden bg-gray-100 h-full relative
          ${
            mode === "pan"
              ? "cursor-grab active:cursor-grabbing"
              : "cursor-move"
          }`}
        onPointerDown={handleMouseDown}
        onPointerMove={handleMouseMove}
        onPointerUp={handleMouseUp}
        onPointerLeave={handleMouseUp}
        onDragOver={(e) => e.preventDefault()}
      >
        {/* Floating Card: render only if a unit is selected */}
        {unitid && (
          <UnitDetail unitId={unitid} onClose={() => setUnitid(null)} />
        )}
        <div
          className="absolute top-0 left-0 transition-transform duration-100"
          style={{
            transform: `translate(${offset.x}px, ${offset.y}px) scale(${zoom})`,
            transformOrigin: "center",
            position: "relative",
            overflow: "hidden",
            width: "1400px",
            height: "700px",
          }}
        >
          {/* Gambar siteplan sebagai elemen img */}
          <img
            src={blobUrl ?? "-"}
            ref={containerRef}
            onLoad={(e) => {
              const img = e.currentTarget;
              setContainerSize({
                width: img.offsetWidth,
                height: img.offsetHeight,
              });
            }}
            style={{
              width: "100%",
              height: "100%",
              objectFit: "contain",
              display: "block",
              pointerEvents: "none",
              position: "absolute",
              top: 0,
              left: 0,
              zIndex: 0,
              ["--handle-size" as any]: `${20}px`,
            }}
          />

          {/* Unit box di atas gambar */}
          {containerSize.height > 0 &&
            containerSize.width > 0 &&
            units.map((unit) => (
              <UnitBox
                key={unit.id}
                {...unit}
                onDragEnd={handleDragEnd}
                onClick={handleClick}
                selectedUnitId={selectedUnitId}
                setSelectedUnitId={setSelectedUnitId}
                zoom={zoom}
              />
            ))}
        </div>
      </div>
    </div>
  );
}

type UnitDetailProps = {
  unitId: string | null | undefined;
  onClose: () => void;
};



export function UnitDetail({ unitId, onClose }: UnitDetailProps) {
  const [unit, setUnit] = useState<PropertyUnit | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!unitId) return;
    setLoading(true);
    setError(null);
    axios
      .get(`/property/unit-property/${unitId}`)
      .then((res) => {
        setUnit(res.data.data);
      })
      .catch((err) => {
        console.error("❌ Gagal fetch unit detail:", err);
        setError("Gagal memuat unit detail");
      })
      .finally(() => {
        setLoading(false);
      });
  }, [unitId]);
  const router = useRouter();

  if (!unitId) return null;

  return (
    <div
      className="floating-card"
      style={{
        position: "absolute",
        top: 40,
        right: 40,
        minHeight: "600px",
        width: "360px",
        zIndex: 50,
        pointerEvents: "auto",
      }}
    >
      <div className="bg-white rounded-xl shadow-lg border p-4 h-full flex flex-col">
        <div className="flex justify-between items-center mb-3">
          <h2 className="text-lg font-semibold">
            Unit {(unit as any)?.unit_number} ({(unit as any)?.unit_type})
          </h2>
          <button
            onClick={onClose}
            className="px-2 py-1 text-sm rounded bg-gray-200 hover:bg-gray-300"
          >
            ✕
          </button>
        </div>

        {error && <p className="text-red-500 text-sm">{error}</p>}

        {loading && <p className="text-gray-500 text-sm">Loading...</p>}

        {!error && unit && (
          <div>
            <HeaderUnit data={unit} styling={false} />
            <button
              className="btn btn-sm btn-info text-white mt-2 w-full"
              onClick={() =>
                window.open(
                  `/property/siteplan/unit/${unit.id}/detail`,
                  "_blank"
                )
              }
            >
              Show More
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
