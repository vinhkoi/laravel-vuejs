<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
import Chirp from "./Components/Chirp.vue";
import UserLayouts from "./Layouts/UserLayouts.vue";
//products list
defineProps({
  product: Array,
  products: Array,
  chirps: Array,
});
const responsiveOptions = ref([
  {
    breakpoint: "1400px",
    numVisible: 2,
    numScroll: 1,
  },
  {
    breakpoint: "1199px",
    numVisible: 3,
    numScroll: 1,
  },
  {
    breakpoint: "767px",
    numVisible: 2,
    numScroll: 1,
  },
  {
    breakpoint: "575px",
    numVisible: 1,
    numScroll: 1,
  },
]);
const getSeverity = (status) => {
  switch (status) {
    case 1:
      return "success"; // INSTOCK
    case 0:
      return "danger"; // OUTOFSTOCK
    default:
      return null;
  }
};
const product = usePage().props.product;
const auth = usePage().props.auth;

const wishlist = computed(() => usePage().props.wishlistt.data); // Lấy dữ liệu wishlist từ props

const isInWishlist = computed(() => {
  // Kiểm tra nếu wishlist.value.data, product và auth.user đều được định nghĩa
  if (wishlist.value?.data && product?.id && auth?.user?.id) {
    return wishlist.value.data.some(
      (item) => item.product_id === product.id && item.user_id === auth.user.id
    );
  }
  return false;
});
const addToWishList = (product) => {
  console.log(product);
  router.post(route("wishlist.store", product), {
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
const addToCartt = (product) => {
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
</script>
<template>
  <UserLayouts>
    <section class="text-gray-700 body-font overflow-hidden bg-white">
      <div class="container px-5 py-24 mx-auto">
        <div class="lg:w-4/5 mx-auto flex flex-wrap">
          <img
            v-if="product.product_images.length > 0"
            :src="`/${product.product_images[0].image}`"
            :alt="product.imageAlt"
            class="lg:w-1/2 w-full object-cover object-center rounded border border-gray-200"
          />
          <img
            v-else
            src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
            :alt="product.imageAlt"
            class="lg:w-1/2 w-full object-cover object-center rounded border border-gray-200"
          />

          <div class="lg:w-1/2 w-full lg:pl-10 lg:py-6 mt-6 lg:mt-0">
            <h2 class="text-sm title-font text-gray-500 tracking-widest">
              {{ product.brand.name }}
            </h2>
            <h1 class="text-gray-900 text-3xl title-font font-medium mb-1">
              {{ product.title }}
            </h1>
            <!-- <div class="flex mb-4">
              <span class="flex items-center">
                <svg
                  v-for="star in 5"
                  :key="star"
                  fill="currentColor"
                  :stroke="star <= product.rating ? 'currentColor' : 'none'"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  class="w-4 h-4 text-red-500"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                  ></path>
                </svg>
                <span class="text-gray-600 ml-3"
                  >{{ product.reviews_count }} Reviews</span
                >
              </span>
              <span class="flex ml-3 pl-3 py-2 border-l-2 border-gray-200">
                <a class="text-gray-500">
                  <svg
                    fill="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    class="w-5 h-5"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"
                    ></path>
                  </svg>
                </a>
                <a class="ml-2 text-gray-500">
                  <svg
                    fill="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    class="w-5 h-5"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"
                    ></path>
                  </svg>
                </a>
                <a class="ml-2 text-gray-500">
                  <svg
                    fill="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    class="w-5 h-5"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"
                    ></path>
                  </svg>
                </a>
              </span>
            </div> -->
            <p class="leading-relaxed">{{ product.description }}</p>
            <div class="flex mt-6 items-center pb-5 border-b-2 border-gray-200 mb-5">
              <div class="flex">
                <span class="mr-3">Category: {{ product.category.name }}</span>
              </div>
            </div>
            <div class="flex">
              <div class="b">
                <span
                  v-if="product.flash_sale && product.flash_sale[0]"
                  class="title-font font-medium text-2xl text-gray-900 a"
                >
                  ${{ product.flash_sale[0].discounted_price }}
                </span>
                <span
                  :class="{ 'line-through': product.flash_sale && product.flash_sale[0] }"
                  class="title-font font-medium text-2xl text-gray-900"
                  v-if="product.flash_sale && product.flash_sale[0]"
                >
                  ${{ product.price }}
                </span>
                <span v-else class="title-font font-medium text-2xl text-gray-900">
                  ${{ product.price }}
                </span>
              </div>

              <button
                v-if="product.inStock"
                class="flex ml-auto text-white bg-red-500 border-0 py-2 px-6 focus:outline-none hover:bg-red-600 rounded"
                @click="addToCart(product)"
              >
                Add to Cart
              </button>

              <button
                v-else
                disabled
                class="flex ml-auto text-white bg-gray-400 border-0 py-2 px-6 cursor-not-allowed opacity-50 rounded"
              >
                Out of Stock
              </button>
              <button
                :class="{ 'text-red-500': isInWishlist }"
                class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4"
                @click="addToWishList(product)"
              >
                <svg
                  fill="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  class="w-5 h-5"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"
                  ></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <h2 class="text-2xl font-medium text-gray-900 text-center mt-8">
          Related Products
        </h2>

        <Carousel
          :value="products"
          :numVisible="3"
          :numScroll="1"
          :responsiveOptions="responsiveOptions"
          circular
          :autoplayInterval="3000"
        >
          <template #item="slotProps">
            <div class="border-1 surface-border border-round m-2 p-3 d">
              <div class="mb-3">
                <Link
                  :href="route('detail.view', { id: slotProps.data.id })"
                  class="relative mx-auto image-container"
                >
                  <img
                    v-if="slotProps.data.product_images.length > 0"
                    :src="`/${slotProps.data.product_images[0].image}`"
                    :alt="slotProps.data.imageAlt"
                    class="w-full border-round fixed-size-image"
                  />
                  <img
                    v-else
                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                    :alt="product.imageAlt"
                    class="w-full border-round fixed-size-image"
                  />

                  <Tag
                    :value="slotProps.data.inStock ? 'In Stock' : 'Out of Stock'"
                    :severity="getSeverity(slotProps.data.inStock)"
                    class="absolute"
                    style="left: 5px; top: 5px"
                  />
                </Link>
              </div>
              <Link :href="route('detail.view', { id: slotProps.data.id })">
                <div class="mb-3 font-medium">{{ slotProps.data.title }}</div>
              </Link>
              <div class="flex justify-content-between align-items-center">
                <div class="mt-0 font-semibold text-xl">${{ slotProps.data.price }}</div>
                <span>
                  <Button
                    icon="pi pi-heart"
                    severity="secondary"
                    outlined
                    @click="addToWishList(slotProps.data)"
                  />
                  <Button
                    v-if="slotProps.data.inStock"
                    icon="pi pi-shopping-cart"
                    class="ml-2"
                    @click="addToCartt(slotProps.data)"
                  />
                </span>
              </div>
            </div>
          </template>
        </Carousel>
      </div>
      <div>
        <h2 class="text-2xl font-medium text-gray-900 text-center mt-8">
          Customer review
        </h2>
        <Chirp :chirps="chirps"></Chirp>
      </div>
    </section>
  </UserLayouts>
</template>
<style>
.a {
  color: red;
  margin-right: 30px;
}
.d {
  border-radius: var(--border-radius) !important;
  border-style: solid;
  border-width: 1px !important;
}
img {
  border-radius: var(--border-radius) !important;
}
.p-carousel .p-carousel-indicators {
  padding: 1rem;
}
.p-carousel-indicators {
  display: flex;
  flex-direction: row;
  justify-content: center;
  flex-wrap: wrap;
}
.p-reset {
  margin: 0;
  padding: 0;
  border: 0;
  outline: 0;
  text-decoration: none;
  font-size: 100%;
  list-style: none;
}
.p-carousel .p-carousel-indicators .p-carousel-indicator {
  margin-right: 0.5rem;
  margin-bottom: 0.5rem;
}
.image-container {
  width: 300px; /* Adjust the width as needed */
  height: 300px; /* Adjust the height as needed */
  overflow: hidden;
  position: relative;
}

.fixed-size-image {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Cover the entire container while maintaining the aspect ratio */
}
.review-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9fafb;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
