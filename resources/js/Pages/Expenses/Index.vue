<template>
  <AppLayout title="Expenses">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Expenses
      </h2>
    </template>

    <div class="py-2 md:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
          <div class="md:text-right text-center mx-2 my-2">
            <a :href="route('expenses.export')">
              <SecondaryButton class="mr-2" :class="{ 'opacity-25': itemForm.processing }">
                Export
              </SecondaryButton>
            </a>
            <PrimaryButton @click="editItem(0)" :class="{ 'opacity-25': itemForm.processing }"
              :disabled="itemForm.processing">
              New Expense
            </PrimaryButton>
            <DangerButton class="ml-2" @click="deleteSelectedItems" :class="{ 'opacity-25': itemForm.processing }"
              v-if="data.selectedItems.length > 0"
              :disabled="itemForm.processing">
              Delete Selected
            </DangerButton>
          </div>
          <div class="md:table w-full">
            <div class="md:table-header-group md:display hidden">
              <div class="table-row">
                <div class="table-cell text-center">
                  <input
                    type="checkbox"
                    @click="toggleAll()"
                    :checked="data.selectedItems.length == items.data.length"
                  >
                </div>
                <div
                  class="table-cell text-left border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                </div>
                <div class="table-cell text-left">Date</div>
                <div class="table-cell text-left">Name</div>
                <div class="table-cell text-center px-5">Currency</div>
                <div class="table-cell text-right px-5">Amount</div>
                <div class="table-cell px-5">Category</div>
                <div class="table-cell px-5">Payment</div>
              </div>
            </div>
            <div class="md:table-row-group">
              <div v-for="item, i in props.items.data" :key="item.id"
                class="md:table-row odd:bg-white even:bg-gray-200 border py-2 my-2">
                <div class="md:table-cell md:text-center hidden md:visible text-center">
                  <input
                    type="checkbox"
                    class="mx-1"
                    @click="toggleSelection(item.id)"
                    :checked="data.selectedItems.includes(item.id)"
                  >
                </div>
                <div class="md:table-cell md:text-center hidden md:visible">
                  {{ i + 1 + ((props.items.current_page - 1) * props.items.per_page) }}.
                </div>
                <div class="md:table-cell text-center md:text-left">
                  {{ moment(item.transaction_date).format("DD MMM YYYY") }}
                </div>
                <div class="md:table-cell text-center md:text-left">
                  {{ item.name }}
                </div>
                <div class="md:table-cell hidden md:visible text-center">
                  {{ item.currency.code }}
                </div>
                <div class="md:table-cell text-center md:text-left md:px-5">
                  <span class="md:hidden">{{ item.currency.symbol }}</span>
                  {{
                    item.amount.toLocaleString('en-US', {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2
                    })
                  }}
                </div>
                <div class="md:table-cell hidden md:visible px-5">
                  {{ item.category?.name }}
                </div>
                <div class="md:table-cell hidden md:visible px-5">
                  {{ item.payment_type.name }}
                </div>
                <div class="md:table-cell text-center md:text-right">
                  <SecondaryButton class="mx-2 my-1" @click="editItem(item.id)"
                    :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing">
                    Edit
                  </SecondaryButton>
                  <DangerButton class="mx-2 my-1" @click="deleteItem(item.id)"
                    :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing">
                    Delete
                  </DangerButton>
                </div>
              </div>
            </div>
          </div>
          <div class="flex justify-center gap-1 text-gray-600 mt-4">
            <NavLink :key="key" v-for="(link, key) in items.links" :href="link.url" :active="link.active">
              <span v-html="link.label"></span>
            </NavLink>
          </div>
        </div>
      </div>
    </div>
    <DialogModal :show="editingItem" @close="editingItem = false">
      <template #title>
        {{ editingItemText }} Item
      </template>

      <template #content>
        <div class="grid gap-6" @keyup.enter="saveItem">
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Name" />
            <TextInput id="name" v-model="itemForm.name" type="text" class="mt-1 block w-full" autofocus
              ref="nameInput" />
            <InputError :message="itemForm.errors.name" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="amount" value="Amount" />
            <input
              class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
              v-model="itemForm.amount" type="number" id="amount" />
            <InputError :message="itemForm.errors.amount" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="category_id" value="Category" />
            <select id="category_id" v-model="itemForm.category_id"
              class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
              <option :key="category.id" :value="category.id" v-for="category in props.categories">{{
                category.name
              }}</option>
            </select>
            <InputError :message="itemForm.errors.category_id" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="transaction_date" value="Date" />
            <input
              class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
              v-model="itemForm.transaction_date" type="date" id="transaction_date" />
            <InputError :message="itemForm.errors.transaction_date" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="currency_id" value="Currency" />
            <select id="currency_id" v-model="itemForm.currency_id"
              class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
              <option :key="currency.id" :value="currency.id" v-for="currency in props.currencies">{{
                currency.name
              }}</option>
            </select>
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="payment_type_id" value="Payment Type" />
            <select id="payment_type_id" v-model="itemForm.payment_type_id"
              class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
              <option :key="paymentType.id" :value="paymentType.id" v-for="paymentType in props.paymentTypes">{{
                paymentType.name }}</option>
            </select>
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="description" value="Notes" />
            <textarea name="description" v-model="itemForm.description" id="description"
              class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
              cols="30" rows="5"></textarea>
            <InputError :message="itemForm.errors.description" class="mt-2" />
          </div>
        </div>
      </template>

      <template #footer>
        <SecondaryButton @click="editingItem = false">
          Nevermind
        </SecondaryButton>

        <PrimaryButton class="ml-2" @click="saveItem" :class="{ 'opacity-25': itemForm.processing }"
          :disabled="itemForm.processing">
          Save
        </PrimaryButton>
      </template>
    </DialogModal>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import moment from "moment";
