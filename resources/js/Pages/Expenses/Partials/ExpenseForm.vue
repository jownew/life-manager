<template>
  <DialogModal :show="isOpen" @close="close">
    <template #title> {{ pageMode }} Expense </template>
    <template #content>
      <div class="grid gap-6" @keyup.enter="saveItem">
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="name" value="Name" />
          <input
            id="name"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            v-model="form.name"
            type="text"
            autofocus
            ref="focusInput"
          />
          <InputError :message="form.errors.name" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="amount" value="Amount" />
          <input
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            v-model="form.amount"
            type="number"
            id="amount"
          />
          <InputError :message="form.errors.amount" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="category_id" value="Category" />
          <select
            id="category_id"
            v-model="form.category_id"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
          >
            <option
              :key="category.id"
              :value="category.id"
              v-for="category in data.categories"
            >
              {{ category.name }}
            </option>
          </select>
          <InputError :message="form.errors.category_id" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="transaction_date" value="Date" />
          <input
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            v-model="form.transaction_date"
            type="date"
            id="transaction_date"
          />
          <InputError :message="form.errors.transaction_date" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="currency_id" value="Currency" />
          <select
            id="currency_id"
            v-model="form.currency_id"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
          >
            <option
              :key="currency.id"
              :value="currency.id"
              v-for="currency in props.currencies"
            >
              {{ currency.name }}
            </option>
          </select>
          <InputError :message="form.errors.currency_id" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="payment_type_id" value="Payment From" />
          <select
            id="payment_type_id"
            v-model="form.payment_type_id"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
          >
            <option
              :key="paymentType.id"
              :value="paymentType.id"
              v-for="paymentType in data.paymentTypes"
            >
              {{ paymentType.name }}
            </option>
          </select>
          <InputError :message="form.errors.payment_type_id" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="payment_to_id" value="Payment To" />
          <select
            id="payment_to_id"
            v-model="form.payment_to_id"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
          >
            <option></option>
            <option
              :key="paymentType.id"
              :value="paymentType.id"
              v-for="paymentType in data.paymentTypes"
            >
              {{ paymentType.name }}
            </option>
          </select>
          <InputError :message="form.errors.payment_to_id" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="description" value="Notes" />
          <textarea
            name="description"
            v-model="form.description"
            id="description"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            cols="30"
            rows="5"
          ></textarea>
          <InputError :message="form.errors.description" class="mt-2" />
        </div>
      </div>
    </template>

    <template #footer>
      <SecondaryButton
        @click="clone"
        v-if="props.itemId != '' && props.itemId == data.itemId"
      >
        Clone
      </SecondaryButton>

      <SecondaryButton class="ml-2" @click="close"> Nevermind </SecondaryButton>

      <PrimaryButton
        class="ml-2"
        @click="saveForm"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        {{ saveMode }}
      </PrimaryButton>
    </template>
  </DialogModal>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { nextTick, computed, ref, watch, reactive } from 'vue';
import moment from 'moment';

const emit = defineEmits(['close']);

const focusInput = ref(null);

const props = defineProps({
  isOpen: Boolean,
  itemId: String,
  currencies: Array,
});

const data = reactive({
  itemId: props.itemId,
  categories: [],
  paymentTypes: [],
});

const close = () => {
  emit('close');
};

const clone = () => {
  data.itemId = '';
  form.transaction_date = moment().format('YYYY-MM-DD');
};

const pageMode = computed(() => (data.itemId == '' ? 'Add' : 'Edit'));
const saveMode = computed(() => (data.itemId == '' ? 'Add' : 'Update'));

const form = useForm({
  name: '',
  description: '',
  currency_id: '',
  payment_type_id: '',
  payment_to_id: '',
  amount: null,
  transaction_date: null,
  category_id: null,
});

const saveForm = () => {
  if (form.is_owed === null) {
    form.amount = null;
    form.currency_id = null;
  }

  if (data.itemId == '') {
    form.post(route('expenses.store'), {
      onSuccess: () => close(),
    });
  } else {
    form.patch(route('expenses.update', data.itemId), {
      onSuccess: () => close(),
    });
  }
};

watch(
  () => props.isOpen,
  () => {
    if (props.isOpen) {
      data.itemId = props.itemId;
      fetchData();
      nextTick(() => {
        focusInput.value.focus();
      });
    }
  }
);

