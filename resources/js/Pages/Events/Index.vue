<template>
  <AppLayout title="Events">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Events
      </h2>
    </template>

    <div class="py-2 md:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
          <div class="md:block hidden float-left">
              <label class="flex items-center">
                <Checkbox :checked="props.showAll" @click="getItems()" />
                <span class="ml-2 text-sm text-gray-600">Show All</span>
              </label>
            </div>
          <div class="md:text-right text-center mx-2 my-2">
            <PrimaryButton class="my-1" @click="editItem()" :class="{ 'opacity-25': itemForm.processing }"
              :disabled="itemForm.processing">
              New Event
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
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-right">
                </div>
                <div
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-left">
                  Title</div>
                <div
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">
                  Days Left</div>
                <div
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">
                  Date</div>
                <div
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">
                  Status</div>
                <div
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">
                </div>
              </div>
            </div>
            <div class="md:table-row-group">
              <div v-for="item, i in items.data" :key="item.id"
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
                <div class="md:table-cell md:text-left text-center">
                  {{ item.title }}
                  <span class="bg-green-100 text-green-800 px-2.5 py-0.5 rounded-lg text-xs"
                    v-if="item.is_owed === 0">I</span>
                  <span class="bg-red-100 text-red-800 px-2.5 py-0.5 rounded-lg text-xs"
                    v-if="item.is_owed === 1">E</span>
                </div>
                <div class="md:table-cell text-center">
                  {{ item.days }}
                </div>
                <div class="md:table-cell text-center">
                  {{ moment(item.event_date).format("DD MMM YYYY") }}
                </div>
                <div class="md:table-cell hidden md:visible text-center">
                  {{ item.status }}
                </div>
                <div class="md:table-cell hidden md:visible text-right">

                  <SecondaryButton class="mx-2 my-1" @click="changeStatus(item.id, 'active')" v-if="item.status == 'completed'"
                    :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing">
                    <Icon icon="carbon:checkbox-checked" class="w-5 h-5" />
                  </SecondaryButton>
                  <SecondaryButton class="mx-2 my-1" @click="changeStatus(item.id, 'completed')" v-else
                    :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing">
                    <Icon icon="carbon:checkbox" class="w-5 h-5" />
                  </SecondaryButton>
                  <SecondaryButton class="mx-2 my-1" @click="editItem(item.id)"
                    :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing">
                    <Icon icon="carbon:edit" class="w-5 h-5" />
                  </SecondaryButton>
                  <DangerButton class="mx-2 my-1" @click="deleteItem(item.id)"
                    :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing">
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
    <EventForm :isOpen="data.isEditModalOpen" :isReadOnly="data.isReadOnly" :itemId="data.itemId" :currencies="currencies"
      @close="closeModal" @toReadOnly="toReadOnly" />
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
import { useForm } from '@inertiajs/inertia-vue3';
import { reactive } from 'vue';
import EventForm from './Partials/EventForm.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
  items: Object,
  currencies: Array,
  showAll: Boolean
});

const data = reactive({
  itemId: null,
  isEditModalOpen: false,
  isReadOnly: false,
  selectedItems: [],
});

const itemForm = useForm({
  name: '',
  description: '',
  payment_type_id: '',
  amount: null,
  transaction_date: null,
});

const editItem = (id = '') => {
  data.itemId = id;
  data.isEditModalOpen = true;
}

const changeStatus = (id, newStatus) => {
  Inertia.post(route('events.changeStatus', id), {status: newStatus}, {
    preserveScroll: true,
    onBefore: () => confirm(`Set this item to ${newStatus}?`)
  });
};

const deleteItem = (id) => {
  clearSelectedItems();
  Inertia.delete(route('events.destroy', id), {
    preserveScroll: true,
    onBefore: () => confirm('Are you sure you want to delete this item?')
  });
};

const getItems = () => {
  if (props.showAll) {
    Inertia.get(route('events.index'));
  } else {
    Inertia.get(route('events.showAll'));
  }
}

const closeModal = () => {
  data.isEditModalOpen = false;
  data.isReadOnly = false;
}

const toReadOnly = (value) => {
  data.isReadOnly = value;
}

const deleteSelectedItems = () => {
  Inertia.post(route('events.destroyMany'), {
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
