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

    <Head :title="`Electricity Topup`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiLightningBoltOutline" :title="`Electricity Topup`" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->

        <BaseButton :href="route('user_vtu_history_page') + '?length=10&type=electricity&isDirty=true&__rememberable=true'"
          :icon="mdiHistory" label="View His." color="success" rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->



      <CardBox isForm @submit.prevent="verifyMeterDetails" class="">

        <h4 class="subhead">Select Disco</h4>
        
        <div class="mx-2 my-9">
          <div class="grid grid-cols-12 gap-3">
        
            <div v-for="(disco,index) in discos" :key="index" :class="outputDiscoRowClasses(index)"
              @click="selectedDisco(index)">
              <div class="card-body text-center">
                <img :src="'/images/'+ disco.image" :alt="disco.name" class="col-12">
                <span v-html="disco.name"></span>
              </div>
        
            </div>
        
        
        
          </div>
          <div class="row">
            <div v-if="buy_electricity_request.errors.disco" class="form-error">{{ buy_electricity_request.errors.disco }}</div>
          </div>
        </div>

        <h4 class="subhead my-5">Meter Type</h4>
        
        <FormField class="" label="">
          
          <FormCheckRadioGroup v-model="buy_electricity_request.meter_type" :errors="buy_electricity_request.errors.meter_type" name="meter_type" :options="meterTypeOptions" type="radio" />
        </FormField>

        <h4 class="subhead my-5">Meter Number</h4>

        <FormField class="" label="">
          <FormControl v-model="buy_electricity_request.meter_number" :error="buy_electricity_request.errors.meter_number"
             type="text" id="meter_number" placeholder="e.g 45062872259" />
        </FormField>

        <h4 class="subhead my-5">Amount</h4>
        
        <FormField class="" label="">
          <FormControl v-model="buy_electricity_request.amount" :error="buy_electricity_request.errors.amount"
            type="number" :icon="mdiCurrencyNgn" id="amount" placeholder="In Naira(₦)" />
        </FormField>

        <h4 class="subhead my-5">Mobile Number</h4>
        
        <FormField class="" label="">
          <FormControl v-model="buy_electricity_request.mobile_number" :error="buy_electricity_request.errors.mobile_number"
            type="text" :icon="mdiPhone" id="mobile_number" placeholder="e.g 08127027321" />
        </FormField>

        <h4 class="subhead my-5">Email Address</h4>
        
        <FormField class="" label="">
          <FormControl v-model="buy_electricity_request.email" :error="buy_electricity_request.errors.email"
            type="email" :icon="mdiGmail" id="email" placeholder="e.g ikechukwunwogo@gmail.com" />
        </FormField>

        <div id="sms-check-div" class="mt-[15px] col-span-6">
        
          <input type="checkbox" class="login-checkbox" id="sms-check"
            v-model="buy_electricity_request.sms_check" />
          <label for="sms-check" class="login-checkbox-label ">Send Token As SMS?</label>
        </div>

        

        <p class="text-sm my-4 font-semibold"><em class="text-primary">Note: SMS Cost ₦5.00</em></p>


        <button :class="buy_electricity_request.processing ? 'opacity-80 cursor-not-allowed' : ''"
          @mouseleave="btn_hovered = false" @mouseover="btn_hovered = true" type="submit"
          class="app-form-button mt-9 text-left pl-6">
          Continue
          <img v-if="buy_electricity_request.processing" class="inline-block w-7 h-6 float-right"
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
            <table class="table table-bordered ">
              <tbody v-if="buy_electricity_request.disco != null">
                <tr>
                  <td>DISCO</td>
                  <td><em v-html="buy_electricity_request.disco" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>METER TYPE</td>
                  <td><em v-html="buy_electricity_request.meter_type" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>METER NUMBER</td>
                  <td><em v-html="buy_electricity_request.meter_number" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>CUSTOMER NAME</td>
                  <td><em v-html="buy_electricity_request.customer_name" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>AMOUNT</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_electricity_request.amount)" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>MOBILE NUMBER</td>
                  <td><em v-html="buy_electricity_request.mobile_number" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>EMAIL</td>
                  <td><em v-html="buy_electricity_request.email" class="text-primary"></em></td>
                </tr>
                
                
                <tr>
                  <td>PAYABLE</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_electricity_request.payable)" class="text-primary"></em></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="justify-content-center text-center">

            <BaseButton @click="confirmAndProceedWithTransaction" class="" label="Confirm" color="success" rounded />
            <br>
            <p class="text-red-500 cursor-pointer" @click="showPreviewTransactionModal = false">Cancel and return</p>
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
  mdiCellphoneDock,
  mdiAccountCog,
  mdiAccountCash,
  mdiCurrencyNgn,
  mdiPhone,
  mdiGmail,
  mdiLightningBoltOutline,
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
import FloatingTextButton from "@/components/FloatingTextButton.vue";
import FormLoaderDark from '@/Loaders/form_loader_dark.gif'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'

