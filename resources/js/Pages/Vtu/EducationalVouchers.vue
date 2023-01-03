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

    <Head :title="`Educational Vouchers`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiRouterWireless" :title="`Educational Vouchers`" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->

        <BaseButton :href="route('user_vtu_history_page') + '?length=10&type=E-Pin&isDirty=true&__rememberable=true'"
          :icon="mdiHistory" label="View His." color="success" rounded-full small />
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->



      <CardBox v-if="epins_text == ''" isForm @submit.prevent="validateBuyRouterRequest" class="">
        <h4 class="subhead">Select Network</h4>

        <div class="mx-2 my-9">
          
          <div class="grid grid-cols-12 gap-3" v-if="voucher_types.length > 0">
          
            <!-- <div > -->
            <div v-for="row in voucher_types" :key="row.index"
              class="col-span-3 sm:col-span-2 card voucher_type-card"
              :class="buy_voucher_request.voucher_type == row.name ? 'selected' : ''"
              @click="selectVoucherType(row.name,row.index)">
              <div class="card-body text-center">
                <img :src="row.image" alt="WAEC" class="col-12">
                <p v-html="'₦'+mainStore.addCommas(row.price)" class="text-bold"></p>
              </div>
              
            </div>
          
            
            <!-- </div> -->
          
          </div>
          <div class="row">
            <div v-if="buy_voucher_request.errors.voucher_type" class="form-error">{{
            buy_voucher_request.errors.voucher_type }}
            </div>
          </div>
        </div>




        <button :class="buy_voucher_request.processing ? 'opacity-80 cursor-not-allowed' : ''"
          @mouseleave="btn_hovered = false" @mouseover="btn_hovered = true" type="submit"
          class="app-form-button mt-9 text-left pl-6">
          Continue
          <img v-if="buy_voucher_request.processing" class="inline-block w-7 h-6 float-right"
            :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
        </button>
      </CardBox>

      <CardBox  id="epins-card" v-if="epins_text != ''">
        <div class="card-body" v-html="epins_text">
      
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
              <tbody>
                <tr>
                  <td>VOUCHER TYPE</td>
                  <td><em v-html="buy_voucher_request.voucher_type" class="text-primary"></em></td>
                </tr>
                <tr>
                  <td>QUANTITY (UNITS)</td>
                  <td><em v-html="buy_voucher_request.quantity" class="text-primary"></em></td>
                </tr>
              
                <tr>
                  <td>COST PER UNIT</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_voucher_request.amount)" class="text-primary"></em></td>
                </tr>
              
                <tr>
                  <td>PAYABLE</td>
                  <td><em v-html="'₦ ' + mainStore.addCommas(buy_voucher_request.payable)" class="text-primary"></em></td>
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
  mdiRouterWireless,
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
const voucher_types = props.value?.voucher_types;

const mainStore = useMainStore();

const btn_hovered = ref(false);


const epins_text = ref("");



const showPreviewTransactionModal = ref(false);

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)



const useSearchBtn = mainStore.useSearchBtn;


const lengthOptions = ref([
  10,
  20,
  50,
  100
]);


const buy_voucher_request = useForm({
  voucher_type: null,
  quantity: 1,
  amount: null,
  payable: null,
  index: null,
});
const check_voucher_availability = useForm({
  voucher_type: null,

});





const submit = () => {

};

