<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head :title="`Airtime Combo Requests`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiClipboardTextClock" :title="`Airtime Combo Requests`" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->

      <!-- <CardBox v-if="useSearchBtn" form @submit.prevent="submitFilterForm" class=""> -->
      <CardBox isForm @submit.prevent="submitFilterForm" class="">
        <div class="sm:grid sm:grid-cols-12 sm:gap-6">
          <FormField class="sm:col-span-4" label="Length">
            <FormControl v-model="form.length" :options="lengthOptions" />
          </FormField>

          <FormField class="sm:col-span-4" label="Member's Name">
            <FormControl v-model="form.name" />
          </FormField>
          <FormField class="sm:col-span-4" label="Email">
            <FormControl v-model="form.email" />
          </FormField>

          <FormField class="sm:col-span-4" label="Amount">
            <FormControl v-model="form.amount" type="number" />
          </FormField>
          <FormField class="sm:col-span-4" label="Mobile Number">
            <FormControl v-model="form.number" type="number" />
          </FormField>

          <FormField class="sm:col-span-4" label="Request Date">
            <FormControl v-model="form.date" type="date" />
          </FormField>

          <FormField class="sm:col-span-4" label="Recharge Date">
            <FormControl v-model="form.credited_date" type="date" />
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

        <div v-if="requests.data.length > 0" class="">
          <table>
            <thead class="text-sm">
              <tr>

                <th></th>
                

                <th>Member's Name</th>
                <th>Email</th>
                <th>Amount (₦)</th>
                <th>Mobile Number</th>
                <th>Request Date / Time</th>
                <th>Recharge Date / Time</th>
              </tr>
            </thead>
            <tbody class="">
              <tr v-for="(row,index) in requests.data" :key="row.id">

                <td v-html="`${(index + 1) +((requests.current_page - 1) * form.length)}.`"></td>
               

                <td class="capitalize" data-label="Member's Name">
                  {{ row.name }}
                </td>

                <td class="text-xs text-primary italic" data-label="Email">
                  {{ row.email }}
                </td>

                <td data-label="Amount" v-html="mainStore.addCommas((row.amount - 0).toFixed(2))">

                </td>

                <td class="capitalize" data-label="Mobile Number">
                  {{ row.number }}
                </td>


                <td data-label="Request Date / Time" v-html="`${row.date} ${row.time}`">

                </td>

                <td data-label="Recharge Date / Time" v-html="`${row.credited_date}`">
                
                </td>


              </tr>
            </tbody>
          </table>
          <div class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
            <BaseLevel>
              <BaseButtons>
                <BaseButton v-for="page in requests.links" :key="page" :active="page.active" :label="page.label"
                  :color="page.active ? 'lightDark' : 'whiteDark'" small @click="currentPage = page"
                  :href="page.url != null ? page.url : ''" :disabled="page.url === null" />
              </BaseButtons>
              <small>Page {{ requests.current_page }} of {{ requests.last_page }}</small>
            </BaseLevel>
          </div>
        </div>
      </CardBox>


    </SectionMain>
    <FloatingActionButton :title="'Go Back'"
      @click="Inertia.visit(route('airtime_combo_requests')) ">

      <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
    </FloatingActionButton>
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
  mdiCheckBold,
  mdiCloseThick,
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
import FloatingActionButton from "@/components/FloatingActionButton.vue";
import CardBoxModal from "@/components/CardBoxModal.vue";

import { useMainStore } from "@/stores/main";
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { computed, ref, reactive, watch } from 'vue'
import axios from "axios";
import _ from 'lodash';

const page = usePage();
const mainStore = useMainStore();

const selected_request_id = ref(null);
const selected_user_id = ref(null);
const selected_user = ref({});


// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const user = props.value?.user;
const requests = props.value?.requests;
const useSearchBtn = mainStore.useSearchBtn;
console.log(requests)

const lengthOptions = ref([
  10,
  20,
  50,
  100
]);




const form = useForm({
  length: props.value?.length,

  name: props.value?.name,
  email: props.value?.email,

  amount: props.value?.amount,
  number: props.value?.number,

  date: props.value?.date,
  credited_date: props.value?.credited_date,
  start_date: props.value?.start_date,
  end_date: props.value?.end_date,

})
// console.log(form)

const clearFilterForm = () => {
  form.length = 10

  form.name = null
  form.email = null


  form.amount = null
  form.number = null

  form.date = null
  form.credited_date = null
  form.start_date = null
  form.end_date = null


  // console.log(form)
}



const submitFilterForm = () => {
  // console.log()
  let query = _.pickBy(form);
  let queryRoute = route('airtime_combo_recharge_history');
  let params = Object.keys(query).length ? query : {
    remember: 'forget'
  }
  // console.log(queryRoute)



  Inertia.get(queryRoute, params, {}, {
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


async function refreshUserData() {



  await axios
    .post(route('get_user_data', selected_user_id.value))
    .then((response) => {

      console.log(response)
      if (response.data.success) {
        selected_user.value = response.data.user
        // users.data[selected_user_index.value] = selected_user.value
      }
    })
    .catch((error) => {
      alert(error.message);
    });

}

const submit = () => {

};
</script>

