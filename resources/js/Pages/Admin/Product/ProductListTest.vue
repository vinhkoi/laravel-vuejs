<template>
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
        :value="products"
        v-model:selection="selectedProducts"
        dataKey="id"
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} products"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 align-items-center justify-content-between aa">
            <h4 class="m-0">Manage Products</h4>
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

        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

        <Column
          field="title"
          header="Title"
          sortable
          headerStyle="width:14%; min-width:10rem;"
        >
          <template #body="slotProps">
            <span class="p-column-title">Title</span>
            {{ slotProps.data.title }}
          </template>
        </Column>
        <Column
          field="category.name"
          header="Category ID"
          sortable
          headerStyle="width:14%; min-width:10rem;"
        >
          <template #body="slotProps">
            <span class="p-column-title">Category</span>
            {{ slotProps.data.category.name }}
          </template>
        </Column>
        <Column
          field="brand_id"
          header="Brand ID"
          sortable
          headerStyle="width:14%; min-width:10rem;"
          ><template #body="slotProps">
            <span class="p-column-title">Brand</span>
            {{ slotProps.data.brand.name }}
          </template>
        </Column>
        <Column
          field="quantity"
          header="Quantity"
          sortable
          headerStyle="width:14%; min-width:10rem;"
          ><template #body="slotProps">
            <span class="p-column-title">Quantity</span>
            {{ slotProps.data.quantity }}
          </template>
        </Column>
        <Column
          field="description"
          header="Description"
          sortable
          headerStyle="width:14%; min-width:10rem;"
          ><template #body="slotProps">
            <span class="p-column-title">Description</span>
            <div class="description-container">
              {{ slotProps.data.description }}
            </div>
          </template>
        </Column>
        <Column
          field="price"
          header="Price"
          sortable
          headerStyle="width:14%; min-width:10rem;"
          ><template #body="slotProps">
            <span class="p-column-title">Price</span>
            ${{ slotProps.data.price }}
          </template>
        </Column>
        <Column
          field="inStock"
          header="In Stock"
          sortable
          headerStyle="width:14%; min-width:10rem;"
          ><template #body="slotProps">
            <span
              class="p-tag p-component p-tag-success"
              v-if="slotProps.data.inStock === 1"
              >In Stock</span
            >
            <span class="p-tag p-component p-tag-danger" v-else>Out Stock</span>

            <!-- {{ slotProps.data.inStock === 1 ? "IN STOCK" : "OUT STOCK" }} -->
          </template>
        </Column>
        <Column
          field="published"
          header="Published"
          sortable
          headerStyle="width:14%; min-width:10rem;"
          ><template #body="slotProps">
            <span
              class="p-tag p-component p-tag-success"
              v-if="slotProps.data.published === 1"
              >Published</span
            >
            <span class="p-tag p-component p-tag-danger" v-else>Unpublished</span>
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
      header="Product Details"
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
        <label for="title">Title</label>
        <InputText
          id="title"
          v-model.trim="title"
          required="true"
          autofocus
          :class="{ 'p-invalid': submitted && !title }"
        />
        <small class="p-error" v-if="submitted && !title">Title is required.</small>
      </div>
      <div class="field">
        <label for="description">Description</label>
        <Textarea
          id="description"
          v-model="description"
          required="true"
          rows="3"
          cols="20"
        />
      </div>

      <div class="field">
        <label
          for="countries"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Select Category</label
        >
        <select
          v-model="category_id"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <div class="field">
        <label
          for="countries"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Select Brand</label
        >
        <select
          v-model="brand_id"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >
          <option v-for="brand in brands" :key="brand.id" :value="brand.id">
            {{ brand.name }}
          </option>
        </select>
      </div>
      <div class="field">
        <label
          for="published"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Published</label
        >
        <select
          v-model="published"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >
          <option value="0">Unpublished</option>
          <option value="1">Published</option>
        </select>
      </div>
      <div class="field">
        <label
          for="isAdmin"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >InStock</label
        >
        <select
          v-model="inStock"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >
          <option value="0">Out of Stock</option>
          <option value="1">InStock</option>
        </select>
      </div>
      <div class="formgrid grid">
        <div class="field col">
          <label for="price">Price</label>
          <InputNumber
            id="price"
            v-model="price"
            mode="currency"
            currency="USD"
            locale="en-US"
          />
        </div>
        <div class="field col">
          <label for="quantity">Quantity</label>
          <InputNumber id="quantity" v-model="quantity" integeronly />
        </div>
      </div>
      <div class="card">
        <el-upload
          v-model:file-list="productImages"
          action="/upload-images"
          method="post"
          list-type="picture-card"
          multiple
          :on-preview="handlePictureCardPreview"
          :on-remove="handleRemove"
          :on-change="handleFileChange"
        >
          <el-icon class="avatar-uploader-icon">
            <Plus />
          </el-icon>
        </el-upload>
      </div>

      <template #footer>
        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
        <Button label="Save" icon="pi pi-check" text @click="AddProduct" />
      </template>
    </Dialog>

    <Dialog
      v-model:visible="editproductDialog"
      :style="{ width: '450px' }"
      header="Product Details"
      :modal="true"
      class="p-fluid"
    >
      <div class="field">
        <label for="title">Title</label>
        <InputText
          id="title"
          v-model.trim="product.title"
          required="true"
          autofocus
          :class="{ 'p-invalid': submitted && !title }"
        />
        <small class="p-error" v-if="submitted && !title">Title is required.</small>
      </div>
      <div class="field">
        <label for="description">Description</label>
        <Textarea
          id="description"
          v-model="product.description"
          required="true"
          rows="3"
          cols="20"
        />
      </div>

      <div class="field">
        <label
          for="countries"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Select Category</label
        >
        <select
          v-model="product.category_id"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <div class="field">
        <label
          for="countries"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Select Brand</label
        >
        <select
          v-model="product.brand_id"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >
          <option v-for="brand in brands" :key="brand.id" :value="brand.id">
            {{ brand.name }}
          </option>
        </select>
      </div>
      <div class="field">
        <label
          for="published"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >Published</label
        >
        <select
          v-model="product.published"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >
          <option value="0">Unpublished</option>
          <option value="1">Published</option>
        </select>
      </div>
      <div class="field">
        <label
          for="isAdmin"
          class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
          >InStock</label
        >
        <select
          v-model="product.inStock"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        >
          <option value="0">Out of Stock</option>
          <option value="1">InStock</option>
        </select>
      </div>
      <div class="formgrid grid">
        <div class="field col">
          <label for="price">Price</label>
          <InputNumber
            id="price"
            v-model="product.price"
            mode="currency"
            currency="USD"
            locale="en-US"
          />
        </div>
        <div class="field col">
          <label for="quantity">Quantity</label>
          <InputNumber id="quantity" v-model="product.quantity" integeronly />
        </div>
      </div>
      <div class="card">
        <el-upload
          v-model:file-list="productImages"
          action="/upload-images"
          method="post"
          list-type="picture-card"
          multiple
          :on-preview="handlePictureCardPreview"
          :on-remove="handleRemove"
          :on-change="handleFileChange"
        >
          <el-icon class="avatar-uploader-icon">
            <Plus />
          </el-icon>
        </el-upload>
      </div>
      <div class="flex flex-nowrap mb-8">
        <div
          v-for="(pimage, index) in product.product_images"
          :key="pimage.id"
          class="relative w-32 h-32"
        >
          <img class="w-24 h-20 rounded" :src="`/${pimage.image}`" alt="" />
          <span
            class="absolute top-0 right-8 transform -translate-y-1/2 w-3.5 h-3.5 bg-red-400 border-2 border-white dark:border-gray-800 rounded-full"
          >
            <span
              @click="deleteImage(pimage, index)"
              class="text-white text-xs font-bold absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
              >x</span
            >
          </span>
        </div>
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
        <span v-if="product"
          >Are you sure you want to delete <b>{{ product.title }}</b
          >?</span
        >
      </div>
      <template #footer>
        <Button label="No" icon="pi pi-times" text @click="deleteProductDialog = false" />
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
        <span v-if="product">Are you sure you want to delete the selected products?</span>
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
</template>

