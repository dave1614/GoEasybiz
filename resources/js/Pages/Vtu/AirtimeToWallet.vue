<style>
.frame-area {
  display: block;
  width: 100%;
  /* max-width: 400px; */
  height: 1000px;
  /* overflow: auto; */
  border: #999999 1px solid;
  margin: 0px;
  padding: 0px;
}

.subhead {
  font-weight: bold;
  font-size: 16px;
  margin-top: 15px;
}

.network-card {
  cursor: pointer;
  transition: border 0.05s;
}

.network-card,
.network-card .card-body,
.network-card img {
  padding: 0;
}



.network-card img {
  height: 100%;
}



@media screen and (min-width: 574px) {
  .amount-card {
    margin-right: 10px;
  }
}
</style>
<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head :title="`Airtime To Wallet`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiWalletPlus" :title="`Airtime To Wallet`" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->

        <BaseButton :href="route('user_vtu_history_page') + '?length=10&type=airtime_to_wallet&isDirty=true&__rememberable=true'"
          :icon="mdiHistory" label="View His." color="success" rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->



      <CardBox isForm @submit.prevent="validateBuyAirtimeRequest" class="">
        <h4 class="subhead">Select Network</h4>

        <div class="mx-2 my-9">
          <div class="grid grid-cols-12 gap-1">

            <div class="col-span-2 sm:col-span-1 card network-card"
              :class="airtime_to_wallet_request.network == 'mtn' ? 'selected' : ''" @click="selectNetwork('mtn')">
              <div class="card-body text-center">
                <img src="/images/mtn_logo.png" alt="MTN" class="">
                <!-- <p>MTN</p> -->
              </div>

            </div>

            <div class="col-span-1">

            </div>

            <div class="col-span-2 sm:col-span-1 card network-card"
              :class="airtime_to_wallet_request.network == 'glo' ? 'selected' : ''" @click="selectNetwork('glo')">
              <div class="card-body text-center">
                <img src="/images/glo_logo.jpg" alt="GLO" class="">
                <!-- <p>MTN</p> -->
              </div>
            </div>

            <div class="col-span-1">

            </div>

            <div class="col-span-2 sm:col-span-1 card network-card"
              :class="airtime_to_wallet_request.network == 'airtel' ? 'selected' : ''" @click="selectNetwork('airtel')">
              <div class="card-body text-center">
                <img src="/images/airtel_logo.png" alt="Airtel" class="">
                <!-- <p>MTN</p> -->
              </div>
            </div>



            <div class="col-span-1">

            </div>

            <div class="col-span-2 sm:col-span-1 card network-card"
              :class="airtime_to_wallet_request.network == '9mobile' ? 'selected' : ''"
              @click="selectNetwork('9mobile')">
              <div class="card-body text-center">
                <img src="/images/9mobile-1.png" alt="9mobile" class="">
                <!-- <p>MTN</p> -->
              </div>
            </div>

          </div>
          <div class="row">
            <div v-if="airtime_to_wallet_request.errors.network" class="form-error">{{
            airtime_to_wallet_request.errors.network }}</div>
          </div>
        </div>



        <h4 class="subhead my-5">Enter Amount</h4>

        <FormField class="" label="">
          <FormControl v-model="airtime_to_wallet_request.amount" :error="airtime_to_wallet_request.errors.amount"
            :icon="mdiCurrencyNgn" type="number" id="amount" placeholder="1,000-20,000" />
        </FormField>

        <h4 class="subhead my-5">Enter Phone Number</h4>


        <FormField class="" label="">
          <FormControl v-model="airtime_to_wallet_request.phone_number"
            :error="airtime_to_wallet_request.errors.phone_number" :icon="mdiPhone" type="text" id="phone_number"
            placeholder="e.g 08127027321" />
        </FormField>






        <button :class="airtime_to_wallet_request.processing ? 'opacity-80 cursor-not-allowed' : ''"
          @mouseleave="btn_hovered = false" @mouseover="btn_hovered = true" type="submit"
          class="app-form-button mt-9 text-left pl-6">
          Continue
          <img v-if="airtime_to_wallet_request.processing" class="inline-block w-7 h-6 float-right"
            :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
        </button>
      </CardBox>



      <CardBoxModal v-model="showPreviewTransactionModal" button="danger" buttonLabel="Close"
        :title="`Preview Transaction`">
        <div class="">
          <div class="text-center">
            <p>Kindly confirm that the details you entered are valid before clicking the "Confirm" button.</p>

          </div>
          <div class="">
            <table class="table table-bordered mt-4">
              <tbody>
                <tr>
                  <td>NETWORK</td>
                  <td><em v-html="airtime_to_wallet_request.network" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>PHONE NUMBER</td>
                  <td><em v-html="airtime_to_wallet_request.phone_number" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>AMOUNT</td>
                  <td><em v-html="'₦ ' +mainStore.addCommas(airtime_to_wallet_request.amount)" class="text-primary"></em>
                  </td>
                </tr>

                <tr>
                  <td>CHARGE</td>
                  <td><em v-html="airtime_to_wallet_request.perc_charge + '%'" class="text-primary"></em></td>
                </tr>

                <tr>
                  <td>CREDITED AMOUNT</td>
                  <td><em v-html="'₦ ' +mainStore.addCommas(airtime_to_wallet_request.credited_amount)"
                      class="text-primary"></em></td>
                </tr>

                <tr>
                  <td>TRANSFER CODE</td>
                  <td><em v-html="airtime_to_wallet_request.transfer_code" class="text-primary"></em></td>
                </tr>

                <tr>
                  <td>INSTRUCTIONS</td>
                  <td><em v-html="airtime_to_wallet_request.instruction" class="text-primary"></em></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="justify-content-center text-center">

          <BaseButton @click="confirmAndProceedWithTransaction" class="" label="Confirm" color="success" rounded />
          <br>
          <p class="text-red-500 cursor-pointer" @click="showPreviewTransactionModal = false">Cancel and return</p>
        </div>
        

      </CardBoxModal>
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
  mdiWalletPlus,
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


