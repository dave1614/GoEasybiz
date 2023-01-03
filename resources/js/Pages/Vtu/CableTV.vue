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

    <Head :title="`Cable Tv Recharge`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiTelevisionClassic" :title="`Cable Tv Recharge`" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->

        <BaseButton :href="route('user_vtu_history_page') + '?length=10&type=cable&isDirty=true&__rememberable=true'"
          :icon="mdiHistory" label="View His." color="success" rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->



      <CardBox v-if="!show_cable_plans" isForm @submit.prevent="verifyDecoderNumber" class="">
        <h4 class="subhead">Select Operator For Cable Tv</h4>

        <div class="mx-2 my-9">
          <div class="grid grid-cols-12 gap-1">

            <div class="col-span-2 sm:col-span-1 card operator-card"
              :class="buy_cable_request.operator == 'dstv' ? 'selected' : ''" @click="selectedTvOperator('dstv')">
              <div class="card-body text-center">
                <img src="/images/dstv_logo.jpg" alt="DSTV" class="">
                <!-- <p>MTN</p> -->
              </div>

            </div>

            <div class="col-span-1">

            </div>

            <div class="col-span-2 sm:col-span-1 card operator-card"
              :class="buy_cable_request.operator == 'gotv' ? 'selected' : ''" @click="selectedTvOperator('gotv')">
              <div class="card-body text-center">
                <img src="/images/gotv_logo.jpg" alt="GOTV" class="">
                <!-- <p>MTN</p> -->
              </div>
            </div>

            <div class="col-span-1">

            </div>

            <div class="col-span-2 sm:col-span-1 card operator-card"
              :class="buy_cable_request.operator == 'startimes' ? 'selected' : ''" @click="selectedTvOperator('startimes')">
              <div class="card-body text-center">
                <img src="/images/startimes_logo.jpg" alt="STARTIMES" class="col-12">
                <!-- <p>MTN</p> -->
              </div>
            </div>

          </div>

         
          <div class="row">
            <div v-if="buy_cable_request.errors.operator" class="form-error">{{ buy_cable_request.errors.operator }}
            </div>
          </div>
        </div>


        <h4 class="subhead my-5" v-html="decoder_number_title"></h4>

        <FormField class="" label="">
          <FormControl v-model="buy_cable_request.decoder_number" :error="buy_cable_request.errors.decoder_number"
           type="text" id="decoder_number" placeholder="e.g 08127027321" />
        </FormField>

        


        <button :class="buy_cable_request.processing ? 'opacity-80 cursor-not-allowed' : ''"
          @mouseleave="btn_hovered = false" @mouseover="btn_hovered = true" type="submit"
          class="app-form-button mt-9 text-left pl-6">
          Proceed
          <img v-if="buy_cable_request.processing" class="inline-block w-7 h-6 float-right"
            :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
        </button>
      </CardBox>

      <CardBox id="cable-plans-card" v-if="show_cable_plans">
        <div class="card-header">
          <BaseButton label="Re-Enter Details" color="warning" @click="reEnterCableDetails">
            </BaseButton>
          <h3 class="card-title" v-html="cable_plans_card_title"></h3>
        </div>
        <div class="card-body">
          <div class=""
            v-if="(buy_cable_request.operator == 'dstv' || buy_cable_request.operator == 'gotv' || (buy_cable_request.operator == 'startimes' && buy_cable_request.platform == 'club')) && cable_plans.length > 0">
            <div class="container" v-if="cable_plans.length > 0">
              <div @click="purchasePlan(plan.index - 1)"
                class="cable-plans-card card"
                :class="buy_cable_request.selected_plan_index == (plan.index - 1) ? ' selected' : '' "
                v-for="plan in cable_plans" :key="plan.index">
                <div class="card-body grid grid-cols-12 gap-2">
      
                  <div class="col-span-1 sm:col-span-3 col-3 col-sm-1">
                    <span v-html="plan.index + '.'"></span>
                  </div>
      
                  <div class="col-span-6 sm:col-span-4 col-4 col-sm-6">
                    <span v-html="plan.name"></span>
                  </div>
      
                  <div class="col-span-5 col-5 col-sm-5">
                    <span v-html="'₦' + mainStore.addCommas(plan.amount)"></span>
                  </div>
      
                
                </div>
              </div>
            </div>
          </div>
      
          <div
            v-else-if="buy_cable_request.operator == 'startimes' && buy_cable_request.platform == 'payscribe' && cable_plans.length > 0">
      
      
            <div class="container" v-for="plan in cable_plans" :key="plan.index">
      
              <div class="cable-plans-card card display">
                <div class="card-body grid grid-cols-12 gap-2">
      
                  <div class="col-span-1 sm:col-span-3 col-3 col-sm-1">
                    <span v-html="plan.index + '.'" style="font-size:15px;"></span>
                  </div>
      
                  <div class="col-span-6 sm:col-span-4 col-4 col-sm-6">
                    <span v-html="plan.name" style="font-size:15px;"></span>
                  </div>
      
                  <div class="col-span-5 col-5 col-sm-5">
      
                  </div>
      
                </div>
              </div>
      
              <div @click="purchasePlan(plan.index - 1,'daily')"
                :class="buy_cable_request.selected_plan.cycle == 'daily' && buy_cable_request.selected_plan.amount == plan.cycles.daily ? 'cable-plans-card card selected' : 'cable-plans-card card'">
                <div class="card-body grid grid-cols-12 gap-2">
      
                  <div class="col-span-1 sm:col-span-3 col-3 col-sm-1">
                    <span>i.</span>
                  </div>
      
                  <div class="col-span-6 sm:col-span-4 col-4 col-sm-6">
                    <span>Daily</span>
                  </div>
      
                  <div class="col-span-5 col-5 col-sm-5">
                    <span v-html="'₦' + mainStore.addCommas(plan.cycles.daily)"></span>
                  </div>
      
                </div>
              </div>
      
              <div @click="purchasePlan(plan.index - 1,'weekly')"
                :class="buy_cable_request.selected_plan.cycle == 'weekly' && buy_cable_request.selected_plan.amount == plan.cycles.weekly ? 'cable-plans-card card selected' : 'cable-plans-card card'">
                <div class="card-body grid grid-cols-12 gap-2">
      
                  <div class="col-span-1 sm:col-span-3 col-3 col-sm-1">
                    <span>ii</span>
                  </div>
      
                  <div class="col-span-6 sm:col-span-4 col-4 col-sm-6">
                    <span>Weekly</span>
                  </div>
      
                  <div class="col-span-5 col-5 col-sm-5">
                    <span v-html="'₦' + mainStore.addCommas(plan.cycles.weekly)"></span>
                  </div>
      
                </div>
              </div>
      
              <div @click="purchasePlan(plan.index - 1,'monthly')"
                :class="buy_cable_request.selected_plan.cycle == 'monthly' && buy_cable_request.selected_plan.amount == plan.cycles.monthly ? 'cable-plans-card card selected' : 'cable-plans-card card'">
                <div class="card-body grid grid-cols-12 gap-2">
      
                  <div class="col-span-1 sm:col-span-3 col-3 col-sm-1">
                    <span>iii</span>
                  </div>
      
                  <div class="col-span-6 sm:col-span-4 col-4 col-sm-6">
                    <span>Monthly</span>
                  </div>
      
                  <div class="col-span-5 col-5 col-sm-5">
                    <span v-html="'₦' + mainStore.addCommas(plan.cycles.monthly)"></span>
                  </div>
                </div>
              </div>
      
            </div>
      
      
          </div>
        </div>
      </CardBox>

      <CardBoxModal v-model="showPreviewTransactionModal" button="danger" buttonLabel="Close"
        :title="`Preview Transaction`">
        <div class="">
          <div class="text-center">
            <p>Kindly confirm that the details you entered are valid before clicking the "Confirm" button.</p>

          </div>

          <div class="">
            <table class="table table-bordered ">
              <tbody
                v-if="buy_cable_request.operator == 'dstv' || buy_cable_request.operator == 'gotv' || (buy_cable_request.operator == 'startimes' && buy_cable_request.platform == 'club')">
                <tr>
                  <td>CABLE OPERATOR</td>
                  <td style="text-transform"><em v-html="buy_cable_request.operator" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td v-if="buy_cable_request.operator == 'dstv' || buy_cable_request.operator == 'startimes'">Smart Card Number</td>
                  <td v-else>IUC Number</td>
                  <td style="text-transform"><em v-html="buy_cable_request.decoder_number" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>CUSTOMER NAME</td>
                  <td style="text-transform"><em v-html="buy_cable_request.customer_name" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>BOUQUET</td>
                  <td><em v-html="buy_cable_request.selected_plan.name" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>COST</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_cable_request.selected_plan.amount)" class="text-primary"></em></td>
                </tr>
              
                <tr>
                  <td>PAYABLE</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_cable_request.selected_plan.amount)" class="text-primary"></em></td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr>
                  <td>CABLE OPERATOR</td>
                  <td style="text-transform"><em v-html="buy_cable_request.operator" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>Smart Card Number</td>
                  <td style="text-transform"><em v-html="buy_cable_request.decoder_number" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>CUSTOMER NAME</td>
                  <td style="text-transform"><em v-html="buy_cable_request.customer_name" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>PACKAGE NAME</td>
                  <td><em v-html="buy_cable_request.selected_plan.package_name" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>CYCLE</td>
                  <td><em v-html="buy_cable_request.selected_plan.cycle" class="text-primary"></em></td>
                </tr>
              
                <tr>
                  <td>COST</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_cable_request.selected_plan.amount)" class="text-primary"></em></td>
                </tr>
              
                <tr>
                  <td>PAYABLE</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_cable_request.selected_plan.amount)" class="text-primary"></em></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="justify-content-center text-center">

            <BaseButton @click="confirmAndProceedWithTransaction" class="" label="Confirm" color="success" rounded />
            <br>
            <p class="text-red-500 cursor-pointer" @click="cancelTransaction">Cancel and return</p>
          </div>
        </div>

      </CardBoxModal>
    </SectionMain>

  </LayoutAuthenticated>
  <div @click="validateBuyCableRequest" v-if="!buy_cable_request.processing && show_cable_plans">
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
  mdiCellphoneDock,
  mdiAccountCog,
  mdiAccountCash,
  mdiCurrencyNgn,
  mdiPhone,
  mdiTelevisionClassic,
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


