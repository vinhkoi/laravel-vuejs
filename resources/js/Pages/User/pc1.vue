<template>
    <UserLayouts>
      <div class="pc-builder container mx-auto p-4">
        <h1 class="text-2xl font-semibold mb-4 text-center">PC Builder</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="component-selection">
            <Accordion :activeIndex="0">
              <AccordionTab
                v-for="componentType in componentTypes"
                :key="componentType"
                :header="componentType"
              >
                <div class="mb-4">
                  <Dropdown
                    v-model="selectedComponents[componentType]"
                    :options="components[componentType]"
                    optionLabel="title"
                    optionValue="id"
                    placeholder="Chọn linh kiện"
                    class="w-full"
                  />
                </div>
              </AccordionTab>
            </Accordion>
          </div>

          <div class="selected-components">
            <h2 class="text-lg font-semibold mb-2">Linh kiện đã chọn</h2>
            <div
              v-for="(componentId, componentType) in selectedComponents"
              :key="componentType"
            >
              <SelectedComponent
                v-if="componentId"
                :componentId="componentId"
                :componentType="componentType"
              />
            </div>

            <div class="total-price mt-4">
              <h3 class="text-lg font-semibold">Tổng cộng: ${{ totalCost }}</h3>
            </div>
          </div>
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