const show_other_overlay = ref(false);


const showPreviewTransactionModal = ref(false);

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const user = props.value?.user;


const useSearchBtn = mainStore.useSearchBtn;


const lengthOptions = ref([
  10,
  20,
  50,
  100
]);



const airtime_to_wallet_request = useForm({
  network: null,
  phone_number: null,
  amount: null,
  credited_amount: null,
  perc_charge: null,
  transfer_code: null,
  instruction: "",
  from: null,

});

const get_charge_request = useForm({
  network: null,


});



const submit = () => {

};

const confirmAndProceedWithTransaction = () => {

  var network = airtime_to_wallet_request.network;
  var phone_number = airtime_to_wallet_request.phone_number;
  var amount = airtime_to_wallet_request.amount;
  var credited_amount = airtime_to_wallet_request.credited_amount;
  var perc_charge = airtime_to_wallet_request.perc_charge;
  var transfer_code = airtime_to_wallet_request.transfer_code;
  var instruction = airtime_to_wallet_request.instruction;


  console.log(network)
  console.log(phone_number)
  console.log(amount)
  console.log(credited_amount)
  console.log(perc_charge)
  console.log(transfer_code)
  console.log(instruction)


  showPreviewTransactionModal.value = false;

  get_charge_request.post(route('get_charge_for_airtime_to_wallet_transfer'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.charge != "" && response.phone_number != "" && response.network != "" && response.transfer_code != "" && response.instruction != "") {
        var charge = response.charge;
        var phone_number = response.phone_number;


        airtime_to_wallet_request.from = phone_number;


        airtime_to_wallet_request.post(route('process_airtime_to_wallet_transfer'), {
          preserveScroll: true,
          onSuccess: (page) => {

            var response = page.props.flash.data;
            console.log(response)

            if (response.success && response.order_id !== "") {
              var order_id = response.order_id;

              Swal.fire({
                title: 'Info',
                html: `You Request Has Been Sent Successfully. For <em class='text-primary'>${phone_number}</em> And Credit Worth ₦${mainStore.addCommas(amount)} On <span style='text-transform: capitalize;'>${network}</span> Network. You Would Be Automatically Credited When You Send This Code In This Format From The Requested Number <em class='text-primary'>${transfer_code}</em>. Note: <em class='text-primary'>${instruction}. Amount Sent Must Match Account Requested.</em> The Order Id For This Transaction Is <em class='text-primary'>${order_id}</em>`,
                icon: 'info',
                confirmButtonColor: '#3085d6',
                allowEscapeKey: false,
                allowOutsideClick: false,
              }).then((result) => {
                Inertia.visit(route('airtime_to_wallet'));

              });
            } else if (response.invalid_amount) {

              Swal.fire({
                title: 'Ooops!',
                html: "Invalid Amount Was Entered. Your Money Has Been Refunded",
                icon: 'error'
              });
            } else if (response.invalid_recipient) {

              Swal.fire({
                title: 'Ooops',
                html: "Invalid Mobile Number Was Entered. Your Money Has Been Refunded",
                icon: 'error'
              });
            } else if (response.insuffecient_funds) {

              Swal.fire({
                title: 'Ooops',
                html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
                icon: 'error'
              });
            } else {
              Swal.fire({
                title: 'Error',
                html: "Something Went Wrong. Please Try Again",
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

      } else {
        Swal.fire({
          title: 'Error',
          html: "Something Went Wrong. Please Try Again",
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

};

const validateBuyAirtimeRequest = () => {

  var network = airtime_to_wallet_request.network;
  var phone_number = airtime_to_wallet_request.phone_number;
  var amount = airtime_to_wallet_request.amount;
  var credited_amount = airtime_to_wallet_request.credited_amount;
  var perc_charge = airtime_to_wallet_request.perc_charge;

  console.log(network)
  console.log(phone_number)
  console.log(amount)
  console.log(credited_amount)
  console.log(perc_charge)
  if (network != null) {

    airtime_to_wallet_request.post(route('validate_airtime_to_wallet_details'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        console.log(response)

        if (response.success) {

          credited_amount = (perc_charge / 100) * amount;
          credited_amount = amount - credited_amount;
          airtime_to_wallet_request.credited_amount = credited_amount;

          showPreviewTransactionModal.value = true;
        } else {
          Swal.fire({
            title: 'Error',
            html: "Something Went Wrong. Please Try Again",
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

  } else {
    swal({
      title: 'Error',
      text: "Please Select Network To Proceed",
      type: 'error'
    })
  }
};


const selectNetwork = (network) => {


  if (airtime_to_wallet_request.network != network) {
    get_charge_request.network = network;

    get_charge_request.post(route('get_charge_for_airtime_to_wallet_transfer'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        console.log(response)

        if (response.success && response.charge != "" && response.phone_number != "" && response.network != "" && response.transfer_code != "" && response.instruction != "") {
          var charge = response.charge;
          var phone_number = response.phone_number;
          var network_str = response.network;
          var transfer_code = response.transfer_code;
          var instruction = response.instruction;



          Swal.fire({
            title: 'Info',
            html: `Charge For ${network_str} Network Is  <em class='text-primary'>${charge}%</em>. Proceed?`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes Proceed!',
            cancelButtonText: "No"
          }).then((result) => {
            if (result.isConfirmed) {
              airtime_to_wallet_request.network = network;
              airtime_to_wallet_request.perc_charge = charge;
              airtime_to_wallet_request.transfer_code = transfer_code;
              airtime_to_wallet_request.instruction = instruction;
            }
          });
        } else {
          Swal.fire({
            title: 'Error',
            html: "Something Went Wrong. Please Try Again",
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

};


</script>

