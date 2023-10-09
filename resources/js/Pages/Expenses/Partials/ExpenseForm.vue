<template>
  <DialogModal :show="isOpen" @close="close">
    <template #title>
      {{ pageMode }} Expense
    </template>
    <template #content>
      <div class="grid gap-6" @keyup.enter="saveItem">
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="name" value="Name" />
          <input id="name"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            v-model="form.name" type="text" autofocus ref="focusInput" />
          <InputError :message="form.errors.name" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="amount" value="Amount" />
          <input
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            v-model="form.amount" type="number" id="amount" />
          <InputError :message="form.errors.amount" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="category_id" value="Category" />
          <select id="category_id" v-model="form.category_id"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
            <option :key="category.id" :value="category.id" v-for="category in data.categories">{{
              category.name
            }}</option>
          </select>
          <InputError :message="form.errors.category_id" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="transaction_date" value="Date" />
          <input
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            v-model="form.transaction_date" type="date" id="transaction_date" />
          <InputError :message="form.errors.transaction_date" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="currency_id" value="Currency" />
          <select id="currency_id" v-model="form.currency_id"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
            <option :key="currency.id" :value="currency.id" v-for="currency in props.currencies">{{
              currency.name
            }}</option>
          </select>
          <InputError :message="form.errors.currency_id" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="payment_type_id" value="Payment From" />
          <select id="payment_type_id" v-model="form.payment_type_id"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
            <option :key="paymentType.id" :value="paymentType.id" v-for="paymentType in data.paymentTypes">{{
              paymentType.name }}</option>
          </select>
          <InputError :message="form.errors.payment_type_id" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="payment_to_id" value="Payment To" />
          <select id="payment_to_id" v-model="form.payment_to_id"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
            <option></option>
            <option :key="paymentType.id" :value="paymentType.id" v-for="paymentType in data.paymentTypes">{{
              paymentType.name }}</option>
          </select>
          <InputError :message="form.errors.payment_to_id" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="description" value="Notes" />
          <textarea name="description" v-model="form.description" id="description"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            cols="30" rows="5"></textarea>
          <InputError :message="form.errors.description" class="mt-2" />
        </div>
      </div>
    </template>

    <template #footer>
      <SecondaryButton @click="close">
        Nevermind
      </SecondaryButton>

      <PrimaryButton class="ml-2" @click="saveForm" :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing">
        Save
      </PrimaryButton>
    </template>
  </DialogModal>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { nextTick, computed, ref, watch, reactive } from 'vue';
import moment from "moment";

const emit = defineEmits(['close']);

const focusInput = ref(null);

const props = defineProps({
  isOpen: Boolean,
  itemId: String,
  currencies: Array,
})

const data = reactive({
  categories: [],
  paymentTypes: [],
});

const close = () => {
  emit('close');
};

const pageMode = computed(() => props.itemId == '' ? 'Add' : 'Edit');

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

  if (props.itemId == '') {
    form.post(route('expenses.store'), {
      onSuccess: () => close(),
    });
  } else {
    form.patch(route('expenses.update', props.itemId), {
      onSuccess: () => close(),
    })
  }
}

watch(() => props.isOpen, () => {
  if (props.isOpen) {
    fetchData();
    nextTick(() => { focusInput.value.focus() });
  }
});

const fetchData = () => {
  getPaymentTypes();
  getCategories();

  form.clearErrors();
  resetForm();

  if (!props.itemId) {
    return;
  }

  axios.get(route('expenses.show', props.itemId)).then(response => {
    form.name = response.data.name;
    form.description = response.data.description;
    form.currency_id = response.data.currency_id;
    form.payment_type_id = response.data.payment_type_id;
    form.payment_to_id = response.data.payment_to_id;
    form.amount = response.data.amount;
    form.transaction_date = response.data.transaction_date;
    form.category_id = response.data.category_id;
  });
}

const resetForm = () => {
  form.name = '';
  form.description = '';
  form.currency_id = props.currencies[0]?.id ?? '';
  form.payment_type_id = '';
  form.payment_to_id = '';
  form.amount = null;
  form.transaction_date = moment().format('YYYY-MM-DD');
  form.category_id = null;
}

const getPaymentTypes = () => {
  return axios.get(route('paymentTypes.index')).then(response => {
    data.paymentTypes = response.data;
  });
}

const getCategories = () => {
  return axios.get(route('categories.index')).then(response => {
    data.categories = response.data;
  });
}

</script>
