<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head :title="`Downline Members`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiClipboardTextClock" :title="`Downline Members`" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->

      <!-- <CardBox v-if="useSearchBtn" form @submit.prevent="submitFilterForm" class=""> -->

      <CardBox class="mb-5">
        <div class="mx-3 my-5 font-bold text-sm">
          
          <h4 class="text-lg">Total Downline Members</h4>
          <p  class="text-primary" v-html="total_downline_num"></p>
          
          
          <h4 class="text-lg mt-4">Total Levels</h4>
          <p class="text-primary">{{total_levels}}</p>
          
        </div>
      </CardBox>

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

          <FormField class="sm:col-span-4" label="Level">
            <FormControl v-model="form.level" type="number" />
          </FormField>
          

          <FormField class="sm:col-span-4" label="Registration Date">
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
            <thead class="text-sm">
              <tr>

                <th></th>


                <th>Member's Name</th>
                <th>Email</th>
                <th>Placement's Name</th>
                <th>Placement's Email</th>
                <th>Sponsor's Name</th>
                <th>Sponsor's Email</th>
                <th>Level</th>
                <th>Registration Date</th>
              </tr>
            </thead>
            <tbody class="text-sm">
              <tr v-for="(row,index) in history.data" :key="row.id">

                <td v-html="`${(index + 1) +((history.current_page - 1) * form.length)}.`"></td>


                <td class="capitalize" data-label="Member's Name">
                  {{ row.name }}
                </td>

                <td class="text-xs text-primary italic" data-label="Email">
                  {{ row.email }}
                </td>

                <td class="capitalize" data-label="Placements's Name">
                  {{ row.placement_name }}
                </td>
                
                <td class="text-xs text-primary italic" data-label="Placements's Email">
                  {{ row.placement_email }}
                </td>

                <td class="capitalize" data-label="Sponsor Name">
                  {{ row.sponsor_name }}
                </td>
                
                <td class="text-xs text-primary italic" data-label="Sponsor Email">
                  {{ row.sponsor_email }}
                </td>

                

                <td class="capitalize text-primary font-bold text-sm" data-label="Level">
                  {{ row.stage }}
                </td>


                <td data-label="Registration Date / Time" v-html="`${row.date_created} ${row.time_created}`">

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
const history = props.value?.history;
const total_downline_num = props.value?.total_downline_num;
const total_levels = props.value?.total_levels;
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

  name: props.value?.name,
  email: props.value?.email,

  level: props.value?.level,
  

  date: props.value?.date,
  start_date: props.value?.start_date,
  end_date: props.value?.end_date,

})
// console.log(form)

const clearFilterForm = () => {
  form.length = 10

  form.name = null
  form.email = null


  form.level = null
  

  form.date = null
  
  form.start_date = null
  form.end_date = null


  // console.log(form)
}



const submitFilterForm = () => {
  // console.log()
  let query = _.pickBy(form);
  let queryRoute = route('downline_members');
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




const submit = () => {

};
</script>

