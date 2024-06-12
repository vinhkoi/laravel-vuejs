<template>
  <AdminLayout>
    <div>
      <div class="surface-ground px-4 py-5 md:px-6 lg:px-8">
        <div class="grid a">
          <div class="col-12 md:col-6 lg:col-3">
            <div class="surface-card shadow-2 p-3 border-round">
              <div class="flex justify-content-between mb-3">
                <div>
                  <span class="block text-500 font-medium mb-3">Orders</span>
                  <div class="text-900 font-medium text-xl">{{ totalOrders }}</div>
                </div>
                <div
                  class="flex align-items-center justify-content-center bg-blue-100 border-round"
                  style="width: 2.5rem; height: 2.5rem"
                >
                  <i class="pi pi-shopping-cart text-blue-500 text-xl"></i>
                </div>
              </div>
              <span class="text-green-500 font-medium">24 new </span>
              <span class="text-500">since last visit</span>
            </div>
          </div>
          <div class="col-12 md:col-6 lg:col-3">
            <div class="surface-card shadow-2 p-3 border-round">
              <div class="flex justify-content-between mb-3">
                <div>
                  <span class="block text-500 font-medium mb-3">Revenue</span>
                  <div class="text-900 font-medium text-xl">${{ totalRevenue }}</div>
                </div>
                <div
                  class="flex align-items-center justify-content-center bg-orange-100 border-round"
                  style="width: 2.5rem; height: 2.5rem"
                >
                  <i class="pi pi-map-marker text-orange-500 text-xl"></i>
                </div>
              </div>
              <span class="text-green-500 font-medium">%52+ </span>
              <span class="text-500">since last week</span>
            </div>
          </div>
          <div class="col-12 md:col-6 lg:col-3">
            <div class="surface-card shadow-2 p-3 border-round">
              <div class="flex justify-content-between mb-3">
                <div>
                  <span class="block text-500 font-medium mb-3">Customers</span>
                  <div class="text-900 font-medium text-xl">{{ totalUser }}</div>
                </div>
                <div
                  class="flex align-items-center justify-content-center bg-cyan-100 border-round"
                  style="width: 2.5rem; height: 2.5rem"
                >
                  <i class="pi pi-inbox text-cyan-500 text-xl"></i>
                </div>
              </div>
              <span class="text-green-500 font-medium">520 </span>
              <span class="text-500">newly registered</span>
            </div>
          </div>
          <div class="col-12 md:col-6 lg:col-3">
            <div class="surface-card shadow-2 p-3 border-round">
              <div class="flex justify-content-between mb-3">
                <div>
                  <span class="block text-500 font-medium mb-3">Comments</span>
                  <div class="text-900 font-medium text-xl">152 Unread</div>
                </div>
                <div
                  class="flex align-items-center justify-content-center bg-purple-100 border-round"
                  style="width: 2.5rem; height: 2.5rem"
                >
                  <i class="pi pi-comment text-purple-500 text-xl"></i>
                </div>
              </div>
              <span class="text-green-500 font-medium">85 </span>
              <span class="text-500">responded</span>
            </div>
          </div>
        </div>
      </div>
      <div class="border-2 rounded-lg border-gray-300 dark:border-gray-600 h-96 mb-4">
        <div class="card h-96">
          <div class="card-header">
            <h5 class="card-title">Order in month</h5>
          </div>

          <div class="card-body h-93">
            <Chart type="bar" :data="chartData" :options="chartOptions" class="h-30rem" />
          </div>
        </div>
      </div>
      <div class="border-2 rounded-lg border-gray-300 dark:border-gray-600 h-96 mb-4">
        <div class="card h-96">
          <div class="card-header">
            <h5 class="card-title">Order in 12 months</h5>
          </div>

          <div class="card-body h-93">
            <Charts :orders="orders"></Charts>
          </div>
        </div>
      </div>
      <div class="border-2 rounded-lg border-gray-300 dark:border-gray-600 mb-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">New Order in 7 days</h5>
          </div>

          <div class="card-body">
            <NewOrder :orders="orders"></NewOrder>
          </div>
        </div>
      </div>
      <div class="border-2 rounded-lg border-gray-300 dark:border-gray-600 mb-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">New User in 7 days</h5>
          </div>

          <div class="card-body">
            <NewUser :users="users"></NewUser>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

