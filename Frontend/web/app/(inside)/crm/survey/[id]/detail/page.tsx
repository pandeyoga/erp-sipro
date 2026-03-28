"use client";

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import axios from "@/lib/axios";
import { PageLoading } from "@/components/loading/loading";
import Image from "next/image";
import { getProtectedImageUrl } from "@/lib/protected-image";

export default function LeadDetailPage() {
  const { id } = useParams() as { id: string };
  const [lead, setLead] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  
  const [profile, setProfile] = useState('');

  useEffect(() => {
    const fetchLead = async () => {
      try {
        const res = await axios.get(`/crm/lead/${id}`);
        setLead(res.data.data);
      } catch (err) {
        console.error("Gagal mengambil data lead:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchLead();
  }, [id]);

  const statusMap: Record<string, string> = {
    new: "New",
    prospect: "Prospect",
    reserve: "Reserve",
    document_and_legal_process: "Document & Legal Process",
    complete: "Complete",
    cancel: "Cancel",
  };
  
  function getStatusLabel(value: string): string {
    return statusMap[value] || value;
  }

  useEffect(()=>{
    if(lead){
      async function loadProfile(){
        const url = await getProtectedImageUrl(lead.survey_documentation)
        if(url) setProfile(url)    
      }
      loadProfile()
    }
  },[lead])

  if (loading) return <PageLoading />;
  const defaultImage = `https://api.dicebear.com/6.x/initials/svg?seed=${lead.name}`;
  return (
    <div className="mx-auto p-6">
      <h1 className="text-2xl font-bold mb-6">Detail Lead</h1>
      <div className="grid grid-cols-1  sm:grid-cols-2 gap-6">
        {/* Profile Card */}
        <div className="bg-base-100 rounded-xl p-6 flex flex-col gap-6 items-center md:items-start col-span-1">
          {/* Foto Profil */}
          <div className="avatar mx-auto">
            <div className="w-32 h-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2 overflow-hidden">
              <img
                src={profile || defaultImage}
                alt="Profile Picture"
                width={128}
                height={128}
                className="object-cover w-full h-full"
              />
            </div>
          </div>

          {/* Informasi Pribadi */}
          <div className="flex-1 w-full">
            <h2 className="text-2xl font-bold text-gray-800 mb-2">
              {lead.name}
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 text-lg text-gray-700">
              <div>
                <span className="font-semibold">Email:</span>
                <p>{lead.email || "-"}</p>
              </div>
              <div>
                <span className="font-semibold">Telepon:</span>
                <p>{lead.phone || "-"}</p>
              </div>
              <div>
                <span className="font-semibold">Status:</span>
                <div>
                  {lead.status ? (
                    <span >
                      {getStatusLabel(lead.status)} 
                    </span>
                  ) : (
                    <span className="text-gray-400 italic">
                      Belum ditentukan
                    </span>
                  )}
                </div>
              </div>
              <div>
                <span className="font-semibold">Tanggal Survey:</span>
                <p>{lead.survey_date || "-"}</p>
              </div>
              <div>
                <span className="font-semibold">Lokasi Survey:</span>
                <p>{lead.survey_location || "-"}</p>
              </div>
              <div>
                <span className="font-semibold">Marketing Agent:</span>
                <p>{lead.marketing_agent_name || "-"}</p>
              </div>
              <div>
                <span className="font-semibold">Tanggal Survey Aktual:</span>
                <p>{lead.actual_survey_date || "-"}</p>
              </div>
              <div>
                <span className="font-semibold">Preferensi unit:</span>
                <p>{lead.unit_preference_type || "-"}</p>
              </div>
              <div>
                <span className="font-semibold">Catatan:</span>
                <p>{lead.notes || "-"}</p>
              </div>
            </div>
          </div>
        </div>

        

        <div className="col-span-1 bg-base-100 rounded-xl p-6 shadow">
          <h3 className="text-lg font-bold mb-6 text-primary">
            📜 Riwayat Aktivitas
          </h3>

          

          {lead.history?.length > 0 ? (
  <ul className="timeline timeline-vertical">
    {lead.history.map((log: any, index: number) => {
      const isEven = index % 2 === 0;

      return (
        <li key={index}>
          <hr className="bg-primary" />
          {isEven ? (
            <>
              <div className="timeline-start timeline-box bg-base-200 border-l-4 border-primary shadow">
                <p className="text-lg text-gray-700">
                  Lead telah berubah status menjadi{" "}
                  <span className="text-primary font-semibold">{getStatusLabel(log.new_status)}</span>
                </p>
                <p className="text-md text-gray-500">oleh {log.action_by_name}</p>
              </div>
              <div className="timeline-middle">
                <div className="dot bg-primary"></div>
              </div>
              <div className="timeline-end  ">
                <p className="text-md text-gray-400 mt-1">
                  {new Date(log.changed_at).toLocaleString("id-ID", {
                    dateStyle: "medium",
                    timeStyle: "short",
                  })}
                </p>
              </div>
            </>
          ) : (
            <>

<div className="timeline-start  ">
                <p className="text-md text-gray-400 mt-1">
                  {new Date(log.changed_at).toLocaleString("id-ID", {
                    dateStyle: "medium",
                    timeStyle: "short",
                  })}
                </p>
              </div>
            
              <div className="timeline-middle">
                <div className="dot bg-primary"></div>
              </div>
              <div className="timeline-end timeline-box bg-base-200 border-r-4 border-primary shadow">
                <p className="text-lg text-gray-700">
                  Lead telah berubah status menjadi{" "}
                  <span className="text-primary font-semibold">{getStatusLabel(log.new_status)}</span>
                </p>
                <p className="text-md text-gray-500">oleh {log.action_by_name}</p>
              </div>
            </>
          )}
          <hr className="bg-primary" />
        </li>
      );
    })}
  </ul>
) : (
  <p className="text-gray-500 italic">Belum ada aktivitas tercatat.</p>
)}

        </div>
      </div>
    </div>
  );
}
