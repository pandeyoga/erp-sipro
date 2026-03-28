// types/crm.ts

export interface MarketingPerformanceItem {
    user_id: string;
    user_name: string;
    total_tasks: number;
    on_time: number;
    late: number;
    ontime_percentage: number;
  }
  
  export interface NewLeadItem {
    lead_id: string;
    lead_name: string;
    source: string;
    status: string;
    phone: string;
    due_date: string | null;
    agent: string | null;
    created_at: string;
  }
  
  export interface PendingTaskItem {
    lead_id: string;
    lead_name: string;
    task: string;
    source: string;
    status: string;
    phone: string;
    due_date: string | null;
    is_late: boolean;
    remaining_days: number | null;
    agent: string | null;
    created_at: string;
  }
  
  export interface SummaryStatusData {
    document_and_legal_process?: number;
    new?: number;
    prospect?: number;
    reserve?: number;
    survey?: number;
    // Tambahkan status lain sesuai API Anda
    [key: string]: number | undefined;
  }
  
  export interface LeadFunnelStage {
    total: number;
    percentage: number;
  }
  
  export interface LeadFunnelData {
    new: LeadFunnelStage;
    survey: LeadFunnelStage;
    reservation: LeadFunnelStage;
    payment: LeadFunnelStage;
  }

  // types/crm.ts (Tambahan)

export interface SummarySourceData {
    [sourceName: string]: number; // e.g., "instagram": 1, "ads_facebook": 5
}

export interface SummaryChangedStatusData {
    [date: string]: number; // e.g., "2025-10-11": 1, "2025-10-12": 3
}

export interface TaskPerformanceData {
    [userName: string]: {
        late: number;
        ontime: number;
    }; // e.g., "Wina": { late: 0, ontime: 4 }
}
  // Catatan: Tipe untuk SummarySource dan SummaryChanged akan serupa dengan SummaryStatus