<template>
  <div class="antialiased bg-gray-50 dark:bg-gray-900">
    <!-- navbar start -->
    <Navbar />
    <!-- end -->
    <!-- Sidebar -->
    <Sidebar />
    <!-- end -->
    <main class="p-4 md:ml-64 h-auto pt-20">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import {
  initFlowbite,
  initAccordions,
  initCarousels,
  initCollapses,
  initDials,
  initDismisses,
  initDrawers,
  initDropdowns,
  initModals,
  initPopovers,
  initTabs,
  initTooltips,
} from "flowbite";
import Navbar from "./Navbar.vue";
import Sidebar from "./Sidebar.vue";
// initialize components based on data attribute selectors
onMounted(() => {
  initFlowbite();
  initAccordions();
  initCarousels();
  initCollapses();
  initDials();
  initDismisses();
  initDrawers();
  initDropdowns();
  initModals();
  initPopovers();
  initTabs();
  initTooltips();
});
</script>
