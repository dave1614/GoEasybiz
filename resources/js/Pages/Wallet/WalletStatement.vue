<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head title="E-Wallet Statement" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiClipboardTextClock" title="E-Wallet Statement" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->

      <!-- <CardBox v-if="useSearchBtn" form @submit.prevent="submitFilterForm" class=""> -->
      <CardBox isForm @submit.prevent="submitFilterForm" class="">
        <div class="sm:grid sm:grid-cols-12 sm:gap-6">
          <FormField class="sm:col-span-4" label="Length: ">
            <FormControl v-model="form.length" :options="lengthOptions" />
          </FormField>
          <FormField class="sm:col-span-4" label="Amount">
            <FormControl v-model="form.amount" type="number" step="any"/>
          </FormField>
          <FormField class="sm:col-span-4" label="Balance">
            <FormControl v-model="form.balance" type="number" step="any"/>
          </FormField>
          <FormField class="sm:col-span-4" label="Summary">
            <FormControl v-model="form.summary" type="textarea" />
          </FormField>
          <FormField class="sm:col-span-4" label="Transaction Date">
            <FormControl v-model="form.date" type="date" />
          </FormField>
          <FormField class="sm:col-span-4" label="Start Date">
            <FormControl v-model="form.start_date" type="date" />
          </FormField>

          <FormField class="sm:col-span-4" label="End Date">
            <FormControl v-model="form.end_date" type="date" />
          </FormField>


        </div>
        <BaseButtons>
          <BaseButton v-if="useSearchBtn" type="submit" color="info" label="Filter" class="px-9 mb-8" />
          <BaseButton @click="clearFilterForm" type="reset" color="info" outline label="Clear" :icon="mdiClose"
            class="px-9 mb-8" />
        </BaseButtons>
        <BaseDivider />
      </CardBox>

      <CardBox class="mb-6" has-table>
        
        <div v-if="wallet_statement.data.length > 0" class="">
          <table>
            <thead>
              <tr>
          
                <th></th>
                <th>Summary</th>
                <th>Amount (₦)</th>
                <th>Date / Time</th>
                <th>Balance</th>
                
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row,index) in wallet_statement.data" :key="row.id">
               
                <!-- <td>{{ row.index }}. </td> -->
                
                <td v-html="`${(index + 1) +((wallet_statement.current_page - 1) * form.length)}.`"></td>
                <td data-label="Summary">
                  {{ row.summary }}
                </td>
                <td :class="row.amount_after > row.amount_before ? 'text-green-500' : 'text-red-500'" data-label="Amount"  v-html="row.amount_after > row.amount_before ? '+' + mainStore.addCommas(row.amount) : '-' + mainStore.addCommas(row.amount)">
          
                </td>
                <td data-label="Date / Time" v-html="`${row.date} ${row.time}`">
          
                </td>
                <td data-label="Balance" v-html="mainStore.addCommas(row.amount_after)">
          
                </td>
                
              </tr>
            </tbody>
          </table>
          <div class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
            <BaseLevel>
              <BaseButtons>
                <BaseButton v-for="page in wallet_statement.links" :key="page" :active="page.active" :label="page.label"
                  :color="page.active ? 'lightDark' : 'whiteDark'" small @click="currentPage = page"
                  :href="page.url != null ? page.url : ''" :disabled="page.url === null" />
              </BaseButtons>
              <small>Page {{ wallet_statement.current_page }} of {{ wallet_statement.last_page }}</small>
            </BaseLevel>
          </div>
        </div>
      </CardBox>


    </SectionMain>
  </LayoutAuthenticated>
</template>
<script setup>
import {
  mdiMonitorCellphone,
  mdiTableBorder,
  mdiTableOff,
  mdiGithub,
  mdiHospitalBuilding,
  mdiClose,
  mdiClipboardTextClock
} from "@mdi/js";

import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import SectionMain from "@/components/SectionMain.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import CardBox from "@/components/CardBox.vue";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import BaseLevel from "@/components/BaseLevel.vue";
import CardBoxComponentEmpty from "@/components/CardBoxComponentEmpty.vue";
import { useMainStore } from "@/stores/main";
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { computed, ref, reactive, watch } from 'vue'
import _ from 'lodash';

const page = usePage();
const mainStore = useMainStore();

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const wallet_statement = props.value?.wallet_statement;
const useSearchBtn = mainStore.useSearchBtn;
console.log(wallet_statement)

const lengthOptions = ref([
  10,
  20,
  50,
  100
]);




const form = useForm({
  length: props.value?.length,
  amount: props.value?.amount,
  balance: props.value?.balance,
  summary: props.value?.summary,
  date: props.value?.date,
  start_date: props.value?.start_date,
  end_date: props.value?.end_date,
  
})
// console.log(form)

const clearFilterForm = () => {
  form.length = 10
  form.amount = null
  form.balance = null
  form.summary = null
  form.date = null
  form.start_date = null
  form.end_date = null
  

  // console.log(form)
}



const submitFilterForm = () => {
  console.log('test')
  let query = _.pickBy(form);
  let queryRoute = route('wallet_statement', Object.keys(query).length ? query : {
    remember: 'forget'
  });


  Inertia.get(queryRoute, {}, {
    // preserveState: true, 
    preserveScroll: true

  });
}

watch(form,
  _.throttle(() => {
    if (useSearchBtn) { return }
    submitFilterForm();
  }, 200), {
  deep: true
}
);


const submit = () => {

};
</script>

