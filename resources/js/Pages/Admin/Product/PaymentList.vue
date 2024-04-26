<template>
  <AdminLayout>
    <div>
      <div class="card">
        <Toolbar class="mb-4">
          <template #start>
            <!-- <Button
              label="New"
              icon="pi pi-plus"
              severity="success"
              class="mr-2"
              @click="openNew"
            /> -->
            <Button
              label="Delete"
              icon="pi pi-trash"
              severity="danger"
              class="delete_btn"
              @click="confirmDeleteSelected"
              :disabled="!selectedProducts || !selectedProducts.length"
            />
          </template>

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
          :value="payments"
          v-model:selection="selectedProducts"
          dataKey="id"
          :paginator="true"
          :rows="10"
          :filters="filters"
          paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
          :rowsPerPageOptions="[5, 10, 25]"
          currentPageReportTemplate="Showing {first} to {last} of {totalRecords} categories"
        >
          <template #header>
            <div
              class="flex flex-wrap gap-2 align-items-center justify-content-between aa"
            >
              <h4 class="m-0">Manage Payment</h4>
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

          <Column
            selectionMode="multiple"
            style="width: 3rem"
            :exportable="false"
          ></Column>

          <Column
            field="id"
            header="ID"
            sortable
            headerStyle="width:14%; min-width:10rem;"
          >
            <template #body="slotProps">
              <span class="p-column-title">ID Payment</span>
              {{ slotProps.data.id }}
            </template>
          </Column>
          <Column
            field="order_id"
            header="Order ID"
            sortable
            headerStyle="width:14%; min-width:10rem;"
          >
            <template #body="slotProps">
              <span class="p-column-title">Name</span>
              {{ slotProps.data.order_id }}
            </template>
          </Column>
          <Column
            field="amount"
            header="Price"
            sortable
            headerStyle="width:14%; min-width:10rem;"
            ><template #body="slotProps">
              <span class="p-column-title">Price</span>
              {{ slotProps.data.amount }}
            </template>
          </Column>
          <Column
            field="type"
            header="Type"
            sortable
            headerStyle="width:14%; min-width:10rem;"
            ><template #body="slotProps">
              <span class="p-column-title">Type</span>
              {{ slotProps.data.type === "stripe" ? "Stripe" : "COD" }}
            </template>
          </Column>
          <Column
            field="status"
            header="Status"
            sortable
            headerStyle="width:14%; min-width:10rem;"
            ><template #body="slotProps">
              <span
                class="p-tag p-component p-tag-warning"
                v-if="slotProps.data.status == 'pending'"
                >{{ slotProps.data.status }}</span
              >
              <span
                class="p-tag p-component p-tag-danger"
                v-else-if="slotProps.data.status === 'cancel'"
                >{{ slotProps.data.status }}</span
              >
              <span class="p-tag p-component p-tag-success" v-else>{{
                slotProps.data.status
              }}</span>
            </template>
          </Column>

          <Column :exportable="false" header="Action" style="min-width: 8rem">
            <template #body="slotProps">
              <Button
                icon="pi pi-pencil"
                outlined
                rounded
                class="mr-2 pencill"
                @click="editProduct(slotProps.data)"
              />
              <Button
                icon="pi pi-trash"
                outlined
                rounded
                severity="danger"
                class="trashh"
                @click="confirmDeleteProduct(slotProps.data)"
              />
            </template>
          </Column>
        </DataTable>
      </div>

      <Dialog
        v-model:visible="productDialog"
        :style="{ width: '450px' }"
        header="Payment Details"
        :modal="true"
        class="p-fluid"
      >
        <div class="field">
          <label for="description">Order ID</label>
          <InputText
            id="description"
            v-model="order_id"
            required="true"
            rows="3"
            cols="20"
          />
        </div>
        <div class="field">
          <label for="description">Price</label>
          <InputText id="amount" v-model="amount" required="true" rows="3" cols="20" />
        </div>
        <!-- <div class="field">
          <label for="description">Type</label>
          <InputText
            id="type"
            v-model="payment.type"
            required="true"
            rows="3"
            cols="20"
          />
        </div> -->
        <div class="field">
          <label
            for="countries"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >Select Type</label
          >
          <select
            v-model="type"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="stripe">Stripe</option>
            <option value="cash_on_delivery">COD</option>
          </select>
        </div>
        <!-- <div class="field">
          <label for="description">Status</label>
          <InputText
            id="status"
            v-model="payment.status"
            required="true"
            rows="3"
            cols="20"
          />
        </div> -->
        <div class="field">
          <label
            for="countries"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >Status</label
          >
          <select
            v-model="status"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="success">Success</option>
            <option value="pending">Pending</option>
            <option value="cancel">Cancel</option>
          </select>
        </div>

        <template #footer>
          <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
          <Button label="Save" icon="pi pi-check" text @click="AddProduct" />
        </template>
      </Dialog>

      <Dialog
        v-model:visible="editproductDialog"
        :style="{ width: '450px' }"
        header="Payment Details"
        :modal="true"
        class="p-fluid"
      >
        <div class="field">
          <label for="name">ID</label>
          <InputText
            id="id"
            v-model.trim="payment.id"
            required="true"
            autofocus
            :class="{ 'p-invalid': submitted && !name }"
          />
          <small class="p-error" v-if="submitted && !name">ID is required.</small>
        </div>
        <div class="field">
          <label for="description">Order ID</label>
          <InputText
            id="description"
            v-model="payment.order_id"
            required="true"
            rows="3"
            cols="20"
          />
        </div>
        <div class="field">
          <label for="description">Price</label>
          <InputText
            id="amount"
            v-model="payment.amount"
            required="true"
            rows="3"
            cols="20"
          />
        </div>
        <!-- <div class="field">
          <label for="description">Type</label>
          <InputText
            id="type"
            v-model="payment.type"
            required="true"
            rows="3"
            cols="20"
          />
        </div> -->
        <div class="field">
          <label
            for="countries"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >Select Type</label
          >
          <select
            v-model="payment.type"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="stripe">Stripe</option>
            <option value="cash_on_delivery">COD</option>
          </select>
        </div>
        <!-- <div class="field">
          <label for="description">Status</label>
          <InputText
            id="status"
            v-model="payment.status"
            required="true"
            rows="3"
            cols="20"
          />
        </div> -->
        <div class="field">
          <label
            for="countries"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >Status</label
          >
          <select
            v-model="payment.status"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="success">Success</option>
            <option value="pending">Pending</option>
            <option value="cancel">Cancel</option>
          </select>
        </div>

        <template #footer>
          <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
          <Button label="Save" icon="pi pi-check" text @click="updateProduct" />
        </template>
      </Dialog>

      <Dialog
        v-model:visible="deleteProductDialog"
        :style="{ width: '450px' }"
        header="Confirm"
        :modal="true"
      >
        <div class="confirmation-content">
          <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
          <span v-if="payment"
            >Are you sure you want to delete <b>{{ payment.id }}</b
            >?</span
          >
        </div>
        <template #footer>
          <Button
            label="No"
            icon="pi pi-times"
            text
            @click="deleteProductDialog = false"
          />
          <Button label="Yes" icon="pi pi-check" text @click="deleteProduct" />
        </template>
      </Dialog>

      <Dialog
        v-model:visible="deleteProductsDialog"
        :style="{ width: '450px' }"
        header="Confirm"
        :modal="true"
      >
        <div class="confirmation-content">
          <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
          <span v-if="product"
            >Are you sure you want to delete the selected products?</span
          >
        </div>
        <template #footer>
          <Button
            label="No"
            icon="pi pi-times"
            text
            @click="deleteProductsDialog = false"
          />
          <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedProducts" />
        </template>
      </Dialog>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";
