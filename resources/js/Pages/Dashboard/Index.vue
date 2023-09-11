<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-2 md:py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-5 md:mx-10 w-full md:w-1/2 text-center">
            <div class="grid grid-cols-3 justify-items-center items-center">
            <Link :href="route('dashboard.index', props.previousMonth)">
            <SecondaryButton class="mr-5">
              <Icon icon="carbon:caret-left" class="w-5 h-5" />
              {{ moment(props.previousMonth).format("MMM") }}
            </SecondaryButton>
            </Link>
              {{ moment(props.date).format("MMM YYYY") }}
            <Link :href="route('dashboard.index', props.nextMonth)">
            <SecondaryButton class="ml-5">
              {{ moment(props.nextMonth).format("MMM") }}
              <Icon icon="carbon:caret-right" class="w-5 h-5" />
            </SecondaryButton>
            </Link>
          </div>
          </div>
          <div class="p-5 md:mx-10 md:mb-10 border w-full md:w-1/2">
            <div class="table w-full">
              <div class="table-header-group">
                <div class="table-row">
                  <div class="table-cell text-left pb-3">Category</div>
                  <div class="table-cell text-right">Budget</div>
                  <div class="table-cell text-right">Amount</div>
                </div>
              </div>
              <div class="table-row-group">
                <div class="table-row" v-for="category in categories">
                  <div class="table-cell text-left pb-2">{{ category.name }}</div>
                  <div class="table-cell text-right">
                    {{
                      category.budget.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                      })
                    }}
                  </div>
                  <div class="table-cell text-right">
                    {{
                      subTotals.find(st => st.category == category.name).total.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                      })
                    }}
                  </div>
                </div>
                <div class="table-row">
                  <div class="table-cell text-left">Total</div>
                  <div class="table-cell text-right">
                    {{
                      budgetTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                      })
                    }}
                  </div>
                  <div class="table-cell text-right">
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
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import moment from "moment";
import { Link } from '@inertiajs/inertia-vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Icon } from '@iconify/vue';

const props = defineProps({
  categories: Array,
  expenses: Array,
  date: String,
  previousMonth: String,
  nextMonth: String,
});

const sum = props.expenses.reduce((accumulator, currentValue) => {
  return accumulator + (+currentValue.amount);
}, 0);

const budgetTotal = props.categories.reduce((accumulator, currentValue) => {
  return accumulator + (+currentValue.budget);
}, 0);

const output = props.categories.map(category => category.name);

const subTotal = (category) => {
  return props.expenses.filter(expense => expense.category?.name == category).reduce((accumulator, currentValue) => {
    return accumulator + (+currentValue.amount);
  }, 0);
}

const subTotals = output.map(name => {
  return {
    category: name,
    total: subTotal(name),
  }
});

</script>
