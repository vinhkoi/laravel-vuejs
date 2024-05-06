<template>
  <section class="bg-white dark:bg-gray-900">
    <div id="default-carousel" class="relative w-full" data-carousel="slide">
      <!-- Carousel wrapper -->
      <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
        <!-- Items -->
        <template v-for="(item, index) in banner" :key="index">
          <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img
              :src="item.image_url"
              class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
              alt="Banner Image"
            />
          </div>
        </template>
      </div>
      <!-- Slider indicators -->
      <div
        class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse"
      >
        <button
          v-for="(item, index) in banner"
          :key="index"
          type="button"
          class="w-3 h-3 rounded-full"
          :aria-current="index === 0 ? 'true' : 'false'"
          :aria-label="`Slide ${index + 1}`"
          :data-carousel-slide-to="index"
        ></button>
      </div>
      <!-- Slider controls -->
      <button
        type="button"
        class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
        data-carousel-prev
      >
        <!-- Previous button icon -->
      </button>
      <button
        type="button"
        class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
        data-carousel-next
      >
        <!-- Next button icon -->
      </button>
    </div>
  </section>
</template>
<script setup>
defineProps({
  banner: Array,
});
</script>
<style scoped>
.abc {
  background-color: blue;
}
</style>
