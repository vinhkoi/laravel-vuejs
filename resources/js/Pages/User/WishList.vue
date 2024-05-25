<script setup>
import { ref, computed, reactive } from "vue";

import UserLayouts from "./Layouts/UserLayouts.vue";
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
const toast = useToast();
defineProps({
  wishlist: Object,
});

const wishlistItems = computed(() => usePage().props.wishlist.data); // Lấy dữ liệu wishlist
// const products = usePage().props.products;
const addToCart = (product) => {
  console.log(product);
  router.post(route("cart.store", product), {
    onSuccess: (page) => {
      if (page.props.flash.success) {
        Swal.fire({
          toast: true,
          icon: "success",
          position: "top-end",
          showConfirmButton: false,
          timer: 3000,
          title: page.props.flash.success,
        });
      }
    },
  });
};
//remove form cart
const remove = (product) => router.delete(route("wishlist.delete", product));
</script>
<template>
  <UserLayouts>
    <div class="container px-5 py-8 mx-auto">
      <h1 class="text-2xl font-semibold mb-4">
        My Wishlist <i class="pi pi-heart text-red-500"></i>
      </h1>

      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead
          class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
        >
          <tr>
            <th scope="col" class="px-6 py-3 sr-only">Image</th>
            <th scope="col" class="px-6 py-3">Product name</th>
            <th scope="col" class="px-6 py-3">Unit price</th>
            <th scope="col" class="px-6 py-3">Stock status</th>
            <th scope="col" class="px-6 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in wishlistItems"
            :key="item.id"
            class="border-b dark:bg-gray-800 dark:border-gray-700"
          >
            <td class="w-32 p-4">
              <img
                v-if="item.product.product_images.length > 0"
                :src="`/${item.product.product_images[0].image}`"
                :alt="item.product.title"
                class="h-full w-full object-cover object-center"
              />
              <img
                v-else
                src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                :alt="item.product.title"
                class="h-full w-full object-cover object-center"
              />
            </td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
              {{ item.product.title }}
            </td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
              <span
                v-if="
                  item.product.flashSale &&
                  item.product.flashSale[0] &&
                  item.product.flashSale[0].discounted_price
                "
              >
                ${{ item.product.flashSale[0].discounted_price }}
              </span>
              <span v-else>${{ item.product.price }}</span>
            </td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
              {{ item.product.inStock ? "In Stock" : "Out of Stock" }}
            </td>
            <td class="px-6 py-4 text-right">
              <button
                @click="addToCart(item.product)"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                :disabled="!item.product.inStock"
              >
                Add to cart
              </button>
              <button
                @click="remove(item.product)"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2"
              >
                Remove
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </UserLayouts>
</template>
<style scoped>
.btn {
  display: flex;
  justify-content: space-around;
}
.a {
  text-decoration: line-through;
}
.container {
  display: block; /* Thay đổi thành block để căn giữa */
}
.container h1 {
  text-align: center; /* Căn giữa tiêu đề */
  margin-bottom: 1rem; /* Thêm khoảng cách dưới tiêu đề */
}

.product-border {
  border: 1px solid #ccc; /* Thêm viền cho mỗi sản phẩm */
  padding: 1rem; /* Thêm padding xung quanh sản phẩm */
  border-radius: 8px; /* Bo góc nhẹ */
}
</style>
