<template>
  <UserLayouts>
    <div class="pc-builder container mx-auto p-4">
      <h1 class="text-2xl font-semibold mb-4">PC Builder</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-for="componentType in componentTypes" :key="componentType" class="mb-4">
          <label :for="componentType" class="block text-sm font-medium text-gray-700">
            {{ componentType }}
          </label>
          <select
            v-model="selectedComponents[componentType]"
            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">Chọn {{ componentType }}</option>
            <option
              v-for="component in components[componentType]"
              :key="component.id"
              :value="component.id"
            >
              {{ component.title }} - ${{ component.price }}
            </option>
          </select>
        </div>
      </div>

      <div class="mt-4">
        <h2 class="text-xl font-semibold">Tổng giá: ${{ totalCost }}</h2>
      </div>
    </div>
  </UserLayouts>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import UserLayouts from "./Layouts/UserLayouts.vue";

const { props } = usePage();

const selectedComponents = ref({});
const componentTypes = ref(props.componentTypes);
const components = ref(props.components);

const totalCost = computed(() => {
  return Object.values(selectedComponents.value).reduce((acc, componentId) => {
    const componentType = Object.keys(selectedComponents.value).find(
      (key) => selectedComponents.value[key] === componentId
    );
    const component = components.value[componentType]?.find((c) => c.id === componentId);
    return component ? acc + Number(component.price) : acc;
  }, 0);
});

onMounted(() => {
  componentTypes.value.forEach((type) => {
    selectedComponents.value[type] = "";
  });
});
</script>

<style></style>