<script setup>
import { ref, onMounted } from "vue";
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";
import { ProductService } from "./ProductService.js";
import axios from "axios";
import { router, usePage } from "@inertiajs/vue3";
import { Plus } from "@element-plus/icons-vue";

defineProps({
  products: Array,
});

const brands = usePage().props.brands;
const categories = usePage().props.categories;

// onMounted(() => {
//     ProductService.getProducts().then((data) => (products.value = data));
// });

// const products = ref([]);
const toast = useToast();
const dt = ref();
const id = ref("");
const title = ref("");
const price = ref("");
const quantity = ref("");
const description = ref("");
const product_images = ref([]);
const published = ref("");
const category_id = ref("");
const brand_id = ref("");
const inStock = ref("");
const productDialog = ref(false);
const dialogVisible = ref(false);
const editproductDialog = ref(false);

const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref({});
const selectedProducts = ref();
const productImages = ref([]);
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);

const formatCurrency = (value) => {
  if (value) return value.toLocaleString("en-US", { style: "currency", currency: "USD" });
  return;
};
const openNew = () => {
  product.value = {};
  submitted.value = false;
  productDialog.value = true;
};
const hideDialog = () => {
  productDialog.value = false;
  submitted.value = false;
  editproductDialog.value = false;
};

const editProduct = (editProduct) => {
  product.value = { ...editProduct };
  editproductDialog.value = true;
  console.log("Selected product:", editProduct);
};
const confirmDeleteProduct = (prod) => {
  product.value = prod;
  deleteProductDialog.value = true;
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
const handleFileChange = (file) => {
  console.log(file);
  productImages.value.push(file);
};

const handlePictureCardPreview = (file) => {
  dialogImageUrl.value = file.url;
  dialogVisible.value = true;
};

const handleRemove = (file) => {
  console.log(file);
};
const AddProduct = async () => {
  const formData = new FormData();
  formData.append("title", title.value);
  formData.append("price", price.value);
  formData.append("quantity", quantity.value);
  formData.append("description", description.value);
  formData.append("inStock", inStock.value);
  formData.append("published", published.value);
  formData.append("brand_id", brand_id.value);
  formData.append("category_id", category_id.value);
  // Append product images to the FormData
  for (const image of productImages.value) {
    formData.append("product_images[]", image.raw);
  }
  try {
    await router.post("products/store", formData, {
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
const resetFormData = () => {
  id.value = "";
  title.value = "";
  price.value = "";
  quantity.value = "";
  description.value = "";
  published.value = "";
  inStock.value = "";
  productImages.value = [];
  // dialogImageUrl.value = ''
};

const deleteImage = async (pimage, index) => {
  try {
    console.log(pimage);
    await router.delete("/admin/products/image/" + pimage.id, {
      onSuccess: (page) => {
        productImages.value.splice(index, 1);
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

const updateProduct = async () => {
  const formData = new FormData();
  formData.append("title", product.value.title);
  formData.append("price", product.value.price);
  formData.append("quantity", product.value.quantity);
  formData.append("description", product.value.description);
  formData.append("inStock", product.value.inStock);
  formData.append("published", product.value.published);
  formData.append("brand_id", product.value.brand_id);
  formData.append("category_id", product.value.category_id);
  formData.append("_method", "PUT");
  // Append product images to the FormData
  for (const image of productImages.value) {
    formData.append("product_images[]", image.raw);
  }
  try {
    await router.post("products/update/" + product.value.id, formData, {
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
const deleteProduct = () => {
  try {
    router.delete("products/destroy/" + product.value.id, {
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
.description-container {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 200px; /* Điều chỉnh giá trị này theo ý muốn */
}
</style>
