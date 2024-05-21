<script setup>
import UserLayout from "./Layouts/UserLayout.vue";
import DashboardTable from "./Components/DashboardTable.vue";
// import Sidebarr from "./Layouts/Sidebarr.vue";
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import { ref } from "vue";

const visible = ref(false);
defineProps({
  orders: Array,
  //   orderItems: Array,
});
onMounted(() => {
  initFlowbite();
});
</script>
<template>
  <UserLayout>
    <Card class="shadow-md">
      <template #title>
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Danh sách đơn hàng</h2>
        </div>
      </template>
      <template #content>
        <DashboardTable :orders="orders" />
      </template>
    </Card>
  </UserLayout>
</template>

<style scoped>
.a {
  float: right;
}
.p-datatable .p-datatable-header {
  background-color: #f8f9fa; /* Màu nền header */
}

.p-datatable .p-datatable-tbody > tr:nth-child(even) {
  background-color: #f2f2f2; /* Màu nền dòng chẵn */
}

.p-datatable .p-paginator {
  background-color: transparent; /* Loại bỏ màu nền của paginator */
  justify-content: center;
}
</style>
