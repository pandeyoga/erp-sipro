// components/SummaryChangedStatusChart.tsx

import { Line } from "react-chartjs-2";
import { SummaryChangedStatusData } from "../types";
import { ChartData, ChartOptions } from "chart.js";

interface SummaryChangedStatusProps {
  data: SummaryChangedStatusData;
}

const SummaryChangedStatusChart: React.FC<SummaryChangedStatusProps> = ({ data }) => {
  if (!data || Object.keys(data).length === 0) {
    return <div className="text-center text-gray-500">No status change history available.</div>;
  }

  // Mengurutkan data berdasarkan tanggal
  const sortedDates = Object.keys(data).sort((a, b) => new Date(a).getTime() - new Date(b).getTime());
  
  const labels = sortedDates.map(date => new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
  const values = sortedDates.map(date => data[date]);

  const chartData: ChartData<'line'> = {
    labels: labels,
    datasets: [
      {
        label: "Status Changes",
        data: values,
        borderColor: 'rgba(168, 85, 247, 1)', // Secondary color
        backgroundColor: 'rgba(168, 85, 247, 0.2)',
        tension: 0.4, // Untuk garis yang lebih mulus
        fill: true,
      },
    ],
  };

  const options: ChartOptions<'line'> = {
    responsive: true,
    plugins: {
      legend: {
        position: 'top',
      },
      title: {
        display: false,
      },
    },
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Total Changes'
            }
        },
        x: {
            title: {
                display: true,
                text: 'Date'
            }
        }
    }
  };

  return (
    <div className="w-full">
      <Line data={chartData} options={options} />
    </div>
  );
};

export default SummaryChangedStatusChart;