const fetchData = () => {
  getPaymentTypes();
  getCategories();

  form.clearErrors();
  resetForm();

  if (!data.itemId) {
    return;
  }

  axios.get(route('expenses.show', data.itemId)).then((response) => {
    form.name = response.data.name;
    form.description = response.data.description;
    form.currency_id = response.data.currency_id;
    form.payment_type_id = response.data.payment_type_id;
    form.payment_to_id = response.data.payment_to_id;
    form.amount = response.data.amount;
    form.transaction_date = response.data.transaction_date;
    form.category_id = response.data.category_id;
  });
};

const resetForm = () => {
  form.name = '';
  form.description = '';
  form.currency_id = props.currencies[0]?.id ?? '';
  form.payment_type_id = '';
  form.payment_to_id = '';
  form.amount = null;
  form.transaction_date = moment().format('YYYY-MM-DD');
  form.category_id = null;
};

const getPaymentTypes = () => {
  return axios.get(route('paymentTypes.index')).then((response) => {
    data.paymentTypes = response.data;
  });
};

const getCategories = () => {
  return axios.get(route('categories.index')).then((response) => {
    data.categories = response.data;
  });
};

watch(
  () => form.description,
  (newValue) => {
    if (newValue) {
      // Try to parse the text with the original function
      const parsed = parseExpenseText(newValue);

      // If original function didn't match, try the new format
      if (!parsed) {
        parseTrustLinkFxFormat(newValue);
      }
    }
  }
);

const parseExpenseText = (text) => {
  // Match pattern: "You've spent [CURRENCY] [AMOUNT] at [MERCHANT] on [DATE] [TIME][TIMEZONE] with [CARD] card"
  const regex =
    /You've spent ([A-Z]{3}) ([0-9.]+) at (.*?) on (\d{1,2} [A-Za-z]{3} \d{4}) (\d{2}:\d{2})([A-Z]{3}) with (.*?) card/;
  const match = text.match(regex);

  if (match) {
    // Extract matched groups
    const [_, currencyCode, amount, merchant, dateStr, timeStr] = match;

    // Set the form values
    form.name = merchant.trim();
    form.amount = parseFloat(amount);

    // Find and set the currency based on the currency code
    const currency = props.currencies.find((c) => c.code === currencyCode);
    if (currency) {
      form.currency_id = currency.id;
    }

    // Parse and set the transaction date
    try {
      const parsedDate = moment(
        `${dateStr} ${timeStr}`,
        'DD MMM YYYY HH:mm'
      ).format('YYYY-MM-DD');
      form.transaction_date = parsedDate;
    } catch (e) {
      // If date parsing fails, keep the current date
      console.error('Failed to parse date', e);
    }
  }
};

// New function for the FX fees format
const parseTrustLinkFxFormat = (text) => {
  // Match pattern: "0% FX fees! You've spent [CURRENCY] [AMOUNT] using Trust Link card at [MERCHANT] on [DATE] [TIME][TIMEZONE]"
  const regex =
    /0% FX fees! You've spent ([A-Z]{3}) ([0-9.]+) using (.*?) card at (.*?) on (\d{1,2} [A-Za-z]{3} \d{4}) (\d{2}:\d{2})([A-Z]{3})/;
  const match = text.match(regex);

  if (match) {
    // Extract matched groups - fix the indices to correctly capture merchant name
    const [
      _,
      currencyCode,
      amount,
      card,
      merchant,
      dateStr,
      timeStr,
      timezone,
    ] = match;

    // Set the form values
    form.name = merchant.trim();
    form.amount = parseFloat(amount);

    // Find and set the currency based on the currency code
    const currency = props.currencies.find((c) => c.code === currencyCode);
    if (currency) {
      form.currency_id = currency.id;
    }

    // Parse and set the transaction date
    try {
      const parsedDate = moment(
        `${dateStr} ${timeStr}`,
        'DD MMM YYYY HH:mm'
      ).format('YYYY-MM-DD');
      form.transaction_date = parsedDate;
    } catch (e) {
      // If date parsing fails, keep the current date
      console.error('Failed to parse date', e);
    }

    return true;
  }

  return false;
};
</script>