const showPreviewTransactionModal = ref(false);

const show_cable_plans = ref(false);
const cable_plans = ref([]);
const decoder_number_title = ref("Enter Smart Card Number");
const cable_plans_card_title = ref("");

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)



const useSearchBtn = mainStore.useSearchBtn;


const lengthOptions = ref([
  10,
  20,
  50,
  100
]);
 
const buy_cable_request = useForm({
  operator: "dstv",
  selected_plan_index: null,

  decoder_number: null,
  selected_plan: {},
  productCode: '',
  productToken: '',
  customer_name: '',
  platform: '',

});

const get_cable_plans_request = useForm({
  operator: "",
  combo: false,
});


    



const submit = () => {

};

const confirmAndProceedWithTransaction = () => {
  

  var selected_plan = buy_cable_request.selected_plan;
  var operator = buy_cable_request.operator;

  var decoder_number = buy_cable_request.decoder_number;
  var productCode = buy_cable_request.productCode;
  var productToken = buy_cable_request.productToken;

  console.log(selected_plan)
  console.log(operator)
  console.log(decoder_number)
  console.log(productCode)
  console.log(productToken)

  if (Object.keys(selected_plan).length > 0) {

    
    showPreviewTransactionModal.value = false;
    
    buy_cable_request.post(route('purchase_cable_tv_plan'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        console.log(response)

        if (response.success && response.order_id !== "") {
          buy_cable_request.selected_plan_index = null;
          buy_cable_request.selected_plan = {};
          var order_id = response.order_id;
          var transaction_pending = response.transaction_pending;
          if (!transaction_pending) {
            
            Swal.fire({
              title: 'Info',
              html: `You Have Successfully Recharged Decoder With Number: <em class='text-primary'>${decoder_number}.</em> The Order Id For This Transaction Is <em class='text-primary'>${order_id}</em>`,
              icon: 'info',
              confirmButtonColor: '#3085d6',
              allowEscapeKey: false,
              allowOutsideClick: false,
            }).then((result) => {
              Inertia.visit(route('cable_tv'));

            });
          } else {
            
            Swal.fire({
              title: 'Info',
              html: `You Have Successfully Recharged Decoder With Number: <em class='text-primary'>${decoder_number}.</em> The Order Id For This Transaction Is <em class='text-primary'>${order_id}</em>. Note: This Order Is Currently Pending. You Have Been Debited. To See The Status Of Your Transaction, Track This Transaction From The Recharge Vtu Transaction History Page.`,
              icon: 'info',
              confirmButtonColor: '#3085d6',
              allowEscapeKey: false,
              allowOutsideClick: false,
            }).then((result) => {
              Inertia.visit(route('cable_tv'));

            });
          }
        } else if (response.invalid_no) {

          Swal.fire({
            title: 'Ooops!',
            html: "Invalid Smart Card No. Was Entered. Your Money Has Been Refunded",
            icon: 'error'
          });
        } else if (response.insuffecient_funds) {

          Swal.fire({
            title: 'Ooops!',
            html: "Sorry You Do Not Have Suffecient Funds To Complete This Transaction.",
            icon: 'error'
          });
        } else if (response.error_message != "") {

          Swal.fire({
            title: 'Ooops!',
            html: response.error_message,
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
};

const cancelTransaction = () => {
  buy_cable_request.selected_plan_index = null;
  buy_cable_request.selected_plan = {};
  showPreviewTransactionModal.value = false;
};

const validateBuyCableRequest = () => {
  
  var selected_plan = JSON.parse(JSON.stringify(buy_cable_request.selected_plan));

  console.log(Object.keys(selected_plan).length)
  if (Object.keys(selected_plan).length > 0) {
    
    showPreviewTransactionModal.value = true;
  } else {
    Swal.fire({
      title: 'Error!',
      html: "You Must Select A Plan To Proceed",
      icon: 'error'
    });
  }

};

const reEnterCableDetails = () => {
  
  buy_cable_request.customer_name = "";
  cable_plans.value = [];
  buy_cable_request.productCode = "";
  buy_cable_request.productToken = "";
  show_cable_plans.value = false;
  cable_plans_card_title.value = "";
};

const purchasePlan = (index, cycle = "") => {
  
  if (cycle == "") {
    buy_cable_request.selected_plan_index = index;
    buy_cable_request.selected_plan = cable_plans.value[index];
  } else {
    buy_cable_request.selected_plan = {};
    buy_cable_request.selected_plan.package_name = cable_plans.value[index].name;
    if (cycle == "daily") {
      buy_cable_request.selected_plan.amount = cable_plans.value[index].cycles.daily;
      buy_cable_request.selected_plan.cycle = "daily";
    } else if (cycle == "weekly") {
      buy_cable_request.selected_plan.amount = cable_plans.value[index].cycles.weekly;
      buy_cable_request.selected_plan.cycle = "weekly";
    } else if (cycle == "monthly") {
      buy_cable_request.selected_plan.amount = cable_plans.value[index].cycles.monthly;
      buy_cable_request.selected_plan.cycle = "monthly";
    }
  }
  console.log(buy_cable_request.selected_plan);
  // console.log(buy_cable_request.selected_plan.cycle)
  // console.log(buy_cable_request.selected_plan.amount)

};

const verifyDecoderNumber = () => {
  
  var selected_plan = buy_cable_request.selected_plan;
  var decoder_number = buy_cable_request.decoder_number;
  var operator = buy_cable_request.operator;


  console.log(selected_plan)
  console.log(decoder_number)
  console.log(operator)

  buy_cable_request.post(route('validate_decoder_number_cable_plans'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.customer_name !== "" && response.cable_plans != "" && response.productCode != "" && response.productToken != "" && response.platform != "") {

        buy_cable_request.platform = response.platform;

        Swal.fire({
          title: 'Info',
          icon: 'info',
          html: `Is This Your Name ? <br> <em class='text-center text-primary'>${response.customer_name}</em>`,
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes Proceed!',
          cancelButtonText: "No"
        }).then((result) => {
          buy_cable_request.customer_name = response.customer_name;
          cable_plans.value = response.cable_plans;
          buy_cable_request.productCode = response.productCode;
          buy_cable_request.productToken = response.productToken;
          show_cable_plans.value = true;
          cable_plans_card_title.value = "Cable Operator: <em class='text-primary'>" + operator + "</em><br>";
          if (operator == "dstv" || operator == "startimes") {
            cable_plans_card_title.value += "SmartCard Number: <em class='text-primary'>" + decoder_number + "</em><br>";

          } else {
            cable_plans_card_title.value += "IUC Number: <em class='text-primary'>" + decoder_number + "</em><br>";
          }

          cable_plans_card_title.value += "Customer Name: <em class='text-primary'>" + buy_cable_request.customer_name + "</em><br>";
          cable_plans_card_title.value += "<p style='font-size:17px; margin-top: 20px;'>Choose Plan Below</p>";

          console.log(buy_cable_request.operator)
          console.log(buy_cable_request.platform)
          console.log(cable_plans.value.length)
        });
      } else if (response.invalid_user) {

        Swal.fire({
          title: 'Ooops!',
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

};

const selectedTvOperator = (operator) => {
  
  if(buy_cable_request.operator != operator){
    buy_cable_request.operator = operator;
    if(operator == "dstv" || operator == "startimes"){
      decoder_number_title.value = "Enter Smart Card Number";
    }else{
      decoder_number_title.value = "Enter IUC Number";
    }
  }
  
    
};



</script>

