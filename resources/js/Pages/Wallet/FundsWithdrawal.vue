<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head :title="`Funds Withdrawal`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiFolderMinus" :title="`Funds Withdrawal`" main>
        <BaseButton :href="route('withdrawal_history')" :icon="mdiHistory" label="FUNDS WITHDRAWAL HISTORY" color="success"
          rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->

      <CardBox class="mb-6">
        <h4 class="text-xl font-bold">Wallet Balance <span class="italic text-primary"
            v-html="`₦${mainStore.addCommas((user.total_income - user.withdrawn).toFixed(2))}`"></span></h4>
        <p class="text-primary">Note: Withdrawal Comes With Charge Of ₦100</p>

        <p class="text-lg my-9">Click Button Below To Withdraw Funds</p>

        <BaseButton @click="showWithdrawFundsModal = true" color="info" label="Withdraw Funds" />
      </CardBox>

      <CardBoxModal v-model="showWithdrawFundsModal" button="danger" buttonLabel="Close" :title="`Withdraw Funds`">


        <form @submit.prevent="submitBankDetailsForm" v-if="banks_arr.status && banks_arr.message == 'Banks retrieved'">
          <label for="bank_name" class="login-form-label">Select Bank Name: </label>
          <select class="col-span-4 mb-[7px]" id="bank_name" v-model="form.bank_name"
            :class="form.errors.bank_name ? 'login-form-input-error' : 'login-form-input'">
            <option v-for="row in banks_arr.data" :value="row.code" :key="row.id"
              v-html="`${row.name}`"></option>
          </select>
          <span class="login-form-error" v-if="form.errors.bank_name">{{ form.errors.bank_name }}</span>

          <text-input v-model="form.account_number" :error="form.errors.account_number" type="number"
            label="Enter Account Number" id="account_number" placeholder="" class="col-sm-12" />


          <button :class="form.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="btn_hovered = false"
            @mouseover="btn_hovered = true" type="submit" class="app-form-button">
            Submit
            <img v-if="form.processing" class="inline-block w-7 h-6 float-right"
              :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
          </button>
        </form>

      </CardBoxModal>


      <CardBoxModal v-model="showEnterPasswordModal" button="danger" buttonLabel="Close"
        :title="`Enter your password to confirm withdrawal of ₦ ${mainStore.addCommas(form.amount)}`">


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

      <CardBoxModal v-model="showEnterAmountModal" button="danger" buttonLabel="Close"
        :title="`Enter Withdrawal Amount To Proceed`">
      
      
        <form @submit.prevent="submitEnterAmountForm">
         <FormField label="Enter Amount">
            <FormControl v-model="form.amount" :icon="mdiAccountCash" type="number" />
      
          </FormField>
      
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
  mdiFolderMinus,
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
const showWithdrawFundsModal = ref(false);
const showEnterPasswordModal = ref(false);
const showEnterAmountModal = ref(false);
const account_name = ref(null);

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const user = props.value?.user;
const countries = props.value?.countries;
const banks_arr = props.value?.banks_arr;

const useSearchBtn = mainStore.useSearchBtn;

const form = useForm({
  amount: null,
  bank_name: user.bank_name,
  account_number: user.account_number,
  account_name: '',
  passw: null,
  passw_confirmation: null
});

const submitEnterPasswordForm = () => {
  form.post(route('validate_withdrawal_otp'), {
    preserveScroll: true,
    onSuccess: (page) => {
      console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.success) {


        Swal.fire({
          title: 'Success',
          icon: 'success',
          html: `Your Request To Transfer <em class='text-primary'>₦${mainStore.addCommas(form.amount)}</em> From Your Easybizu Account To Bank Account With Name <em class='text-primary'>${form.account_name}</em> Has Been Sent To The Admin For Approval.`,
          allowEscapeKey: false,
          allowOutsideClick: false,

        }).then((result) => {
          document.location.reload()

        });

      } else if (response.incorrect_otp) {
        Swal.fire({
          title: 'Ooops!',
          html: "The Password Entered Is Incorrect. Please Enter The Valid One.",
          icon: 'error'
        });
      }
      else if (response.not_bouyant) {
        Swal.fire({
          title: 'Ooops!',
          text: "Sorry You Do Not Have Enough Funds For The Amount You Want To Transfer. Credit Your Account And Try Again",
          icon: 'error'
        });
      } else if (response.too_small) {
        Swal.fire({
          title: 'Ooops!',
          text: "Minimum Withdrawable Amount Is ₦200",
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

const submitEnterAmountForm = () => {



  form.post(route('enter_amount_withdraw_funds'), {
    preserveScroll: true,
    onSuccess: (page) => {
      console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.code != "" && response.phone_number != "") {
        

        Swal.fire({
          title: 'Proceed With Withdrawal?',
          icon: 'success',
          html: `Are You Sure You Want To Transfer <em class='text-primary'>₦ ${mainStore.addCommas(form.amount)}</em> From Your Easybizu Account To Bank Account With Account Name <em class='text-primary'>${form.account_name}</em>?`,
          showCancelButton: true,
          confirmButtonText: 'Yes Proceed!',
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {

            
            showEnterAmountModal.value = false
            showEnterPasswordModal.value = true
          }
        });

      } else if (response.not_referred_enough){
        var min_referrals = response.min_referrals
        var referrals_num = response.referrals_num
        var ref_referrals = min_referrals - referrals_num
        Swal.fire({
          title: 'Ooops!',
          html: `Sorry you have not referred enough members yet. You need to refer at least ${min_referrals} members to withdraw. You have currently referred ${referrals_num} Members. You have ${ref_referrals} more to go.`,
          icon: 'error'
        });
      } else if (response.not_bouyant) {
        Swal.fire({
          title: 'Ooops!',
          html: "Sorry You Do Not Have Enough Funds For The Amount You Want To Transfer. Credit Your Account And Try Again",
          icon: 'error'
        });
      }
      else if (response.too_small) {
        Swal.fire({
          title: 'Ooops!',
          text: "Minimum Withdrawable Amount Is ₦200",
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
        text: `Sorry something went wrong`,
      })
    },
  });
}

const submitBankDetailsForm = () => {



  form.post(route('withdraw_funds_cont'), {
    preserveScroll: true,
    onSuccess: (page) => {
      console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.account_name != "") {
        var account_name = response.account_name
        form.account_name = account_name

        Swal.fire({
          title: 'Proceed With Withdrawal?',
          icon: 'success',
          html: `Is This Your Account Name <em class='text-primary'>${account_name}</em>?`,
          showCancelButton: true,
          confirmButtonText: 'Yes Proceed!',
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            
            showWithdrawFundsModal.value = false
            showEnterAmountModal.value = true
              
          }
        });

      } else if (response.invalid_account) {
        Swal.fire({
          title: 'Invalid Account Details',
          html: "Sorry These Account Details Are Not Linked To Any Account",
          icon: 'error'
        });
      }
      else if (!response.bouyant) {
        Swal.fire({
          title: 'Insuffecient Balance',
          text: "Sorry You Do Not Have Enough Funds In Your Account To Complete This Transaction",
          icon: 'error'
        });
      }else {
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

