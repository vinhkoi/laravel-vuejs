<script setup>
import UserLayouts from "./Layouts/UserLayouts.vue";
import DashboardTable from "./Components/DashboardTable.vue";
defineProps({
  orders: Array,
  //   orderItems: Array,
});
</script>
<template>
  <UserLayouts>
    <!-- <div class="relative max-w-screen-xl py-24 mx-auto overflow-x-auto">
      <table
        v-show="order.order_items.length > 0"
        v-for="order in orders"
        :key="order.id"
        class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mb-5"
      >
        <thead
          class="text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400"
        >
          <tr>
            <th scope="col" class="px-6 py-3">Order ID # {{ order.id }}</th>
            <th scope="col" class="px-6 py-3 a">{{ order.status }}</th>
            <th scope="col" class="px-6 py-3 a">
              <span
                v-if="order.status == 'paid'"
                class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300"
                >PAID</span
              >
              <span
                v-else
                class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300"
                >UNPAID</span
              >
            </th>
          </tr>
          <tr></tr>
        </thead>
        <tbody>
          <tr
            v-for="item in order.order_items"
            :key="item.id"
            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
          >
            <th
              scope="row"
              class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
            >
              {{ item.product.title }}
            </th>
            <td class="px-6 py-4">
              {{ item.product.brand.name }}
            </td>
            <td class="px-6 py-4">
              {{ item.product.category.name }}
            </td>
            <td class="px-6 py-4">${{ item.product.price }}</td>
          </tr>
        </tbody>
      </table>
    </div> -->
    <DashboardTable :orders="orders" />
  </UserLayouts>
</template>
<style scoped>
.a {
  float: right;
}
</style>
