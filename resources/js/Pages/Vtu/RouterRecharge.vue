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

    <Head :title="`Router Recharge`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiRouterWireless" :title="`Router Recharge`" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->

        <BaseButton :href="route('user_vtu_history_page') + '?length=10&type=router&isDirty=true&__rememberable=true'" :icon="mdiHistory" label="View His." color="success"
          rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->



      <CardBox v-if="!show_plans" isForm @submit.prevent="validateBuyRouterRequest" class="">
        <h4 class="subhead">Select Network</h4>

        <div class="mx-2 my-9">
          <div class="grid grid-cols-12 gap-1">

            <div class="col-span-2 sm:col-span-1 card network-card"
              :class="buy_router_request.router_service == 'smile' ? 'selected' : ''" @click="selectRouterService('smile')">
              <div class="card-body text-center">
                <img src="/images/Smile-Logo.jpg" alt="SMILE" class="">
                <!-- <p>MTN</p> -->
              </div>

            </div>

            <div class="col-span-1">

            </div>

            

          </div>
          <div class="row">
            <div v-if="buy_router_request.errors.router_service" class="form-error">{{ buy_router_request.errors.router_service }}
            </div>
          </div>
        </div>


        <h4 class="subhead my-5">Enter Router Number</h4>

        <FormField class="" label="">
          <FormControl v-model="buy_router_request.router_number" :error="buy_router_request.errors.router_number"
             type="text" id="router_number" placeholder="e.g 123456789" />
        </FormField>

        


        <button :class="buy_router_request.processing ? 'opacity-80 cursor-not-allowed' : ''"
          @mouseleave="btn_hovered = false" @mouseover="btn_hovered = true" type="submit"
          class="app-form-button mt-9 text-left pl-6">
          Continue
          <img v-if="buy_router_request.processing" class="inline-block w-7 h-6 float-right"
            :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
        </button>
      </CardBox>

      <CardBox class="card" id="router-plans-card" v-if="show_plans">
        <div class="card-header">
          
          <BaseButton label="Go Back" color="warning" @click="goBackFromPlansCard">
          </BaseButton>
          <h3 class="card-title" v-html="router_plans_card_title"></h3>
        </div>
        <div class="card-body">
          <h4 class="subhead my-4">Select Router Plan Below And Click Floating Action Button To Proceed.</h4>
      
      
          <div class="container" v-if="router_plans.length > 0">
            <div @click="selectRouterPlan(plan.index - 1)"
              class="data-plans-card card"
              :class="buy_router_request.selected_plan_index == (plan.index - 1) ? 'selected' : '' "
              v-for="plan in router_plans" :key="plan.index">
              <div class="card-body grid grid-cols-12 gap-2">
      
                <div class="col-span-1 sm:col-span-2 col-2 col-sm-1">
                  <span v-html="plan.index + '.'"></span>
                </div>
      
                <div class="col-span-4 col-4 col-sm-4">
                  <span v-html="plan.name"></span>
                </div>
      
                <div class="col-span-2 sm:col-span-3 col-3 col-sm-2">
                  <span v-html="'₦' + mainStore.addCommas(plan.amount)"></span>
                </div>
      
                <div class="col-span-5 sm:col-span-3 col-3 col-sm-5">
                  <span v-html="plan.validity"></span>
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
              <tbody v-if="buy_router_request.selected_plan.length != {}">
                <tr>
                  <td>ROUTER SERVICE</td>
                  <td><em v-html="buy_router_request.router_service" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>ROUTER NUMBER</td>
                  <td><em v-html="buy_router_request.router_number" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>CUSTOMER NAME</td>
                  <td><em v-html="buy_router_request.customer_name" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>SELECTED PLAN</td>
                  <td><em v-html="buy_router_request.selected_plan.name" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>SELECTED PLAN VALIDITY</td>
                  <td><em v-html="buy_router_request.selected_plan.validity" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>SELECTED PLAN COST</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_router_request.selected_plan.amount)" class="text-primary"></em></td>
                </tr>
              
                <tr>
                  <td>PAYABLE</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_router_request.selected_plan.amount)" class="text-primary"></em></td>
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
  <div @click="showDetailsBuyRouterRequest" v-if="!showPreviewTransactionModal && router_plans.length > 0 && !buy_router_request.processing">
    <FloatingActionButton :styles="'background: 9124a3;'" :title="'Proceed'">

      <i class="fas fa-arrow-right" style="font-size: 25px; color: #fff;"></i>
      
    </FloatingActionButton>
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
  mdiRouterWireless,
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

const mainStore = useMainStore();

const btn_hovered = ref(false);

const router_plans = ref([]);
const show_plans = ref(false);
const router_plans_card_title = ref("");
const modal_open = ref(false);

  
const showPreviewTransactionModal = ref(false);

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)



const useSearchBtn = mainStore.useSearchBtn;


const lengthOptions = ref([
  10,
  20,
  50,
  100
]);

const buy_router_request = useForm({
  router_service: null,
  selected_plan_index: null,
  router_number: null,
  selected_plan: {},
  productCode: null,
  customer_name: "",
});


  


