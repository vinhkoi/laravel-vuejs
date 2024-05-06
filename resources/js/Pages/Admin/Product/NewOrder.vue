<template>
  <DataTable
    ref="dt"
    v-model:expandedRows="expandedRows"
    :value="ordersWithinLast7Days"
    dataKey="id"
    tableStyle="min-width: 60rem"
    :paginator="true"
    :rows="10"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[5, 10, 25]"
    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} orders"
  >
    <Column sortable field="id" header="ID" headerStyle="width:auto; min-width:10rem;">
      <template #body="slotProps">
        <span class="p-column-title">Title</span>
        {{ slotProps.data.id }}
      </template>
    </Column>

    <Column
      sortable
      field="total_price"
      header="Price"
      headerStyle="width:auto; min-width:10rem;"
    >
      <template #body="slotProps">
        <span class="p-column-title">Title</span>
        {{ slotProps.data.total_price }}
      </template>
    </Column>

    <Column
      sortable
      field="status"
      header="Status"
      headerStyle="width:auto; min-width:10rem;"
    >
      <template #body="slotProps">
        <span
          class="p-tag p-component p-tag-danger"
          v-if="slotProps.data.status == 'unpaid'"
          >{{ slotProps.data.status }}</span
        >

        <span class="p-tag p-component p-tag-success" v-else>{{
          slotProps.data.status
        }}</span>
      </template>
    </Column>
    <Column
      field="user.email"
      header="Created by"
      headerStyle="width:auto; min-width:10rem;"
    >
      <template #body="slotProps">
        <span class="p-column-title">Title</span>
        <!-- {{ slotProps.data.created_at }} -->
        {{ formatDateTime(slotProps.data.created_at) }}
      </template>
    </Column>
  </DataTable>
</template>

<script setup>
defineProps({
  orders: Array,
});
import { router, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";

const orders = usePage().props.orders;
const currentDate = new Date();

// Tạo một computed property để lọc ra những đơn hàng trong vòng 7 ngày qua
const ordersWithinLast7Days = computed(() => {
  const sevenDaysAgo = new Date(currentDate);
  sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7); // Trừ đi 7 ngày

  // Lọc ra những đơn hàng có thời gian tạo trong khoảng 7 ngày qua
  return orders.filter((order) => {
    const orderDate = new Date(order.created_at);
    return orderDate >= sevenDaysAgo && orderDate <= currentDate;
  });
});

const formatDateTime = (dateTimeString) => {
  const dateTime = new Date(dateTimeString);
  const year = dateTime.getFullYear();
  const month = dateTime.getMonth() + 1;
  const day = dateTime.getDate();
  const hours = dateTime.getHours();
  const minutes = dateTime.getMinutes();
  const seconds = dateTime.getSeconds();
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
};
</script>

<style></style>
