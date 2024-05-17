<script setup>
import UserLayouts from "./Layouts/UserLayouts.vue";
import DashboardTable from "./Components/DashboardTable.vue";
defineProps({
  orders: Array,
  //   orderItems: Array,
});
</script>
<template>
  <UserLayouts>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
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
    </div>
  </UserLayouts>
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
