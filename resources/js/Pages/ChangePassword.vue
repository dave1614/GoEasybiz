
<template>
  <LayoutAuthenticated>

    <Head title="Change Password" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiAccountLock" title="Change Password" main>
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

      <div class="flex justify-center">
        <div class="w-full mx-1 sm:w-6/12 my-5 ">
          <NotificationBar v-if="form_success" color="success" :icon="mdiBellBadge">
            <b v-html="`Your password has been changed successfully`"></b>
      
          </NotificationBar>
      
          <NotificationBar v-if="errors_size > 0" color="danger" :icon="mdiAlertCircle">
            <b v-html="`You have ${errors_size} error(s). Please fix them`"></b>
      
          </NotificationBar>
          <CardBox isForm @submit.prevent="submit">
      
            <FormField label="Old Password">
              <FormControl v-model="form.old_password" placeholder="•••••••••••" type="password" :icon="mdiLock" :error="form.errors.old_password" />
      
            </FormField>
      
            <FormField label="New Password">
              <FormControl v-model="form.new_password" placeholder="•••••••••••" type="password" :icon="mdiLock" :error="form.errors.new_password" />
            </FormField>
      
            <FormField label="New Password Confirmation">
              <FormControl v-model="form.new_password_confirmation" placeholder="•••••••••••" type="password" :icon="mdiLock"
                :error="form.errors.new_password_confirmation" />
            </FormField>
      
      
      
      
      
            <template #footer>
              <!-- <BaseButtons>
                    <BaseButton type="submit" color="info" label="Submit" />
                    
                  </BaseButtons> -->
              <button :class="form.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="btn_hovered = false"
                @mouseover="btn_hovered = true" type="submit" class="app-form-button">
                Login
                <img v-if="form.processing" class="inline-block w-7 h-6 float-right"
                  :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
              </button>
      
            </template>
          </CardBox>
      
        </div>
      </div>
    </SectionMain>
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
  mdiAccountLock,
  mdiLock,
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
import FormLoaderDark from '@/Loaders/form_loader_dark.gif'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'



const page = usePage()
const chartData = ref(null);
const btn_hovered = ref(false);
const errors_size = ref(0);
const form_success = ref(false);





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



const form = useForm({
  old_password: null,
  new_password: null,
  new_password_confirmation: null,
  

});

const submit = () => {
  if (!form.processing) {
    errors_size.value = 0;
    form_success.value = false
    form.post(route('process_change_password'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        console.log(response)
        if (response.success){
          form_success.value = true
          
          setTimeout(() => {
            Inertia.visit(response.redirect_url)
          }, 3000);
        }else
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'It seems the old password you entered is incorrect',
          })
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