import NavLink from '@/Components/NavLink.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm } from '@inertiajs/inertia-vue3';
import InputLabel from '@/Components/InputLabel.vue';
import { nextTick, reactive, ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  items: Object,
  currencies: Array,
  paymentTypes: Array,
  categories: Array,
});

const data = reactive({
  itemId: 0,
  selectedItems: [],
});

const itemForm = useForm({
  name: '',
  description: '',
  currency_id: '',
  payment_type_id: '',
  amount: null,
  transaction_date: null,
  category_id: null,
});

const editingItem = ref(false);
const editingItemText = ref('Edit');
const nameInput = ref(null);

const editItem = (id) => {
  data.itemId = id;

  itemForm.clearErrors();

  if (id == 0) {
    resetItem();
  } else {
    getItem(id);
  }

  editingItemText.value = id == 0 ? 'New' : 'Edit'
  editingItem.value = true;
  nextTick(() => { nameInput.value.focus() });
};

const exportItems = () => {
  return axios.get(route('expenses.export'));
}

const resetItem = () => {
  itemForm.name = '';
  itemForm.description = '';
  itemForm.currency_id = props.currencies[0]?.id;
  itemForm.payment_type_id = props.paymentTypes[0]?.id;
  itemForm.amount = null;
  itemForm.category_id = '';
  itemForm.transaction_date = moment().format("YYYY-MM-DD");
}

const getItem = (id) => {
  return axios.get(route('expenses.show', id)).then(response => {
    itemForm.name = response.data.name;
    itemForm.description = response.data.description;
    itemForm.currency_id = response.data.currency_id;
    itemForm.payment_type_id = response.data.payment_type_id;
    itemForm.amount = response.data.amount;
    itemForm.transaction_date = response.data.transaction_date;
    itemForm.category_id = response.data.category_id;
  });
}

const saveItem = () => {
  if (data.itemId == 0) {
    itemForm.post(route('expenses.store'), {
      onSuccess: () => editingItem.value = false,
    });
  } else {
    itemForm.patch(route('expenses.update', data.itemId), {
      onSuccess: () => editingItem.value = false,
    });
  }
};

const deleteItem = (id) => {
  clearSelectedItems();
  Inertia.delete(route('expenses.destroy', id), {
    preserveScroll: true,
    onBefore: () => confirm('Are you sure you want to delete this item?'),
  });
};

const deleteSelectedItems = () => {
  Inertia.post(route('expenses.destroyMany'), {
      items: data.selectedItems,
      _method: 'DELETE',
    }, {
    preserveScroll: true,
    onBefore: () => confirm('Are you sure you want to delete the selected item(s)?'),
    onSuccess: clearSelectedItems(),
  });
};

const toggleSelection = (selectedId) => {
	if (data.selectedItems.indexOf(selectedId) === -1) {
		data.selectedItems.push(selectedId);
	} else {
    data.selectedItems = data.selectedItems.filter(id => id !== selectedId);
	}
}

const toggleAll = () => {
  if (data.selectedItems.length == props.items.data.length) {
    clearSelectedItems();
  } else {
    data.selectedItems = props.items.data.map(item => item.id);
  }
}

const clearSelectedItems = () => {
  data.selectedItems = [];
}
</script>
