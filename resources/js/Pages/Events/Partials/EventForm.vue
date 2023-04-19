<template>
    <DialogModal :show="isOpen" @close="close">
        <template #title>
            {{ pageMode }} Event
        </template>
        <template #content>
            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6">
                    <InputLabel for="title" value="Title" />
                    <input
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        v-model="form.title"
                        type="text"
                        id="title"
                        :readonly="props.isReadOnly"
                        ref="focusInput"
                    />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <div class="col-span-6">
                    <InputLabel for="description" value="Description" />
                    <textarea
                        name="description"
                        v-model="form.description"
                        id="description"
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        cols="30"
                        rows="5"
                        :readonly="props.isReadOnly"
                    ></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <div class="md:col-span-2 col-span-6">
                    <InputLabel for="event_date" value="Event Date" />
                    <input
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        v-model="form.event_date"
                        type="date"
                        id="event_date"
                        :readonly="props.isReadOnly"
                    />
                    <InputError :message="form.errors.event_date" class="mt-2" />
                </div>

                <div class="md:col-span-2 col-span-6">
                    <InputLabel for="action_date" value="Action Date" />
                    <input
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        v-model="form.action_date"
                        type="date"
                        id="action_date"
                        :readonly="props.isReadOnly"
                    />
                    <InputError :message="form.errors.action_date" class="mt-2" />
                </div>

                <div class="md:col-span-2 col-span-6">
                  <div v-if="props.itemId == ''">
                    <InputLabel for="frequency" value="Frequency" />
                    <input
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        v-model="form.frequency"
                        type="number"
                        id="frequency"
                        :readonly="props.isReadOnly"
                    />
                    <InputError :message="form.errors.frequency" class="mt-2" />
                  </div>
                </div>

                <div class="md:col-span-2 col-span-6">
                    <InputLabel for="status" value="Status" />
                    <template v-if="props.isReadOnly">
                        <input
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            :value="form.status ? form.status.charAt(0).toUpperCase() + form.status.slice(1) : ''"
                            type="text"
                            id="status"
                            :readonly="props.isReadOnly"
                        />
                    </template>
                    <template v-else>
                        <select
                            id="status"
                            v-model="form.status"
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            :readonly="props.isReadOnly"
                        >
                            <option :key="i" :value="statusType" v-for="(statusType, i) in statusTypes">{{ statusType.charAt(0).toUpperCase() + statusType.slice(1) }}</option>
                        </select>
                    </template>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>

                <div class="md:col-span-2 col-span-6">
                    <InputLabel for="intervals" value="Next In (months)" />
                    <input
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        v-model="form.intervals"
                        type="text"
                        id="intervals"
                        :readonly="props.isReadOnly"
                    />
                    <InputError :message="form.errors.intervals" class="mt-2" />
                </div>

                <div class="md:col-span-2 col-span-6">
                    <InputLabel for="reminder" value="Reminder (Days)" />
                    <input
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        v-model="form.reminder"
                        type="text"
                        id="reminder"
                        :readonly="props.isReadOnly"
                    />
                    <InputError :message="form.errors.reminder" class="mt-2" />
                </div>

                <div class="md:col-span-2 col-span-6">
                    <InputLabel for="is_owed" value="Event Type" />
                    <template v-if="props.isReadOnly">
                        <input
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            :value="form.is_owed === null ? 'Schedule' : form.is_owed == 1 ? 'Expense' : 'Income'"
                            type="text"
                            id="is_owed"
                            :readonly="props.isReadOnly"
                        />
                    </template>
                    <template v-else>
                        <select
                            id="is_owed"
                            v-model="form.is_owed"
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                            :readonly="props.isReadOnly"
                        >
                            <option :value="null">Schedule</option>
                            <option value="1">Expense</option>
                            <option value="0">Income</option>
                        </select>
                    </template>
                    <InputError :message="form.errors.is_owed" class="mt-2" />
                </div>

                <div class="md:col-span-2 col-span-6" v-if="form.is_owed !== null">
                    <InputLabel for="currency_id" value="Currency" />
                    <select id="currency_id" v-model="form.currency_id"
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full">
                        <option :key="currency.id" :value="currency.id" v-for="currency in props.currencies">{{
                            currency.name
                        }}</option>
                    </select>
                </div>

                <div class="md:col-span-2 col-span-6" v-if="form.is_owed !== null">
                    <InputLabel for="amount" value="Amount" />
                    <input
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        v-model="form.amount"
                        type="text"
                        id="amount"
                        :readonly="props.isReadOnly"
                    />
                    <InputError :message="form.errors.amount" class="mt-2" />
                </div>
            </div>
        </template>

        <template #footer>
            <template v-if="props.isReadOnly">
                <PrimaryButton class="ml-2" @click="markComplete()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Mark as Completed
                </PrimaryButton>

                <PrimaryButton class="ml-2" @click="markCompleteCreateNext()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Mark as Completed & Create Next
                </PrimaryButton>

                <PrimaryButton class="ml-2" @click="toReadOnly(false)" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Edit
                </PrimaryButton>
            </template>
            <template v-else>
                <SecondaryButton @click="close">
                    Nevermind
                </SecondaryButton>

                <PrimaryButton class="ml-2" @click="saveForm" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Save
                </PrimaryButton>
            </template>
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
import { nextTick, computed, ref, watch } from 'vue';
import moment from "moment";

