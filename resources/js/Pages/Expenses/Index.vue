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
              <SecondaryButton class="mr-2">
                Export
              </SecondaryButton>
            </a>
            <PrimaryButton @click="editItem()">
              New Expense
            </PrimaryButton>
            <DangerButton class="ml-2" @click="deleteSelectedItems"
              v-if="data.selectedItems.length > 0">
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
                  <SecondaryButton class="mx-2 my-1" @click="editItem(item.id)">
                    <Icon icon="carbon:edit" class="w-5 h-5" />
                  </SecondaryButton>
                  <DangerButton class="mx-2 my-1" @click="deleteItem(item.id)">
                    <Icon icon="carbon:trash-can" class="w-5 h-5" />
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
    <ExpenseForm :isOpen="data.isEditModalOpen" :itemId="data.itemId" :currencies="currencies"
      @close="closeModal" />
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import moment from "moment";
import NavLink from '@/Components/NavLink.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Inertia } from '@inertiajs/inertia';
import { reactive } from 'vue';
import { Icon } from '@iconify/vue';
import ExpenseForm from './Partials/ExpenseForm.vue';

const props = defineProps({
  items: Object,
  currencies: Array,
  categories: Array,
});

const data = reactive({
  itemId: '',
  selectedItems: [],
  paymentTypes: [],
});

const editItem = (id = '') => {
  data.itemId = id;
  data.isEditModalOpen = true;
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

const closeModal = () => {
  data.isEditModalOpen = false;
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
