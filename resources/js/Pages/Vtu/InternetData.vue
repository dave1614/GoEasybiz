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

    <Head :title="`Internet Data`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiCellphoneDock " :title="`Internet Data`" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->

        <BaseButton :href="route('user_vtu_history_page') + '?length=10&type=data&isDirty=true&__rememberable=true'"
          :icon="mdiHistory" label="View His." color="success" rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->



      <CardBox isForm @submit.prevent="validateBuyDataRequest" class="">
        <h4 class="subhead">Select Network</h4>

        <div class="mx-2 my-9">
          <div class="grid grid-cols-12 gap-1">

            <div class="col-span-2 sm:col-span-1 card network-card"
              :class="buy_data_request.network == 'mtn' ? 'selected' : ''" @click="selectNetwork('mtn')">
              <div class="card-body text-center">
                <img src="/images/mtn_logo.png" alt="MTN" class="">
                <!-- <p>MTN</p> -->
              </div>

            </div>

            <div class="col-span-1">

            </div>

            <div class="col-span-2 sm:col-span-1 card network-card"
              :class="buy_data_request.network == 'glo' ? 'selected' : ''" @click="selectNetwork('glo')">
              <div class="card-body text-center">
                <img src="/images/glo_logo.jpg" alt="GLO" class="">
                <!-- <p>MTN</p> -->
              </div>
            </div>

            <div class="col-span-1">

            </div>

            <div class="col-span-2 sm:col-span-1 card network-card"
              :class="buy_data_request.network == 'airtel' ? 'selected' : ''" @click="selectNetwork('airtel')">
              <div class="card-body text-center">
                <img src="/images/airtel_logo.png" alt="Airtel" class="">
                <!-- <p>MTN</p> -->
              </div>
            </div>



            <div class="col-span-1">

            </div>

            <div class="col-span-2 sm:col-span-1 card network-card"
              :class="buy_data_request.network == '9mobile' ? 'selected' : ''" @click="selectNetwork('9mobile')">
              <div class="card-body text-center">
                <img src="/images/9mobile-1.png" alt="9mobile" class="">
                <!-- <p>MTN</p> -->
              </div>
            </div>

          </div>
          <div class="row">
            <div v-if="buy_data_request.errors.network" class="form-error">{{ buy_data_request.errors.network }}
            </div>
          </div>
        </div>

        
        <h4 class="subhead my-5">Enter Phone Number</h4>

        <FormField class="" label="">
          <FormControl v-model="buy_data_request.phone_number" :error="buy_data_request.errors.phone_number"
            :icon="mdiPhone" type="text" id="phone_number" placeholder="e.g 08127027321" />
        </FormField>

        <h4 class="text-xl font-bold mt-2 mb-6" v-if="data_plans.length > 0">Select Data Plan</h4>
        <!-- <h4 class="subhead text-warning" v-else>Data Plans Could Not Be Loaded.</h4> -->
        
        <div class="container" v-if="data_plans.length > 0">
          <div @click="selectDataPlan(plan.index - 1)"
            class="data-plans-card card"
            :class="buy_data_request.selected_plan_index == (plan.index - 1) ? 'selected' : '' "
            v-for="plan in data_plans" :key="plan.index">
            <div class="grid grid-cols-12 gap-6">
        
              <div class="col-span-1 sm:col-span-3 col-3 col-sm-1">
                <span v-html="plan.index + '.'"></span>
              </div>
        
              <div class="col-span-6 sm:col-span-4 col-4 col-sm-6">
                <span v-html="plan.product_name"></span>
              </div>
        
              <div class="col-span-5">
                <span v-html="'₦' + mainStore.addCommas(plan.amount)"></span>
              </div>
        
            
            </div>
          </div>
        </div>



        <!-- <button :class="buy_data_request.processing ? 'opacity-80 cursor-not-allowed' : ''"
          @mouseleave="btn_hovered = false" @mouseover="btn_hovered = true" type="submit"
          class="app-form-button mt-9 text-left pl-6">
          Continue
          <img v-if="buy_data_request.processing" class="inline-block w-7 h-6 float-right"
            :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
        </button> -->
      </CardBox>

      <CardBoxModal v-model="showPreviewTransactionModal" button="danger" buttonLabel="Close"
        :title="`Preview Transaction`">
        <div class="">
          <div class="text-center">
            <p>Kindly confirm that the details you entered are valid before clicking the "Confirm" button.</p>

          </div>
          
          <!-- <div class="overflow-x-hidden max-h-[270px]"> -->
          <div class="">
            <table class="table table-bordered ">
              <tbody v-if="buy_data_request.selected_plan.length != {}">
                <tr>
                  <td>NETWORK</td>
                  <td><em v-html="buy_data_request.selected_plan.network" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>DATA PLAN</td>
                  <td><em v-html="buy_data_request.selected_plan.product_name" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>AMOUNT</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_data_request.selected_plan.amount)" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>PHONE</td>
                  <td><em v-html="buy_data_request.phone_number" class="text-primary"></em></td>
                </tr>
            
                <tr v-if="buy_data_request.selected_plan.sub_type == 'combo'">
                  <td>RECHARGE TYPE</td>
                  <td><em class="text-primary">Combo Recharge</em></td>
                </tr>
                <tr v-else>
                  <td>RECHARGE TYPE</td>
                  <td><em class="text-primary">Normal Recharge</em></td>
                </tr>
                <tr>
                  <td>PAYABLE</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_data_request.selected_plan.amount)" class="text-primary"></em></td>
                </tr>
              </tbody>
            </table>
            <!-- <div class="justify-content-center text-center">
            
              <BaseButton @click="confirmAndProceedWithTransaction" class="" label="Confirm" color="success" rounded />
              <br>
              <p class="text-red-500 cursor-pointer" @click="showPreviewTransactionModal = false">Cancel and return</p>
            </div> -->
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
  <div @click="validateBuyDataRequest" v-if="!buy_data_request.processing">
    <FloatingTextButton :styles="'background: 9124a3;'" :title="'Proceed'">
  
      <!-- <i class="fas fa-arrow-right" style="font-size: 25px; color: #fff;"></i> -->
      <span style="font-size: 18px; font-weight: bold; color: #fff;">Submit</span>
    </FloatingTextButton>
  </div>
