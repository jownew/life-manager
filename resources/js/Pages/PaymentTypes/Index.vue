<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-2 md:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
          <div class="md:text-right text-center mx-2">
            <PrimaryButton class="my-1" @click="editItem(0)" :class="{ 'opacity-25': itemForm.processing }"
              :disabled="itemForm.processing">
              New Payment Type
            </PrimaryButton>
          </div>
          <div class="md:table w-full">
            <div class="md:table-header-group md:display hidden">
              <div class="table-row">
                <div
                  class="table-cell text-left border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                </div>
                <div
                  class="table-cell text-left border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                  Name</div>
                <div
                  class="table-cell text-left border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                </div>
              </div>
            </div>
            <div class="md:table-row-group">
              <div v-for="item, i in items" :key="item.id"
                class="md:table-row odd:bg-white even:bg-gray-200 md:border py-2 my-2">
                <div class="md:table-cell md:text-center hidden md:visible">
                  {{ i + 1 }}.
                </div>
                <div class="md:table-cell text-center md:text-left">
                  {{ item.name }}
                </div>
                <div class="md:table-cell hidden md:visible text-right">
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
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { nextTick, reactive, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
  items: Array,
});

const data = reactive({
  'itemId': '',
});

const editingItem = ref(false);
const editingItemText = ref('Edit');
const nameInput = ref(null);

const itemForm = useForm({
  name: '',
});

const deleteItem = (id) => {
  Inertia.delete(route('paymentTypes.destroy', id), {
    preserveScroll: true,
    onBefore: () => confirm('Are you sure you want to delete this item?')
  });
}

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

const resetItem = () => {
  itemForm.name = "";
}

const getItem = (id) => {
  return axios.get(route('paymentTypes.show', id)).then(response => {
    itemForm.name = response.data.name;
  });
}

const saveItem = () => {
  if (data.itemId == 0) {
    itemForm.post(route('paymentTypes.store'), {
      onSuccess: () => editingItem.value = false,
    });
  } else {
    itemForm.patch(route('paymentTypes.update', data.itemId), {
      onSuccess: () => editingItem.value = false,
    });
  }
};
</script>
