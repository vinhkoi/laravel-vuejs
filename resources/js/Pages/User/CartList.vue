<script setup>
import { ref, computed, reactive } from "vue";
import { DeleteFilled, Plus } from "@element-plus/icons-vue";

import UserLayouts from "./Layouts/UserLayouts.vue";
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
const toast = useToast();
defineProps({
  userAddress: Object,
});
const addButtonClicked = ref(false);
const paymentMethod = ref(null);
const carts = computed(() => usePage().props.cart.data.items);
const products = computed(() => usePage().props.cart.data.products);

const total = computed(() => usePage().props.cart.data.total);
const itemId = (id) => carts.value.findIndex((item) => item.product_id === id);

const form = reactive({
  address1: null,
  state: null,
  city: null,
  zipcode: null,
  country_code: null,
  type: null,
});
const formFilled = computed(() => {
  return (
    form.address1 !== null &&
    form.state !== null &&
    form.city !== null &&
    form.zipcode !== null &&
    form.country_code !== null &&
    form.type !== null
  );
});

const isFormOpen = ref(false);

const toggleForm = () => {
  isFormOpen.value = !isFormOpen.value;
};

const update = async (product, quantity) => {
  try {
    const response = await router.patch(route("cart.update", product), {
      quantity,
    });
    if (response && response.status === 200) {
      // Update successful
    }
  } catch (error) {
    console.log(error);
    if (error.response && error.response.status === 422) {
      const responseData = error.response.data;
      if (responseData && responseData.error) {
        Swal.fire({
          toast: true,
          icon: "error",
          position: "top-end",
          showConfirmButton: false,
          title: responseData.error,
          timer: 5000, // Thời gian hiển thị toast (ms)
        });
      }
    }
  }
};
//remove form cart
const remove = (product) => router.delete(route("cart.delete", product));

// function submit() {
//   if ((formFilled || userAddress) && paymentMethod.value === "stripe") {
//     // Thực hiện khi thanh toán bằng Stripe
//     router.visit(route("checkout.store"), {
//       method: "post",
//       data: {
//         carts: usePage().props.cart.data.items,
//         products: usePage().props.cart.data.products,
//         total: usePage().props.cart.data.total,
//         address: form,
//       },
//     });
//   } else if ((formFilled || userAddress) && paymentMethod.value === "cash_on_delivery") {
//     router.visit(route("checkout.COD"), {
//       method: "post",
//       data: {
//         carts: usePage().props.cart.data.items,
//         products: usePage().props.cart.data.products,
//         total: usePage().props.cart.data.total,
//         address: form,
//       },
//     });
//   } else if ((formFilled.value || userAddress.value) && paymentMethod.value === "vnpay") {
//     try {
//       const response = await axios.post(route("checkout.vnpay"), {
//         carts: usePage().props.cart.data.items,
//         products: usePage().props.cart.data.products,
//         total: usePage().props.cart.data.total,
//         address: form.value,
//       });

