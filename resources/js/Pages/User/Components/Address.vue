<template>
  <UserLayout>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Address</h2>

    <div class="flex justify-center items-center min-h-screen">
      <div class="bg-gray-100 p-8 rounded-lg shadow-md max-w-lg w-full">
        <div>
          <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">
            Shipping Address
          </h2>
          <p class="leading-relaxed mb-5 text-gray-600">
            {{ userAddress.address1 }} , {{ userAddress.city }},
            {{ userAddress.zipcode }}
          </p>
          <p class="leading-relaxed mb-5 text-gray-600">or you can add new below</p>
        </div>

        <div>
          <p class="leading-relaxed mb-5 text-gray-600">
            Add shipping address to continue
          </p>
        </div>

        <form @submit.prevent="submit">
          <div class="relative mb-4">
            <label for="name" class="leading-7 text-sm text-gray-600">Address 1</label>
            <input
              type="text"
              id="name"
              name="address1"
              v-model="userAddress.address1"
              class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
            />
          </div>
          <div class="relative mb-4">
            <label for="email" class="leading-7 text-sm text-gray-600">City</label>
            <input
              type="text"
              id="email"
              name="city"
              v-model="userAddress.city"
              class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
            />
          </div>
          <div class="relative mb-4">
            <label for="email" class="leading-7 text-sm text-gray-600">State</label>
            <input
              type="text"
              id="email"
              name="state"
              v-model="userAddress.state"
              class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
            />
          </div>
          <div class="relative mb-4">
            <label for="email" class="leading-7 text-sm text-gray-600">Zipcode</label>
            <input
              type="text"
              id="email"
              name="zipcode"
              v-model="userAddress.zipcode"
              class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
            />
          </div>
          <div class="relative mb-4">
            <label for="email" class="leading-7 text-sm text-gray-600"
              >Country Code</label
            >
            <input
              type="text"
              id="email"
              name="countrycode"
              v-model="userAddress.country_code"
              class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
            />
          </div>
          <div class="relative mb-4">
            <label for="email" class="leading-7 text-sm text-gray-600"
              >Address type</label
            >
            <input
              type="text"
              id="email"
              name="type"
              v-model="userAddress.type"
              class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
            />
          </div>
        </form>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from "../Layouts/UserLayout.vue";
import { ref, computed, reactive } from "vue";

defineProps({
  userAddress: Object,
});
const form = reactive({
  address1: null,
  state: null,
  city: null,
  zipcode: null,
  country_code: null,
  type: null,
});
function submit() {
  // Hiển thị cảnh báo nếu không đủ điều kiện
  alert("Please fill out the form completely before proceeding.");
}
</script>

<style></style>
