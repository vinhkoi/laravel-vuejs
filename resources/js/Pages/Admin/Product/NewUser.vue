<template>
  <DataTable
    ref="dt"
    :value="past7DaysUsers"
    v-model:selection="selectedProducts"
    dataKey="id"
    :paginator="true"
    :rows="10"
    :filters="filters"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    :rowsPerPageOptions="[5, 10, 25]"
    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} users"
  >
    <template #header>
      <div class="flex flex-wrap gap-2 align-items-center justify-content-between aa">
        <h4 class="m-0">Manage User</h4>
      </div>
    </template>

    <Column field="id" header="ID" sortable headerStyle="width:14%; min-width:10rem;">
      <template #body="slotProps">
        <span class="p-column-title">ID User</span>
        {{ slotProps.data.id }}
      </template>
    </Column>
    <Column field="name" header="Name" sortable headerStyle="width:14%; min-width:10rem;">
      <template #body="slotProps">
        <span class="p-column-title">Name</span>
        {{ slotProps.data.name }}
      </template>
    </Column>
    <Column
      field="email"
      header="Email"
      sortable
      headerStyle="width:14%; min-width:10rem;"
      ><template #body="slotProps">
        <span class="p-column-title">Email</span>
        {{ slotProps.data.email }}
      </template>
    </Column>
    <Column
      field="isAdmin"
      header="Role"
      sortable
      headerStyle="width:14%; min-width:10rem;"
      ><template #body="slotProps">
        <!-- <span class="p-column-title">
                {{ slotProps.data.isAdmin === 1 ? "Admin" : "Guest" }}
              </span> -->

        {{ slotProps.data.isAdmin === 1 ? "Admin" : "Guest" }}
      </template>
    </Column>
    <Column
      field="created_at"
      header="Created at"
      sortable
      headerStyle="width:14%; min-width:10rem;"
      ><template #body="slotProps">
        <span class="p-column-title">Role</span>
        <!-- {{ slotProps.data.created_at }} -->
        {{ formatDateTime(slotProps.data.created_at) }}
      </template>
    </Column>
  </DataTable>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
defineProps({
  users: Array,
});
const users = usePage().props.users;

const past7DaysUsers = computed(() => {
  const today = new Date();
  const past7Days = new Date(today);
  past7Days.setDate(past7Days.getDate() - 7);

  return users.filter((user) => {
    const userDate = new Date(user.created_at);
    return userDate >= past7Days && userDate <= today;
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
