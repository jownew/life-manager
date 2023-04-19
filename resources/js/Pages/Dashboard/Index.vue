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
          <div class="p-5 m-10 border w-1/4">
            <div>Total Expenses for the month</div>
            <div>
              {{
                sum.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  expenses: Array
});

const sum = props.expenses.reduce((accumulator, currentValue) => {
  return accumulator + (+currentValue.amount);
}, 0);

</script>
