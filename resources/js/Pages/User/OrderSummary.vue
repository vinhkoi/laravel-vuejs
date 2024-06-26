<template>
  <UserLayouts>
    <div
      class="min-w-screen min-h-screen bg-gray-200 flex items-center justify-center px-5 pb-10 pt-20"
    >
      <div
        class="w-full mx-auto rounded-lg bg-white shadow-lg p-5 text-gray-700"
        style="max-width: 600px"
      >
        <div class="w-full pt-1 pb-5">
          <div
            class="bg-indigo-500 text-white overflow-hidden rounded-full w-20 h-20 -mt-16 mx-auto shadow-lg flex justify-center items-center"
          >
            <img :src="`/images/logo.png`" />
          </div>
        </div>

        <div class="mb-10">
          <h1 class="text-center font-bold text-xl uppercase">Order Summary</h1>
        </div>

        <div class="mb-10">
          <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
            Billing & Delivery information
          </h4>
          <dl>
            <dt class="text-base font-medium text-gray-900 dark:text-white">
              Individual
            </dt>
            <dd class="mt-1 text-base font-normal text-gray-500 dark:text-gray-400">
              {{ order.user.name }} - {{ order.user.phone }},
              {{ order.user_address.address1 }}, {{ order.user_address.city }},
              {{ order.user_address.state }}, {{ order.user_address.country_code }},
              {{ order.user_address.zipcode }}
            </dd>
            <dt class="text-base font-medium text-gray-900 dark:text-white">
              Estimated delivery time
            </dt>
            <dd class="mt-1 text-base font-normal text-gray-500 dark:text-gray-400">
              {{ order.estimated_delivery_time }}
            </dd>
          </dl>
        </div>

        <!-- <div class="mb-10">
          <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
            Order Status
          </h4>
          <p class="mt-1 text-base font-normal text-gray-500 dark:text-gray-400">
            {{ order.status }}
          </p>
        </div> -->

        <div class="relative overflow-x-auto">
          <table
            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
          >
            <thead
              class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400"
            >
              <tr>
                <th scope="col" class="px-6 py-3 rounded-s-lg">Product name</th>
                <th scope="col" class="px-6 py-3">Product Image</th>
                <th scope="col" class="px-6 py-3">Quantity</th>
                <th scope="col" class="px-6 py-3 rounded-e-lg">Price</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="item in order.order_items"
                :key="item.id"
                class="bg-white dark:bg-gray-800"
              >
                <th
                  scope="row"
                  class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                >
                  {{ item.product.title }}
                </th>
                <td class="w-32 p-4">
                  <img
                    v-if="item.product.product_images.length > 0"
                    :src="item.product.product_images[0].url"
                    :alt="item.product.name"
                    class="h-auto w-full max-h-full dark:hidden"
                  />
                  <img
                    v-else
                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                    :alt="item.product.name"
                    class="h-auto w-full max-h-full dark:hidden"
                  />
                </td>
                <td class="px-6 py-4">
                  {{ item.quantity }}
                </td>
                <td class="px-6 py-4">
                  {{ item.unit_price }}
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="font-semibold text-gray-900 dark:text-white">
                <th scope="row" class="px-6 py-3 text-base">Total</th>
                <td class="px-6 py-3">{{ total_product }}</td>
                <td class="px-6 py-3">{{ total_product }}</td>
                <td class="px-6 py-3">{{ order.total_price - order.shipping_fee }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="flex justify-end mb-8">
          <div class="text-gray-700 mr-2 dark:text-gray-200">Shipping:</div>
          <div class="text-gray-700 dark:text-gray-200">
            {{ order.shipping_fee }}
          </div>
        </div>
        <div class="flex justify-end mb-8">
          <div class="text-gray-700 mr-2">Total:</div>
          <div class="text-gray-700 font-bold text-xl">{{ order.total_price }}</div>
        </div>
      </div>
    </div>
  </UserLayouts>
</template>

<script setup>
import { ref, watch } from "vue";
import UserLayouts from "./Layouts/UserLayouts.vue";

const props = defineProps({
  order: Object,
});
</script>
