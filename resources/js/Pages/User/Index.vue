<script setup>
import { Link, router } from "@inertiajs/vue3";
import UserLayouts from "./Layouts/UserLayouts.vue";
import Hero from "./Layouts/Hero.vue";
import Product from "../User/Components/Product.vue";
import Products from "../User/Components/Products.vue";
import NewProducts from "../User/Components/NewProducts.vue";
import FlashSale from "../User/Components/FlashSale.vue";
import Category from "../User/Components/Category.vue";
import {
  CircleCheck,
  RefreshRight,
  ShoppingCartFull,
  TrophyBase,
} from "@element-plus/icons-vue";

//products list
defineProps({
  products: Array,
  banner: Array,
  newProducts: Array,
  sales: Array,
  categories: Array,
});
</script>
<template>
  <UserLayouts>
    <!-- here section  -->
    <Hero :banner="banner"></Hero>
    <!-- end -->
    <div class="bg-white">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <Category :categories="categories"></Category>

        <!-- end -->
        /
      </div>
    </div>
    <div class="bg-white">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Flash Sale</h2>
        <FlashSale :sale="sale"></FlashSale>

        <!-- end -->
        /
      </div>
    </div>
    <div class="bg-white">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">News products</h2>
        <NewProducts :newProducts="newProducts"></NewProducts>

        <!-- end -->
        /
      </div>
    </div>
    <div class="bg-white">
      <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">List of products</h2>

        <!-- product list component -->
        <Product :products="products"></Product>
        <!-- end -->
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
    <section class="bg-white dark:bg-gray-900 py-4">
      <div
        class="py-8 mx-auto max-w-screen-xl px-4 border rounded-lg shadow-lg dark:bg-gray-800 dark:border-none"
      >
        <div
          class="grid grid-cols-2 gap-8 text-gray-500 sm:gap-12 lg:grid-cols-4 dark:text-gray-400"
        >
          <div class="flex space-x-5 items-center">
            <div>
              <span>
                <el-icon color="#409EFC" :size="50">
                  <ShoppingCartFull />
                </el-icon>
              </span>
            </div>
            <div>
              <p
                class="text-gray-900 text-xl font-bold tracking-wide mb-1 dark:text-gray-200"
              >
                Free Shipping
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-300">
                When ordering over $100
              </p>
            </div>
          </div>
          <div class="flex space-x-5 items-center">
            <div>
              <span>
                <el-icon color="#409EFC" :size="50">
                  <RefreshRight />
                </el-icon>
              </span>
            </div>
            <div>
              <p
                class="text-gray-900 text-xl font-bold tracking-wide mb-1 dark:text-gray-200"
              >
                Free Return
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-300">
                Get Return within 30 days
              </p>
            </div>
          </div>
          <div class="flex space-x-5 items-center">
            <div>
              <span>
                <el-icon color="#409EFC" :size="50">
                  <CircleCheck />
                </el-icon>
              </span>
            </div>
            <div>
              <p
                class="text-gray-900 text-xl font-bold tracking-wide mb-1 dark:text-gray-200"
              >
                Secure Payment
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-300">
                100% Secure Online Payment
              </p>
            </div>
          </div>
          <div class="flex space-x-5 items-center">
            <div>
              <span>
                <el-icon color="#409EFC" :size="50">
                  <TrophyBase />
                </el-icon>
              </span>
            </div>
            <div>
              <p
                class="text-gray-900 text-xl font-bold tracking-wide mb-1 dark:text-gray-200"
              >
                Best Quality
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-300">
                Original Product Guarenteed
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
      <div class="mx-auto max-w-screen-md sm:text-center">
        <h2
          class="mb-4 text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl dark:text-white"
        >
          Get <strong class="text-blue-500 mx-1">20%</strong> Off Discount Coupon
        </h2>
        <p
          class="mx-auto mb-8 max-w-2xl font-light text-gray-500 md:mb-12 sm:text-xl dark:text-gray-400"
        >
          by Subscribe our Newsletter
        </p>
        <form action="#">
          <div
            class="items-center mx-auto mb-3 space-y-4 max-w-screen-sm sm:flex sm:space-y-0"
          >
            <div class="relative w-full">
              <label
                for="email"
                class="hidden mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                >Email address</label
              >
              <div
                class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
              >
                <svg
                  class="w-5 h-5 text-gray-500 dark:text-gray-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                  ></path>
                  <path
                    d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                  ></path>
                </svg>
              </div>
              <input
                class="block p-3 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 sm:rounded-none sm:rounded-l-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Enter your email"
                type="email"
                id="email"
                required=""
              />
            </div>
            <div>
              <button
                type="submit"
                class="py-3 px-5 w-full text-sm font-medium text-center text-blue rounded-lg border cursor-pointer bg-primary-600 border-primary-600 sm:rounded-none sm:rounded-r-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
              >
                Submit
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </UserLayouts>
</template>
