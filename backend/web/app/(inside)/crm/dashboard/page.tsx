// app/page.js

"use client";

import { useState, useEffect, useCallback } from "react";
import axios from "axios";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  LineElement, // Elemen Garis
  PointElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
} from "chart.js";

// Daftarkan komponen Chart.js
ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement, // Elemen Garis
  PointElement,
  Title,
  Tooltip,
  Legend,
  ArcElement
);


// --- Komponen Visualisasi Impor (akan dibuat di bawah) ---
import MarketingPerformanceChart from "./components/MarketingPerformanceChart";
import SummaryStatusChart from "./components/SummaryStatusChart";

import PendingTasksTable from "./components/PendingTasksTable";
import axiosInstance from "@/lib/axios";
import LeadFunnelChart from "./components/LeadFunnelChart";
import { SummarySourceData } from "./types";
import SummaryChangedStatusChart from "./components/SummaryChangedStatusChart";
import SummarySourceChart from "./components/SummarySourceChart";
import TaskPerformanceYearChart from "./components/TaskPerformanceYearChart";
import { useForm } from "react-hook-form";

// --- Custom Hook untuk Fetching Data ---
const useFetchDashboardData = (endpoint : any, method = "GET", body = {}) => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchData = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await axiosInstance.get(endpoint,{params : body});
      
      if(response.data.pagination){
        setData(response.data);
      }else{
        setData(response.data.data);
      }
    } catch (err : any) {
      setError(err.response ? err.response.data.message : err.message);
    } finally {
      setLoading(false);
    }
  }, [endpoint, method, JSON.stringify(body)]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  return { data, loading, error, refetch: fetchData };
};


