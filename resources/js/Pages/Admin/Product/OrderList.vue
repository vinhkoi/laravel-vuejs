<template>
  <AdminLayout>
    <div class="card">
      <TreeTable
        ref="dt"
        :value="formattedOrders"
        :paginator="true"
        :rows="5"
        :rowsPerPageOptions="[5, 10, 25]"
        dataKey="id"
      >
        <Column field="id" header="Order ID" expander></Column>
        <Column field="total_price" header="Price"></Column>
        <Column field="status" header="Status"></Column>
        <Column field="quantity" header="Quantity"></Column>
        <Column field="brand" header="Brand"></Column>

        <Column :exportable="false" header="Action" style="min-width: 8rem">
          <template #body="{ row }">
            <Button icon="pi pi-pencil" outlined rounded class="mr-2 pencill" />
            <Button
              icon="pi pi-trash"
              outlined
              rounded
              severity="danger"
              class="trashh"
              @click="deleteProduct(row.data.id)"
            />
          </template>
        </Column>
      </TreeTable>
    </div>
    <Dialog
      v-model:visible="deleteProductDialog"
      :style="{ width: '450px' }"
      header="Confirm"
      :modal="true"
    >
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span>Are you sure you want to delete ?</span>
      </div>
      <template #footer>
        <Button label="No" icon="pi pi-times" text @click="deleteProductDialog = false" />
        <Button label="Yes" icon="pi pi-check" text @click="deleteProduct" />
      </template>
    </Dialog>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useToast } from "primevue/usetoast";
import axios from "axios";
import { router, usePage } from "@inertiajs/vue3";

defineProps({
  orders: Array,
});
const dt = ref();
const deleteProductDialog = ref(false);
const confirmDeleteProduct = () => {
  deleteProductDialog.value = true;
};

const order = ref({});

const orders = usePage().props.orders;
// dt = usePage().refs.dt;

console.log(orders);

const formattedOrders = computed(() => {
  if (!Array.isArray(orders)) {
    console.error("Orders is not an array:", orders);
    return [];
  }

  return orders.map((order) => ({
    key: order.id,
    data: {
      id: order.id,
      total_price: order.total_price,
      status: order.status,
    },
    children: order.order_items.map((item) => ({
      id: item.product.title,
      data: {
        id: item.product.title,
        total_price: item.product.price,
        status: order.status,
        quantity: item.quantity,
        brand: item.product.brand.name,
      },
    })),
  }));
  order.value = order;
});

const deleteProduct = () => {
  try {
    router.delete("orders/destroy/" + row.value.id, {
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

import AdminLayout from "../Components/AdminLayout.vue";
</script>
<style scoped>
.p-treetable .p-treetable-tbody > tr > td {
  text-align: left;
  border: 1px solid #e2e8f0;
  border-width: 0 0 1px 0;
  padding: 0.75rem 1rem;
  border-bottom-color: black !important;
}
</style>