//       if (response.data.code === "00") {
//         window.location.href = response.data.data;
//       } else {
//         // Xử lý lỗi
//         console.error("Payment initiation failed", response.data.message);
//       }
//     } catch (error) {
//       // Xử lý lỗi
//       console.error("Error during payment initiation", error);
//     }
//   } else {
//     // Hiển thị cảnh báo nếu không đủ điều kiện
//     alert("Please fill out the form completely before proceeding.");
//   }
// }
const submit = async () => {
  if ((formFilled || userAddress) && paymentMethod.value === "stripe") {
    router.visit(route("checkout.store"), {
      method: "post",
      data: {
        carts: usePage().props.cart.data.items,
        products: usePage().props.cart.data.products,
        total: usePage().props.cart.data.total,
        address: form,
      },
    });
  } else if ((formFilled || userAddress) && paymentMethod.value === "cash_on_delivery") {
    router.visit(route("checkout.COD"), {
      method: "post",
      data: {
        carts: usePage().props.cart.data.items,
        products: usePage().props.cart.data.products,
        total: usePage().props.cart.data.total,
        address: form,
      },
    });
  } else if ((formFilled || userAddress) && paymentMethod.value === "vnpay") {
    try {
      const response = await axios.post(route("checkout.vnpay"), {
        carts: usePage().props.cart.data.items,
        products: usePage().props.cart.data.products,
        total: usePage().props.cart.data.total,
        address: form,
      });

      if (response.data.code === "00") {
        window.location.href = response.data.data;
      } else {
        // Xử lý lỗi
        console.error("Payment initiation failed", response.data.message);
      }
    } catch (error) {
      // Xử lý lỗi
      console.error("Error during payment initiation", error);
    }
  } else {
    // Hiển thị cảnh báo nếu không đủ điều kiện
    alert("Please fill out the form completely before proceeding.");
  }
};
const submitPayment = async () => {
  try {
    const response = await axios.get(route("vnpay.index"));
    if (response.data.code === "00") {
      window.location.href = response.data.data;
    } else {
      // Xử lý lỗi
    }
  } catch (error) {
    // Xử lý lỗi
  }
};
</script>
<template>
  <UserLayouts>
    <section class="text-gray-600 body-font pb-10">
      <div class="container px-5 py-24 mx-auto flex sm:flex-nowrap flex-wrap">
        <div class="lg:w-2/3 md:w-1/2 rounded-lg sm:mr-10 p-10">
          <!-- lis tof cart -->
          <h1 class="text-xl font-semibold mb-4 dark:text-white">Your Cart</h1>
          <table
            class="w-full text-sm text-left overflow-y-scroll max-h-[800px] text-gray-500 dark:text-gray-400 rounded-lg"
          >
            <thead
              class="text-xs text-white uppercase bg-gray-800 dark:bg-gray-700 dark:text-gray-400"
            >
              <tr>
                <th scope="col" class="px-16 py-3 rounded-tl-lg">
                  <span class="sr-only">Image</span>
                </th>
                <th scope="col" class="px-6 py-3">Product</th>
                <th scope="col" class="px-6 py-3">Qty</th>
                <th scope="col" class="px-6 py-3">Price</th>
                <th scope="col" class="px-6 py-3 rounded-tr-lg">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="product in products"
                :key="product.id"
                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                <td class="w-32 p-4">
                  <img
                    v-if="product.product_images.length > 0"
                    :src="`/${product.product_images[0].image}`"
                    alt="Apple Watch"
                  />
                  <img
                    v-else
                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                    alt="Apple Watch"
                  />
                </td>
                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                  {{ product.title }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center space-x-3">
                    <button
                      @click.prevent="
                        update(product, carts[itemId(product.id)].quantity - 1)
                      "
                      :disabled="carts[itemId(product.id)].quantity <= 1"
                      :class="[
                        carts[itemId(product.id)].quantity > 1
                          ? 'cursor-pointer text-purple-600'
                          : 'cursor-not-allowed text-gray-300 dark:text-gray-500',
                        'inline-flex items-center justify-center p-1 text-sm font-medium h-6 w-6 text-gray-500 bg-white border border-gray-300 rounded-full focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700',
                      ]"
                      type="button"
                    >
                      <span class="sr-only">Quantity button</span>
                      <svg
                        class="w-3 h-3"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 18 2"
                      >
                        <path
                          stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M1 1h16"
                        />
                      </svg>
                    </button>
                    <div>
                      <input
                        type="number"
                        id="first_product"
                        v-model="carts[itemId(product.id)].quantity"
                        class="bg-gray-50 w-14 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block px-2.5 py-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="1"
                        required
                      />
                    </div>
                    <button
                      @click.prevent="
                        update(product, carts[itemId(product.id)].quantity + 1)
                      "
                      class="inline-flex items-center justify-center h-6 w-6 p-1 ms-3 text-sm font-mediu cursor-pointer bg-green-500 text-white hover:bg-green-600 border border-gray-300 rounded-full focus:outline-none focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                      type="button"
                    >
                      <span class="sr-only">Quantity button</span>
                      <svg
                        class="w-3 h-3"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 18 18"
                      >
                        <path
                          stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 1v16M1 9h16"
                        />
                      </svg>
                    </button>
                  </div>
                </td>
                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                  <span
                    v-if="
                      product.flashSale &&
                      product.flashSale[0] &&
                      product.flashSale[0].discounted_price
                    "
                  >
                    ${{ product.flashSale[0].discounted_price }}
                  </span>
                  <span v-else> ${{ product.price }} </span>
                </td>

                <td class="px-6 py-4">
                  <a
                    @click="remove(product)"
                    class="font-medium cursor-pointer text-red-600 dark:text-red-500 hover:underline"
                    ><el-icon class="mt-2">
                      <DeleteFilled />
                    </el-icon>
                    <span class="ml-2">Delete</span></a
                  >
                </td>
              </tr>
            </tbody>
          </table>

          <!-- end -->
        </div>
        <div
          class="lg:w-1/3 md:w-1/2 bg-white px-6 flex flex-col md:ml-auto w-full h-fit md:py-8 mt-8 md:mt-0 dark:bg-gray-800 rounded-lg abc"
        >
          <h2 class="text-gray-900 text-2xl mb-1 font-bold dark:text-white">Summary</h2>
          <p class="leading-relaxed mb-5 text-gray-600">Total : $ {{ total }}</p>

          <div v-if="userAddress">
            <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">
              Shipping Address
            </h2>
            <p class="leading-relaxed mb-5 text-gray-600">
              {{ userAddress.address1 }} , {{ userAddress.city }},
              {{ userAddress.zipcode }}
            </p>
            <p class="leading-relaxed mb-5 text-gray-600">or you can add new below</p>
          </div>

          <div v-else>
            <p class="leading-relaxed mb-5 text-gray-600">
              Add shipping address to continue
            </p>
          </div>

          <div>
            <button
              @click="toggleForm"
              class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg"
            >
              {{ isFormOpen ? "Hide Form" : "Show Form" }}
            </button>

            <form v-if="isFormOpen" @submit.prevent="submit">
              <div class="relative mb-4">
                <label for="address1" class="leading-7 text-sm text-gray-600"
                  >Address 1</label
                >
                <input
                  type="text"
                  id="address1"
                  name="address1"
                  v-model="form.address1"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                />
              </div>
              <div class="relative mb-4">
                <label for="city" class="leading-7 text-sm text-gray-600">City</label>
                <input
                  type="text"
                  id="city"
                  name="city"
                  v-model="form.city"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                />
              </div>
              <div class="relative mb-4">
                <label for="state" class="leading-7 text-sm text-gray-600">State</label>
                <input
                  type="text"
                  id="state"
                  name="state"
                  v-model="form.state"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                />
              </div>
              <div class="relative mb-4">
                <label for="zipcode" class="leading-7 text-sm text-gray-600"
                  >Zipcode</label
                >
                <input
                  type="text"
                  id="zipcode"
                  name="zipcode"
                  v-model="form.zipcode"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                />
              </div>
              <div class="relative mb-4">
                <label for="countrycode" class="leading-7 text-sm text-gray-600"
                  >Country Code</label
                >
                <input
                  type="text"
                  id="countrycode"
                  name="countrycode"
                  v-model="form.country_code"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                />
              </div>
              <div class="relative mb-4">
                <label for="type" class="leading-7 text-sm text-gray-600"
                  >Address type</label
                >
                <input
                  type="text"
                  id="type"
                  name="type"
                  v-model="form.type"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                />
              </div>
              <div class="btn">
                <input type="hidden" name="payment_method" v-model="paymentMethod" />
                <button
                  v-if="formFilled || userAddress"
                  @click="paymentMethod = 'stripe'"
                  type="submit"
                  class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                >
                  Stripe
                </button>
                <input type="hidden" name="payment_method" v-model="paymentMethod" />
                <button
                  v-if="formFilled || userAddress"
                  @click="paymentMethod = 'cash_on_delivery'"
                  type="submit"
                  class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                >
                  Cash on delivery
                </button>
                <input type="hidden" name="payment_method" v-model="paymentMethod" />
                <button
                  v-if="formFilled || userAddress"
                  @click="paymentMethod = 'vnpay'"
                  type="submit"
                  class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                >
                  VNpay
                </button>
                <button
                  v-else
                  @click="addButtonClicked = true"
                  class="text-white bg-gray-500 border-0 py-2 px-6 focus:outline-none hover:bg-gray-600 rounded text-lg"
                >
                  Add Address to continue
                </button>
              </div>

              <div v-if="!formFilled && !userAddress && addButtonClicked">
                <p class="text-red-500 mb-2">
                  Please fill out the form completely before proceeding.
                </p>
              </div>
            </form>
          </div>
          <form
            v-bind:action="route('vnpay.index')"
            method="POST"
            @submit.prevent="submitPayment"
          >
            <button
              type="submit"
              class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg"
            >
              Pay with VNPAY
            </button>
          </form>
          <p class="text-xs text-gray-500 mt-3">Continue Shopping</p>
        </div>
      </div>
    </section>
  </UserLayouts>
</template>
<style scoped>
.btn {
  display: flex;
  justify-content: space-around;
}

.a {
  text-decoration: line-through;
}

.abc {
  background-color: #f9fafb;
}
</style>
