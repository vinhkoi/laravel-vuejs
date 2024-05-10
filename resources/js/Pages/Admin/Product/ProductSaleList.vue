<template>
  <AdminLayout>
    <div>
      <div class="card">
        <Toolbar class="mb-4">
          <template #start>
            <Button
              label="New"
              icon="pi pi-plus"
              severity="success"
              class="mr-2"
              @click="openNew"
            />
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
          :value="sale"
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
              <h4 class="m-0">Manage Category</h4>
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
              <span class="p-column-title">ID Category</span>
              {{ slotProps.data.id }}
            </template>
          </Column>
          <Column
            field="product_id"
            header="Product ID"
            sortable
            headerStyle="width:14%; min-width:10rem;"
          >
            <template #body="slotProps">
              <span class="p-column-title">Name</span>
              {{ slotProps.data.product_id }}
            </template>
          </Column>
          <Column
            field="discount_rate"
            header="Discount Rate"
            sortable
            headerStyle="width:14%; min-width:10rem;"
          >
            <template #body="slotProps">
              <span class="p-column-title">Slug</span>
              {{ slotProps.data.discount_rate }}
            </template>
          </Column>
          <Column
            field="discounted_price"
            header="Discount Price"
            sortable
            headerStyle="width:14%; min-width:10rem;"
          >
            <template #body="slotProps">
              <span class="p-column-title">Slug</span>
              {{ slotProps.data.discounted_price }}
            </template>
          </Column>
          <Column
            field="start_date"
            header="Start Date"
            sortable
            headerStyle="width:14%; min-width:10rem;"
            ><template #body="slotProps">
              <span class="p-column-title">Slug</span>
              {{ slotProps.data.start_date }}
            </template>
          </Column>
          <Column
            field="end_date"
            header="End Date"
            sortable
            headerStyle="width:14%; min-width:10rem;"
            ><template #body="slotProps">
              <span class="p-column-title">Slug</span>
              {{ slotProps.data.end_date }}
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
        header="Category Details"
        :modal="true"
        class="p-fluid"
      >
        <!-- <img
            v-if="product.productImages"
            :src="`product.productImages`"
            :alt="product.productImages"
            class="block m-auto pb-3"
          /> -->
        <div class="field">
          <label
            for="countries"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >Select Product</label
          >
          <select
            v-model="product_id"
            v-on:change="onSelectProduct"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option v-for="productt in productes" :key="productt.id" :value="productt.id">
              {{ productt.title }}
            </option>
          </select>
        </div>
        <div class="field">
          <label for="original_price">Original Price</label>
          <InputNumber
            id="original_price"
            v-model.number="originalPrice"
            autofocus
            readonly
          />
        </div>
        <div class="field">
          <label for="slug">Discount Rate</label>
          <InputNumber
            id="discountRate"
            v-model.number="discountRate"
            required="true"
            autofocus
            @change="calculateDiscountedPrice"
            :class="{ 'p-invalid': submitted && !slug }"
          />
          <small class="p-error" v-if="submitted && !slug">Slug is required.</small>
        </div>
        <div class="field">
          <label for="slug">Discounted Price</label>
          <InputNumber
            id="discountedPrice"
            v-model.number="discountedPrice"
            required="true"
            autofocus
            readonly
            :class="{ 'p-invalid': submitted && !slug }"
          />
          <small class="p-error" v-if="submitted && !slug">Slug is required.</small>
        </div>
        <div class="field">
          <FloatLabel>
            <label for="start_date">Start Date</label>
            <Calendar
              id="start_date"
              v-model.trim="start_date"
              required="true"
              autofocus
              :class="{ 'p-invalid': submitted && !slug }"
              dateFormat="dd/mm/yy"
            />
            <small class="p-error" v-if="submitted && !slug">Slug is required.</small>
          </FloatLabel>
        </div>
        <div class="field">
          <FloatLabel
            ><label for="end_date">End Date</label>

            <Calendar
              id="end_date"
              v-model.trim="end_date"
              required="true"
              autofocus
              :class="{ 'p-invalid': submitted && !slug }"
              dateFormat="dd/mm/yy"
            />

            <small class="p-error" v-if="submitted && !slug">Slug is required.</small>
          </FloatLabel>
        </div>

        <template #footer>
          <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
          <Button label="Save" icon="pi pi-check" text @click="AddProduct" />
        </template>
      </Dialog>

      <Dialog
        v-model:visible="editproductDialog"
        :style="{ width: '450px' }"
        header="Category Details"
        :modal="true"
        class="p-fluid"
      >
        <div class="field">
          <label
            for="countries"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
            >Select Product</label
          >
          <select
            v-model="sales.product_id"
            v-on:change="onSelectProduct"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option v-for="productt in productes" :key="productt.id" :value="productt.id">
              {{ productt.title }}
            </option>
          </select>
        </div>
        <div class="field">
          <label for="original_price">Original Price</label>
          <InputNumber
            id="original_price"
            v-model.number="originalPrice"
            autofocus
            readonly
          />
        </div>
        <div class="field">
          <label for="slug">Discount Rate</label>
          <InputNumber
            id="discountRate"
            v-model.number="sales.discount_rate"
            required="true"
            autofocus
            @change="calculateDiscountedPricee"
            :class="{ 'p-invalid': submitted && !slug }"
          />
          <small class="p-error" v-if="submitted && !slug">Slug is required.</small>
        </div>
        <div class="field">
          <label for="slug">Discounted Price</label>
          <InputNumber
            id="discountedPrice"
            v-model.number="sales.discounted_price"
            required="true"
            autofocus
            readonly
            :class="{ 'p-invalid': submitted && !slug }"
          />
          <small class="p-error" v-if="submitted && !slug">Slug is required.</small>
        </div>
        <div class="field">
          <FloatLabel>
            <label for="start_date">Start Date</label>
            <Calendar
              id="start_date"
              v-model.trim="sales.start_date"
              required="true"
              autofocus
              :class="{ 'p-invalid': submitted && !slug }"
              dateFormat="dd/mm/yy"
            />
            <small class="p-error" v-if="submitted && !slug">Slug is required.</small>
          </FloatLabel>
        </div>
        <div class="field">
          <FloatLabel
            ><label for="end_date">End Date</label>

            <Calendar
              id="end_date"
              v-model.trim="sales.end_date"
              required="true"
              autofocus
              :class="{ 'p-invalid': submitted && !slug }"
              dateFormat="dd/mm/yy"
            />

            <small class="p-error" v-if="submitted && !slug">Slug is required.</small>
          </FloatLabel>
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
          <span v-if="sales"
            >Are you sure you want to delete <b>{{ sales.id }}</b
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
import dayjs from "dayjs";
import { ref, onMounted, watch, computed } from "vue";
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";
import axios from "axios";
import { router, usePage } from "@inertiajs/vue3";
import { Plus } from "@element-plus/icons-vue";
import AdminLayout from "../Components/AdminLayout.vue";