const emit = defineEmits(['close', 'toReadOnly']);

const focusInput = ref(null);

const props = defineProps({
    isOpen: Boolean,
    itemId: String,
    isReadOnly: Boolean,
    currencies: Array,
})

const close = () => {
    emit('close');
};

const toReadOnly = (value) => {
    emit('toReadOnly', value);
}

const pageMode = computed(() => props.itemId == '' ? 'Add' : 'Edit');

const statusTypes = [
    '',
    'active',
    'completed',
    'cancelled',
];

const form = useForm({
    title: '',
    description: '',
    intervals: 12,
    reminder: 60,
    event_date: null,
    is_private: true,
    action_date: null,
    status: 'active',
    intervals: 12,
    frequency: 1,
    reminder: 60,
    next: null,
    currency_id: null,
    amount: null,
    is_owed: null,
});

const saveForm = () => {
    if (form.is_owed === null) {
        form.amount = null;
        form.currency_id = null;
    }

    if (props.itemId == '') {
        form.post(route('events.store'), {
            onBefore: () => {
              if (form.frequency > 1) {
                confirm('Are you sure you want to create multiple events based on the frequency?')
              }
            },
            onSuccess: () => close(),
        });
    } else {
        form.patch(route('events.update', props.itemId), {
            onSuccess: () => close(),
        })
    }
}

const markComplete = () => {
    if (confirm(`Are you sure you mark this as completed?`)) {
        form.action_date = moment().format('YYYY-MM-DD HH:mm:ss');
        form.status = 'completed';
        form.next = null;
        saveForm();
    }
}

const markCompleteCreateNext = () => {
    if (confirm(`Mark this as complete and create the next event?`)) {
        form.action_date = moment().format('YYYY-MM-DD HH:mm:ss');
        form.status = 'completed';
        form.next = true;
        saveForm();
    }
}

watch(() => props.itemId, () => {
    fetchData();
});

watch(() => props.isOpen, () => {
    if (props.isOpen) {
        nextTick(() => {focusInput.value.focus()});
    }
});

const fetchData = () => {
    form.clearErrors();
    resetForm();

    if (!props.itemId) {
        return;
    }

    axios.get(route('events.show', props.itemId)).then(response => {
        form.title = response.data.title;
        form.description = response.data.description;
        form.intervals = response.data.intervals;
        form.event_date = moment(response.data.event_date).format('YYYY-MM-DD');
        form.action_date = response.data.action_date ? moment(response.data.action_date).format('YYYY-MM-DD') : null;
        form.status = response.data.status;
        form.intervals = response.data.intervals;
        form.reminder = response.data.reminder;
        form.next = null;
        form.is_owed = response.data.is_owed;
        form.currency_id = response.data.currency_id;
        form.amount = response.data.amount;
    });
}

const resetForm = () => {
    form.title = '';
    form.description = '';
    form.intervals = 12;
    form.event_date = moment().format('YYYY-MM-DD');
    form.action_date = null;
    form.status = 'active';
    form.intervals = 12;
    form.reminder = 60;
    form.next = null;
    form.is_owed = null;
    form.currency_id = null;
    form.amount = null;
}

</script>
