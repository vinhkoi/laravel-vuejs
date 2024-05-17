<template>
  <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
    <form v-if="auth.user" @submit.prevent="AddChirp()">
      <textarea
        v-model="message"
        placeholder="What's on your mind?"
        class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
      ></textarea>
      <PrimaryButton type="submit" class="mt-4">Chirp</PrimaryButton>
    </form>

    <div v-else class="text-center mt-4">
      <!-- <p>Vui lòng đăng nhập hoặc đăng ký để chia sẻ suy nghĩ của bạn.</p> -->
      <p>
        Vui lòng
        <Link :href="route('login')" class="hover:text-blue-500 font-semibold"
          >đăng nhập</Link
        >
        hoặc
        <Link
          :href="route('register')"
          v-if="canRegister"
          class="hover:text-blue-500 font-semibold"
          >đăng ký</Link
        >
        để chia sẻ suy nghĩ của bạn.
      </p>
    </div>
  </div>
  <div class="mt-6 bg-white shadow-sm rounded-lg divide-y chirp-container">
    <div
      class="p-6 flex space-x-2"
      v-for="chirp in chirps"
      :key="chirp.id"
      :chirp="chirp"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 text-gray-600 -scale-x-100"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
        />
      </svg>
      <div class="flex-1">
        <div class="flex justify-between items-center">
          <div>
            <span class="text-gray-800">{{ chirp.user.name }}</span>
            <small class="ml-2 text-sm text-gray-600">{{
              dayjs(chirp.created_at).fromNow()
            }}</small>
          </div>
          <Dropdown v-if="chirp.user_id === auth.user.id">
            <template #trigger>
              <button>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4 text-gray-400"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                  />
                </svg>
              </button>
            </template>
            <template #content>
              <button
                class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:bg-gray-100 transition duration-150 ease-in-out"
                @click="startEditing(chirp)"
              >
                Edit
              </button>
            </template>
          </Dropdown>
        </div>
        <form
          v-if="editing && editingChirpId === chirp.id"
          @submit.prevent="UpdateChirp()"
        >
          <textarea
            v-model="messages"
            class="mt-4 w-full text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
          ></textarea>
          <div class="space-x-2">
            <PrimaryButton class="mt-4">Save</PrimaryButton>
            <button class="mt-4" @click="cancelEditing()">Cancel</button>
          </div>
        </form>
        <p v-else class="mt-4 text-lg text-gray-900">{{ chirp.message }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, Head } from "@inertiajs/vue3";
import dayjs from "dayjs";
import { ref } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";
import relativeTime from "dayjs/plugin/relativeTime";
import Dropdown from "@/Components/Dropdown.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

dayjs.extend(relativeTime);

defineProps({
  chirps: Array,
});
const editing = ref(false);
const editingChirpId = ref(null);
const startEditing = (chirp) => {
  editing.value = true;
  editingChirpId.value = chirp.id;
  messages.value = chirp.message;
};

const cancelEditing = () => {
  editing.value = false;
  editingChirpId.value = null;
  messages.value = "";
};
const resetFormData = () => {
  message.value = "";
};
const auth = usePage().props.auth;
const canRegister = usePage().props.canRegister;

const message = ref("");
const messages = ref("");

const AddChirp = async () => {
  const formData = new FormData();
  formData.append("message", message.value);
  try {
    await router.post("chirps/store", formData, {
      onSuccess: (page) => {
        Swal.fire({
          toast: true,
          icon: "success",
          position: "top-end",
          showConfirmButton: false,
          title: page.props.flash.success,
        });
        resetFormData();
      },
    });
  } catch (error) {
    console.log(error);
  }
};
const UpdateChirp = async () => {
  const formData = new FormData();
  formData.append("message", messages.value);
  formData.append("_method", "PATCH");

  try {
    await router.post("chirps/update/" + editingChirpId.value, formData, {
      onSuccess: (page) => {
        Swal.fire({
          toast: true,
          icon: "success",
          position: "top-end",
          showConfirmButton: false,
          title: page.props.flash.success,
        });
        resetFormData();
      },
    });
  } catch (error) {
    console.log(error);
  }
};
</script>

<style scoped>
.chirp-container {
  padding-bottom: 100px; /* Điều chỉnh giá trị theo chiều cao của footer */
}
</style>
