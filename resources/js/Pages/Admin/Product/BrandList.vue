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
          :value="brands"
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
            <div
              class="flex flex-wrap gap-2 align-items-center justify-content-between aa"
            >
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

          <Column
            selectionMode="multiple"
            style="width: 3rem"
            :exportable="false"
          ></Column>

          <Column
            field="title"
            header="ID Brand"
            sortable
            headerStyle="width:14%; min-width:10rem;"
          >
            <template #body="slotProps">
              <span class="p-column-title">ID Brand</span>
              {{ slotProps.data.id }}
            </template>
          </Column>
          <Column
            field="category.name"
            header="Name"
            sortable
            headerStyle="width:14%; min-width:10rem;"
          >
            <template #body="slotProps">
              <span class="p-column-title">Name</span>
              {{ slotProps.data.name }}
            </template>
          </Column>
          <Column
            field="brand_id"
            header="Slug"
            sortable
            headerStyle="width:14%; min-width:10rem;"
            ><template #body="slotProps">
              <span class="p-column-title">Slug</span>
              {{ slotProps.data.slug }}
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
          <label for="title">Name</label>
          <InputText
            id="title"
            v-model.trim="name"
            required="true"
            autofocus
            :class="{ 'p-invalid': submitted && !title }"
          />
          <small class="p-error" v-if="submitted && !title">Title is required.</small>
        </div>

        <div class="field">
          <label for="slug">Slug</label>
          <InputText
            id="slug"
            v-model.trim="slug"
            required="true"
            autofocus
            :class="{ 'p-invalid': submitted && !slug }"
          />
          <small class="p-error" v-if="submitted && !slug">Slug is required.</small>
        </div>

        <div class="card">
          <el-upload
            v-model:file-list="brandImages"
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
          <!-- <FileUpload
          v-model:file-list="productImages"
          url="/upload-images"
          @upload="onAdvancedUpload($event)"
          :multiple="true"
          accept="image/*"
          :maxFileSize="1000000"
        >
          <template #empty>
            <p>Drag and drop files to here to upload.</p>
          </template>
        </FileUpload> -->
          <!-- <file-pond
          name="product_images"
          ref="pond"
          v-bind:allow-multiple="true"
          accepted-file-types="image/png, image/jpeg"
          v-bind:server="{
            url: 'http://127.0.0.1:8000/',
            timeout: 7000,
            process: {
              url: '/upload-images',
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': $page.props.csrf_token,
              },

              withCredentials: false,
              onload: handleFilePondLoad,
              onerror: () => {},
            },
            remove: handleFilePondRemove,
            revert: handleFilePondRevert,
          }"
          v-model:file="productImages"
          v-on:init="handleFilePondInit"
        >
        </file-pond> -->
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
          <label for="title">Name</label>
          <InputText
            id="title"
            v-model.trim="brand.name"
            required="true"
            autofocus
            :class="{ 'p-invalid': submitted && !title }"
          />
          <small class="p-error" v-if="submitted && !title">Name is required.</small>
        </div>
        <div class="field">
          <label for="description">Slug</label>
          <InputText
            id="description"
            v-model="brand.slug"
            required="true"
            rows="3"
            cols="20"
          />
        </div>

        <div class="card">
          <el-upload
            v-model:file-list="brandImages"
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
            v-for="(pimage, index) in brand.brand_images"
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
import { ProductService } from "./ProductService.js";
import axios from "axios";
import { router, usePage } from "@inertiajs/vue3";
import { Plus } from "@element-plus/icons-vue";

const brands = usePage().props.brands;
const categories = usePage().props.categories;

defineProps({
  brands: Array,
});

import AdminLayout from "../Components/AdminLayout.vue";
const toast = useToast();
const dt = ref();
const id = ref("");
const name = ref("");
const slug = ref("");

const brand_images = ref([]);
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
const brand = ref({});

const selectedProducts = ref();
const brandImages = ref([]);
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);
const statuses = ref([
  { label: "INSTOCK", value: "instock" },
  { label: "LOWSTOCK", value: "lowstock" },
  { label: "OUTOFSTOCK", value: "outofstock" },
]);

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
const saveProduct = () => {
  submitted.value = true;

  if (product.value.name.trim()) {
    if (product.value.id) {
      product.value.inventoryStatus = product.value.inventoryStatus.value
        ? product.value.inventoryStatus.value
        : product.value.inventoryStatus;
      products.value[findIndexById(product.value.id)] = product.value;
      toast.add({
        severity: "success",
        summary: "Successful",
        detail: "Product Updated",
        life: 3000,
      });
    } else {
      product.value.id = createId();
      product.value.code = createId();
      product.value.image = "product-placeholder.svg";
      product.value.inventoryStatus = product.value.inventoryStatus
        ? product.value.inventoryStatus.value
        : "INSTOCK";
      products.value.push(product.value);
      toast.add({
        severity: "success",
        summary: "Successful",
        detail: "Product Created",
        life: 3000,
      });
    }

    productDialog.value = false;
    product.value = {};
  }
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
const findIndexById = (id) => {
  let index = -1;
  for (let i = 0; i < products.value.length; i++) {
    if (products.value[i].id === id) {
      index = i;
      break;
    }
  }

  return index;
};
const createId = () => {
  let id = "";
  var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  for (var i = 0; i < 5; i++) {
    id += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return id;
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

const getStatusLabel = (status) => {
  switch (status) {
    case "INSTOCK":
      return "success";

    case "LOWSTOCK":
      return "warning";

    case "OUTOFSTOCK":
      return "danger";

    default:
      return null;
  }
};
const dialogImageUrl = ref("");
const handleFileChange = (file) => {
  console.log(file);
  brandImages.value.push(file);
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
  formData.append("name", name.value);
  formData.append("slug", slug.value);
  // Append product images to the FormData
  for (const image of brandImages.value) {
    formData.append("brand_images[]", image.raw);
  }
  try {
    await router.post("brands/store", formData, {
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
  brandImages.value = [];
  // dialogImageUrl.value = ''
};

const deleteImage = async (pimage, index) => {
  try {
    console.log(pimage);
    await router.delete("/admin/products/image/" + pimage.id, {
      onSuccess: (page) => {
        brandImages.value.splice(index, 1);
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
  formData.append("brand_id", product.value.brand_id);
  formData.append("category_id", product.value.category_id);
  formData.append("_method", "PUT");
  // Append product images to the FormData
  for (const image of brandImages.value) {
    formData.append("brand_images[]", image.raw);
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
</style>
