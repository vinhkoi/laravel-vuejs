<script setup>
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import Header from "./Header.vue";
import Footer from "./Footer.vue";
import Hero from "./Hero.vue";
import Sidebarr from "./Sidebarr.vue";

// initialize components based on data attribute selectors
onMounted(() => {
  initFlowbite();
  // Add the chatbot script
  const script = document.createElement("script");
  script.src =
    "https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1";
  document.head.appendChild(script);

  // Create df-messenger element
  const dfMessenger = document.createElement("df-messenger");
  dfMessenger.setAttribute("intent", "WELCOME");
  dfMessenger.setAttribute("chat-title", "Online-Shopping");
  dfMessenger.setAttribute("agent-id", "a506bf86-39f8-44ce-ae9d-f08d542e83c8");
  dfMessenger.setAttribute("language-code", "en");
  document.getElementById("chatbot-container").appendChild(dfMessenger);
});
</script>
<template>
  <!-- header -->

  <Header></Header>
  <!-- end -->
  <!-- main content -->

  <div class="pb-20">
    <!-- Add padding to the bottom to avoid overlap -->
    <slot />
  </div>
  <!-- end -->
  <!-- footer -->
  <div id="chatbot-container" class="chatbot-fixed"></div>

  <Footer></Footer>

  <!-- end -->
</template>
<style>
.chatbot-fixed {
  /* position: fixed;
  bottom: 80px;
  right: 20px; */
  /* padding-bottom: 150px; */
}
</style>
