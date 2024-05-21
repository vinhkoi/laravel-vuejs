<script setup>
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import Header from "./Header.vue";
import Footer from "./Footer.vue";
import Hero from "./Hero.vue";
import Navbar from "./Navbar.vue";
import Sidebar from "./Sidebar.vue";
// initialize components based on data attribute selectors
onMounted(() => {
  initFlowbite();
});
</script>
<template>
  <!-- header -->
  <!-- <Header></Header> -->
  <Navbar />

  <Sidebar />

  <main class="p-4 md:ml-64 h-auto pt-20">
    <slot />
  </main>

  <!-- <main class="p-4 md:ml-64 h-auto pt-20"></main> -->
  <!-- end -->
  <!-- footer -->

  <!-- <Footer></Footer> -->

  <!-- end -->
</template>
