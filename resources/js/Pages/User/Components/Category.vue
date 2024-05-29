<template>
  <section class="featured-categories">
    <div class="featured-categories-container">
      <h2 class="text-2xl font-semibold mb-4 text-center">FEATURED CATEGORIES</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-4">
        <Link
          v-for="category in categories"
          :key="category.id"
          :href="route('products.index', { category: category.id })"
          class="category-item cursor-pointer"
        >
          <img
            v-if="category.imageUrl.length > 0"
            :src="category.imageUrl"
            :alt="category.name"
            class="w-full h-auto mb-2 rounded-md transition-transform duration-300 hover:scale-110"
          />
          <img
            v-else
            src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
            :alt="product.imageAlt"
            class="h-full w-full object-cover object-center lg:h-full lg:w-full"
          />
          <p class="text-center text-gray-700">{{ category.name }}</p>
        </Link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Link, router } from "@inertiajs/vue3";

defineProps({
  categories: Array,
});
</script>

<style scoped>
.featured-categories {
  padding: 2rem;
}
.featured-categories-container {
  border: 1px solid #ccc; /* Hoặc màu khác tùy thích */
  padding: 20px;
  border-radius: 5px; /* Bo góc nếu muốn */
  /* color: #f5f5f5; */
}

.category-item {
  @apply flex flex-col items-center justify-center; /* Căn giữa nội dung */
  @apply p-4 border rounded-md shadow-md; /* Thêm viền, bo góc và đổ bóng */
}
</style>
