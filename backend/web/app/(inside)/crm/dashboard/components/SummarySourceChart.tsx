// components/SummarySourceChart.tsx

import { Doughnut } from "react-chartjs-2";
import { SummarySourceData } from "../types";
import { ChartData, ChartOptions } from "chart.js";

interface SummarySourceProps {
  data: SummarySourceData;
}

const SummarySourceChart: React.FC<SummarySourceProps> = ({ data }) => {
  if (!data || Object.keys(data).length === 0) {
    return <div className="text-center text-gray-500">No source summary data available.</div>;
  }

  const labels = Object.keys(data);
  const values = Object.values(data);
  const totalLeads = values.reduce((sum, current) => sum + current, 0);

  // Menggunakan palet warna yang bervariasi
  const backgroundColors = [
    '#38bdf8', // info
    '#facc15', // warning
    '#a855f7', // secondary
    '#22c55e', // success
    '#ef4444', // error
    '#10b981', // emerald
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

  return (
        <Doughnut data={chartData} options={options} />
  );
};

export default SummarySourceChart;