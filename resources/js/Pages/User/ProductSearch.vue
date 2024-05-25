<script setup>
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import UserLayouts from "./Layouts/UserLayouts.vue";
import { ref, watch } from "vue";

defineProps({
  products: Array,
  query: String,

  brands: Array,
  categories: Array,
});

const addToCart = (product) => {
  console.log(product);
  router.post(route("cart.store", product), {
    onSuccess: (page) => {
      if (page.props.flash.success) {
        Swal.fire({
          toast: true,
          icon: "success",
          position: "top-end",
          showConfirmButton: false,
          title: page.props.flash.success,
        });
      }
    },
  });
};

import {
  Dialog,
  DialogPanel,
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import {
  ChevronDownIcon,
  FunnelIcon,
  MinusIcon,
  PlusIcon,
  Squares2X2Icon,
} from "@heroicons/vue/20/solid";
import SecondaryButtonVue from "@/Components/SecondaryButton.vue";

const sortOptions = [
  { name: "Price: Low to High", href: "#", current: false },
  { name: "Price: High to Low", href: "#", current: false },
];

const filterPrices = useForm({
  prices: [0, 100000],
});
//method for price filter
const priceFilter = () => {
  filterPrices
    .transform((data) => ({
      ...data,
      prices: {
        from: filterPrices.prices[0],
        to: filterPrices.prices[1],
      },
    }))
    .get("products", {
      preserveState: true,
      replace: true,
    });
};

const mobileFiltersOpen = ref(false);

//filter brands and categories
const selectedBrands = ref([]);
const selectedCategories = ref([]);

watch(selectedBrands, () => {
  updateFilteredProducts();
});
watch(selectedCategories, () => {
  updateFilteredProducts();
});

async function updateFilteredProducts() {
  const currentQuery = usePage().props.ziggy.query;

  try {
    await router.get(
      route("products.search"),
      {
        query: currentQuery,
        brands: selectedBrands.value,
        categories: selectedCategories.value,
      },
      {
        preserveState: true,
        replace: true,
      }
    );
  } catch (error) {
    console.error("Error fetching filtered products", error);
    // Hiển thị thông báo lỗi cho người dùng (ví dụ: sử dụng Toast hoặc Alert)
  }
}
//   router.get(
//     "products",
//     {
//       brands: selectedBrands.value,
//       categories: selectedCategories.value,
//     },
//     {
//       preserveState: true,
//       replace: true,
//     }
//   );
</script>
<template>
  <UserLayouts>
    <div class="bg-white">
      <div>
        <!-- Mobile filter dialog -->
        <TransitionRoot as="template" :show="mobileFiltersOpen">
          <Dialog
            as="div"
            class="relative z-40 lg:hidden"
            @close="mobileFiltersOpen = false"
          >
            <TransitionChild
              as="template"
              enter="transition-opacity ease-linear duration-300"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="transition-opacity ease-linear duration-300"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-0 z-40 flex">
              <TransitionChild
                as="template"
                enter="transition ease-in-out duration-300 transform"
                enter-from="translate-x-full"
                enter-to="translate-x-0"
                leave="transition ease-in-out duration-300 transform"
                leave-from="translate-x-0"
                leave-to="translate-x-full"
              >
                <DialogPanel
                  class="relative ml-auto flex h-full w-full max-w-xs flex-col overflow-y-auto bg-white py-4 pb-12 shadow-xl"
                >
                  <div class="flex items-center justify-between px-4">
                    <h2 class="text-lg font-medium text-gray-900">Filters</h2>
                    <button
                      type="button"
                      class="-mr-2 flex h-10 w-10 items-center justify-center rounded-md bg-white p-2 text-gray-400"
                      @click="mobileFiltersOpen = false"
                    >
                      <span class="sr-only">Close menu</span>
                      <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                    </button>
                  </div>

                  <!-- Filters -->
                  <form class="mt-4 border-t border-gray-200">
                    <h3 class="sr-only">Categories</h3>
                    <ul role="list" class="px-2 py-3 font-medium text-gray-900">
                      <li v-for="category in subCategories" :key="category.name">
                        <a :href="category.href" class="block px-2 py-3">{{
                          category.name
                        }}</a>
                      </li>
                    </ul>

                    <Disclosure
                      as="div"
                      v-for="section in filters"
                      :key="section.id"
                      class="border-t border-gray-200 px-4 py-6"
                      v-slot="{ open }"
                    >
                      <h3 class="-mx-2 -my-3 flow-root">
                        <DisclosureButton
                          class="flex w-full items-center justify-between bg-white px-2 py-3 text-gray-400 hover:text-gray-500"
                        >
                          <span class="font-medium text-gray-900">{{
                            section.name
                          }}</span>
                          <span class="ml-6 flex items-center">
                            <PlusIcon v-if="!open" class="h-5 w-5" aria-hidden="true" />
                            <MinusIcon v-else class="h-5 w-5" aria-hidden="true" />
                          </span>
                        </DisclosureButton>
                      </h3>
                      <DisclosurePanel class="pt-6">
                        <div class="space-y-6">
                          <div
                            v-for="(option, optionIdx) in section.options"
                            :key="option.value"
                            class="flex items-center"
                          >
                            <input
                              :id="`filter-mobile-${section.id}-${optionIdx}`"
                              :name="`${section.id}[]`"
                              :value="option.value"
                              type="checkbox"
                              :checked="option.checked"
                              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label
                              :for="`filter-mobile-${section.id}-${optionIdx}`"
                              class="ml-3 min-w-0 flex-1 text-gray-500"
                              >{{ option.label }}</label
                            >
                          </div>
                        </div>
                      </DisclosurePanel>
                    </Disclosure>
                  </form>
                </DialogPanel>
              </TransitionChild>
            </div>
          </Dialog>
        </TransitionRoot>

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div
            class="flex items-baseline justify-between border-b border-gray-200 pb-6 pt-24"
          >
            <h1 class="text-4xl font-bold tracking-tight text-gray-900">
              Kết quả tìm kiếm cho "{{ query }}"
            </h1>

            <div class="flex items-center">
              <Menu as="div" class="relative inline-block text-left">
                <div>
                  <MenuButton
                    class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900"
                  >
                    Sort
                    <ChevronDownIcon
                      class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500"
                      aria-hidden="true"
                    />
                  </MenuButton>
                </div>

                <transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <MenuItems
                    class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none"
                  >
                    <div class="py-1">
                      <MenuItem
                        v-for="option in sortOptions"
                        :key="option.name"
                        v-slot="{ active }"
                      >
                        <a
                          :href="option.href"
                          :class="[
                            option.current
                              ? 'font-medium text-gray-900'
                              : 'text-gray-500',
                            active ? 'bg-gray-100' : '',
                            'block px-4 py-2 text-sm',
                          ]"
                          >{{ option.name }}</a
                        >
                      </MenuItem>
                    </div>
                  </MenuItems>
                </transition>
              </Menu>

              <button
                type="button"
                class="-m-2 ml-5 p-2 text-gray-400 hover:text-gray-500 sm:ml-7"
              >
                <span class="sr-only">View grid</span>
                <Squares2X2Icon class="h-5 w-5" aria-hidden="true" />
              </button>
              <button
                type="button"
                class="-m-2 ml-4 p-2 text-gray-400 hover:text-gray-500 sm:ml-6 lg:hidden"
                @click="mobileFiltersOpen = true"
              >
                <span class="sr-only">Filters</span>
                <FunnelIcon class="h-5 w-5" aria-hidden="true" />
              </button>
            </div>
          </div>

          <section aria-labelledby="products-heading" class="pb-24 pt-6">
            <h2 id="products-heading" class="sr-only">Products</h2>

            <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4">
              <!-- Filters -->
              <form class="hidden lg:block">
                <h3 class="sr-only">Prices</h3>
                <!-- price filter -->
                <div class="flex items-center justify-between space-x-3">
                  <div class="basis-1/3">
                    <label
                      for="filters-price-from"
                      class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                    >
                      From
                    </label>

                    <input
                      type="number"
                      id="filters-price-from"
                      placeholder="Min price"
                      v-model="filterPrices.prices[0]"
                      class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                    />
                  </div>
                  <div class="basis-1/3">
                    <label
                      for="filters-price-to"
                      class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"
                    >
                      To
                    </label>

                    <input
                      type="number"
                      id="filters-price-to"
                      v-model="filterPrices.prices[1]"
                      placeholder="Max price"
                      class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                    />
                  </div>
                  <SecondaryButtonVue class="self-end" @click="priceFilter()">
                    Ok
                  </SecondaryButtonVue>
                </div>

                <!-- end -->

                <Disclosure
                  as="div"
                  class="border-b border-gray-200 py-6"
                  v-slot="{ open }"
                >
                  <h3 class="-my-3 flow-root">
                    <DisclosureButton
                      class="flex w-full items-center justify-between bg-white py-3 text-sm text-gray-400 hover:text-gray-500"
                    >
                      <span class="font-medium text-gray-900">Brands</span>
                      <span class="ml-6 flex items-center">
                        <PlusIcon v-if="!open" class="h-5 w-5" aria-hidden="true" />
                        <MinusIcon v-else class="h-5 w-5" aria-hidden="true" />
                      </span>
                    </DisclosureButton>
                  </h3>
                  <DisclosurePanel class="pt-6">
                    <div class="space-y-4">
                      <div
                        v-for="brand in brands"
                        :key="brand.id"
                        class="flex items-center"
                      >
                        <input
                          :id="`filter-${brand.id}`"
                          :value="brand.id"
                          type="checkbox"
                          v-model="selectedBrands"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <label
                          :for="`filter-${brand.id}`"
                          class="ml-3 text-sm text-gray-600"
                          >{{ brand.name }}</label
                        >
                      </div>
                    </div>
                  </DisclosurePanel>
                </Disclosure>

                <!-- category filter -->

                <Disclosure
                  as="div"
                  class="border-b border-gray-200 py-6"
                  v-slot="{ open }"
                >
                  <h3 class="-my-3 flow-root">
                    <DisclosureButton
                      class="flex w-full items-center justify-between bg-white py-3 text-sm text-gray-400 hover:text-gray-500"
                    >
                      <span class="font-medium text-gray-900">Categories</span>
                      <span class="ml-6 flex items-center">
                        <PlusIcon v-if="!open" class="h-5 w-5" aria-hidden="true" />
                        <MinusIcon v-else class="h-5 w-5" aria-hidden="true" />
                      </span>
                    </DisclosureButton>
                  </h3>
                  <DisclosurePanel class="pt-6">
                    <div class="space-y-4">
                      <div
                        v-for="category in categories"
                        :key="category.id"
                        class="flex items-center"
                      >
                        <input
                          :id="`filter-${category.id}`"
                          :value="category.id"
                          type="checkbox"
                          v-model="selectedCategories"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <label
                          :for="`filter-${category.id}`"
                          class="ml-3 text-sm text-gray-600"
                          >{{ category.name }}</label
                        >
                      </div>
                    </div>
                  </DisclosurePanel>
                </Disclosure>

                <!-- end -->
              </form>

              <!-- Product grid -->
              <div class="lg:col-span-3">
                <div
                  class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8"
                >
                  <div
                    v-for="product in products.data"
                    :key="product.id"
                    class="group relative product-border"
                  >
                    <div
                      class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none lg:h-80"
                    >
                      <img
                        v-if="product.product_images.length > 0"
                        :src="`/${product.product_images[0].image}`"
                        :alt="product.imageAlt"
                        class="h-full w-full object-cover object-center lg:h-full lg:w-full"
                      />
                      <img
                        v-else
                        src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                        :alt="product.imageAlt"
                        class="h-full w-full object-cover object-center lg:h-full lg:w-full"
                      />

                      <div
                        class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer"
                      >
                        <template v-if="product.inStock">
                          <div class="bg-blue-700 p-2 rounded-full">
                            <a @click="addToCart(product)">
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-6 h-6 text-white"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                                />
                              </svg>
                            </a>
                          </div>
                        </template>
                        <template v-else>
                          <p class="bg-red-500 text-white p-2 rounded-full">
                            OUT OF STOCK
                          </p>
                        </template>
                        <div class="bg-blue-700 p-2 rounded-full ml-2">
                          <Link :href="route('detail.view', { id: product.id })">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke-width="1.5"
                              stroke="currentColor"
                              class="w-6 h-6 text-white"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                              />
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                              />
                            </svg>
                          </Link>
                        </div>
                      </div>
                    </div>
                    <div class="mt-4 flex justify-between">
                      <div>
                        <h3 class="text-sm text-gray-700">
                          <span aria-hidden="true" class="" />
                          {{ product.title }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">{{ product.brand.name }}</p>
                      </div>
                      <p class="text-sm font-medium text-gray-900">
                        ${{ product.price }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </main>
      </div>
    </div>
  </UserLayouts>
</template>
<style scoped>
.product-border {
  border: 2px solid #e5e7eb; /* Màu viền */
  border-radius: 8px; /* Bo góc */
  padding: 16px; /* Khoảng cách giữa các sản phẩm */
  background-color: #f5f5f5;
}
</style>