defineProps({
  sale: Array,
  product: Array,
});
const productes = usePage().props.product;
const discountRate = ref(null);
const discountedPrice = ref(null);

const product_id = ref(null); // Chọn ID của sản phẩm
const originalPrice = ref(null);
const toast = useToast();
const dt = ref();
const id = ref("");
const name = ref("");
const slug = ref("");
const sales = ref({ discount_rate: null, discounted_price: null });

const start_date = ref(null);
const end_date = ref(null);

const productDialog = ref(false);
const dialogVisible = ref(false);
const editproductDialog = ref(false);

const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);

const selectedProducts = ref();
const onSelectProduct = (event) => {
  product_id.value = event.target.value; // Cập nhật product_id
  console.log(product_id.value);
};
const updateOriginalPrice = () => {
  if (!product_id.value) {
    console.log("product_id is invalid:", product_id.value);
    return;
  }

  const productMap = new Map(productes.map((product) => [product.id, product]));
  const selectedProduct = productMap.get(Number(product_id.value));

  if (!selectedProduct) {
    console.error("Product not found with id:", product_id.value);
    return;
  }

  originalPrice.value = selectedProduct.price;
};

watch(product_id, (newProductId) => {
  updateOriginalPrice();
});
const calculateDiscountedPrice = () => {
  if (discountRate.value !== null && originalPrice.value !== null) {
    discountedPrice.value = originalPrice.value * (1 - discountRate.value / 100);
    console.log(discountedPrice.value);
  }
};
// Computed property để tính toán giá giảm khi discountRate hoặc originalPrice thay đổi
// const discountedPriceComputed = computed(() => {
//   if (discountRate.value !== null && originalPrice.value !== null) {
//     return originalPrice.value * (1 - discountRate.value / 100);
//   }
//   return null;
// });

