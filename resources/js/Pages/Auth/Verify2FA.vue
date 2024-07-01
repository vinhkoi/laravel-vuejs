<template>
  <GuestLayout>
    <div>
      <h2 class="flex a">2FA Guard</h2>
      <form @submit.prevent="submit">
        <div class="mt-4 flex items-center justify-between">
          <InputLabel for="code" value="Code" />

          <TextInput
            id="code"
            type="text"
            class="mt-1 block w-full"
            v-model="code"
            required
            autofocus
          />
          <!-- <label for="code">Mã xác thực</label> -->
          <!-- <input type="text" v-model="code" id="code" required /> -->
        </div>
        <div class="card flex a">
          <!-- <Button label="Submit" type="submit" /> -->
          <button
            type="submit"
            class="text-gray-900 bg-gradient-to-r from-teal-200 to-lime-200 hover:bg-gradient-to-l hover:from-teal-200 hover:to-lime-200 focus:ring-4 focus:outline-none focus:ring-lime-200 dark:focus:ring-teal-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
          >
            Submit
          </button>
        </div>
      </form>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";

const code = ref("");
const form = useForm({
  code: "",
});

const submit = () => {
  form.code = code.value;
  form.post(route("verify-2fa.verify"));
};
</script>
<style scoped>
.a {
  justify-content: space-around;
  margin-top: 20px;
}
</style>
