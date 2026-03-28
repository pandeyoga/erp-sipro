"use client";
import { AddUnitDialog } from "@/components/siteplan/AddUnitDIalog";
import { ChoosePropertyDialog } from "@/components/siteplan/ChoosePropertyDialog";
import { SitemapCanvas } from "@/components/siteplan/SitemapCanvas";
import { UploadSiteplanDialog } from "@/components/siteplan/UploadSiteplanDialog";
import { useSitemapContext } from "@/context/useSitemapContext";
import axios from "@/lib/axios";
import { useSearchParams } from "next/navigation";
import React, { useEffect, useState } from "react";
import toast from "react-hot-toast";

function SitemapHeader() {
  const { project, setProject } = useSitemapContext();

  const searchParams = useSearchParams();

  const [default_project_id, setDefaultProjectId] = useState();

  // GET: project list
  useEffect(() => {
    axios
      .get("/property/construction/project-lists")
      .then((res) => {
        let list = res.data.data;
        if (list) {
          setDefaultProjectId(list[0]?.id);
        }
      })
      .catch(() => toast.error("Gagal mengambil project"));
  }, []);

  useEffect(() => {
    let projectId = searchParams.get("project-id");
    if (!projectId) {
      if (!default_project_id) return;
      projectId = default_project_id;
    }

    const fetchProject = async () => {
      try {
        const res = await axios.get(`/property/projects/${projectId}`);
        setProject(res.data.data); // asumsi respons: { success: true, data: {...} }
      } catch (error) {
        console.error("Error loading project:", error);
      }
    };

    fetchProject();
  }, [searchParams, setProject, default_project_id]);

  return (
    <div className="space-y-4 mb-4">
      {/* Tombol Aksi */}
      <div className="flex gap-4">
        <ChoosePropertyDialog />
        {project ? (
          <React.Fragment>
            <AddUnitDialog />
            <UploadSiteplanDialog />
          </React.Fragment>
        ) : null}
      </div>
    </div>
  );
}

export default function FullcsreeSiteplan() {
  return (
    <React.Fragment>
      <div className="hidden">
        <SitemapHeader />
      </div>
      <SitemapCanvas />
    </React.Fragment>
  );
}