// Watcher để theo dõi sự thay đổi của discountRate và cập nhật giá trị discountedPrice
watch([discountRate, originalPrice], () => {
  calculateDiscountedPrice();
});
const calculateDiscountedPricee = () => {
  if (sales.value.discount_rate !== null && originalPrice.value !== null) {
    sales.value.discounted_price =
      originalPrice.value * (1 - sales.value.discount_rate / 100);
    console.log(sales.value.discounted_price);
  }
};

// Theo dõi sự thay đổi của trường discount_rate và tự động tính toán giá giảm giá
watch(() => sales.discount_rate, calculateDiscountedPricee);
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);

const openNew = () => {
  sales.value = {};
  submitted.value = false;
  productDialog.value = true;
};
const hideDialog = () => {
  productDialog.value = false;
  submitted.value = false;
  editproductDialog.value = false;
};

const editProduct = (editProduct) => {
  sales.value = { ...editProduct };
  editproductDialog.value = true;
  console.log("Selected product:", editProduct);
};
const confirmDeleteProduct = (prod) => {
  sales.value = prod;
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
watch([start_date, end_date], ([newStartDate, newEndDate]) => {
  // Kiểm tra nếu cả start_date và end_date đã được nhập
  if (newStartDate && newEndDate) {
    // Chuyển đổi start_date và end_date sang định dạng mong muốn (ví dụ: YYYY-MM-DD HH:mm:ss)
    const formattedStartDate = dayjs(newStartDate).format("YYYY-MM-DD HH:mm:ss");
    const formattedEndDate = dayjs(newEndDate).format("YYYY-MM-DD HH:mm:ss");

    // Cập nhật lại giá trị của start_date và end_date với các giá trị đã được định dạng
    start_date.value = formattedStartDate;
    end_date.value = formattedEndDate;
    console.log(start_date.value);
    console.log(end_date.value);
  }
});
const AddProduct = async () => {
  const formData = new FormData();
  formData.append("product_id", product_id.value);
  formData.append("discount_rate", discountRate.value);
  formData.append("discounted_price", discountedPrice.value);
  formData.append("start_date", start_date.value);
  formData.append("end_date", end_date.value);

  // Append product images to the FormData

  try {
    await router.post("sale/store", formData, {
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
  formData.append("product_id", sales.value.product_id);
  formData.append("discount_rate", sales.value.discountRate);
  formData.append("discounted_price", sales.value.discountedPrice);
  formData.append("start_date", sales.value.start_date);
  formData.append("end_date", sales.value.end_date);
  formData.append("_method", "PUT");
  // Append product images to the FormData

  try {
    await router.post("sale/update/" + sales.value.id, formData, {
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
  discount_rate.value = "";
  discounted_price.value = "";
  product_id.value = "";
  start_date.value = "";
  end_date.value = "";
  discountRate.value = "";
  discountPrice.value = "";
};

const deleteProduct = () => {
  try {
    router.delete("sale/destroy/" + sales.value.id, {
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
