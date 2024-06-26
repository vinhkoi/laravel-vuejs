<script setup>
import { ref, computed, reactive, onMounted, watch } from "vue";
import { DeleteFilled, Plus } from "@element-plus/icons-vue";

import UserLayouts from "./Layouts/UserLayouts.vue";
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import axios from "axios";

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
  country_code: null,
  type: null,
  wardCode: null,
  toDistrictId: null,
});
const formFilled = computed(() => {
  return (
    form.address1 !== null &&
    form.state !== null &&
    form.city !== null &&
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

const cities = ref([]);
const districts = ref([]);
const wards = ref([]);

const selectedCity = ref(null);
const selectedDistrict = ref(null);
const selectedWard = ref(null);

const services = ref([]);
const shippingFee = ref(null);
const selectedService = ref(null);
const estimatedDeliveryTime = ref(null);
const orderDate = ref(null);

const totalWithShipping = computed(() => {
  return total.value + shippingFee.value;
});

const fetchCities = async () => {
  try {
    const response = await axios.get(
      "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province",
      {
        headers: {
          Token: "b75f3a9a-3204-11ef-8e53-0a00184fe694",
        },
      }
    );
    cities.value = response.data.data;
  } catch (error) {
    console.error("Error fetching cities:", error);
  }
};

const fetchDistricts = async (cityId) => {
  try {
    const response = await axios.get(
      "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district",
      {
        headers: {
          Token: "b75f3a9a-3204-11ef-8e53-0a00184fe694",
        },
        params: {
          province_id: cityId,
        },
      }
    );
    districts.value = response.data.data;
  } catch (error) {
    console.error("Error fetching districts:", error);
  }
};

const fetchWards = async (districtId) => {
  try {
    const response = await axios.get(
      "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward",
      {
        headers: {
          Token: "b75f3a9a-3204-11ef-8e53-0a00184fe694",
        },
        params: {
          district_id: districtId,
        },
      }
    );
    wards.value = response.data.data;
  } catch (error) {
    console.error("Error fetching wards:", error);
  }
};
const fetchServices = async (districtId) => {
  try {
    const response = await axios.get(
      "https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services",
      {
        headers: {
          Token: "b75f3a9a-3204-11ef-8e53-0a00184fe694",
        },
        params: {
          shop_id: "192660",
          from_district: 1486,
          to_district: districtId,
        },
      }
    );
    services.value = response.data.data;
  } catch (error) {
    console.error("Error fetching services:", error);
  }
};

const calculateShippingFee = async () => {
  try {
    const response = await axios.post(
      "https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee",
      {
        service_id: selectedService.value,
        insurance_value: 0,
        coupon: null,
        from_district_id: 1486,
        to_district_id: parseInt(form.toDistrictId, 10),
        to_ward_code: form.wardCode,
        height: 20,
        length: 30,
        weight: 30,
        width: 40,
        // items: [
        //   {
        //     name: "TEST1",
        //     quantity: 1,
        //     height: 200,
        //     weight: 1000,
        //     length: 200,
        //     width: 200,
        //   },
        // ],
      },
      {
        headers: {
          Token: "b75f3a9a-3204-11ef-8e53-0a00184fe694",
        },
      }
    );
    shippingFee.value = response.data.data.total;
  } catch (error) {
    console.error("Error calculating shipping fee:", error);
  }
};

const calculateEstimatedDeliveryTime = async () => {
  try {
    const response = await axios.post(
      "https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/leadtime",
      {
        shop_id: "192660",
        from_district_id: 1486,
        from_ward_code: "1A0417",
        to_district_id: parseInt(form.toDistrictId, 10),
        to_ward_code: form.wardCode,
        service_id: selectedService.value,
      },
      {
        headers: {
          Token: "b75f3a9a-3204-11ef-8e53-0a00184fe694",
        },
      }
    );
    const leadtimeTimestamp = response.data.data.leadtime;
    const orderDateTimestamp = response.data.data.order_date;

    estimatedDeliveryTime.value = new Date(leadtimeTimestamp * 1000).toLocaleString();
    orderDate.value = new Date(orderDateTimestamp * 1000).toLocaleString();
  } catch (error) {
    console.error("Error calculating estimated delivery time:", error);
  }
};

onMounted(() => {
  fetchCities();
});

const handleCityChange = async (event) => {
  const cityId = event.target.value;
  selectedCity.value = cityId;
  const city = cities.value.find((city) => city.ProvinceID == selectedCity.value);
  form.city = city.ProvinceName;

  await fetchDistricts(cityId);
};

const handleDistrictChange = async (event) => {
  const districtId = event.target.value;
  const districtIdd = parseInt(event.target.value, 10);
  selectedDistrict.value = districtId;
  const district = districts.value.find(
    (district) => district.DistrictID == selectedDistrict.value
  );
  form.state = district.DistrictName;
  form.toDistrictId = districtIdd;
  await fetchWards(districtId);
  await fetchServices(districtId);
};
const handleWardChange = (event) => {
  const wardId = event.target.value;
  selectedWard.value = event.target.value;
  const ward = wards.value.find((ward) => ward.WardCode == selectedWard.value);
  form.address1 = ward.WardName;
  form.wardCode = wardId;
};
watch(selectedService, async (newService) => {
  if (newService) {
    await calculateShippingFee(form.toDistrictId, form.wardCode);
    await calculateEstimatedDeliveryTime();
  }
});

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
// const submit = async () => {
//   if ((formFilled || userAddress) && paymentMethod.value === "Stripe") {
//     router.visit(route("checkout.store"), {
//       method: "post",
//       data: {
//         carts: usePage().props.cart.data.items,
//         products: usePage().props.cart.data.products,
//         total: usePage().props.cart.data.total,
//         address: form,
//       },
//     });
//   } else if ((formFilled || userAddress) && paymentMethod.value === "COD") {
//     router.visit(route("checkout.COD"), {
//       method: "post",
//       data: {
//         carts: usePage().props.cart.data.items,
//         products: usePage().props.cart.data.products,
//         total: usePage().props.cart.data.total,
//         address: form,
//       },
//     });
//   } else if ((formFilled || userAddress) && paymentMethod.value === "VNPAY") {
//     try {
//       const response = await axios.post(route("checkout.vnpay"), {
//         carts: usePage().props.cart.data.items,
//         products: usePage().props.cart.data.products,
//         total: usePage().props.cart.data.total,
//         address: form,
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
// };
const paymentMethods = ref([
  {
    name: "COD",
    logo:
      "data:image/png;base64,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",
  },
  {
    name: "VNPAY",
    logo:
      "data:image/png;base64,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",
  },
  {
    name: "Stripe",
    logo:
      "data:image/png;base64,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",
  },
]);
const submit = async () => {
  if ((formFilled || userAddress) && paymentMethod.value === "Stripe") {
    router.visit(route("checkout.store"), {
      method: "post",
      data: {
        carts: usePage().props.cart.data.items,
        products: usePage().props.cart.data.products,
        total: totalWithShipping.value,
        address: form,
        shipping_fee: shippingFee.value,
        estimated_delivery_time: estimatedDeliveryTime.value,
      },
    });
  } else if ((formFilled || userAddress) && paymentMethod.value === "COD") {
    router.visit(route("checkout.COD"), {
      method: "post",
      data: {
        carts: usePage().props.cart.data.items,
        products: usePage().props.cart.data.products,
        total: totalWithShipping.value,
        address: form,
        shipping_fee: shippingFee.value,
        estimated_delivery_time: estimatedDeliveryTime.value,
      },
    });
  } else if ((formFilled || userAddress) && paymentMethod.value === "VNPAY") {
    try {
      const response = await axios.post(route("checkout.vnpay"), {
        carts: usePage().props.cart.data.items,
        products: usePage().props.cart.data.products,
        total: totalWithShipping.value,
        address: form,
        shipping_fee: shippingFee.value,
        estimated_delivery_time: estimatedDeliveryTime.value,
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
              {{ userAddress.address1 }} , {{ userAddress.city }},{{ userAddress.state }}
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
              <!-- <div class="relative mb-4">
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
              </div> -->

              <!-- <div class="btn">
                <input type="hidden" name="payment_method" v-model="paymentMethod" />
                <button
                  v-if="formFilled || userAddress"
                  @click="paymentMethod = 'stripe'"
                  type="submit"
                  class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                >
                  Stri
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
              </div> -->

              <div class="relative mb-4">
                <label for="city" class="leading-7 text-sm text-gray-600">City</label>
                <select
                  id="city"
                  name="city"
                  v-model="selectedCity"
                  @change="handleCityChange"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                >
                  <option value="" disabled>Select City</option>
                  <option
                    v-for="city in cities"
                    :key="city.ProvinceID"
                    :value="city.ProvinceID"
                  >
                    {{ city.ProvinceName }}
                  </option>
                </select>
              </div>

              <div class="relative mb-4">
                <label for="district" class="leading-7 text-sm text-gray-600"
                  >District</label
                >
                <select
                  id="district"
                  name="district"
                  v-model="selectedDistrict"
                  @change="handleDistrictChange"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                >
                  <option value="" disabled>Select District</option>
                  <option
                    v-for="district in districts"
                    :key="district.DistrictID"
                    :value="district.DistrictID"
                  >
                    {{ district.DistrictName }}
                  </option>
                </select>
              </div>
              <div class="hidden relative mb-4">
                <select v-model="form.toDistrictId">
                  <option
                    v-for="district in districts"
                    :key="district.DistrictID"
                    :value="district.DistrictID"
                  >
                    {{ district.DistrictName }}
                  </option>
                </select>
              </div>
              <div class="relative mb-4">
                <label for="ward" class="leading-7 text-sm text-gray-600">Ward</label>
                <select
                  id="ward"
                  name="ward"
                  v-model="selectedWard"
                  @change="handleWardChange"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                >
                  <option value="" disabled>Select Ward</option>
                  <option
                    v-for="ward in wards"
                    :key="ward.WardCode"
                    :value="ward.WardCode"
                  >
                    {{ ward.WardName }}
                  </option>
                </select>
              </div>
              <div class="hidden relative mb-4">
                <select v-model="form.wardCode">
                  <option
                    v-for="ward in wards"
                    :key="ward.WardCode"
                    :value="ward.WardCode"
                  >
                    {{ ward.WardName }}
                  </option>
                </select>
              </div>
              <div class="relative mb-4">
                <label for="countrycode" class="leading-7 text-sm text-gray-600"
                  >Address</label
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
              <div class="relative mb-4">
                <label for="service">Select Service</label>
                <select
                  v-model="selectedService"
                  class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                >
                  <option
                    v-for="service in services"
                    :key="service.service_id"
                    :value="service.service_id"
                  >
                    {{ service.short_name }}
                  </option>
                </select>
              </div>
              <div class="my-5 grid gap-6">
                <div class="relative" v-for="method in paymentMethods" :key="method.name">
                  <input
                    class="peer hidden"
                    v-model="paymentMethod"
                    :value="method.name"
                    :id="'radio-payment-' + method.name"
                    type="radio"
                    name="payment_method"
                    required
                  />
                  <span
                    class="peer-checked:border-gray-700 absolute right-4 top-1/2 box-content block h-3 w-3 -translate-y-1/2 rounded-full border-8 border-gray-300 bg-white"
                  ></span>
                  <label
                    class="peer-checked:border-2 peer-checked:border-gray-700 peer-checked:bg-gray-50 flex cursor-pointer select-none rounded-lg border border-gray-300 p-4"
                    :for="'radio-payment-' + method.name"
                  >
                    <img
                      class="w-14 object-contain"
                      :src="
                        method.logo ||
                        'https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png'
                      "
                      :alt="method.name + ' Logo'"
                    />
                    <div class="ml-5">
                      <span class="mt-2 font-semibold capitalize">{{ method.name }}</span>
                    </div>
                  </label>
                </div>
              </div>

              <!-- Hiển thị phí vận chuyển -->
              <div v-if="shippingFee !== null">Shipping Fee: {{ shippingFee }}</div>
              <div v-if="estimatedDeliveryTime">
                Estimated Delivery Time: {{ estimatedDeliveryTime }}
              </div>

              <div class="btn">
                <button
                  v-if="formFilled || userAddress"
                  @click="submit"
                  type="submit"
                  class="text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                >
                  Submit Payment
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