import { useMainStore } from "@/stores/main";
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { computed, ref, reactive, watch } from 'vue'
import axios from "axios";
import _ from 'lodash';

const page = usePage();
const props = page.props;
const user = props.value?.user;
const test_plans = props.value?.test_plans;
const mainStore = useMainStore();

const btn_hovered = ref(false);

const meterTypeOptions = ref({
  prepaid: 'prepaid',
  postpaid: 'postpaid',

});


const show_other_overlay = ref(false);

const discos = ref([
  {
    name: "Ikeja Electric",
    type: 'ikeja',
    image: 'ikeja_logo.png',
  },
  {
    name: "Eko Electric",
    type: 'eko',
    image: 'eko_logo.jpg',
  },
  {
    name: "Abuja Electric",
    type: 'abuja',
    image: 'abuja_logo.jpg',
  },
  {
    name: "Kano Electric",
    type: 'kano',
    image: 'kano_logo.png',
  },
  {
    name: "Jos Electric",
    type: 'jos',
    image: 'jos_logo.png',
  },
  {
    name: "Ibadan Electric",
    type: 'ibadan',
    image: 'ibadan_logo.png',
  },
  {
    name: "ENUGU Electric",
    type: 'enugu',
    image: 'enugu_logo.jpg',
  },
  {
    name: "Port Harcourt Electric",
    type: 'phc',
    image: 'PHEDC.jpg',
  },
  {
    name: "Kaduna Electric",
    type: 'kaduna',
    image: 'kaduna-electric.jpg',
  },
]);



    
     
// console.log(data_plans.value)

const showPreviewTransactionModal = ref(false);

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)



const useSearchBtn = mainStore.useSearchBtn;


const lengthOptions = ref([
  10,
  20,
  50,
  100
]);



const buy_electricity_request = useForm({
  selected_disco_index: null,
  disco: null,
  meter_type: meterTypeOptions.value.prepaid,
  meter_number: null,
  amount: "",
  mobile_number: "",
  email: "",
  sms_check: false,
  customer_name: '',
  productCode: "",
  productToken: "",
  use_payscribe: false,
  payable: null,

});
const check_disco_availability_request = useForm({
  disco: "",

});

const submit = () => {

};