import axios from "axios";
import { router, usePage } from "@inertiajs/vue3";
import { Plus } from "@element-plus/icons-vue";

defineProps({
  payments: Array,
});

import AdminLayout from "../Components/AdminLayout.vue";
const toast = useToast();
const dt = ref();
const id = ref("");
const order_id = ref("");
const amount = ref("");
const type = ref("");
const status = ref("");

const productDialog = ref(false);
const dialogVisible = ref(false);
const editproductDialog = ref(false);

const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const payment = ref({});

const selectedProducts = ref();

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);

const openNew = () => {
  payment.value = {};
  submitted.value = false;
  productDialog.value = true;
};
const hideDialog = () => {
  productDialog.value = false;
  submitted.value = false;
  editproductDialog.value = false;
};

const editProduct = (editProduct) => {
  payment.value = { ...editProduct };
  editproductDialog.value = true;
  console.log("Selected product:", editProduct);
};
const confirmDeleteProduct = (prod) => {
  payment.value = prod;
  deleteProductDialog.value = true;
};
const deleteProductt = () => {
  products.value = products.value.filter((val) => val.id !== product.value.id);
  deleteProductDialog.value = false;
  product.value = {};
  toast.add({
    severity: "success",
    summary: "Successful",
    detail: "Product Deleted",
    life: 3000,
  });
};