// --- Komponen Dashboard Utama ---
export default function DashboardPage() {
  // Contoh Penggunaan Hooks
  const { data: marketingData, loading: loadingMarketing } = useFetchDashboardData(
    "/crm/dashboard/marketing-performance"
  );
  
  // Contoh dengan Body Request (misal untuk ringkasan status bulan ini)
  const [timelineStatus,setTimelineStatus] = useState<"today" | "last_week" | "last_month" | "last_year">('today')
  const [timelineSource,setTimelineSource] = useState<"today" | "last_week" | "last_month" | "last_year">('today')
  const [timelineChanged,setTimelineChanged] = useState<"today" | "last_week" | "last_month" | "last_year">('today')

  const { data: summaryStatusData, loading: loadingSummary } = useFetchDashboardData(
    "/crm/dashboard/summary-status",
    "GET",
    { when: timelineStatus } // Mengikuti dokumentasi yang menyertakan 'body' untuk GET
  );

  // Contoh Lead Funnel
  
  const [currentYear,setCurrentYear] = useState((new Date().getFullYear().toString() as string))
  const [currentMonth,setCurrentMonth] = useState((new Date().getMonth() + 1).toString() as string)
  const { data: funnelData, loading: loadingFunnel } = useFetchDashboardData(
    "/crm/dashboard/lead-funnel",
    "GET",
    { month: currentMonth, year: currentYear }
  );

  // Contoh Pending Task (Tabel)
  const { data: pendingTasks, loading: loadingPending } = useFetchDashboardData(
    "/crm/dashboard/pending-tasks"
  );

  const { data: contactData, loading: loadingContact } = useFetchDashboardData(
    "/crm/contact",
    "GET",
    { page: 1, per_page : 1 }
  );

  // --- Hooks untuk Endpoint Baru ---
  const { data: summarySourceData, loading: loadingSource, error: errorSource } = useFetchDashboardData(
    "/crm/dashboard/summary-source",
    "GET",
    { when: timelineSource }
  );

  const { data: changedStatusData, loading: loadingChanged, error: errorChanged } = useFetchDashboardData(
    "/crm/dashboard/summary-changed",
    "GET",
    { when: timelineChanged }
  );

  const [currentYearTaskPerformance,setCurrentYearTaskPerformance] = useState((new Date().getFullYear().toString() as string))
  
  const { data: taskPerformanceData, loading: loadingTaskYear, error: errorTaskYear } = useFetchDashboardData(
    "/crm/dashboard/task-performance",
    "GET",
    { year: currentYearTaskPerformance }
  );
  // ---------------------------------


  // Komponen Loading/Error
  const LoadingIndicator = () => (
    <div className="flex justify-center items-center p-8">
        <span className="loading loading-spinner loading-lg text-primary"></span>
    </div>
  );

  return (
    <div className="space-y-8 min-h-screen">
      {/* <h1 className="text-3xl font-bold text-gray-800 border-b pb-4">CRM Dashboard Overview</h1> */}

      {/* --- Bagian 1: Performance & Funnel --- */}
      <div className="grid grid-cols-1 md:grid-cols-1 gap-6">
        {/* Marketing Performance */}
        <div className="card bg-white rounded-xl col-span-1">
          <div className="card-body">
            <h2 className="card-title text-lg">Marketing Task Performance</h2>
            {loadingMarketing ? (
              <LoadingIndicator />
            ) : (
              <MarketingPerformanceChart data={marketingData ?? []} />
            )}
          </div>
        </div>

        {/* Lead Funnel */}
        <div className="card bg-white rounded-xl col-span-1">
          <div className="card-body">
            <h2 className="card-title text-lg">Lead Funnel ({currentMonth}/{currentYear})</h2>
            <FilterMonthYear currentYear={currentYear} currrentMonth={currentMonth} setMonth={setCurrentMonth} setYear={setCurrentYear}/>
            
            {loadingFunnel ? (
              <LoadingIndicator />
            ) : (
              <LeadFunnelChart data={funnelData} contactData={contactData} />
            )}
          </div>
        </div>

        {/* Task Performance (Tahunan) */}
        <div className="card bg-white rounded-xl col-span-1"> {/* Mengambil 2 kolom */}
          <div className="card-body">
            <h2 className="card-title text-lg">Agent Task Performance ({currentYear})</h2>
            <FilterMonthYear currentYear={currentYearTaskPerformance} setYear={setCurrentYearTaskPerformance}/>
            {loadingTaskYear ? (
              <LoadingIndicator />
            ) : (
              <TaskPerformanceYearChart data={taskPerformanceData || {}} />
            )}
          </div>
        </div>
      </div>

      {/* --- Bagian 2: Sumber, Tren, dan Task Performance --- */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">

        {/* Summary by Source */}
        <div className="card bg-white rounded-xl col-span-1">
          <div className="card-body">
            <h2 className="card-title text-lg">Leads by Source</h2>
            <FilterTimeline timeline={timelineSource} setTimeline={setTimelineSource}/>
            { loadingSource ? (
              <LoadingIndicator />
            ) : (
              <SummarySourceChart data={summarySourceData || {}} />
            )}
          </div>
        </div>
        {/* Summary by Status */}
        <div className="card bg-white rounded-xl col-span-1">
          <div className="card-body">
            <h2 className="card-title text-lg">Leads by Status (Last Month)</h2>
            <FilterTimeline timeline={timelineStatus} setTimeline={setTimelineStatus}/>
            {loadingSummary ? (
              <LoadingIndicator />
            ) : (
              <SummaryStatusChart data={summaryStatusData} />
            )}
          </div>
        </div>

        
      </div>
      
      {/* --- Bagian 3: Tren Status Berubah --- */}
      <div className="card bg-white rounded-xl">
        <div className="card-body">
            <h2 className="card-title text-xl">Trend: Leads Status Changed (Last Month)</h2>
            <FilterTimeline timeline={timelineChanged} setTimeline={setTimelineChanged}/>
            {loadingChanged ? (
              <LoadingIndicator />
            ) : (
              <SummaryChangedStatusChart data={changedStatusData || {}} />
            )}
        </div>
        
      </div>

      {/* --- Bagian 2: Tabel Pending Tasks --- */}
      <div className="card bg-white rounded-xl">
        <div className="card-body">
          <h2 className="card-title text-xl">Pending Tasks</h2>
          
          {loadingPending ? (
            <LoadingIndicator />
          ) : (
            <PendingTasksTable data={pendingTasks ?? []} />
          )}
        </div>
      </div>
      
    </div>
  );
}

type FilterMonthProps = {
  setYear: (year: string) => void;
  setMonth?: (month: string) => void;
  currentYear: string;
  currrentMonth?: string;
};


function FilterMonthYear({currentYear, currrentMonth, setYear, setMonth } : FilterMonthProps){
  
  const {
      register,
      watch,
      formState: { errors },
    } = useForm<{
      year: string;
      month: string;
    }>({
      defaultValues : {
        year : currentYear,
        month : currrentMonth
      }
    });

  const month = watch('month')
  const year = watch('year')

  useEffect(()=>{
    setYear(year)
    if(setMonth) setMonth(month)
  },[month, year])
    
  return (
    <form className="flex items-end gap-4 w-1/2 mb-6">
        {(currrentMonth) && (
          <div className="form-control w-full">
            <label className="label font-medium">Month</label>
            <select
              {...register("month")}
              className="select select-bordered w-full"
              required
            >
              <option value="">-- Pilih Bulan --</option>
              <option value="1">Januari</option>
              <option value="2">Februari</option>
              <option value="3">Maret</option>
              <option value="4">April</option>
              <option value="5">Mei</option>
              <option value="6">Juni</option>
              <option value="7">Juli</option>
              <option value="8">Agustus</option>
              <option value="9">September</option>
              <option value="10">Oktober</option>
              <option value="11">November</option>
              <option value="12">Desember</option>
            </select>
          </div>
        )}

        <div className="form-control w-full">
          <label className="label font-medium">Year</label>
          <select
            {...register("year")}
            className="select select-bordered w-full"
            required
          >
            <option value="">-- Pilih Tahun --</option>
            {Array.from({ length: 10 }, (_, i) => {
              const year = new Date().getFullYear() - i;
              return (
                <option key={year} value={year}>
                  {year}
                </option>
              );
            })}
          </select>
        </div>
      </form>
  )
}

type FilterTimelineProps = {
  setTimeline: (timeline: "today" | "last_week" | "last_month" | "last_year") => void;
  timeline : "today" | "last_week" | "last_month" | "last_year"
};

function FilterTimeline({ timeline:timeline_def, setTimeline } : FilterTimelineProps){
  const {
      register,
      watch,
      formState: { errors },
    } = useForm<{
      timeline: "today" | "last_week" | "last_month" | "last_year";
    }>({
      defaultValues : {
        timeline : timeline_def
      }
    });

  const timeline = watch('timeline')

  useEffect(()=>{
    setTimeline(timeline)
  },[timeline])

  const timeOptions : {value : string; label : string}[] = [
    { value: "today", label: "Today" },
    { value: "last_week", label: "Last Week" },
    { value: "last_month", label: "Last Month" },
    { value: "last_year", label: "Last Year" },
  ];

    
  return (
    <form className="flex items-end gap-4 w-1/2 mb-6">
        <div className="form-control w-full">
          <label className="label font-medium">Tmeline</label>
          <select
            {...register("timeline")}
            className="select select-bordered w-full"
            required
          >
            <option value="">-- Pilih --</option>

            {timeOptions.map((time)=><option key={time.value} value={time.value}>{time.label}</option>)}
          </select>
        </div>
      </form>
  )
}