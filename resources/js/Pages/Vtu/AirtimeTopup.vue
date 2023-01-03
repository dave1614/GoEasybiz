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

    <Head :title="`Airtime Topup`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiCellphoneWireless" :title="`Airtime Topup`" main>
        
        <BaseButton :href="route('user_vtu_history_page') + '?length=10&type=airtime&isDirty=true&__rememberable=true'"
          :icon="mdiHistory" label="View His." color="success" rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->


      <div v-if="user.country_id == 151">

        <CardBox v-if="!epins_received" isForm @submit.prevent="validateBuyAirtimeRequest" class="">
          <h4 class="subhead">Select Network</h4>
          
          <div class="mx-2 my-9">
            <div class="grid grid-cols-12 gap-1">
          
              <div
                class="col-span-2 sm:col-span-1 card network-card"
                :class="buy_airtime_request.network == 'mtn' ? 'selected' : ''"
                @click="selectNetwork('mtn')">
                <div class="card-body text-center">
                  <img src="/images/mtn_logo.png" alt="MTN" class="">
                  <!-- <p>MTN</p> -->
                </div>
          
              </div>
          
              <div class="col-span-1">
          
              </div>
          
              <div
                class="col-span-2 sm:col-span-1 card network-card"
                :class="buy_airtime_request.network == 'glo' ? 'selected' : ''"
                @click="selectNetwork('glo')">
                <div class="card-body text-center">
                  <img src="/images/glo_logo.jpg" alt="GLO" class="">
                  <!-- <p>MTN</p> -->
                </div>
              </div>
          
              <div class="col-span-1">
          
              </div>
          
              <div
                class="col-span-2 sm:col-span-1 card network-card"
                :class="buy_airtime_request.network == 'airtel' ? 'selected' : ''"
                @click="selectNetwork('airtel')">
                <div class="card-body text-center">
                  <img src="/images/airtel_logo.png" alt="Airtel" class="">
                  <!-- <p>MTN</p> -->
                </div>
              </div>
          
          
          
              <div class="col-span-1">
          
              </div>
          
              <div
                class="col-span-2 sm:col-span-1 card network-card" 
                :class="buy_airtime_request.network == '9mobile' ? 'selected' : ''"
                @click="selectNetwork('9mobile')">
                <div class="card-body text-center">
                  <img src="/images/9mobile-1.png" alt="9mobile" class="">
                  <!-- <p>MTN</p> -->
                </div>
              </div>
          
            </div>
            <div class="row">
              <div v-if="buy_airtime_request.errors.network" class="form-error">{{ buy_airtime_request.errors.network }}</div>
            </div>
          </div>

          <h4 class="subhead my-3">Choose An Amount</h4>
          
          <div class="mx-2 my-5">
            <div class="grid grid-cols-12">
          
              <div v-for="(amount, index) in amounts"
                class="col-span-6 sm:col-span-2 card amount-card "
                :class="buy_airtime_request.selected_amount == amount ? 'selected' : ''"
                @click="selectAmount(amount)" :key="index">
                <div class="card-body text-center">
                  <span v-html="'NGN ' + amount"></span>
                </div>
              </div>
          
          
          
            </div>
          </div>

          <h4 class="subhead my-5" v-if="!epin_checkbox_checked">Or Enter Amount Directly</h4>
          
          <FormField v-if="!epin_checkbox_checked" class="" label="">
            <FormControl v-model="buy_airtime_request.entered_amount" :error="buy_airtime_request.errors.amount" :icon="mdiCurrencyNgn" type="number" id="entered_amount" placeholder="100-50,000" />
          </FormField>

          <h4 class="subhead my-5" v-if="!epin_checkbox_checked">Enter Phone Number</h4>
          
          

          <FormField v-if="!epin_checkbox_checked" class="" label="">
            <FormControl v-model="buy_airtime_request.phone_number" :error="buy_airtime_request.errors.phone_number"
              :icon="mdiPhone" type="text" id="phone_number" placeholder="e.g 08127027321" />
          </FormField>

        
          <div class="grid grid-cols-12 gap-6">
            <div v-if="!epin_checkbox_checked && buy_airtime_request.network == 'mtn'" id="mtn-airtime-bonus-check-div"
              class="mt-[15px] col-span-6">
            
              <input type="checkbox" class="login-checkbox" id="mtn-airtime-bonus-check"
                v-model="buy_airtime_request.airtime_bonus" />
              <label for="mtn-airtime-bonus-check" class="login-checkbox-label ">MTN Awuf (400% airtime
              bonus)</label>
            </div>

            
            <div v-if="!epin_checkbox_checked && buy_airtime_request.network == 'glo'" id="glo-airtime-bonus-check-div"
              class="mt-[15px] col-span-6">

              <input type="checkbox" class="login-checkbox" id="glo-airtime-bonus-check" v-model="buy_airtime_request.airtime_bonus" />
              <label for="glo-airtime-bonus-check" class="login-checkbox-label ">Glo 5X (500% airtime
              bonus)</label>
            </div>
            

            <div id="generate-epin-check-div" class="mt-[15px] col-span-6">
            
              <input @change="epinCheckBoxChanged" type="checkbox" class="login-checkbox" id="generate-epin-check"
                v-model="epin_checkbox_checked" />
              <label for="generate-epin-check" class="login-checkbox-label ">Generate E-PIN</label>
            </div>

          </div>

          <p  id="epin-cashback" v-if="epin_checkbox_checked"><em class="text-primary">{{cashback_text}}</em></p>

          <button :class="buy_airtime_request.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="btn_hovered = false"
            @mouseover="btn_hovered = true" type="submit" class="app-form-button mt-9 text-left pl-6">
            Continue
            <img v-if="buy_airtime_request.processing" class="inline-block w-7 h-6 float-right"
              :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
          </button>
        </CardBox>

        <CardBox v-else id="epin-card">
        
          <h3 class="font-bold text-2xl capitalize mt-2 mb-5" v-html="epin_card_title"></h3>
        
          <div class="">
            <h4 class="text-lg">Details: </h4>
            <h4 class="text-lg">Amount: <em class='text-primary' v-html="'₦' + mainStore.addCommas(epins_amount)"></em>
              <br>Quantity: <em class='text-primary' v-html='epins_quantity'></em> Unit(s)</h4>
        
        
            <div class='container'>
              <!-- <button @click="printRechargePins"  style='margin-bottom: 30px;'  class='btn btn-success' >Print E-Pins</button> -->
              <div class='row' style='margin-bottom: 5px;'>
        
                <h4 class='col-12' v-html="pin"></h4>
        
              </div>
            </div>
        
          </div>
        </CardBox>
      </div>


      <div v-else>
        <CardBox isForm @submit.prevent="validateBuyReloadlyAirtimeRequest" class="">
          
          
          

          <h4 class="subhead my-5" >Enter Amount</h4>
          
          <FormField  class="" label="">
            <FormControl v-model="reloadly_airtime_request.amount" :error="reloadly_airtime_request.errors.amount"  :icon="mdiCurrencyNgn" type="number" placeholder="In Naira" />
          </FormField>

          <h4 class="subhead my-5" >Enter Phone Number</h4>
          
          

          <FormField class="" label="">
            <FormControl v-model="reloadly_airtime_request.phone_number" :error="reloadly_airtime_request.errors.phone_number"
              :icon="mdiPhone" type="text" placeholder="e.g 08127027321" />
          </FormField>

        
          <button :class="reloadly_airtime_request.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="btn_hovered = false"
            @mouseover="btn_hovered = true" type="submit" class="app-form-button mt-9 text-left pl-6">
            Continue
            <img v-if="reloadly_airtime_request.processing" class="inline-block w-7 h-6 float-right"
              :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
          </button>
        </CardBox>
      </div>

      

      <CardBoxModal v-model="showPreviewTransactionModal" button="danger" buttonLabel="Close" :title="`Preview Transaction`">
        <div class="">
          <div class="text-center">
            <p>Kindly confirm that the details you entered are valid before clicking the "Confirm" button.</p>
          
          </div>
          
          <div class="">
            <table class="table table-bordered mt-4">
              <tbody>
                <tr v-if="buy_airtime_request.recharge_type == 'epin'">
                  <td>QUANTITY</td>
                  <td><em v-html="buy_airtime_request.quantity + ' Unit(s)'"
                      class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>AMOUNT</td>
                  <td><em v-html="`₦${mainStore.addCommas(buy_airtime_request.amount)}`" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>PHONE</td>
                  <td><em v-html="buy_airtime_request.phone_number" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>NETWORK</td>
                  <td><em class="text-primary">{{buy_airtime_request.network}}</em></td>
                </tr>
                <tr>
                  <td>RECHARGE TYPE</td>
                  <td><em class="text-primary">{{buy_airtime_request.recharge_type}}</em></td>
                </tr>
                <tr>
                  <td>PAYABLE</td>
                  <td><em v-html="`₦${mainStore.addCommas(buy_airtime_request.payable)}`" class="text-primary"></em></td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="justify-content-center text-center">
            
            <BaseButton @click="confirmAndProceedWithTransaction"  class="" label="Confirm" color="success"
              rounded />
            <br>
            <p class="text-red-500 cursor-pointer"  @click="showPreviewTransactionModal = false">Cancel and return</p>
          </div>
        </div>
      
      </CardBoxModal>

      <CardBoxModal v-model="showPreviewReloadlyTransactionModal" button="danger" buttonLabel="Close" :title="`Preview Transaction`">
        <div class="">
          <div class="text-center">
            <p>Kindly confirm that the details you entered are valid before clicking the "Confirm" button.</p>
      
          </div>
          <div class="overflow-x-hidden max-h-[350px]">
            <table class="table table-bordered mt-4">
              <tbody>
                
                <tr>
                  <td>AMOUNT</td>
                  <td><em v-html="`₦${mainStore.addCommas(reloadly_airtime_request.amount)}`" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>PHONE</td>
                  <td><em v-html="reloadly_airtime_request.phone_number" class="text-primary"></em></td>
                </tr>
                
               
                <tr>
                  <td>PAYABLE</td>
                  <td><em v-html="`₦${mainStore.addCommas(reloadly_airtime_request.payable)}`" class="text-primary"></em></td>
                </tr>
              </tbody>
            </table>
          </div>
      
          <div class="justify-content-center text-center">
      
            <BaseButton @click="confirmAndProceedWithReloadlyTransaction" class="" label="Confirm" color="success" rounded />
            <br>
            <p class="text-red-500 cursor-pointer" @click="showPreviewReloadlyTransactionModal = false">Cancel and return</p>
          </div>
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
const amounts = ref([100,200,500,1000,2000,5000,10000]);
      
