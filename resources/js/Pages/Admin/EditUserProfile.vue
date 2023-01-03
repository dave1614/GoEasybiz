
<template>
  <LayoutAuthenticated>

    <Head :title="`Edit ${user.name}'s Profile`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiAccountEdit" :title="`Edit ${user.name}'s Profile`" main>
        <!-- <BaseButton
          href="https://github.com/justboil/admin-one-vue-tailwind"
          target="_blank"
          :icon="mdiGithub"
          label="Star on GitHub"
          color="contrast"
          rounded-full
          small
        /> -->
      </SectionTitleLineWithButton>

      <CustomNotificationBar color="success" :icon="mdiBellBadge">
        <b>Your profile has been updated successfully.</b>
        <span v-if="mainStore.email_changed"> But we noticed your email was changed. A link has been sent to your new
          email address. Click this link to finally update your email address.</span>
      </CustomNotificationBar>

      <NotificationBar v-if="errors_size > 0" color="danger" :icon="mdiAlertCircle">
        <b v-html="`You have ${errors_size} error(s). Please fix them`"></b>

      </NotificationBar>
      <CardBox isForm @submit.prevent="submit">
        <div class="sm:grid sm:grid-cols-12 sm:gap-6">
          <FormField class="sm:col-span-6" label="Full name">
            <FormControl v-model="form.name" :icon="mdiAccount" :error="form.errors.name" />

          </FormField>

          <FormField class="sm:col-span-6" label="Email">
            <FormControl v-model="form.email" type="email" :icon="mdiMail" :error="form.errors.email" />
          </FormField>


          <!-- <FormControl v-model="form.phone" type="number" placeholder="Your phone number" /> -->


          <FormField class="sm:col-span-6" label="Phone">
            <div class="grid grid-cols-12 gap-6 my-3">

              <!-- <select class="col-span-4 mb-[7px]" id="country" v-model="form.country"
                :class="form.errors.country ? 'login-form-input-error' : 'login-form-input'">
                <option v-for="country in countries" :value="country.id" :key="country.id"
                  v-html="`${country.name} (${country.phone_code})`"></option>
              </select> -->
              <FormControl class="col-span-4" v-model="form.country" :options="countries"
                :error="form.errors.country" />
              <!-- <FormControl v-model="form.phone" type="select" /> -->

              <div class="col-span-8">
                <!-- <text-input v-model="form.phone" :error="form.errors.phone" type="number" label="Phone Number" id="phone"
                  placeholder="" /> -->
                <FormControl v-model="form.phone" type="text" placeholder="Your phone number"
                  :error="form.errors.phone" />
              </div>
            </div>
          </FormField>



          <!-- <BaseDivider /> -->

          <FormField class="sm:col-span-6" label="Address">
            <FormControl v-model="form.address" type="textarea" placeholder="Enter your address"
              :error="form.errors.address" />
          </FormField>
        </div>

        <template #footer>
          <!-- <BaseButtons>
            <BaseButton type="submit" color="info" label="Submit" />
            
          </BaseButtons> -->
          <button :class="form.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="btn_hovered = false"
            @mouseover="btn_hovered = true" type="submit" class="app-form-button">
            Submit
            <img v-if="form.processing" class="inline-block w-7 h-6 float-right"
              :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
          </button>

        </template>
      </CardBox>
    </SectionMain>

    <FloatingActionButton :title="'Go Back'" @click="mainStore.goBack">
    
      <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
    </FloatingActionButton>
  </LayoutAuthenticated>
</template>

<script setup>
import { useForm, usePage, Head } from '@inertiajs/inertia-vue3';
import { computed, ref, onMounted } from "vue";
import { useMainStore } from "@/stores/main";
import { Inertia } from '@inertiajs/inertia'
import {
  mdiAccountEdit,
  mdiAccount,
  mdiMail,
  mdiBellBadge,
  mdiClose,
  mdiAlertCircle,
} from "@mdi/js";
import * as chartConfig from "@/Components/Charts/chart.config.js";
import LineChart from "@/Components/Charts/LineChart.vue";
import SectionMain from "@/Components/SectionMain.vue";
import CardBoxWidget from "@/Components/CardBoxWidget.vue";
import CardBox from "@/Components/CardBox.vue";
import TableSampleClients from "@/Components/TableSampleClients.vue";
import CustomNotificationBar from "@/Components/CustomNotificationBar.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import CardBoxTransaction from "@/Components/CardBoxTransaction.vue";
import CardBoxClient from "@/Components/CardBoxClient.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import SectionBannerStarOnGitHub from "@/Components/SectionBannerStarOnGitHub.vue";
import UserCard from "@/components/UserCard.vue";
import UserAvatarCurrentUser from "@/components/UserAvatarCurrentUser.vue";
import MultipurposeButton from '@/Components/MultipurposeButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import FormField from '@/Components/FormField.vue';
import FormControl from '@/Components/FormControl.vue';
import UserAvatar from '@/Components/UserAvatar.vue';
import BaseDivider from '@/Components/BaseDivider.vue';
import FloatingActionButton from '@/Components/FloatingActionButton.vue';

import FormLoaderDark from '@/Loaders/form_loader_dark.gif'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'



const page = usePage()
const chartData = ref(null);
const btn_hovered = ref(false);
const errors_size = ref(0);





const fillChartData = () => {
  chartData.value = chartConfig.sampleChartData();
};

const mainStore = useMainStore();
onMounted(() => {
  fillChartData();
  // mainStore.changeIsAdminVal(true)
});

const i = 1;


const clientBarItems = computed(() => mainStore.clients.slice(0, 4));

const transactionBarItems = computed(() => mainStore.history);


const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const user = page.props.value?.user;
const countries = page.props.value?.countries;
const selected_country = page.props.value?.selected_country;
console.log(selected_country)


const form = useForm({
  name: user.name,
  email: user.email,
  country: countries[selected_country],
  phone: user.phone,
  address: user.address,

});

const submit = () => {
  if (!form.processing) {
    errors_size.value = 0;
    mainStore.notiDismissed = true
    mainStore.email_changed = false
    form.post(route('process_edit_users_profile',user.id), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        console.log(response)
        if (response.success)
          mainStore.notiDismissed = false

        if (response.email_changed) {
          mainStore.email_changed = true;
          console.log(mainStore.email_changed)
        } 
      }, onError: (errors) => {
        var size = Object.keys(errors).length;
        errors_size.value = size;
        console.log(errors_size.value)
        // Swal.fire({
        //   icon: 'error',
        //   title: 'Oops...',
        //   text: `There are ${size} form errors. Please fix them`,
        // })
      },
    });
  }
};
</script>
