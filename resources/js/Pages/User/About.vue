<script setup>
import { Link, router } from "@inertiajs/vue3";
import UserLayouts from "./Layouts/UserLayouts.vue";
</script>
<template>
  <UserLayouts>
    <img
      src="https://cdn.pixabay.com/photo/2017/01/18/10/43/banner-1989514_1280.png"
      alt="Hình ảnh blog"
      class="w-full h-64 object-cover"
    />
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
      <div class="text-center max-w-2xl a">
        <h2>About</h2>
        <p>
          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
          incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet,
          consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
          dolore magna aliqua.
          <br />
          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
          incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet,
          consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
          etdolore magna aliqua.
        </p>
      </div>
    </div>
  </UserLayouts>
</template>
<style scoped>
.container {
  display: flex;
  justify-content: space-around;
}
</style>
