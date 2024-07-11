<template>
  <AppLayout title="To Do Tasks">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        To Do Tasks
      </h2>
    </template>

    <div class="py-2 md:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
          <div class="grid grid-cols-1 md:grid-cols-2 pb-5 items-center">
            <SearchFilter v-model="data.form.search" class="mr-4 w-full max-w-md pb-5 md:pb-0" @reset="reset"
              placeholder="Search Task">
              <label class="block text-gray-700">Trashed:</label>
              <select v-model="data.form.trashed" class="form-select mt-1 w-full">
                <option :value="null" />
                <option value="with">With Trashed</option>
                <option value="only">Only Trashed</option>
              </select>
            </SearchFilter>
            <div class="text-center md:text-right">
              <label class="pr-5">
                <Checkbox :checked="data.form.status == 'all'" @click="toggleShowAll()" />
                <span class="ml-2 text-sm text-gray-600">Show All</span>
              </label>
              <PrimaryButton class="my-1" @click="editItem()" :class="{ 'opacity-25': itemForm.processing }"
                :disabled="itemForm.processing">
                New Task
              </PrimaryButton>
              <DangerButton class="ml-2" @click="deleteSelectedItems" :class="{ 'opacity-25': itemForm.processing }"
                v-if="data.selectedItems.length > 0" :disabled="itemForm.processing">
                Delete Selected
              </DangerButton>
            </div>
          </div>
          <div class="md:table w-full">
            <div class="md:table-header-group md:display hidden">
              <div class="table-row">
                <div class="table-cell text-center">
                  <input type="checkbox" @click="toggleAll()" :checked="data.selectedItems.length == items.data.length">
                </div>
                <div
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-right">
                </div>
                <div
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-left">
                  Title
                </div>
                <div
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-left">
                  Due
                </div>
                <div
                  class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">
                </div>
              </div>
            </div>
            <div class="md:table-row-group">
              <div v-for="item, i in items.data" :key="item.id"
                class="md:table-row odd:bg-white even:bg-gray-200 border py-2 my-2">
                <div class="md:table-cell md:text-center hidden md:visible text-center">
                  <input type="checkbox" class="mx-1" @click="toggleSelection(item.id)"
                    :checked="data.selectedItems.includes(item.id)">
                </div>
                <div class="md:table-cell md:text-center hidden md:visible">
                  {{ i + 1 + ((props.items.current_page - 1) * props.items.per_page) }}.
                </div>
                <div class="md:table-cell md:text-left text-center">
                  {{ item.title }}
                </div>
                <div class="md:table-cell md:text-left text-center" :title="'Created: ' + formatDate(item.created_at)">
                  {{ formatDate(item.planned_time) }}
                </div>
                <div class="md:table-cell hidden md:visible text-right">
                  <SecondaryButton class="mx-1 my-1" @click="changeStatus(item.id, item.completed_time)"
                    :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing">
                    <Icon v-if="item.completed_time != null" icon="carbon:checkbox-checked"
                      class="w-5 h-5 text-green-600" />
                    <Icon v-else icon="carbon:checkbox" class="w-5 h-5" />
                  </SecondaryButton>
                  <SecondaryButton class="mx-1 my-1" @click="snooze(item.id)"
                    :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing">
                    <Icon icon="carbon:snooze" class="w-5 h-5" />
                  </SecondaryButton>
                  <SecondaryButton class="mx-1 my-1" @click="editItem(item.id)"
                    :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing">
                    <Icon icon="carbon:edit" class="w-5 h-5" />
                  </SecondaryButton>
                  <DangerButton class="mx-1 my-1" @click="deleteItem(item.id)"
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
    <DailyTaskForm :isOpen="data.isEditModalOpen" :itemId="data.itemId" @close="closeModal" />
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
import { reactive, watch } from 'vue';
import DailyTaskForm from './Partials/DailyTaskForm.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Icon } from '@iconify/vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import pickBy from 'lodash/pickBy';
import debounce from 'lodash/debounce';

const props = defineProps({
  items: Object,
  filters: Object,
});

const data = reactive({
  itemId: null,
  isEditModalOpen: false,
  isReadOnly: false,
  selectedItems: [],
  form: {
    search: props.filters.search,
    trashed: props.filters.trashed,
    status: props.filters.status,
  },
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

const changeStatus = (id, completedDate = null) => {
  var status = completedDate == null ? 'done' : 'not yet done';
  Inertia.post(route('daily-tasks.toggle', id), {}, {
    preserveScroll: true,
    onBefore: () => confirm(`Mark task as ${status}?`)
  });
};

const snooze = (id) => {
  Inertia.post(route('daily-tasks.snooze', id), {}, {
    preserveScroll: true,
    onBefore: () => confirm(`Do later?`)
  });
};

const deleteItem = (id) => {
  clearSelectedItems();
  Inertia.delete(route('daily-tasks.destroy', id), {
    preserveScroll: true,
    onBefore: () => confirm('Are you sure you want to delete this item?')
  });
};

const closeModal = () => {
  data.isEditModalOpen = false;
  data.isReadOnly = false;
}

const toReadOnly = (value) => {
  data.isReadOnly = value;
}

const deleteSelectedItems = () => {
  Inertia.post(route('daily-tasks.destroy-many'), {
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

const reset = () => {
  data.form.search = '';
}

const getItems = debounce(() => {
  Inertia.get(route('daily-tasks.index'), pickBy(data.form), { preserveState: true });
}, 500);

const toggleShowAll = () => {
  if (data.form.status == 'all') {
    data.form.status = '';
  } else {
    data.form.status = 'all';
  }

  getItems();
}

watch(data.form, () => {
  getItems();
});

const formatDate = (date) => {
  return moment(date).format('DD MMM YYYY');
}
</script>
