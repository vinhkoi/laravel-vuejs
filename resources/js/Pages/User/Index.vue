<template>
  <UserLayouts>
    <!-- here section  -->
    <Hero></Hero>
    <div class="bg-white">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">
          Customers also purchased
        </h2>
        <Products :products="products"></Products>
        <div class="flex justify-center mt-5">
          <Link
            :href="route('products.index')"
            class="text-gray-900 bg-gradient-to-r from-teal-200 to-lime-200 hover:bg-gradient-to-l hover:from-teal-200 hover:to-lime-200 focus:ring-4 focus:outline-none focus:ring-lime-200 dark:focus:ring-teal-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2"
          >
            View All Products</Link
          >
        </div>
      </div>
    </div>
    <!-- end -->
  </UserLayouts>
</template>

<script setup>
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import UserLayouts from "./Layouts/UserLayouts.vue";
import Hero from "./Layouts/Hero.vue";
import { router, usePage, Link } from "@inertiajs/vue3";
import Products from "./Components/Products.vue";

// initialize components based on data attribute selectors
onMounted(() => {
  initFlowbite();
});
defineProps({
  products: Array,
});


</script>

<style>
.abc {
  background-color: blue;
}
</style>
