<template>
  <UserLayouts>
    <div class="pc-builder container mx-auto p-4">
      <h1 class="text-2xl font-semibold mb-4 text-center">PC Builder</h1>

      <table class="w-full border-collapse">
        <thead>
          <tr>
            <th class="p-2 border">Component</th>
            <th class="p-2 border">Selected</th>
            <th class="p-2 border">Price</th>
            <th class="p-2 border"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="componentType in componentTypes" :key="componentType">
            <td class="p-2 border">{{ componentType }}</td>
            <!-- <td class="p-2 border">
              {{ selectedComponents[componentType]?.title || "Chưa chọn" }}
            </td> -->
            <td class="p-2 border flex items-center">
              <img
                v-if="selectedComponents[componentType]?.product_images?.[0]?.image"
                :src="selectedComponents[componentType].product_images[0]?.image"
                :alt="selectedComponents[componentType].title"
                class="w-12 h-12 mr-2 rounded-md object-cover"
              />
              <img
                v-else
                src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                alt="Placeholder Image"
                class="w-12 h-12 mr-2 rounded-md object-cover"
              />
              {{ selectedComponents[componentType]?.title || "Chưa chọn" }}
            </td>
            <td class="p-2 border">
              {{
                selectedComponents[componentType]?.price
                  ? "$" + selectedComponents[componentType].price
                  : ""
              }}
            </td>
            <td class="p-2 border">
              <div class="flex justify-center items-center">
                <Button label="Choose" @click="openDialog(componentType)" />
              </div>
              <Dialog
                v-model:visible="dialogs[componentType]"
                :header="componentType"
                :modal="true"
                class="w-1/2"
              >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div
                    v-for="component in components[componentType]"
                    :key="component.id"
                    class="component-card cursor-pointer"
                  >
                    <Link :href="route('detail.view', { id: component.id })">
                      <img
                        v-if="
                          component.product_images && component.product_images.length > 0
                        "
                        :src="component.product_images[0].image"
                        :alt="component.title"
                        class="w-full h-40 object-cover rounded-md"
                      />
                      <img
                        v-else
                        src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                        alt="Placeholder Image"
                        class="w-full h-40 object-cover rounded-md"
                      />
                    </Link>
                    <div class="p-2">
                      <Link :href="route('detail.view', { id: component.id })">
                        <h3 class="text-lg font-semibold">
                          {{ component.title }}
                        </h3>
                      </Link>
                      <p class="text-gray-500">${{ component.price }}</p>
                      <Button
                        label="Select"
                        class="mt-2"
                        @click="selectComponent(componentType, component)"
                      />
                    </div>
                  </div>
                </div>
                <div class="flex justify-center mt-4">
                  <Button
                    label="Previous"
                    :disabled="currentPage[componentType] === 1"
                    class="pagination-button"
                    @click="changePage(componentType, currentPage[componentType] - 1)"
                  />
                  <span class="mx-2">Page {{ currentPage[componentType] }}</span>
                  <Button
                    label="Next"
                    :disabled="currentPage[componentType] === totalPages(componentType)"
                    class="pagination-button"
                    @click="changePage(componentType, currentPage[componentType] + 1)"
                  />
                </div>
              </Dialog>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="total-price mt-4 text-center">
        <h3 class="text-lg font-semibold">Tổng cộng: ${{ totalCost }}</h3>
        <Button label="Add to Cart" class="mt-4" @click="addToCart" />
      </div>
    </div>
  </UserLayouts>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import UserLayouts from "./Layouts/UserLayouts.vue";

const { props } = usePage();

const selectedComponents = ref({});
const currentPage = ref({}); // Đối tượng lưu trữ trạng thái trang hiện tại cho từng loại linh kiện
const ITEMS_PER_PAGE = 6; // Số lượng sản phẩm mỗi trang

const componentTypes = ref(props.componentTypes);
const components = ref(props.components);

const dialogs = ref({}); // Đối tượng lưu trữ trạng thái hiển thị của dialog

const totalCost = computed(() => {
  return Object.values(selectedComponents.value).reduce((acc, component) => {
    return component ? acc + Number(component.price) : acc;
  }, 0);
});

const openDialog = (componentType) => {
  dialogs.value[componentType] = true; // Mở dialog tương ứng với componentType
};

const selectComponent = (componentType, component) => {
  selectedComponents.value[componentType] = component;
  dialogs.value[componentType] = false; // Đóng dialog sau khi chọn
};

const paginatedComponents = (componentType) => {
  const start = (currentPage.value[componentType] - 1) * ITEMS_PER_PAGE;
  const end = start + ITEMS_PER_PAGE;
  return components.value[componentType].slice(start, end);
};
const totalPages = (componentType) => {
  return Math.ceil(components.value[componentType].length / ITEMS_PER_PAGE);
};

const changePage = (componentType, page) => {
  currentPage.value[componentType] = page;
};
// const addToCart = async () => {
//   const selectedProducts = Object.values(selectedComponents.value).filter(
//     (component) => component !== null
//   );

//   for (const component of selectedProducts) {
//     router.post(route("cart.store", { id: component.id }), {
//       product_id: component.id,
//       quantity: 1,
//       onSuccess: (page) => {
//         if (page.props.flash.success) {
//           Swal.fire({
//             toast: true,
//             icon: "success",
//             position: "top-end",
//             showConfirmButton: false,
//             title: page.props.flash.success,
//             timer: 1500,
//           });
//         }
//       },
//     });
//   }
// };
const addToCart = async () => {
  const selectedProducts = Object.values(selectedComponents.value)
    .filter((component) => component !== null)
    .map((component) => ({ id: component.id, quantity: 1 }));

  router.post(route("cart.storeMultiple"), {
    products: selectedProducts,
    onSuccess: (page) => {
      if (page.props.flash.success) {
        Swal.fire({
          toast: true,
          icon: "success",
          position: "top-end",
          showConfirmButton: false,
          title: page.props.flash.success,
          timer: 1500,
        });
      }
    },
  });
};
onMounted(() => {
  componentTypes.value.forEach((type) => {
    selectedComponents.value[type] = "";
    currentPage.value[type] = 1; // Khởi tạo trang hiện tại cho từng loại linh kiện
  });
});
</script>

<style scoped>
.p-button {
  color: #ffffff;
  background: #378bf3;
  border: 1px solid #378bf3;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  transition: background-color 0.2s, color 0.2s, border-color 0.2s, box-shadow 0.2s,
    outline-color 0.2s;
  border-radius: 6px;
  outline-color: transparent;
}
.pagination-button {
  width: 100px;
  text-align: center;
}
</style>