const confirmAndProceedWithTransaction = () => {
  
  // buy_voucher_request.voucher_type = "testy";
  var voucher_type = buy_voucher_request.voucher_type;
  var quantity = buy_voucher_request.quantity;
  var amount = buy_voucher_request.amount;
  var payable = buy_voucher_request.payable;


  console.log(voucher_type)
  console.log(quantity)
  console.log(amount)
  console.log(payable)



  
  showPreviewTransactionModal.value = false;
  
  buy_voucher_request.post(route('buy_eminence_educational_voucher_vtu'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.epins != "" && response.amount != "") {
        var epins = response.epins;

        var amount = response.amount;
        var text = "<p class='capitalize'>" + voucher_type + " Result Checker ePin generated successfully<br> <h5>Details: </h5>Quantity: <em class='text-primary'>" + quantity + "</em> Unit(s)</p>";

        var j = 0;
        text += "<div class='container'>";
        // for(var i = 0; i < epins.length; i++){
        j++;
        // var pin = epins[i].pin;
        // var serial = epins[i].serial;

        text += "<div class='row' style='margin-bottom: 5px;'>";
        text += "<p class='col-1'>" + j + "</p>";
        text += "<p class='col-11'>" + epins + "</p>";
        // text += "<p class='col-5'>"+serial+"</p>";
        text += "</div>";
        // }

        text += "</div>";

        epins_text.value = text;

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

const validateBuyRouterRequest = () => {
  var self = this;
  var voucher_type = buy_voucher_request.voucher_type;
  var quantity = buy_voucher_request.quantity;
  var index = buy_voucher_request.index;
  console.log(voucher_types[index]['price'])

  buy_voucher_request.amount = voucher_types[index].price;
  buy_voucher_request.payable = voucher_types[index].price;
  
  showPreviewTransactionModal.value = true;


  // console.log(voucher_type)
  // console.log(quantity)


  // if(voucher_type != null){
  //   buy_voucher_request.post(self.route('validate_educational_voucher_info'), {
  //     preserveScroll: true,
  //     onSuccess: (page) => {

  //       var response = JSON.parse(JSON.stringify(self.response_arr))
  //       console.log(response)

  //       if(response.success && response.amount != "" && response.payable != ""){
  //         buy_voucher_request.amount = response.amount;
  //         buy_voucher_request.payable = response.payable;
  //         $("#preview-transaction-modal").modal("show");
  //       }else{
  //         swal({
  //           title: 'Ooops',
  //           text: "Something Went Wrong. Please Try Again",
  //           type: 'error'
  //         })
  //       }
  //     },onError: (errors) => {

  //       var errors = JSON.parse(JSON.stringify(errors))
  //       var errors_num = Object.keys(errors).length;

  //       if(errors_num > 0){
  //         $.notify({
  //           message: errors_num + " Field(s) Have Error(s). Please Correct Them."
  //         },{
  //           type : "warning",
  //           z_index: 20000,
  //         });
  //       }
  //     },
  //   });

  // }else{
  //   swal({
  //     title: 'Error',
  //     text: "No Router Service Selected. Please Select One To Proceed",
  //     type: 'error'
  //   })
  // }

};

const selectVoucherType = (voucher_type, index) => {
  
  if (buy_voucher_request.voucher_type != voucher_type) {
    buy_voucher_request.voucher_type = voucher_type
    buy_voucher_request.index = index - 1;
    // self.check_voucher_availability.voucher_type = voucher_type;
    // self.check_voucher_availability.post(self.route('check_if_educational_voucher_is_available'), {
    //   preserveScroll: true,
    //   onSuccess: (page) => {

    //     var response = JSON.parse(JSON.stringify(self.response_arr))
    //     console.log(response)

    //     if(response.success){

    //       buy_voucher_request.voucher_type = voucher_type
    //     }else{
    //       swal({
    //         title: 'Ooops',
    //         text: voucher_type + " Is Not Available At The Moment. Please Try Again Later.",
    //         type: 'error'
    //       })
    //     }
    //   },onError: (errors) => {

    //     var errors = JSON.parse(JSON.stringify(errors))
    //     var errors_num = Object.keys(errors).length;

    //     if(errors_num > 0){
    //       $.notify({
    //         message: errors_num + " Field(s) Have Error(s). Please Correct Them."
    //       },{
    //         type : "warning",
    //         z_index: 20000,
    //       });
    //     }
    //   },
    // });

  }

};



</script>

