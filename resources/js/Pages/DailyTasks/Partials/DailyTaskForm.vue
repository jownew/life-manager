<template>
  <DialogModal :show="isOpen" @close="close">
    <template #title>
      {{ pageMode }} Task
    </template>
    <template #content>
      <div class="grid gap-6" @keyup.enter="saveItem">
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="title" value="Name" />
          <input id="title"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            v-model="form.title" type="text" autofocus ref="focusInput" />
          <InputError :message="form.errors.title" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="description" value="Description" />
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

const emit = defineEmits(['close']);

const focusInput = ref(null);

const props = defineProps({
  isOpen: Boolean,
  itemId: String,
})

const close = () => {
  emit('close');
};

const pageMode = computed(() => props.itemId == '' ? 'Add' : 'Edit');

const form = useForm({
  title: '',
  description: '',
});

const saveForm = () => {
  if (form.is_owed === null) {
    form.amount = null;
    form.currency_id = null;
  }

  if (props.itemId == '') {
    form.post(route('daily-tasks.store'), {
      onSuccess: () => close(),
    });
  } else {
    form.patch(route('daily-tasks.update', props.itemId), {
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
  resetForm();

  if (!props.itemId) {
    return;
  }

  axios.get(route('daily-tasks.show', props.itemId)).then(response => {
    form.title = response.data.title;
    form.description = response.data.description;
  });
}

const resetForm = () => {
  form.clearErrors();
  form.reset();
  form.title = '';
  form.description = '';
}
</script>
