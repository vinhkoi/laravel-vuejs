<template>
  <div class="card">
    <Chart type="bar" :data="chartData" :options="chartOptions" class="h-30rem" />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";

defineProps({
  orders: Array,
});
onMounted(() => {
  chartData.value = setChartData();
  chartOptions.value = setChartOptions();
});
const orderss = usePage().props.orders;

const chartData = ref();
const chartOptions = ref();

const setChartData = () => {
  const documentStyle = getComputedStyle(document.documentElement);
  const today = new Date();
  const currentMonth = today.getMonth(); // Chỉ số của tháng hiện tại
  const chartData = {
    labels: getPast12MonthsLabels(currentMonth), // Truyền chỉ số của tháng hiện tại cho hàm getPast12MonthsLabels()
    datasets: [
      {
        type: "line",
        label: "Total Revenue",
        borderColor: documentStyle.getPropertyValue("--orange-500"),
        borderWidth: 2,
        yAxisID: "y1",
        fill: false,
        tension: 0.4,
        data: Array(12).fill(0), // Dữ liệu cho thanh line
      },
      {
        type: "bar",
        label: "Order count",
        yAxisID: "y",
        backgroundColor: documentStyle.getPropertyValue("--cyan-500"),
        data: Array(12).fill(0), // Dữ liệu cho thanh bar
        borderColor: "white",
        borderWidth: 2,
      },
    ],
  };

  orderss.forEach((order) => {
    const date = new Date(order.created_at);
    const monthIndex = date.getMonth(); // Lấy chỉ số tháng (0-11)
    const totalPrice = parseFloat(order.total_price);
    const quantity = 1;
    if (!isNaN(monthIndex)) {
      const adjustedMonthIndex = (11 - currentMonth + monthIndex) % 12; // Điều chỉnh chỉ số tháng để lưu trữ ở vị trí đúng trong mảng
      chartData.datasets[0].data[adjustedMonthIndex] += totalPrice; // Cập nhật dữ liệu cho doanh thu
      chartData.datasets[1].data[adjustedMonthIndex] += quantity; // Cập nhật dữ liệu cho số lượng đơn hàng
    }
  });

  return chartData;
};

const getPast12MonthsLabels = (currentMonth) => {
  const labels = [];
  const today = new Date();
  const currentYear = today.getFullYear(); // Năm hiện tại
  for (let i = currentMonth; i > currentMonth - 12; i--) {
    const month = i < 0 ? 12 + i : i; // Xác định tháng trong khoảng 0-11
    const year = i < 0 ? currentYear - 1 : currentYear; // Năm của tháng
    labels.unshift(`${month + 1}/${year}`); // Thêm nhãn của tháng vào mảng
  }
  return labels;
};
const setChartOptions = () => {
  const documentStyle = getComputedStyle(document.documentElement);
  const textColor = documentStyle.getPropertyValue("--text-color");
  const textColorSecondary = documentStyle.getPropertyValue("--text-color-secondary");
  const surfaceBorder = documentStyle.getPropertyValue("--surface-border");

  return {
    maintainAspectRatio: false,
    aspectRatio: 0.6,
    plugins: {
      legend: {
        labels: {
          color: textColor,
        },
      },
    },
    scales: {
      x: {
        ticks: {
          color: textColorSecondary,
        },
        grid: {
          color: surfaceBorder,
        },
      },
      y: {
        type: "linear",
        display: true,
        position: "left",
        ticks: {
          color: textColorSecondary,
        },
        grid: {
          color: surfaceBorder,
        },
      },
      y1: {
        type: "linear",
        display: true,
        position: "right",
        ticks: {
          color: textColorSecondary,
        },
        grid: {
          drawOnChartArea: false,
          color: surfaceBorder,
        },
      },
    },
  };
};
const getPast30DaysLabels = () => {
  const labels = [];
  for (let i = 29; i >= 0; i--) {
    const date = new Date();
    date.setDate(date.getDate() - i);
    labels.push(date.toLocaleDateString("en-US", { month: "short", day: "numeric" }));
  }
  return labels;
};

const getDayIndex = (date) => {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  date.setHours(0, 0, 0, 0);
  const diffInTime = today.getTime() - date.getTime();
  const diffInDays = diffInTime / (1000 * 3600 * 24);
  return 29 - diffInDays; // Trả về chỉ số ngược với ngày hiện tại trong mảng
};
</script>
