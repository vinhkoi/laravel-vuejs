<template>
  <div class="card">
    <Toolbar class="mb-4">
      <template #end>
        <Button
          label="Export"
          icon="pi pi-upload"
          severity="help"
          @click="exportCSV($event)"
        />
      </template>
    </Toolbar>
    <DataTable
      ref="dt"
      v-model:expandedRows="expandedRows"
      :value="orders"
      dataKey="id"
      @rowExpand="onRowExpand"
      @rowCollapse="onRowCollapse"
      tableStyle="min-width: 60rem"
      :paginator="true"
      :rows="10"
      :filters="filters"
      paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
      :rowsPerPageOptions="[5, 10, 25]"
      currentPageReportTemplate="Showing {first} to {last} of {totalRecords} orders"
    >
      <template #header>
        <div class="flex flex-wrap justify-content-end gap-2 c">
          <Button text icon="pi pi-minus" label="Collapse All" @click="collapseAll" />
          <div class="bb">
            <IconField iconPosition="left">
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="filters['global'].value" placeholder="Search..." />
            </IconField>
          </div>
        </div>
      </template>
      <Column expander style="width: 5rem" />
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
          {{ slotProps.data.user.email }}
        </template>
      </Column>

      <template #expansion="slotProps">
        <div class="p-3" v-if="slotProps.data">
          <h5>Orders {{ slotProps.data.id }}</h5>
          <DataTable :value="slotProps.data.order_items" ref="dt">
            <Column field="title" header="Name">
              <template #body="slotProps">
                <span class="p-column-title">Title</span>
                {{ slotProps.data.product.title }}
              </template>
            </Column>
            <Column sortable field="quantity" header="Quantity"></Column>
            <Column sortable field="unit_price" header="Price"></Column>
            <Column sortable field="product_id" header="Brand">
              <template #body="slotProps">
                <span class="p-column-title">Title</span>
                {{ slotProps.data.product.brand.name }}
              </template>
            </Column>
            <Column sortable field="product_id" header="Category">
              <template #body="slotProps">
                <span class="p-column-title">Title</span>
                {{ slotProps.data.product.category.name }}
              </template>
            </Column>
          </DataTable>
        </div>
      </template>
    </DataTable>
    <Toast />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";
import { router, usePage } from "@inertiajs/vue3";

defineProps({
  orders: Array,
});
const auth = usePage().props.auth;
const orderss = usePage().props.orders;
const expandedRows = ref({});
const toast = useToast();
console.log(orderss);
const dt = ref();
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const exportCSV = () => {
  dt.value.exportCSV();
};
const onRowExpand = (event) => {
  toast.add({
    severity: "info",
    summary: "Product Expanded",
    detail: event.data.name,
    life: 3000,
  });
};
const onRowCollapse = (event) => {
  toast.add({
    severity: "success",
    summary: "Product Collapsed",
    detail: event.data.name,
    life: 3000,
  });
};

const collapseAll = () => {
  expandedRows.value = null;
};
const deleteProduct = (orderId) => {
  try {
    router.delete("orders/destroy/" + orderId, {
      onSuccess: (page) => {
        Swal.fire({
          toast: true,
          icon: "success",
          position: "top-end",
          showConfirmButton: false,
          title: page.props.flash.success,
        });
      },
    });
  } catch (error) {
    console.log(error);
  }
};
</script>
<style scoped>
@media screen and (min-width: 768px) {
  .md\:align-items-center {
    align-items: center !important;
  }
}

@media screen and (min-width: 768px) {
  .md\:justify-content-between {
    justify-content: space-between !important;
  }
}

@media screen and (min-width: 768px) {
  .md\:flex-row {
    flex-direction: row !important;
  }
}

.aa {
  display: flex;
  justify-content: space-between;
}

.mt-2 {
  margin-top: 0.5rem !important;
}

.p-icon-field-left > .p-inputtext {
  padding-left: 2.5rem;
}

.p-inputtext {
  font-family: var(--font-family);
  font-feature-settings: var(--font-feature-settings, normal);
  font-size: 1rem;
  color: #334155;
  background: #ffffff;
  padding: 0.5rem 0.75rem;
  border: 1px solid #cbd5e1;
  transition: background-color 0.2s, color 0.2s, border-color 0.2s, box-shadow 0.2s,
    outline-color 0.2s;
  appearance: none;
  border-radius: 6px;
  outline-color: transparent;
}

.p-checkbox.p-variant-filled .p-checkbox-box {
  background-color: #f8fafc;
}

.p-checkbox-box {
  border: 1px solid #cbd5e1;
}

#inventoryStatus {
  background: #f8fafc;
}

.p-dropdown .p-dropdown-label.p-placeholder {
  color: #64748b;
}

.p-dropdown .p-dropdown-label {
  box-shadow: none;
  border: 0 none;
  background: #f8fafc;
}

.field {
  margin-bottom: 1rem;
}

.formgrid.grid {
  margin-top: 0;
}

.cc {
  display: flex;
  flex-wrap: wrap;
  margin-right: -1rem;
  margin-left: -1rem;
  margin-top: -1rem;
}

.field-radiobutton {
  padding-top: 0;
  padding-bottom: 0;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
}

.col-6 {
  flex: 0 0 auto;
  padding: 1rem;
  width: 50%;
}

.p-radiobutton .p-radiobutton-input {
  appearance: none;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  padding: 0;
  margin: 0;
  opacity: 0;
  z-index: 1;
  outline: 0 none;
  border: 1px solid #cbd5e1;
  border-radius: 50%;
}

.p-radiobutton-box {
  background-color: #f5f5f5;
}

.p-button.p-button-outlined {
  background-color: transparent;
  color: #10b981;
  border: 1px solid;
}

.p-button .pencill {
  color: #ffffff;
  background: #10b981;
  border: 1px solid #10b981;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  transition: background-color 0.2s, color 0.2s, border-color 0.2s, box-shadow 0.2s,
    outline-color 0.2s;
  border-radius: 39px;
  outline-color: transparent;
}

.p-button {
  color: #ffffff;
  background: #10b981;
  border: 1px solid #10b981;
  padding: 0.5rem 1rem;
}

.delete_btn {
  color: #ffffff;
  background: #ef4444;
  border: 1px solid #ef4444;
}

.trashh {
  color: #ffffff;
  background: #ef4444;
  border: 1px solid #ef4444 !important;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  transition: background-color 0.2s, color 0.2s, border-color 0.2s, box-shadow 0.2s,
    outline-color 0.2s;
  border-radius: 39px;
  outline-color: transparent;
}

.pi-trash {
  color: #ef4444;
}

.p-button.p-button-danger.pi-trash {
  color: white;
}
.p-button.p-button-outlined {
  color: #ef4444;
  border: 1px solid;
}
.columnheader {
  width: auto !important;
}
.c {
  display: flex;
  justify-content: space-between;
}
</style>
