// components/SummaryStatusChart.tsx

import { Doughnut } from "react-chartjs-2";
import { SummaryStatusData } from "../types";
import { ChartData, ChartOptions } from "chart.js";

interface SummaryStatusProps {
  data: SummaryStatusData | null;
}

const SummaryStatusChart: React.FC<SummaryStatusProps> = ({ data }) => {
  if (!data || Object.keys(data).length === 0) {
    return <div className="text-center text-gray-500">No status data available.</div>;
  }

  const labels = Object.keys(data).map(key => key.replace(/_/g, ' ').toUpperCase());
  const values = Object.values(data) as number[];
  const totalLeads = values.reduce((sum, current) => sum + current, 0);

  const backgroundColors = [
    '#38bdf8', '#8b5cf6', '#a855f7', '#facc15', '#ef4444', '#22c55e',
  ];

  const chartData: ChartData<'doughnut'> = {
    labels: labels,
    datasets: [
      {
        data: values,
        backgroundColor: backgroundColors.slice(0, labels.length),
        borderColor: "#ffffff",
        borderWidth: 2,
      },
    ],
  };

  const options: ChartOptions<'doughnut'> = {
    responsive: true,
    plugins: {
      legend: {
        position: "right",
      },
      title: {
        display: true,
        text: `Total Leads: ${totalLeads}`,
        font: { size: 16 }
      },
    },
  };

  return <Doughnut data={chartData} options={options} />;
};

export default SummaryStatusChart;