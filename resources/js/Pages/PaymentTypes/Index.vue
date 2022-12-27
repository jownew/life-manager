<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    items: Array,
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="md:table w-full">
                        <div class="md:table-header-group md:display hidden">
                            <div class="table-row">
                                <div class="table-cell text-left border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400"></div>
                                <div class="table-cell text-left">Name</div>
                                <div class="table-cell text-left"></div>
                            </div>
                        </div>
                        <div class="md:table-row-group">
                            <div
                                v-for="item, i in items"
                                :key="item.id"
                                class="md:table-row odd:bg-white even:bg-gray-200 border py-2 my-2"
                                >
                                <div class="md:table-cell md:text-center hidden md:visible">
                                    {{ i + 1 }}.
                                </div>
                                <div class="md:table-cell hidden md:visible">
                                    {{ item.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>