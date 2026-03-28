// components/MarketingPerformanceChart.tsx

import { Bar } from "react-chartjs-2";
import { ChartData, ChartOptions } from "chart.js";
import { MarketingPerformanceItem } from "../types";

interface MarketingPerformanceProps {
  data: MarketingPerformanceItem[];
}

const MarketingPerformanceChart: React.FC<MarketingPerformanceProps> = ({ data }) => {
  if (!data || data.length === 0) {
    return <div className="text-center text-gray-500">No performance data available.</div>;
  }

  const userNames = data.map((item) => item.user_name.split(' ')[0]);
  const onTimeData = data.map((item) => item.on_time);
  const lateData = data.map((item) => item.late);

  const chartData: ChartData<'bar'> = {
    labels: userNames,
    datasets: [
      {
        label: "On Time Tasks",
        data: onTimeData,
        backgroundColor: "rgba(52, 211, 153, 0.8)",
      },
      {
        label: "Late Tasks",
        data: lateData,
        backgroundColor: "rgba(239, 68, 68, 0.8)",
      },
    ],
  };

  const options: ChartOptions<'bar'> = {
    responsive: true,
    scales: {
        x: {
            stacked: true,
        },
        y: {
            stacked: true,
            beginAtZero: true,
        },
    },
    plugins: {
      legend: {
        position: "top",
      },
      tooltip: {
        callbacks: {
          afterLabel: function(context) {
             const index = context.dataIndex;
             // Pastikan index ada sebelum mengakses data
             if (data[index]) {
                const total = data[index].total_tasks;
                const percentage = data[index].ontime_percentage;
                return `Total: ${total} | On Time: ${percentage}%`;
             }
             return '';
          }
        }
      }
    },
  };

  return <Bar data={chartData} options={options} />;
};

export default MarketingPerformanceChart;