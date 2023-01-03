<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head :title="`Funds Transfer`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiWalletPlus" :title="`Funds Transfer`" main>
        <BaseButton :href="route('transfer_history')" :icon="mdiHistory" label="FUNDS TRANSFER HISTORY"
          color="success" rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->

      <CardBox class="mb-6">
        <h4 class="text-xl font-bold">Wallet Balance <span class="italic text-primary"
            v-html="`₦${mainStore.addCommas((user.total_income - user.withdrawn).toFixed(2))}`"></span></h4>

        <p class="text-lg my-8">Transfer to other users</p>

        <BaseButton @click="showTransferFundsModal = true" color="info" label="Transfer Funds"/>
      </CardBox>

      <CardBoxModal v-model="showTransferFundsModal" button="danger" buttonLabel="Close"
        :title="`Transfer Funds`">
        
      
        <form @submit.prevent="submitTransferFundsForm">
          <FormField label="Enter Amount">
            <FormControl v-model="form.amount" :icon="mdiAccountCash" type="number" required />
      
          </FormField>

          <div class="grid grid-cols-12 gap-6 my-3">
          
            <select class="col-span-4 mb-[7px]" id="country" v-model="form.country"
              :class="form.errors.country ? 'login-form-input-error' : 'login-form-input'">
              <option v-for="country in countries" :value="country.id" :key="country.id"
                v-html="`${country.name} (${country.phone_code})`"></option>
            </select>
          
            <div class="col-span-8">
              <text-input v-model="form.phone" :error="form.errors.phone" label="Phone Number" id="phone"
                placeholder="" />
            </div>
          </div>
      
          <button :class="form.processing ? 'opacity-80 cursor-not-allowed' : ''"
            @mouseleave="btn_hovered = false" @mouseover="btn_hovered = true" type="submit" class="app-form-button">
            Submit
            <img v-if="form.processing" class="inline-block w-7 h-6 float-right"
              :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
          </button>
        </form>
      </CardBoxModal>
      

      <CardBoxModal v-model="showEnterPasswordModal" button="danger" buttonLabel="Close" :title="`Enter your password to confirm transfer of ₦ ${mainStore.addCommas(form.amount)} to ${recepient_details.name} `">
      
      
        <form @submit.prevent="submitEnterPasswordForm">
          <text-input v-model="form.passw" :error="form.errors.passw" type="password" label="Password" id="passw"
            placeholder="" class="" autocomplete="false" />
          <text-input v-model="form.passw_confirmation" :error="form.errors.passw_confirmation" type="password"
              label="Confirm Password" id="passw_confirmation" placeholder="" class="" autocomplete="false" />
      
          <button :class="form.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="btn_hovered = false"
            @mouseover="btn_hovered = true" type="submit" class="app-form-button">
            Submit
            <img v-if="form.processing" class="inline-block w-7 h-6 float-right"
              :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
          </button>
        </form>
      </CardBoxModal>



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
import TextInput from '@/Components/TextInput.vue'
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
const showTransferFundsModal = ref(false);
const showEnterPasswordModal = ref(false);
const recepient_details = ref({});

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const user = props.value?.user;
const countries = props.value?.countries;

const useSearchBtn = mainStore.useSearchBtn;

const form = useForm({
  amount: null,
  country: 151,
  phone: null,
  recepient_id: null,
  passw: null,
  passw_confirmation: null
});

const submitEnterPasswordForm = () => {
  form.post(route('verify_transfer_otp'), {
    preserveScroll: true,
    onSuccess: (page) => {
      console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.success) {
       

        Swal.fire({
          title: 'Success',
          icon: 'success',
          html: `You Have Successfully Transfered <em class='text-primary'>₦ ${mainStore.addCommas(form.amount)} </em> to <em class='text-primary'> ${recepient_details.value.name} </em>`,
          allowEscapeKey: false,
          allowOutsideClick: false,
          
        }).then((result) => {
          document.location.reload()
          
        });

      } else if (response.not_bouyant) {
        Swal.fire({
          title: 'Ooops!',
          html: "Sorry You Do Not Have Enough Funds For The Amount You Want To Transfer. Credit Your Account And Try Again",
          icon: 'error'
        });
      }
      else if (response.recepient_does_not_exist) {
        Swal.fire({
          title: 'Ooops!',
          text: "Sorry This User Does Not Exist",
          icon: 'error'
        });
      } else if (response.recepient_is_self) {
        Swal.fire({
          title: 'Ooops!',
          text: "Sorry you cannot transfer to yourself",
          icon: 'error'
        });
      } else if (response.incorrect_password) {
        Swal.fire({
          title: 'Ooops!',
          text: "Password entered is incorrect!",
          icon: 'error'
        });
      } else {
        Swal.fire({
          title: 'Ooops!',
          text: "Sorry something went wrong",
          icon: 'error'
        });
      }

    }, onError: (errors) => {
      var size = Object.keys(errors).length;
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: `There are ${size} form errors. Please fix them`,
      })
    },
  });

}

const submitTransferFundsForm = () => {

  
  
  form.post(route('transfer_funds_to_user'), {
    preserveScroll: true,
    onSuccess: (page) => {
      console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.user_details != []) {
        var user_details = response.user_details
        recepient_details.value = user_details

        Swal.fire({
          title: 'Are These Details Correct',
          icon: 'warning',
          html: `<div class='grid grid-cols-12 gap-6'>
              
               <h5 class='col-span-6 text-lg'>Full Name: </h5> 
               <h6 class='col-span-6 italic text-sm font-bold text-primary capitalize'>${user_details.name}</h6> 

               <h5 class='col-span-6 text-lg'>Email Address: </h5>
               <h6 class='col-span-6 italic text-sm font-bold text-primary lowercase'>${user_details.email}</h6> 
              
            </div>`,
          showCancelButton: true,
          confirmButtonText: 'Yes Proceed!',
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            Swal.fire({
              title: 'Proceed With Transfer?',
              icon: 'question',
              html: `Are You Sure You Want To Transfer <em class='text-primary'>₦${mainStore.addCommas(form.amount)}</em> To <em class='text-primary'>${user_details.name}</em> ?`,
              showCancelButton: true,
              confirmButtonText: 'Yes Proceed!',
            }).then((result) => {
              /* Read more about isConfirmed, isDenied below */
              if (result.isConfirmed) {
                showTransferFundsModal.value = false
                showEnterPasswordModal.value = true
              }
            });
          }
        });

      } else if (response.not_bouyant) {
        Swal.fire({
          title: 'Ooops!',
          html: "Sorry You Do Not Have Enough Funds For The Amount You Want To Transfer. Credit Your Account And Try Again",
          icon: 'error'
        });
      } 
      else if (response.recepient_does_not_exist) {
        Swal.fire({
          title: 'Ooops!',
          text: "Sorry This User Does Not Exist",
          icon: 'error'
        });
      } else if (response.recepient_is_self) {
        Swal.fire({
          title: 'Ooops!',
          text: "Sorry you cannot transfer to yourself",
          icon: 'error'
        });
      } else{
        Swal.fire({
          title: 'Ooops!',
          text: "Sorry something went wrong",
          icon: 'error'
        });
      }
      
    }, onError: (errors) => {
      var size = Object.keys(errors).length;
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: `There are ${size} form errors. Please fix them`,
      })
    },
  });
}

</script>

