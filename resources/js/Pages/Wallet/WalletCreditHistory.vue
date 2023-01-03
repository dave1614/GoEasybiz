<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head title="Wallet Credit History" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiClipboardTextClock" title="Wallet Credit History" main>
        <BaseButton :href="route('credit_user_wallet')" :icon="mdiWalletPlus" label="Credit Your Wallet" color="success"
          rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->



      <CardBox isForm @submit.prevent="submitFilterForm" class="">
        <div class="sm:grid sm:grid-cols-12 sm:gap-6">
          <FormField class="sm:col-span-4" label="Length">
            <FormControl v-model="form.length" :options="lengthOptions" />
          </FormField>
          <FormField class="sm:col-span-4" label="Amount">
            <FormControl v-model="form.amount" type="number" />
          </FormField>
          <FormField class="sm:col-span-4" label="Payment Option">
            <FormControl v-model="form.payment_option" />
          </FormField>

          <FormField class="sm:col-span-4" label="Reference">
            <FormControl v-model="form.reference" />
          </FormField>
          <FormField class="sm:col-span-4" label="Date">
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

        <div v-if="history.data.length > 0" class="">
          <table>
            <thead>
              <tr>

                <th>#</th>
                <th>Amount</th>
                <th>Payment Option</th>
                <th>Reference</th>
                <th>Date / Time</th>

              </tr>
            </thead>
            <tbody>
              <tr v-for="(row,index) in history.data" :key="row.id">

                <td v-html="`${(index + 1) +((history.current_page - 1) * form.length)}.`"></td>
                <td data-label="Amount" v-html="mainStore.addCommas((row.amount - 0).toFixed(2))">

                </td>
                <td class="capitalize" data-label="Payment Option">
                  {{ row.payment_option }}
                </td>
                <td data-label="Reference">
                  {{ row.reference }}
                </td>
                <td data-label="Date / Time" v-html="`${row.date} ${row.time}`">

                </td>

              </tr>
            </tbody>
          </table>
          <div class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
            <BaseLevel>
              <BaseButtons>
                <BaseButton v-for="page in history.links" :key="page" :active="page.active" :label="page.label"
                  :color="page.active ? 'lightDark' : 'whiteDark'" small @click="currentPage = page"
                  :href="page.url != null ? page.url : ''" :disabled="page.url === null" />
              </BaseButtons>
              <small>Page {{ history.current_page }} of {{ history.last_page }}</small>
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
  mdiClipboardTextClock,
  mdiAccountCog,
  mdiAccountCash,
  mdiWalletPlus,
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
import CardBoxModal from "@/components/CardBoxModal.vue";
import FloatingActionButton from "@/components/FloatingActionButton.vue";
import FormLoaderDark from '@/Loaders/form_loader_dark.gif'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'

import { useMainStore } from "@/stores/main";
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { computed, ref, reactive, watch } from 'vue'
import axios from "axios";
import _ from 'lodash';

const page = usePage();
const mainStore = useMainStore();

const btn_hovered = ref(false);

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const user = props.value?.user;
const history = page.props.value?.history;

const useSearchBtn = mainStore.useSearchBtn;
console.log(history)

const lengthOptions = ref([
  10,
  20,
  50,
  100
]);




const form = useForm({
  length: props.value?.length,
  amount: props.value?.amount,
  payment_option: props.value?.payment_option,
  reference: props.value?.reference,
  date: props.value?.date,

  start_date: props.value?.start_date,
  end_date: props.value?.end_date,

})


const clearFilterForm = () => {
  form.length = 10
  form.amount = null
  form.payment_option = null
  form.reference = null
  form.date = null
  form.start_date = null
  form.end_date = null



  // console.log(form)
}



const submitFilterForm = () => {
  // console.log('test')
  let query = _.pickBy(form);
  let queryRoute = route('wallet_credit_history');
  let params = Object.keys(query).length ? query : {
    remember: 'forget'
  }
  // console.log(queryRoute)


  Inertia.get(queryRoute, params, {
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