const proceedWithBuyPower = () => {
  
  var selected_disco_index = buy_electricity_request.selected_disco_index;
  var disco = buy_electricity_request.disco;
  var meter_type = buy_electricity_request.meter_type;
  var meter_number = buy_electricity_request.meter_number;
  var amount = buy_electricity_request.amount;
  var mobile_number = buy_electricity_request.mobile_number;

  var email = buy_electricity_request.email;
  var sms_check = buy_electricity_request.sms_check;
  var customer_name = buy_electricity_request.customer_name;
  var productCode = buy_electricity_request.productCode;
  var productToken = buy_electricity_request.productToken;
  var use_payscribe = buy_electricity_request.use_payscribe;


  console.log(selected_disco_index)
  console.log(disco)
  console.log(meter_type)
  console.log(meter_number)
  console.log(amount)
  console.log(mobile_number)

  console.log(email)
  console.log(sms_check)
  console.log(customer_name)
  console.log(productCode)
  console.log(productToken)
  console.log(use_payscribe)

   
  buy_electricity_request.post(route('purchase_electricity_with_buypower'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.order_id !== "") {
        var order_id = response.order_id;
        var transaction_pending = response.transaction_pending;

        var text = "You Have Successfully Credited Your " + meter_type + " <em class='text-primary'>" + discos.value[selected_disco_index].name + "</em> With Meter Number: <em class='text-primary'>" + meter_number + "</em> Account With ₦" + mainStore.addCommas(amount) + ".";
        if (transaction_pending) {


          if (meter_type == "prepaid") {
            text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction And Your Meter Token, Track This Transaction From The Recharge Vtu Transaction History Page";
          } else {
            text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";
          }

        } else {

          if (meter_type == "prepaid") {
            if (response.metertoken != "") {
              var metertoken = response.metertoken;
              text += " Your Meter Token Is: <em class='text-primary'>" + metertoken + "</em>";
            } else {
              text += " Your Meter Token Will be Sent To Your Email And Notification Panel Soon.";
            }
          }
        }

        Swal.fire({
          title: 'Info',
          html: text,
          icon: 'info',
          confirmButtonColor: '#3085d6',
          allowEscapeKey: false,
          allowOutsideClick: false,
        }).then((result) => {
          Inertia.visit(route('electricity_topup'));

        });
      } else if (response.invalid_meterno) {

        Swal.fire({
          title: 'Ooops!',
          html: "An invalid Meter number was entered. Your Money Has Been Refunded",
          icon: 'error'
        });
      } else if (response.meter_type_not_available) {

        Swal.fire({
          title: 'Ooops!',
          html: "Selected MeterType is not currently available. Your Money Has Been Refunded",
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

const proceedWithPayscribe = () => {
  
  var selected_disco_index = buy_electricity_request.selected_disco_index;
  var disco = buy_electricity_request.disco;
  var meter_type = buy_electricity_request.meter_type;
  var meter_number = buy_electricity_request.meter_number;
  var amount = buy_electricity_request.amount;
  var mobile_number = buy_electricity_request.mobile_number;

  var email = buy_electricity_request.email;
  var sms_check = buy_electricity_request.sms_check;
  var customer_name = buy_electricity_request.customer_name;
  var productCode = buy_electricity_request.productCode;
  var productToken = buy_electricity_request.productToken;
  var use_payscribe = buy_electricity_request.use_payscribe;


  console.log(selected_disco_index)
  console.log(disco)
  console.log(meter_type)
  console.log(meter_number)
  console.log(amount)
  console.log(mobile_number)

  console.log(email)
  console.log(sms_check)
  console.log(customer_name)
  console.log(productCode)
  console.log(productToken)
  console.log(use_payscribe)

  buy_electricity_request.post(route('purchase_electricity_with_payscribe'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.order_id !== "") {
        var order_id = response.order_id;
        var transaction_pending = response.transaction_pending;

        var text = "You Have Successfully Credited Your " + meter_type + " <em class='text-primary'>" + discos.value[selected_disco_index].name + "</em> With Meter Number: <em class='text-primary'>" + meter_number + "</em> Account With ₦" + mainStore.addCommas(amount) + ".";
        if (transaction_pending) {


          if (meter_type == "prepaid") {
            text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction And Your Meter Token, Track This Transaction From The Recharge Vtu Transaction History Page";
          } else {
            text += " Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";
          }

        } else {

          if (meter_type == "prepaid") {
            if (response.metertoken != "") {
              var metertoken = response.metertoken;
              text += " Your Meter Token Is: <em class='text-primary'>" + metertoken + "</em>";
            } else {
              text += " Your Meter Token Will be Sent To Your Email And Notification Panel Soon.";
            }
          }
        }

        Swal.fire({
          title: 'Info',
          html: text,
          icon: 'info',
          confirmButtonColor: '#3085d6',
          allowEscapeKey: false,
          allowOutsideClick: false,
        }).then((result) => {
          Inertia.visit(route('electricity_topup'));

        });
      } else if (response.invalid_meterno) {

        Swal.fire({
          title: 'Ooops!',
          html: "An invalid Meter number was entered. Your Money Has Been Refunded",
          icon: 'error'
        });
      } else if (response.meter_type_not_available) {

        Swal.fire({
          title: 'Ooops!',
          html: "Selected MeterType is not currently available. Your Money Has Been Refunded",
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

const confirmAndProceedWithTransaction = () => {
  
  var selected_disco_index = buy_electricity_request.selected_disco_index;
  var disco = buy_electricity_request.disco;
  var meter_type = buy_electricity_request.meter_type;
  var meter_number = buy_electricity_request.meter_number;
  var amount = buy_electricity_request.amount;
  var mobile_number = buy_electricity_request.mobile_number;

  var email = buy_electricity_request.email;
  var sms_check = buy_electricity_request.sms_check;
  var customer_name = buy_electricity_request.customer_name;
  var productCode = buy_electricity_request.productCode;
  var productToken = buy_electricity_request.productToken;
  var use_payscribe = buy_electricity_request.use_payscribe;


  console.log(selected_disco_index)
  console.log(disco)
  console.log(meter_type)
  console.log(meter_number)
  console.log(amount)
  console.log(mobile_number)

  console.log(email)
  console.log(sms_check)
  console.log(customer_name)
  console.log(productCode)
  console.log(productToken)
  console.log(use_payscribe)

  if (disco != null) {

    showPreviewTransactionModal.value = true;
    // if(disco == "eko"){
    //   self.proceedWithGsubz();
    // }else{
    if (use_payscribe) {
      proceedWithPayscribe();
    } else {
      proceedWithBuyPower();
    }
    // }

  } else {
    Swal.fire({
      title: 'Error!',
      html: "No Disco Was Selected. Please Select A Disco To Proceed.",
      icon: 'error'
    });
  }
};

const verifyMeterDetails = () => {
  
  var selected_disco_index = buy_electricity_request.selected_disco_index;
  var disco = buy_electricity_request.disco;
  var meter_type = buy_electricity_request.meter_type;
  var meter_number = buy_electricity_request.meter_number;
  var amount = buy_electricity_request.amount;
  var mobile_number = buy_electricity_request.mobile_number;

  var email = buy_electricity_request.email;
  var sms_check = buy_electricity_request.sms_check;
  var customer_name = buy_electricity_request.customer_name;
  var productCode = buy_electricity_request.productCode;
  var productToken = buy_electricity_request.productToken;
  var use_payscribe = buy_electricity_request.use_payscribe;


  console.log(selected_disco_index)
  console.log(disco)
  console.log(meter_type)
  console.log(meter_number)
  console.log(amount)
  console.log(mobile_number)

  console.log(email)
  console.log(sms_check)
  console.log(customer_name)
  console.log(productCode)
  console.log(productToken)
  console.log(use_payscribe)

  if (disco != null) {

    
    buy_electricity_request.post(route('validate_meter_number_disco'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        console.log(response)

        if (response.success && response.customer_name !== "") {
          var proceed = false;
          buy_electricity_request.customer_name = response.customer_name;
          buy_electricity_request.use_payscribe = response.use_payscribe;
          buy_electricity_request.productCode = response.productCode;
          buy_electricity_request.productToken = response.productToken;

          if (buy_electricity_request.use_payscribe) {
            proceed = true;

          } else {
            // if (amount >= 1000) {
              proceed = true;
            // }
          }

          if (proceed) {
            Swal.fire({
              title: 'Info',
              icon: 'info',
              html: `Is This Your Name ? <br> <em class='text-center text-primary'>${buy_electricity_request.customer_name}</em>`,
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes Proceed!',
              cancelButtonText: "No"
            }).then((result) => {
              /* Read more about isConfirmed, isDenied below */
              if (buy_electricity_request.sms_check) {
                buy_electricity_request.payable = Number(amount) + 5;
              } else {
                buy_electricity_request.payable = amount;
              }
              
              showPreviewTransactionModal.value = true;
            });
          } else {
            Swal.fire({
              title: 'Error!',
              html: "Amount Must Be At Least 1000",
              icon: 'error'
            });
          }
        } else if (response.invalid_user) {
          
          Swal.fire({
            title: 'Error!',
            html: "The Details Entered Were Invalid. Please Try Again.",
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
      title: 'Error!',
      html: "No Disco Was Selected. Please Select A Disco To Proceed.",
      icon: 'error'
    });
  }
};

const selectedDisco = (index) => {
  
  if (buy_electricity_request.selected_disco_index != index) {
    check_disco_availability_request.disco = discos.value[index].type;
    
    check_disco_availability_request.post(route('check_if_disco_is_available'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        console.log(response)

        if (response.success) {
          buy_electricity_request.selected_disco_index = index;
          buy_electricity_request.disco = discos.value[index].type;
        } else {
          Swal.fire({
            title: 'Error',
            html: "<em class='text-primary'>" + discos.value[index].name + "</em> Disco Is Not Available At The Moment",
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

const outputDiscoRowClasses = (index) => {
  
  var str = "";
  if (buy_electricity_request.selected_disco_index == index) {
    str += 'col-span-3 sm:col-span-1 col-3 col-sm-1 card disco-card selected';
  } else {
    str += 'col-span-3 sm:col-span-1 col-3 col-sm-1 card disco-card';
  }

  if (index != 0) {
    str += ' offset-1';
  }
  return str;
}

</script>