</template>
<script setup>
import {
  mdiMonitorCellphone,
  mdiTableBorder,
  mdiTableOff,
  mdiGithub,
  mdiHospitalBuilding,
  mdiClose,
  mdiCellphoneDock ,
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



const show_other_overlay = ref(false);
const data_plans = ref([]);
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



const buy_data_request = useForm({
  network: false,
  selected_plan_index: null,
  phone_number: null,
  selected_plan: {},
  ported: false,
});
// buy_data_request.selected_plan_index = 0

const get_data_plans_request = useForm({
  network: "",
  combo: false,
});


const submit = () => {

};

const proceedWithEminenceDataRequest = () => {

  var selected_plan = buy_data_request.selected_plan;
  var phone_number = buy_data_request.phone_number;
  var sub_type = buy_data_request.selected_plan.sub_type;
  var network = buy_data_request.selected_plan.network;
  var ported = buy_data_request.ported;
  var product_name = buy_data_request.selected_plan.product_name;
  var amount = buy_data_request.selected_plan.amount;

  console.log(selected_plan)
  console.log(phone_number)
  console.log(sub_type)
  console.log(network)
  console.log(ported)

  buy_data_request.post(route('purchase_eminence_data'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)


      if (response.success) {

        var order_id = response.order_id;
        var transaction_pending = response.transaction_pending;

        if (network == "glo" || network == "airtel") {
          var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span class='capitalize'>" + network + "</span> Network Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" + order_id + "</em>.";
        } else {
          var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span class='capitalize'>" + network + "</span> Network Is Successful. Note You Have Been Debited Of ₦" + mainStore.addCommas(amount) + ". The Order Id For This Transaction Is <em class='text-primary'>" + order_id + "</em>.";
        }


        if (transaction_pending) {
          text_html += " Note: This Order Is Currently Pending. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";

        }


        Swal.fire({
          title: 'Info',
          html: text_html,
          icon: 'info',
          confirmButtonColor: '#3085d6',
          allowEscapeKey: false,
          allowOutsideClick: false,
        }).then((result) => {
          Inertia.visit(route('internet_data'));

        });


      } else if (response.invalid_data_plan) {
        Swal.fire({
          title: 'Ooops',
          html: "Invalid Data Plan Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        })
      } else if (response.invalid_recipient) {
        Swal.fire({
          title: 'Ooops',
          html: "Invalid Mobile Number Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        })
      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops',
          html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
          icon: 'error'
        })
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

const proceedWithGsubzDataRequest = () => {

  var selected_plan = buy_data_request.selected_plan;
  var phone_number = buy_data_request.phone_number;
  var sub_type = buy_data_request.selected_plan.sub_type;
  var network = buy_data_request.selected_plan.network;
  var ported = buy_data_request.ported;
  var product_name = buy_data_request.selected_plan.product_name;
  var amount = buy_data_request.selected_plan.amount;

  console.log(selected_plan)
  console.log(phone_number)
  console.log(sub_type)
  console.log(network)
  console.log(ported)

  buy_data_request.post(route('purchase_gsubz_data'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)
    

      if (response.success) {

        var order_id = response.order_id;
        var transaction_pending = response.transaction_pending;

        if (network == "glo" || network == "airtel") {
          var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span class='capitalize'>" + network + "</span> Network Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" + order_id + "</em>.";
        } else {
          var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span class='capitalize'>" + network + "</span> Network Is Successful. Note You Have Been Debited Of ₦" + mainStore.addCommas(amount) + ". The Order Id For This Transaction Is <em class='text-primary'>" + order_id + "</em>.";
        }


        if (transaction_pending) {
          text_html += " Note: This Order Is Currently Pending. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";

        }


        Swal.fire({
          title: 'Info',
          html: text_html,
          icon: 'info',
          confirmButtonColor: '#3085d6',
          allowEscapeKey: false,
          allowOutsideClick: false,
        }).then((result) => {
          Inertia.visit(route('internet_data'));

        });


      } else if (response.invalid_data_plan) {
        Swal.fire({
          title: 'Ooops',
          html: "Invalid Data Plan Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        })
      } else if (response.invalid_recipient) {
        Swal.fire({
          title: 'Ooops',
          html: "Invalid Mobile Number Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        })
      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops',
          html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
          icon: 'error'
        })
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

const proceedWithClubDataRequest = () => {
  

  var selected_plan = buy_data_request.selected_plan;
  var phone_number = buy_data_request.phone_number;
  var sub_type = buy_data_request.selected_plan.sub_type;
  var network = buy_data_request.selected_plan.network;
  var ported = buy_data_request.ported;
  var product_name = buy_data_request.selected_plan.product_name;
  var amount = buy_data_request.selected_plan.amount;

  console.log(selected_plan)
  console.log(phone_number)
  console.log(sub_type)
  console.log(network)
  console.log(ported)
  
  buy_data_request.post(route('purchase_clubkonnect_data'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      

      if (response.success) {

        var order_id = response.order_id;
        var transaction_pending = response.transaction_pending;

        if (network == "glo" || network == "airtel") {
          var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span class='capitalize'>" + network + "</span> Network Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" + order_id + "</em>.";
        } else {
          var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span class='capitalize'>" + network + "</span> Network Is Successful. Note You Have Been Debited Of ₦" + mainStore.addCommas(amount) + ". The Order Id For This Transaction Is <em class='text-primary'>" + order_id + "</em>.";
        }


        if (transaction_pending) {
          text_html += " Note: This Order Is Currently Pending. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";

        }


        Swal.fire({
          title: 'Info',
          html: text_html,
          icon: 'info',
          confirmButtonColor: '#3085d6',
          allowEscapeKey: false,
          allowOutsideClick: false,
        }).then((result) => {
          Inertia.visit(route('internet_data'));

        });


      } else if (response.invalid_data_plan) {
        Swal.fire({
          title: 'Ooops',
          html: "Invalid Data Plan Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        })
      } else if (response.invalid_recipient) {
        Swal.fire({
          title: 'Ooops',
          html: "Invalid Mobile Number Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        })
      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops',
          html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
          icon: 'error'
        })
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

const proceedWithPayscribeDataRequest = () => {
  

  var selected_plan = buy_data_request.selected_plan;
  var phone_number = buy_data_request.phone_number;
  var sub_type = buy_data_request.selected_plan.sub_type;
  var network = buy_data_request.selected_plan.network;
  var ported = buy_data_request.ported;
  var product_name = buy_data_request.selected_plan.product_name;
  var amount = buy_data_request.selected_plan.amount;

  console.log(selected_plan)
  console.log(phone_number)
  console.log(sub_type)
  console.log(network)
  console.log(ported)

  buy_data_request.post(route('purchase_payscribe_data'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success) {

        var order_id = response.order_id;
        var transaction_pending = response.transaction_pending;

        if (network == "glo" || network == "airtel") {
          var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span class='capitalize'>" + network + "</span> Network Is Successful. The Order Id For This Transaction Is <em class='text-primary'>" + order_id + "</em>.";
        } else {
          var text_html = "Your Request To Top Up <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span class='capitalize'>" + network + "</span> Network Is Successful. Note You Have Been Debited Of ₦" + mainStore.addCommas(amount) + ". The Order Id For This Transaction Is <em class='text-primary'>" + order_id + "</em>.";
        }


        if (transaction_pending) {
          text_html += " Note: This Order Is Currently Pending. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page";

        }

        Swal.fire({
          title: 'Info',
          html: text_html,
          icon: 'info',
          confirmButtonColor: '#3085d6',
          allowEscapeKey: false,
          allowOutsideClick: false,
        }).then((result) => {
          Inertia.visit(route('internet_data'));

        });


      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops',
          html: "Invalid Data Plan Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        })
      } else if (response.invalid_recipient) {
        Swal.fire({
          title: 'Ooops',
          html: "Invalid Mobile Number Was Entered. Your Money Has Been Refunded",
          icon: 'error'
        })
      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops',
          html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
          icon: 'error'
        })
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
  var self = this;

  var selected_plan = buy_data_request.selected_plan;
  var phone_number = buy_data_request.phone_number;
  var sub_type = buy_data_request.selected_plan.sub_type;
  var network = buy_data_request.selected_plan.network;
  var ported = buy_data_request.ported;
  var product_name = buy_data_request.selected_plan.product_name;
  var amount = buy_data_request.selected_plan.amount;

  console.log(selected_plan)
  console.log(phone_number)
  console.log(sub_type)
  console.log(network)
  console.log(ported)

  buy_data_request.post(route('purchase_9mobile_combo_data'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success) {

        var text_html = "Your SME DATA Recharge Request To Credit <em class='text-primary'>" + phone_number + "</em> With Data Worth " + product_name + " On <span class='capitalize;'>" + network + "</span> Network Has Been Sent To The Admin. You Would Be Credited Shortly . Note You Have Been Debited Of ₦" + mainStore.addCommas(amount) + "."

       
        Swal.fire({
          title: 'Info',
          html: text_html,
          icon: 'info',
          confirmButtonColor: '#3085d6',
          allowEscapeKey: false,
          allowOutsideClick: false,
        }).then((result) => {
          Inertia.visit(route('internet_data'));

        });
        

      } else if (response.insuffecient_funds) {
        Swal.fire({
          title: 'Ooops',
          html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
          icon: 'error'
        })
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

const confirmAndProceedWithTransaction = () => {
  

  var selected_plan = buy_data_request.selected_plan;
  var phone_number = buy_data_request.phone_number;
  var sub_type = buy_data_request.selected_plan.sub_type;
  var network = buy_data_request.selected_plan.network;

  console.log(selected_plan)
  console.log(phone_number)
  console.log(sub_type)
  console.log(network)



  
  showPreviewTransactionModal.value = false
  if (sub_type == "combo" && network == "9mobile") {
    proceedWithComboRequest();
  } else if (sub_type == "payscribe") {
    proceedWithPayscribeDataRequest();
  } else if (sub_type == "clubkonnect") {
    proceedWithClubDataRequest();
  } else if (sub_type == "gsubz") {
    proceedWithGsubzDataRequest();
  } else if (sub_type == "eminence") {
    proceedWithEminenceDataRequest();
  }
};

const proceedToSubmitBuyDataRequest = () => {
  
  var network = buy_data_request.network;
  var selected_plan = buy_data_request.selected_plan;
  var phone_number = buy_data_request.phone_number;


  showPreviewTransactionModal.value = true;
  

}

const validateBuyDataRequest = () => {
  
  var network = buy_data_request.network;
  var selected_plan = buy_data_request.selected_plan;
  var phone_number = buy_data_request.phone_number;

  if (network == "mtn" || network == "glo" || network == "airtel" || network == "9mobile") {
    if (phone_number != null && phone_number != 0 && mainStore.isNumeric(phone_number)) {
      proceedToSubmitBuyDataRequest();

    } else {
      
      Swal.fire({
        title: 'Error',
        html: "Phone Number Entered Is Not Valid. Please Enter A Valid One",
        icon: 'error'
      })
    }

  } else {
    Swal.fire({
      title: 'Error',
      html: "Invalid Network Selected. Please Select A Valid One",
      icon: 'error'
    })
  }

};
  
const selectDataPlan = (index) => {
  
  buy_data_request.selected_plan_index = index;
  buy_data_request.selected_plan = data_plans.value[index];
  console.log(buy_data_request.selected_plan);
};

const getDataPlans = (network) => {
  
  get_data_plans_request.network = network;
  get_data_plans_request.post(route('get_data_plans_by_network'), {
    preserveScroll: true,
    onSuccess: (page) => {
      
      var response = page.props.flash.data;
      console.log(response)

      if (response.success) {

        if (response.data_plans.length > 0) {
          buy_data_request.selected_plan_index = 0;
          buy_data_request.network = network;
          data_plans.value = response.data_plans;
          buy_data_request.selected_plan = data_plans.value[buy_data_request.selected_plan_index];
        } else {
          Swal.fire({
            title: 'Ooops!',
            html: "Data Plans Could Not Be Loaded. Please Try Again",
            icon: 'error'
          })
        }

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

const selectNetwork = (network) => {
  
  if (buy_data_request.network != network) {
    // if (network == "9mobile") {
    //   Swal.fire({
    //     title: 'Choose Option',
    //     icon: 'info',
    //     html: `Choose Recharge Option: `,
    //     showCancelButton: false,
    //     showDenyButton: true,
    //     confirmButtonColor: '#3085d6',
    //     denyButtonColor: '#4caf50',
    //     confirmButtonText: 'Normal Recharge',
    //     denyButtonText: "SME DATA Recharge"
    //   }).then((result) => {
    //     /* Read more about isConfirmed, isDenied below */
    //     if (result.isConfirmed) {

    //       get_data_plans_request.combo = false;
    //       getDataPlans(network);
    //     } else if (result.isDenied) {
    //       get_data_plans_request.combo = true;
    //       getDataPlans(network);
    //     }
    //   });
    // } else {
      // get_data_plans_request.combo = false;
      // getDataPlans(network);
    // }

    get_data_plans_request.combo = false;
    getDataPlans(network);
  }


};


</script>