const show_other_overlay = ref(false);
const epin_checkbox_checked = ref(false);
const epins_received = ref(false);
const epin_card_title = ref('');
const epins = ref([]);
const epins_json = ref('');
const epins_amount = ref('');
const epins_quantity = ref('');
const pin = ref('');
const cashback_text = ref('Note: 2% Cashback On All Transactions');

const showPreviewTransactionModal = ref(false);
const showPreviewReloadlyTransactionModal = ref(false);

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const user = props.value?.auth.user;
console.log(user)


const useSearchBtn = mainStore.useSearchBtn;


const lengthOptions = ref([
  10,
  20,
  50,
  100
]);



const buy_airtime_request = useForm({
  network: "mtn",
  selected_amount: 100,
  entered_amount: "100",
  phone_number: null,
  amount: null,
  recharge_type: "normal",
  quantity: 1,
  payable: null,
  epin_check: false,
  airtime_bonus: false,
});

const reloadly_airtime_request = useForm({
  amount: null,
  phone_number: null,
  payable: null,
});

console.log(epin_checkbox_checked.value)

const submit = () => {

};

const confirmAndProceedWithReloadlyTransaction = () => {
  
  var phone_number = reloadly_airtime_request.phone_number;
  var amount = reloadly_airtime_request.amount;
  


  reloadly_airtime_request.post(route('reloadly_recharge_request'), {
    preserveScroll: true,
    onSuccess: (page) => {
      console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.order_id !== "") {
        var order_id = response.order_id;

        Swal.fire({
          title: 'Info',
          icon: 'info',
          html: `You Have Successfully Credited <em class='text-primary'>${phone_number}</em> With Airtime Worth ₦${mainStore.addCommas(amount)}. Note You Have Been Debited Of ₦${mainStore.addCommas(amount)}. The Order Id For This Transaction Is <em class='text-primary'>"${order_id}</em>`,
          allowEscapeKey: false,
          allowOutsideClick: false,

        }).then((result) => {
          Inertia.visit(route('airtime_topup'));

        });

      } else if (response.invalid_amount) {
        Swal.fire({
          title: 'Ooops!',
          html: "Invalid Amount Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        });
      } else if (response.invalid_recipient) {
        Swal.fire({
          title: 'Ooops!',
          html: "Invalid Mobile Number Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        });
      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops!',
          html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
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


};

const validateBuyReloadlyAirtimeRequest = () => {
  
  var amount = reloadly_airtime_request.amount;
  var phone_number = reloadly_airtime_request.phone_number;

    
      

  if (phone_number != null && phone_number != 0 && mainStore.isNumeric(phone_number)) {
    
    reloadly_airtime_request.payable = amount;
    showPreviewReloadlyTransactionModal.value = true
      
  } else {

    Swal.fire({
      title: 'Error',
      html: "Phone Number Entered Is Not Valid. Please Enter A Valid One",
      icon: 'error'
    });
  }
   
};

const proceedWithEpinAirtimeRequest = () => {
  
  var network = buy_airtime_request.network;
  var selected_amount = buy_airtime_request.selected_amount;
  var entered_amount = buy_airtime_request.entered_amount;
  var phone_number = buy_airtime_request.phone_number;
  var amount = buy_airtime_request.amount;
  var recharge_type = buy_airtime_request.recharge_type;
  var quantity = buy_airtime_request.quantity;


  buy_airtime_request.post(route('generate_vtu_epin'), {
    preserveScroll: true,
    onSuccess: (page) => {
      // console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.amount != "" && response.pin != "") {


        epins_amount.value = response.amount;
        epins_quantity.value = quantity;
        pin.value = response.pin;

        epins_received.value = true;
        epin_card_title.value= "E-pin For " + network + " Network";
      } else if (response.no_available_epin) {
        Swal.fire({
          title: 'Ooops!',
          html: "No Available E-PIN For The Parameters You Selected Currently. Please Try Again Later.",
          icon: 'error'
        });
      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops!',
          html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
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

};

const proceedWithNormalAirtimeRequest = () => {
  
  var network = buy_airtime_request.network;
  var selected_amount = buy_airtime_request.selected_amount;
  var entered_amount = buy_airtime_request.entered_amount;
  var phone_number = buy_airtime_request.phone_number;
  var amount = buy_airtime_request.amount;
  var recharge_type = buy_airtime_request.recharge_type;


  buy_airtime_request.post(route('normal_airtime_recharge_request'), {
    preserveScroll: true,
    onSuccess: (page) => {
      console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.order_id !== "") {
        var order_id = response.order_id;

        Swal.fire({
          title: 'Info',
          icon: 'info',
          html: `You Have Successfully Credited <em class='text-primary'>${phone_number}</em> With Airtime Worth ₦${mainStore.addCommas(amount)} On <span class='capitalize'>${network}</span> Network. Note You Have Been Debited Of ₦${mainStore.addCommas(amount)}. The Order Id For This Transaction Is <em class='text-primary'>"${order_id}</em>`,
          allowEscapeKey: false,
          allowOutsideClick: false,

        }).then((result) => {
          Inertia.visit(route('airtime_topup'));

        });

      } else if (response.invalid_amount) {
        Swal.fire({
          title: 'Ooops!',
          html: "Invalid Amount Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        });
      } else if (response.invalid_recipient) {
        Swal.fire({
          title: 'Ooops!',
          html: "Invalid Mobile Number Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        });
      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops!',
          html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
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

};

const proceedWithComboRequest = () => {
  
  var network = buy_airtime_request.network;
  var selected_amount = buy_airtime_request.selected_amount;
  var entered_amount = buy_airtime_request.entered_amount;
  var phone_number = buy_airtime_request.phone_number;
  var amount = buy_airtime_request.amount;
  var recharge_type = buy_airtime_request.recharge_type;

  
  buy_airtime_request.post(route('request_9mobile_combo_recharge'), {
    preserveScroll: true,
    onSuccess: (page) => {
      console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.success) {
       

        Swal.fire({
          title: 'Info',
          icon: 'info',
          html: `You Have Successfully Credited <em class='text-primary'>${phone_number}</em> With Airtime Worth ₦${mainStore.addCommas(amount)} On <span class='capitalize'>${network}</span> Network. Note You Have Been Debited Of ₦${mainStore.addCommas(amount)}.`,
          allowEscapeKey: false,
          allowOutsideClick: false,
          
        }).then((result) => {
          Inertia.visit(route('airtime_topup'));
          
        });

      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops!',
          html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
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

};

const confirmAndProceedWithTransaction = () => {
  
  var network = buy_airtime_request.network;
  var selected_amount = buy_airtime_request.selected_amount;
  var entered_amount = buy_airtime_request.entered_amount;
  var phone_number = buy_airtime_request.phone_number;
  var amount = buy_airtime_request.amount;
  var recharge_type = buy_airtime_request.recharge_type;



  
  showPreviewTransactionModal.value = false
  if (network == "9mobile" && recharge_type == "combo") {
    proceedWithComboRequest();
  } else if (recharge_type == "normal") {
    proceedWithNormalAirtimeRequest();
  } else if (recharge_type == "epin" && (network == "mtn" || network == "airtel" || network == "glo" || network == "9mobile")) {
    proceedWithEpinAirtimeRequest();
  }
}

const proceedToSubmitBuyAirtimeRequest = () => {
  
  var network = buy_airtime_request.network;
  var selected_amount = buy_airtime_request.selected_amount;
  var entered_amount = buy_airtime_request.entered_amount;
  var phone_number = buy_airtime_request.phone_number;
  var amount = buy_airtime_request.amount;


  if (network == "9mobile" && amount >= 1000) {

    Swal.fire({
      title: 'Choose Option',
      icon: 'info',
      html: `Choose Recharge Option: `,
      showCancelButton: false,
      showDenyButton: true,
      confirmButtonColor: '#3085d6',
      denyButtonColor: '#4caf50',
      confirmButtonText: 'Normal Recharge',
      denyButtonText: "Combo Recharge"
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        
        showPreviewTransactionModal.value = true;
      } else if (result.isDenied) {
        buy_airtime_request.recharge_type = "combo";
        
        showPreviewTransactionModal.value = true;
      }
    });
  } else {
    
    showPreviewTransactionModal.value = true;
  }
};

const validateBuyAirtimeRequest = () => {
  
  var network = buy_airtime_request.network;
  var selected_amount = buy_airtime_request.selected_amount;
  var entered_amount = buy_airtime_request.entered_amount;
  var phone_number = buy_airtime_request.phone_number;
  
  var quantity = buy_airtime_request.quantity;


  if (!epin_checkbox_checked.value) {
    buy_airtime_request.recharge_type = "normal";
    if (network == "mtn" || network == "glo" || network == "airtel" || network == "9mobile") {
      if (amounts.value.includes(selected_amount)) {

        if (phone_number != null && phone_number != 0 && mainStore.isNumeric(phone_number)) {
          if (entered_amount == null || entered_amount == "") {
            buy_airtime_request.amount = selected_amount;
            buy_airtime_request.payable = selected_amount;
            proceedToSubmitBuyAirtimeRequest();
          } else {
            if (entered_amount >= 100 && entered_amount <= 50000) {
              buy_airtime_request.amount = entered_amount;
              buy_airtime_request.payable = entered_amount;
              proceedToSubmitBuyAirtimeRequest();
            } else {
             
              Swal.fire({
                title: 'Error',
                html: "Amount Entered Is Not Valid. Amount Must Be Between ₦100 And ₦50,000",
                icon: 'error'
              });
            }
          }
        } else {
          
          Swal.fire({
            title: 'Error',
            html: "Phone Number Entered Is Not Valid. Please Enter A Valid One",
            icon: 'error'
          });
        }
      } else {
        Swal.fire({
          title: 'Error',
          html: "Invalid Amount Selected. Please Select A Valid One",
          icon: 'error'
        });
      }
    } else {
      
      Swal.fire({
        title: 'Error',
        html: "Invalid Network Selected. Please Select A Valid One",
        icon: 'error'
      });
    }
  } else {
    buy_airtime_request.recharge_type = "epin";
    if (network == "mtn" || network == "airtel" || network == "glo" || network == "9mobile") {
      if (amounts.value.includes(selected_amount)) {

        if (quantity != null) {
          if (quantity >= 1 && quantity <= 20) {

            buy_airtime_request.amount = selected_amount;
            var discount = 0.00;

            if (network == "mtn") {

              discount = 0.01;
            } else if (network == "glo") {

              discount = 0.02;
            } else if (network == "9mobile") {

              discount = 0.02;
            } else if (network == "airtel") {

              discount = 0.02;
            }

            var payable = selected_amount - (discount * selected_amount);
            payable = payable * quantity;
            buy_airtime_request.payable = payable;
            proceedToSubmitBuyAirtimeRequest();

          } else {

            Swal.fire({
              title: 'Error',
              html: "Quantity Entered Must Be Between 1 And 20.",
              icon: 'error'
            });
          }
        } else {
          
          Swal.fire({
            title: 'Error',
            html: "Quantity Entered Is Not Valid. Please Enter A Valid One",
            icon: 'error'
          });
        }
      } else {
        Swal.fire({
          title: 'Error',
          html: "Invalid Amount Selected. Please Select A Valid One",
          icon: 'error'
        });
      }
    } else {
      
      Swal.fire({
        title: 'Error',
        html: "Invalid Network Selected. Please Select A Valid One",
        icon: 'error'
      });
    }
  }
};

const selectAmount = (amount) => {
  
  buy_airtime_request.selected_amount = amount;
  buy_airtime_request.entered_amount = amount.toString();
}

const selectNetwork = (network) => {
  
  if (epin_checkbox_checked.value) {
    
    if (network == "mtn") {
      cashback_text.value = "Note: 1% Cashback On All Transactions"
    } else {
      cashback_text.value = "Note: 2% Cashback On All Transactions"
    }
    buy_airtime_request.network = network;
    // }
  } else {
    buy_airtime_request.network = network;
  }
};


</script>

