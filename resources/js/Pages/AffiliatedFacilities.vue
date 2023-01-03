
<template inheritAttrs="false">
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiHospitalBuilding" title="Affiliated Health Facilities" main>
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
          <FormField class="sm:col-span-4" label="Facility Name: ">
            <FormControl v-model="form.facility_name" />
          </FormField>
          <FormField class="sm:col-span-4" label="Department: ">
            <FormControl v-model="form.dept" />
          </FormField>
          <FormField class="sm:col-span-4" label="Sub Department: ">
            <FormControl v-model="form.sub_dept" />
          </FormField>
          <FormField class="sm:col-span-4" label="Personnel: ">
            <FormControl v-model="form.personnel" />
          </FormField>

          <FormField class="sm:col-span-4 capitalize" label="Role: " wrap-body>
            <FormCheckRadioGroup v-model="form.role" name="role"
              :options="roleOptions" type="radio" />
          </FormField>

          
        </div>
        <BaseButtons>
          <BaseButton v-if="useSearchBtn" type="submit" color="info" label="Filter" class="px-9 mb-8" />
          <BaseButton @click="clearFilterForm" type="reset" color="info" outline label="Clear" :icon="mdiClose" class="px-9 mb-8" />
        </BaseButtons>
        <BaseDivider />
      </CardBox>

      <CardBox class="mb-6" has-table>
        <AffiliatedFacilitiesTable :roles="roles" />
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
  mdiClose
} from "@mdi/js";

import FormCheckRadioGroup from "@/components/FormCheckRadioGroup.vue";
import FormField from "@/components/FormField.vue";
import FormControl from "@/components/FormControl.vue";
import SectionMain from "@/components/SectionMain.vue";
import NotificationBar from "@/components/NotificationBar.vue";
import AffiliatedFacilitiesTable from "@/components/AffiliatedFacilitiesTable.vue";
import CardBox from "@/components/CardBox.vue";
import LayoutAuthenticated from "@/layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/components/SectionTitleLineWithButton.vue";
import BaseDivider from "@/components/BaseDivider.vue";
import BaseButton from "@/components/BaseButton.vue";
import BaseButtons from "@/components/BaseButtons.vue";
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
const roles = props.value?.roles;
const useSearchBtn = mainStore.useSearchBtn;
console.log(useSearchBtn)

const lengthOptions = ref([
  10,
  20,
  50,
  100
]);

const roleOptions = ref({ 
  all: 'all',
  admin: 'admin', 
  sub_admin: 'sub_admin', 
  personnel: 'personnel'
});


const form = useForm({
  length: props.value?.length,
  facility_name: props.value?.facility_name,
  dept: props.value?.dept,
  sub_dept: props.value?.sub_dept,
  personnel: props.value?.personnel,
  role: roleOptions.value[props.value?.role]
})
// console.log(form)

const clearFilterForm = () => {
  form.length = 10
  form.facility_name = null
  form.dept = null
  form.sub_dept = null
  form.personnel = null
  form.role = "all"

  // console.log(form)
}

console.log(roleOptions.value.all)

const submitFilterForm = () => {
  console.log('test')
  let query = _.pickBy(form);
  let queryRoute = route('affiliated_facilities', Object.keys(query).length ? query : {
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