const exportCSV = () => {
  dt.value.exportCSV();
};
const confirmDeleteSelected = () => {
  deleteProductsDialog.value = true;
};
const deleteSelectedProducts = () => {
  products.value = products.value.filter((val) => !selectedProducts.value.includes(val));
  deleteProductsDialog.value = false;
  selectedProducts.value = null;
  toast.add({
    severity: "success",
    summary: "Successful",
    detail: "Products Deleted",
    life: 3000,
  });
};

const dialogImageUrl = ref("");

const AddProduct = async () => {
  const formData = new FormData();
  formData.append("order_id", order_id.value);
  formData.append("amount", amount.value);
  formData.append("type", type.value);
  formData.append("status", status.value);

  try {
    await router.post("payments/store", formData, {
      onSuccess: (page) => {
        Swal.fire({
          toast: true,
          icon: "success",
          position: "top-end",
          showConfirmButton: false,
          title: page.props.flash.success,
        });

        productDialog.value = false;
        resetFormData();
      },
    });
  } catch (error) {
    console.log(error);
  }
};
const updateProduct = async () => {
  const formData = new FormData();
  formData.append("order_id", payment.value.order_id);
  formData.append("amount", payment.value.amount);
  formData.append("type", payment.value.type);
  formData.append("status", payment.value.status);
  formData.append("_method", "PUT");
  // Append product images to the FormData

  try {
    await router.post("payments/update/" + payment.value.id, formData, {
      onSuccess: (page) => {
        Swal.fire({
          toast: true,
          icon: "success",
          position: "top-end",
          showConfirmButton: false,
          title: page.props.flash.success,
        });
        editproductDialog.value = false;
        resetFormData();
      },
    });
  } catch (err) {
    console.log(err);
  }
};
const resetFormData = () => {
  order_id.value = "";
  amount.value = "";
  type.value = "";
  status.value = "";
};

const deleteProduct = () => {
  try {
    router.delete("payments/destroy/" + payment.value.id, {
      onSuccess: (page) => {
        Swal.fire({
          toast: true,
          icon: "success",
          position: "top-end",
          showConfirmButton: false,
          title: page.props.flash.success,
        });
        deleteProductDialog.value = false;
      },
    });
  } catch (error) {
    console.log(error);
  }
};
</script>
<style>
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

.p-fileupload-buttonbar {
  display: flex;
  flex-wrap: nowrap;
}

.p-button.p-button-danger.pi-trash {
  color: white;
}
.p-sortable-column {
  width: auto !important;
}
</style>