import AdminLayout from "./Components/AdminLayout.vue";
import Charts from "./Product/Charts.vue";
import NewOrder from "./Product/NewOrder.vue";
import NewUser from "./Product/NewUser.vue";

// initialize components based on data attribute selectors
onMounted(() => {
  initFlowbite();
  chartData.value = setChartData();
  chartOptions.value = setChartOptions();
});
defineProps({
  orders: Array,
  totalRevenue: Array,
  totalUser: Array,
  users: Array,
});

// Nhận mảng order từ props
const orders = usePage().props.orders;
const totalRevenue = usePage().props.totalRevenue;
const totalUser = usePage().props.totalUser;
console.log(totalRevenue);
console.log(orders.length);
const totalOrders = computed(() => {
  return orders.length;
});

const chartData = ref();
const chartOptions = ref();

const setChartData = () => {
  const documentStyle = getComputedStyle(document.documentElement);

  const chartData = {
    labels: getPast30DaysLabels(),
    datasets: [
      {
        type: "line",
        label: "Total Revenue",
        borderColor: documentStyle.getPropertyValue("--orange-500"),
        borderWidth: 2,
        yAxisID: "y1",
        fill: false,
        tension: 0.4,
        data: Array(30).fill(0), // Dữ liệu cho thanh line
      },
      {
        type: "bar",
        label: " Order count",
        yAxisID: "y",
        backgroundColor: documentStyle.getPropertyValue("--cyan-500"),
        data: Array(30).fill(0), // Dữ liệu cho thanh bar
        borderColor: "white",
        borderWidth: 2,
      },
    ],
  };
  orders.forEach((order) => {
    const date = new Date(order.created_at);
    const dayIndex = getDayIndex(date);
    const totalPrice = parseFloat(order.total_price);
    const quantity = 1;
    if (!isNaN(dayIndex)) {
      chartData.datasets[0].data[dayIndex] += totalPrice;
      chartData.datasets[1].data[dayIndex] += quantity;
    }
  });

  return chartData;
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
      tooltip: {
        enabled: true,
        callbacks: {
          label: function (context) {
            const label = context.parsed.x || ""; // Lấy ngày
            const revenue = context.parsed.y.toLocaleString(); // Định dạng doanh số
            return `${label}: đ${revenue}`;
          },
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
          callback: function (value, index, ticks) {
            return "" + value.toLocaleString();
          },
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
</script>
<style>
.a {
  display: flex;
  flex-wrap: wrap;
  margin-right: -0.5rem;
  margin-left: -0.5rem;
  margin-top: -0.5rem;
}
.col-12 {
  flex: 0 0 auto;
  padding: 0.5rem;
}
.md\:col-6 {
  flex: 0 0 auto;
  padding: 0.5rem;
  width: 50%;
}
@media screen and (min-width: 992px) {
  .lg\:col-3 {
    flex: 0 0 auto;
    padding: 0.5rem;
    width: 25%;
  }
}
@media screen and (min-width: 768px) {
  .md\:col-6 {
    flex: 0 0 auto;
    padding: 0.5rem;
  }
}
.grid > .col,
.grid > [class*="col"] {
  box-sizing: border-box;
}
.border-round {
  border-radius: var(--border-radius) !important;
}
.shadow-2 {
  box-shadow: 0 4px 10px #00000008, 0 0 2px #0000000f, 0 2px 6px #0000001f !important;
}
.surface-card {
  background-color: var(--surface-card) !important;
}
.justify-content-between {
  justify-content: space-between !important;
}
.text-500 {
  color: var(--surface-500) !important;
}
.justify-content-center {
  justify-content: center !important;
}
.align-items-center {
  align-items: center !important;
}
.h-93 {
  height: 22rem;
}
.p-chart {
  height: 22rem;
}
.card-header {
  display: flex;
  justify-content: space-around;
}
</style>
