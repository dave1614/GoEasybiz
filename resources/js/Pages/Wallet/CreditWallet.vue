<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head :title="`Credit Your Wallet`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiWalletPlus" :title="`Credit Your Wallet`" main>
        <BaseButton :href="route('wallet_credit_history')"  :icon="mdiHistory"
          label="WALLET CREDIT HISTORY" color="success" rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->

      <CardBox class="mb-6">
        <h4 class="text-xl font-bold">Wallet Balance <span class="italic text-primary" v-html="`₦${mainStore.addCommas((user.total_income - user.withdrawn).toFixed(2))}`"></span></h4>

        <h5 class="text-lg font-semibold">Personalized Account Funding</h5>
        
        <p class="my-3">Funding less than ₦9000 is ₦25, while above ₦9000 cost ₦75 only.</p>
        
      </CardBox>

      <CardBox class="mb-6" has-table>

        <div class="">
          <table>
            
            <tbody>
              <tr>
                <td>Bank Name</td>
                <td>Providus Bank</td>
              </tr>
              <tr>
                <td>Account Name</td>
                <td class="text-bold italic text-sm text-primary">{{ user.providus_account_name }}</td>
              </tr>
              <tr>
                <td>Account Number</td>
                <td class="text-bold italic text-sm text-primary">{{ user.providus_account_number }}</td>
              </tr>
            </tbody>
          </table>
          
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
  mdiHistory,
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

const useSearchBtn = mainStore.useSearchBtn;





</script>

