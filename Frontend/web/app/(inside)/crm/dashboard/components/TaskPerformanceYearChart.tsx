// components/TaskPerformanceYearChart.tsx

import { Bar } from "react-chartjs-2";
import { TaskPerformanceData } from "../types";
import { ChartData, ChartOptions } from "chart.js";

interface TaskPerformanceProps {
  data: TaskPerformanceData;
}

const TaskPerformanceYearChart: React.FC<TaskPerformanceProps> = ({ data }) => {
  if (!data || Object.keys(data).length === 0) {
    return <div className="text-center text-gray-500">No task performance data available for the year.</div>;
  }

  const agents = Object.keys(data);
  const onTimeData = agents.map(agent => data[agent].ontime);
  const lateData = agents.map(agent => data[agent].late);
  
  const chartData: ChartData<'bar'> = {
    labels: agents,
    datasets: [
      {
        label: "On Time",
        data: onTimeData,
        backgroundColor: "rgba(34, 197, 94, 0.8)", // Success color
      },
      {
        label: "Late",
        data: lateData,
        backgroundColor: "rgba(239, 68, 68, 0.8)", // Error color
      },
    ],
  };

  const options: ChartOptions<'bar'> = {
    responsive: true,
    scales: {
        x: {
            stacked: true,
            title: {
                display: true,
                text: 'Agent'
            }
        },
        y: {
            stacked: true,
            beginAtZero: true,
            title: {
                display: true,
                text: 'Total Tasks'
            }
        },
    },
    plugins: {
      legend: {
        position: "top",
      },
      title: {
        display: false,
      },
      tooltip: {
        callbacks: {
          label: function(context) {
             const agentName = context.label;
             const agentPerformance = data[agentName];
             const totalTasks = agentPerformance.ontime + agentPerformance.late;
             return `Total Tasks: ${totalTasks}`;
          }
        }
      }
    },
  };

  return <Bar data={chartData} options={options} />;
};

export default TaskPerformanceYearChart;