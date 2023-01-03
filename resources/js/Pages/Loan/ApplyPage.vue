<style>

</style>
<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head :title="`Apply For Easy Loan`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiCellphoneWireless" :title="`Easy Loan`" main>
        
        <BaseButton :href="route('easy_loan_history_page') + '?length=10&type=airtime&isDirty=true&__rememberable=true'"
          :icon="mdiHistory" label="View Loans." color="success" rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->



      <CardBox v-if="!startProcess" class="">
        <div class="font-semibold">
          <p>
            It's interest and collateral free loan that members get just at clicks away.
          </p>
          <p>
            It's the cheapest to process, just N250 per month regardless of the amount. The accessable minimum and maximum Loan is
            depended on your previous week earnings.
          </p>
          <p>
            You can get 100% of your previous week earning to enable you pay back with ease from your earnings.
          </p>
          
          <br><br>
            Go-Easybiz,<br>
            ...It's Easy! Reliable! & Amazing!
          <br><br>
            Click the button below to see if you currently qualify.

          
        </div>

        <div class="mt-4">
          <BaseButton @click="checkIfUserQuali" label="Check" color="success"/>
        </div>

        <div class="mt-9">
          <div v-if="!eligible" class="">
            <h5 class="text-lg text-"></h5>
          </div>
        </div>
      </CardBox>

      

      <CardBox v-else isForm @submit.prevent="submit">
        <p class="font-semibold text-lg">Your current min loan amount is ₦{{ mainStore.addCommas(min_loan_amt) }}</p>
        <p class="font-semibold text-lg">Your current max loan amount is ₦{{ mainStore.addCommas(max_loan_amt) }}</p>
        
        
        <h4 class="text-bold text-2xl mt-8 mb-4">Enter amount you'll wish to loan: </h4>

        <div class="sm:grid sm:grid-cols-12 sm:gap-6">
          <FormField class="sm:col-span-12" label="">
            <FormControl v-model="form.amount" type="number" :icon="mdiCurrencyNgn" :error="form.errors.amount" />
      
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
  mdiCellphoneWireless,
  mdiAccountCog,
  mdiAccountCash,
  mdiCurrencyNgn,
  mdiPhone,
mdiHistory,
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
const user = props.value?.auth.user;

const not_enough_referrals = props.value?.not_enough_referrals;
const not_earned_past_week = props.value?.not_earned_past_week;
const amt_earnd_not_enough = props.value?.amt_earnd_not_enough;
const amt_earned = props.value?.amt_earned;
const reasons = props.value?.reasons;
const eligible = props.value?.eligible;
const pending_loans = props.value?.pending_loans;
const min_loan_amt = props.value?.min_loan_amt;
const max_loan_amt = props.value?.max_loan_amt;
const startProcess = ref(false);
const errors_size = ref(0);


console.log(user)

console.log(not_enough_referrals)
console.log(not_earned_past_week)
console.log(amt_earnd_not_enough)
console.log(amt_earned)
console.log(reasons)
console.log(eligible)
console.log(pending_loans)
console.log(min_loan_amt)
console.log(max_loan_amt)


const useSearchBtn = mainStore.useSearchBtn;



const form = useForm({
  amount: null,
  
});



const submit = () => {
  if (!form.processing) {

    errors_size.value = 0;

    Swal.fire({
      title: 'Procceed?',
      icon: 'question',
      html: `Are you sure you want to proceed with loan application of ${form.amount}?`,
      showCancelButton: true,
      confirmButtonText: 'Yes, Proceed',
      cancelButtonText: `No, Cancel`,
    }).then((result) => {
      if (result.isConfirmed) {
       
        form.post(route('process_apply_for_easy_loan'), {
          preserveScroll: true,
          onSuccess: (page) => {

            var response = page.props.flash.data;
            console.log(response)
            if (response.success){
              
              Swal.fire({
                title: 'Success',
                icon: 'success',
                html: `Your request to loan ${form.amount} has been granted. Note: your loan is due after 1 Month.`,
                allowEscapeKey: false,
                allowOutsideClick: false,

              }).then((result) => {
                Inertia.visit(route('apply_for_easy_loan_page'));

              });
            } else if (!response.eligible){
              var reasons = response.reasons;
              var html = 'You are not currently eligible for easy loan. Below are reasons:';
              html += '<div class="mt-5 text-sm font-semibold italic">';
              for (let i = 0; i < reasons.length; i++) {
                html += `<p>${reasons[i]}</p>`;
              }
              html += '</div>';
              Swal.fire({
                title: 'Error',
                icon: 'error',
                html: html,
              });
            } else if (!response.normal_range){
              var min_loan_amt = response.min_loan_amt;
              var max_loan_amt = response.max_loan_amt;
              var html = `The loan amount you are trying to request must be between <em class="text-primary">₦${min_loan_amt}</em> and <em class="text-primary">₦${max_loan_amt}</em>`;
              
              Swal.fire({
                title: 'Error',
                icon: 'error',
                html: html,
              });
            }
          }, onError: (errors) => {
            var size = Object.keys(errors).length;
            errors_size.value = size;
            console.log(errors_size.value)
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: `There are ${size} form errors. Please fix them`,
            })
          },
        });
      }
    });


  }
};


const checkIfUserQuali = () => {
  
  if (eligible){
    Swal.fire({
      title: 'Info',
      icon: 'success',
      html: `You are currently eligible for easy loan. Will you like to apply now?`,
      showCancelButton: true,
      confirmButtonText: 'Yes, Proceed',
      cancelButtonText: `No, Cancel`,
    }).then((result) => {
      if (result.isConfirmed) {
        startProcess.value = true
      } 
    });
  }else{
    var html = 'You are not currently eligible for easy loan. Below are reasons:';
    html += '<div class="mt-5 text-sm font-semibold italic">';
    for (let i = 0; i < reasons.length; i++){
      html += `<p>${reasons[i]}</p>`;
    }
    html += '</div>';
    Swal.fire({
      title: 'Info',
      icon: 'error',
      html: html,
    });
  }

    

};


</script>

