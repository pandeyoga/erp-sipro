// components/LeadFunnelChart.tsx

import { Bar } from "react-chartjs-2";
import { LeadFunnelData, LeadFunnelStage } from "../types";
import { ChartData, ChartOptions } from "chart.js";

interface LeadFunnelProps {
  data: LeadFunnelData | null;
  contactData ?: any;
}

const LeadFunnelChart: React.FC<LeadFunnelProps> = ({ data, contactData }) => {
  if (!data || Object.keys(data).length === 0) {
    return <div className="text-center text-gray-500">No funnel data available for this period.</div>;
  }

  // Mengambil dan memformat label
  const labels = Object.keys(data).map(key => key.charAt(0).toUpperCase() + key.slice(1));
  
  // Mengambil data total dan persentase
  const totalData: number[] = labels.map(key => (data as any)[key.toLowerCase()]?.total || 0);
  const percentageData: number[] = labels.map(key => (data as any)[key.toLowerCase()]?.percentage || 0);

  const chartData: ChartData<'bar'> = {
    labels: labels,
    datasets: [
      {
        label: "Total Leads",
        data: totalData,
        backgroundColor: 'rgba(56, 189, 248, 0.7)', // Daisy UI 'info' color
        borderRadius: 5,
      },
    ],
  };

  const options: ChartOptions<'bar'> = {
    indexAxis: 'y', // Membuat Horizontal Bar Chart (cocok untuk Funnel visualization)
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      x: {
        beginAtZero: true,
        title: {
          display: true,
          text: 'Total Leads'
        }
      },
      y: {
        grid: {
          display: false
        }
      }
    },
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            const label = context.dataset.label || '';
            const value = context.parsed.x;
            const index = context.dataIndex;
            const percentage = percentageData[index];
            
            // Memberikan label yang informatif
            return `${label}: ${value} Leads (${percentage.toFixed(2)}% dari tahap sebelumnya)`;
          }
        }
      }
    },
  };

  return (
    <div>
      <div className="grid grid-cols-1 sm:grid-cols-5 gap-4">
        <div className="bg-secondary text-white rounded-xl p-6 flex items-start gap-4">
            <div>
              <div className="text-sm  font-medium text-white">
                Total Contact
              </div>
              <div className={"text-3xl font-bold "}>
                {contactData?.pagination?.total}
              </div>
            </div>
          </div>
        {labels.map((item , i) => (
          <div key={i} className="bg-base-300 rounded-xl p-6 flex items-start gap-4">
            <div>
              <div className="text-sm font-medium text-base-content">
                Conversion Rate ({item})
              </div>
              <div className={"text-3xl font-bold "}>
                {((totalData[i] / contactData?.pagination?.total) * 100).toFixed(2)}%
              </div>
            </div>
          </div>
        ))}
      </div>
      <div style={{ height: '300px' }}>
      <Bar data={chartData} options={options} />
      </div>
    </div>
  );
};

export default LeadFunnelChart;