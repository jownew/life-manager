<template>
    <AppLayout title="Events">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Events
            </h2>
        </template>

        <div class="md:py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="md:text-right text-center mx-2">
                        <PrimaryButton class="my-1" @click="editItem()" :class="{ 'opacity-25': itemForm.processing }"
                            :disabled="itemForm.processing">
                            New Event
                        </PrimaryButton>
                    </div>
                    <div class="md:table w-full">
                        <div class="md:table-header-group md:display hidden">
                            <div class="table-row">
                                <div
                                    class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-right">
                                </div>
                                <div class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-left">Title</div>
                                <div class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">Days Left</div>
                                <div class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">Date</div>
                                <div class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">Status</div>
                                <div class="table-cell border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center"></div>
                            </div>
                        </div>
                        <div class="md:table-row-group">
                            <div v-for="item, i in items.data" :key="item.id"
                                class="md:table-row odd:bg-white even:bg-gray-200 border py-2 my-2">
                                <div class="md:table-cell md:text-center hidden md:visible">
                                    {{ i + 1 }}.
                                </div>
                                <div class="md:table-cell hidden md:visible">
                                    {{ item.title }}
                                </div>
                                <div class="md:table-cell hidden md:visible text-center">
                                    {{  item.days }}
                                </div>
                                <div class="md:table-cell hidden md:visible text-center">
                                    {{ moment(item.event_date).format("DD MMM YYYY") }}
                                </div>
                                <div class="md:table-cell hidden md:visible text-center">
                                    {{ item.status }}
                                </div>
                                <div class="md:table-cell hidden md:visible text-right">
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
                        <InputLabel for="title" value="Title" />
                        <TextInput id="title" v-model="itemForm.title" type="text" class="mt-1 block w-full" autofocus ref="nameInput" />
                        <InputError :message="itemForm.errors.title" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="description" value="Description" />
                        <textarea name="description" v-model="itemForm.description" id="description"
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            cols="30" rows="5"></textarea>
                        <InputError :message="itemForm.errors.description" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="amount" value="Amount" />
                        <input
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            v-model="itemForm.amount" type="number" id="amount" />
                        <InputError :message="itemForm.errors.amount" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="transaction_date" value="Date" />
                        <input
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            v-model="itemForm.transaction_date" type="date" id="transaction_date" />
                        <InputError :message="itemForm.errors.transaction_date" class="mt-2" />
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
        <EventForm :isOpen="data.isEditModalOpen" :isReadOnly="data.isReadOnly" :itemId="data.itemId"
            @close="closeModal" @toReadOnly="toReadOnly" />
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
import EventForm from './Partials/EventForm.vue';

const props = defineProps({
    items: Object,
    paymentTypes: Array,
});

const itemForm = useForm({
    name: '',
    description: '',
    payment_type_id: '',
    amount: null,
    transaction_date: null,
});

const data = reactive({
    itemId: null,
    isEditModalOpen: false,
    isReadOnly: false,
});

const editingItem = ref(false);
const editingItemText = ref('Edit');
const nameInput = ref(null);

const editItem = (id = '') => {
    data.itemId = id;
    data.isEditModalOpen = true;
}


const deleteItem = (id) => {
    Inertia.delete(route('events.destroy', id), {
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

</script>
