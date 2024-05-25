<script setup>
import { Link, router } from "@inertiajs/vue3";
import UserLayouts from "./Layouts/UserLayouts.vue";
import { ref } from "vue";
const formData = ref({
  name: "",
  email: "",
  phone: "", // Thêm trường số điện thoại
  subject: "",
  message: "",
});

const submitForm = () => {
  // Xử lý gửi form ở đây, ví dụ: gửi dữ liệu lên server
  console.log(formData.value);
};
</script>
<template>
  <UserLayouts>
    <img
      src="https://cdn.pixabay.com/photo/2017/01/18/10/43/banner-1989514_1280.png"
      alt="Hình ảnh blog"
      class="w-full h-64 object-cover"
    />

    <div class="max-w-md mx-auto p-4 sm:p-6 lg:p-8 bg-gray-100 rounded-lg shadow-md mt-8">
      <h2 class="text-2xl font-semibold mb-4 text-center">Liên hệ với chúng tôi</h2>

      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label for="name" class="block text-sm font-medium text-gray-700"
            >Họ và tên</label
          >
          <input
            type="text"
            id="name"
            v-model="formData.name"
            class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring focus:ring-blue-500"
            required
          />
        </div>

        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input
            type="email"
            id="email"
            v-model="formData.email"
            class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring focus:ring-blue-500"
            required
          />
        </div>

        <div class="mb-4">
          <label for="phone" class="block text-sm font-medium text-gray-700"
            >Số điện thoại</label
          >
          <input
            type="tel"
            id="phone"
            v-model="formData.phone"
            class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring focus:ring-blue-500"
          />
        </div>

        <div class="mb-4">
          <label for="subject" class="block text-sm font-medium text-gray-700"
            >Chủ đề</label
          >
          <input
            type="text"
            id="subject"
            v-model="formData.subject"
            class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring focus:ring-blue-500"
            required
          />
        </div>

        <div class="mb-4">
          <label for="message" class="block text-sm font-medium text-gray-700"
            >Lời nhắn</label
          >
          <textarea
            id="message"
            v-model="formData.message"
            rows="4"
            class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring focus:ring-blue-500"
            required
          ></textarea>
        </div>

        <button
          type="submit"
          class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Gửi
        </button>
      </form>
    </div>
  </UserLayouts>
</template>
<style scoped>
.container {
  display: flex;
  justify-content: space-around;
}
.mx-auto {
  margin-top: 25px;
  margin-bottom: 100px;
  max-width: 45rem;
}
</style>
