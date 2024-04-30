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
      <div
        class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-96 mb-4"
      ></div>
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div
          class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
        ></div>
        <div
          class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
        ></div>
        <div
          class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
        ></div>
        <div
          class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
        ></div>
      </div>
      <div
        class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-96 mb-4"
      ></div>
      <div class="grid grid-cols-2 gap-4">
        <div
          class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
        ></div>
        <div
          class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
        ></div>
        <div
          class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
        ></div>
        <div
          class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
        ></div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import AdminLayout from "./Components/AdminLayout.vue";
// initialize components based on data attribute selectors
onMounted(() => {
  initFlowbite();
});
defineProps({
  orders: Array,
  totalRevenue: Array,
  totalUser: Array,
});
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

// Nhận mảng order từ props
const orders = usePage().props.orders;
const totalRevenue = usePage().props.totalRevenue;
const totalUser = usePage().props.totalUser;
console.log(totalRevenue);
console.log(orders.length);
const totalOrders = computed(() => {
  return orders.length;
});
</script>
<style scoped>
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
</style>