const submit = () => {

};

const confirmAndProceedWithTransaction = () => {
  

  var router_service = buy_router_request.router_service;
  var selected_plan_index = buy_router_request.selected_plan_index;
  var router_number = buy_router_request.router_number;
  var selected_plan = buy_router_request.selected_plan;
  var productCode = buy_router_request.productCode;
  var customer_name = buy_router_request.customer_name;

  console.log(router_service)
  console.log(selected_plan_index)
  console.log(router_number)
  console.log(selected_plan)
  console.log(productCode)
  console.log(customer_name)



  
  showPreviewTransactionModal.value = false;
  
  buy_router_request.post(route('recharge_router'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success) {
        
        Swal.fire({
          title: 'Success',
          html: `You Have Successfully Recharged Your ${router_service} Router <em class='text-primary'>${router_number} </em> With ${buy_router_request.selected_plan.name} Which Costs ₦${mainStore.addCommas(buy_router_request.selected_plan.amount)} <p><em>You Have Been Debited Of ₦${mainStore.addCommas(buy_router_request.selected_plan.amount)}</em></p>`,
          icon: 'success',
          confirmButtonColor: '#3085d6',
          allowEscapeKey: false,
          allowOutsideClick: false,
        }).then((result) => {
          Inertia.visit(route('router_recharge'));

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

const cancelTransaction = () => {
  
  buy_router_request.selected_plan_index = null;
  buy_router_request.selected_plan = {};
  showPreviewTransactionModal.value = false;
}

const selectRouterPlan = (index) => {
  
  if (buy_router_request.selected_plan_index != index) {
    buy_router_request.selected_plan_index = index;
    buy_router_request.selected_plan = router_plans.value[index];
    console.log(buy_router_request.selected_plan);
  }
};

const goBackFromPlansCard = () => {
  
  router_plans_card_title.value = "";
  buy_router_request.selected_plan_index = null;
  buy_router_request.selected_plan = {};
  buy_router_request.customer_name = "";
  buy_router_request.productCode = null;
  router_plans.value = [];
  show_plans.value = false;
};

const showDetailsBuyRouterRequest = () => {
  
  var router_service = buy_router_request.router_service;
  var selected_plan_index = buy_router_request.selected_plan_index;
  var router_number = buy_router_request.router_number;
  var selected_plan = buy_router_request.selected_plan;
  var productCode = buy_router_request.productCode;
  var customer_name = buy_router_request.customer_name;

  console.log(router_service)
  console.log(selected_plan_index)
  console.log(router_number)
  console.log(selected_plan)
  console.log(productCode)
  console.log(customer_name)

  if (selected_plan_index != null) {
    // self.modal_open = true;
    // $("#preview-transaction-modal").modal("show");
    showPreviewTransactionModal.value = true;

  } else {

    Swal.fire({
      title: 'Error',
      html: "No Router Plan Selected. Please Select One To Proceed",
      icon: 'error'
    });
  }

};

const validateBuyRouterRequest = () => {
  var self = this;
  var router_service = buy_router_request.router_service;
  var selected_plan_index = buy_router_request.selected_plan_index;
  var router_number = buy_router_request.router_number;
  var selected_plan = buy_router_request.selected_plan;
  var productCode = buy_router_request.productCode;
  var customer_name = buy_router_request.customer_name;

  console.log(router_service)
  console.log(selected_plan_index)
  console.log(router_number)
  console.log(selected_plan)
  console.log(productCode)
  console.log(customer_name)

  if (router_service != null) {
    
    buy_router_request.post(route('load_router_bundles_and_verify_number'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        console.log(response)

        

        if (response.success && response.router_plans != "" && response.customer_name != "") {
          
          var customer_name = response.customer_name;
          var productCode = response.productCode;

          Swal.fire({
            title: 'Info',
            html: `Is This Your Name ? <br> <em class='text-center text-primary'>${customer_name}</em>`,
            icon: 'info',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Yes Proceed!',
            showDenyButton: true,
            showCancelButton: false,
            denyButtonColor: '#d33',
            denyButtonText: `No`,
          }).then((result) => {
            if (result.isConfirmed) {
              router_plans_card_title.value = "Router Service: <em class='text-primary'>" + router_service + "</em>";
              router_plans_card_title.value += "<br>Router Number: <em class='text-primary'>" + router_number + "</em>";
              router_plans_card_title.value += "<br>Customer Name: <em class='text-primary'>" + customer_name + "</em>";

              buy_router_request.customer_name = customer_name;
              buy_router_request.productCode = productCode;
              router_plans.value = response.router_plans;
              show_plans.value = true;
              
            } else if (result.isDenied) {
              
            }
          });
          
        } else if (response.incorrect_number) {

          Swal.fire({
            title: 'Ooops!',
            html: "Sorry The Router Number You Entered Is Invalid",
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
      html: "No Router Service Selected. Please Select One To Proceed",
      icon: 'error'
    });
  }

};

const selectRouterService = (router_service) => {
  
  if (buy_router_request.router_service != router_service) {
    buy_router_request.router_service = router_service;
  }

};


</script>

