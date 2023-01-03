<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head :title="`VTU History`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiHistory" :title="`VTU History`" main>
        <!-- <BaseButton :href="route('funds_transfer')" :icon="mdiWalletPlus" label="Funds Transfer" color="success"
          rounded-full small /> -->
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->

      <!-- <CardBox v-if="useSearchBtn" form @submit.prevent="submitFilterForm" class=""> -->
      <CardBox isForm @submit.prevent="submitFilterForm" class="">
        <div class="sm:grid sm:grid-cols-12 sm:gap-6">
          <FormField class="sm:col-span-4" label="Length">
            <FormControl v-model="form.length" :options="lengthOptions" />
          </FormField>
          <!-- <FormField class="sm:col-span-4 capitalize" label="Role" wrap-body>
            <FormCheckRadioGroup v-model="form.role" name="role" :options="roleOptions" type="radio" />
          </FormField> -->
          <FormField class="sm:col-span-4" label="Recharge Type">
            <FormControl v-model="form.type" />
          </FormField>
          <FormField class="sm:col-span-4" label="Recharge Sub Type">
            <FormControl v-model="form.sub_type" />
          </FormField>
          <FormField class="sm:col-span-4" label="Sender's Email">
            <FormControl v-model="form.sender_email" />
          </FormField>
          <FormField class="sm:col-span-4" label="Order ID">
            <FormControl v-model="form.order_id" />
          </FormField>
          <FormField class="sm:col-span-4" label="Number">
            <FormControl v-model="form.number" />
          </FormField>
          
          <FormField class="sm:col-span-4" label="Amount">
            <FormControl v-model="form.amount" type="number" step="any" />
          </FormField>

          <FormField class="sm:col-span-4" label="Transfer Date">
            <FormControl v-model="form.date" type="date" />
          </FormField>
          <FormField class="sm:col-span-4" label="Start Date">
            <FormControl v-model="form.start_date" type="date" />
          </FormField>

          <FormField class="sm:col-span-4" label="End Date">
            <FormControl v-model="form.end_date" type="date" />
          </FormField>


        </div>
        <BaseButtons>
          <BaseButton v-if="useSearchBtn" type="submit" color="info" label="Filter" class="px-9 mb-8" />
          <BaseButton @click="clearFilterForm" type="reset" color="info" outline label="Clear" :icon="mdiClose"
            class="px-9 mb-8" />
        </BaseButtons>
        <BaseDivider />
      </CardBox>

      <CardBox class="mb-6" has-table>

        <div v-if="history.data.length > 0" class="">
          <table>
            <thead>
              <tr>

                <th></th>
                
                <th>Actions</th>
                <th>Type</th>
                <th>Sub Type</th>
                <th>Amount</th>
                <th>Number</th>
                <th>Order Id</th>
                <th>Date</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row,index) in history.data" :key="row.id">

                <td v-html="`${(index + 1) +((history.current_page - 1) * form.length)}.`"></td>
                
                

                <td class="before:hidden lg:w-1 whitespace-nowrap">
                  <BaseButtons type="justify-start lg:justify-end" no-wrap>
                    <BaseButton @click="trackThisOrder(row)" color="success" label="Track This Order" small />
                    
                  </BaseButtons>
                </td>
                
                <td class="capitalize">{{row.type}}</td>
                <td v-html="row.sub_type == 'reloadly' ? '' : row.sub_type"></td>
                
                <td data-label="Amount" v-html="mainStore.addCommas((row.amount - 0).toFixed(2))">

                </td>

                <td>{{row.number}}</td>
                <td><em class="text-primary">{{row.order_id}}</em></td>
                <td class="date">{{row.date}}</td>
                <td class="time">{{row.time}}</td>
              </tr>
            </tbody>
          </table>
          <div class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
            <BaseLevel>
              <BaseButtons>
                <BaseButton v-for="page in history.links" :key="page" :active="page.active" :label="page.label"
                  :color="page.active ? 'lightDark' : 'whiteDark'" small @click="currentPage = page"
                  :href="page.url != null ? page.url : ''" :disabled="page.url === null" />
              </BaseButtons>
              <small>Page {{ history.current_page }} of {{ history.last_page }}</small>
            </BaseLevel>
          </div>
        </div>
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
  mdiCashRefund,
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
import FloatingActionButton from "@/components/FloatingActionButton.vue";
import { useMainStore } from "@/stores/main";
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { computed, ref, reactive, watch } from 'vue'
import _ from 'lodash';

const page = usePage();
const mainStore = useMainStore();

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const user = props.value?.user;
const history = props.value?.history;
const useSearchBtn = mainStore.useSearchBtn;
console.log(history)

const lengthOptions = ref([
  10,
  20,
  50,
  100
]);

const roleOptions = ref({
  all: 'all',
  sender: 'sender',
  recepient: 'recepient',
});





const form = useForm({
  length: props.value?.length,

  type: props.value?.type,
  sub_type: props.value?.sub_type,
  order_id: props.value?.order_id,
  amount: props.value?.amount,

  date: props.value?.date,
  start_date: props.value?.start_date,
  end_date: props.value?.end_date,

})

const track_vtu_request = useForm({
  show_records: true,
  order_id: ""
});
// console.log(form)

const clearFilterForm = () => {
  form.length = 10

  form.type = null
  form.sub_type = null
  form.order_id = null
  form.amount = null

  form.date = null
  form.start_date = null
  form.end_date = null


  // console.log(form)
}



const submitFilterForm = () => {
  // console.log()
  let query = _.pickBy(form);
  let queryRoute = route('user_vtu_history_page', user.id);
  let params = Object.keys(query).length ? query : {
    remember: 'forget'
  }
  // console.log(queryRoute)



  Inertia.get(queryRoute, params, {}, {
    // preserveState: true, 
    preserveScroll: true

  });
}

watch(form,
  _.throttle(() => {
    if (useSearchBtn) { return }
    submitFilterForm();
  }, 200), {
  deep: true
}
);


const submit = () => {

};


const trackThisOrderReloadly = (order_id) => {

  track_vtu_request.order_id = order_id;

  track_vtu_request.post(route('track_reloadly_vtu_order'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.messages != "") {
        var messages = response.messages;

        Swal.fire({
          title: 'Information On Order Id: ' + order_id,
          html: messages,
          icon: 'success'
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

const trackThisOrderPayscribeEducationalEpin = (order_id) => {
  
  track_vtu_request.order_id = order_id;
 
  track_vtu_request.post(route('track_payscribe_educational_epin'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.messages != "") {
        var messages = response.messages;
        

        Swal.fire({
          title: 'ePin(s) On Order ' + order_id + ' Lookup Successful: ',
          html: messages,
          icon: 'success'
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

const trackThisOrderPayscribeEpin = (order_id) => {
  
  track_vtu_request.order_id = order_id;

  track_vtu_request.post(route('track_payscribe_vtu_epin'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.messages != "" && response.epins_json != "" && response.amount != "") {
        var epins_json = response.epins_json;
        var amount = response.amount;
        text = "<button id='print-recharge-pins-btn' style='margin-bottom: 30px;' data-epins='" + epins_json + "' data-amount='" + amount + "' class='btn btn-success' >Print E-Pins</button>";
        var messages = response.messages;

        

        Swal.fire({
          title: 'Recharge Card Pin On Order ' + order_id + ' Lookup Successful: ',
          html: messages,
          icon: 'success'
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

const trackThisOrderPayscribe = (order_id) => {
  
  track_vtu_request.order_id = order_id;

  
  track_vtu_request.post(route('track_payscribe_vtu_order_data'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.messages != "") {
        var messages = response.messages;

        Swal.fire({
          title: 'Information On Order Id: ' + order_id,
          html: messages,
          icon: 'success'
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



const trackThisOrderEminence = (order_id) => {
  
  track_vtu_request.order_id = order_id;

  track_vtu_request.post(route('track_eminence_vtu_order'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.messages != "") {
        var messages = response.messages;

        Swal.fire({
          title: 'Information On Order Id: ' + order_id,
          html: messages,
          icon: 'success'
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

const trackThisOrderClub = (order_id) => {
  
  track_vtu_request.order_id = order_id;

  track_vtu_request.post(route('track_club_vtu_order'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      console.log(response)

      if (response.success && response.messages != "") {
        var messages = response.messages;
        
        Swal.fire({
          title: 'Information On Order Id: ' + order_id,
          html: messages,
          icon: 'success'
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

const trackThisOrder = (row) => {
  var order_id = row.order_id
  var order_id_cut = order_id.slice(0, 2);
  // console.log(mainStore.isNumeric("542425"))
  if (mainStore.isNumeric(order_id_cut)){
    trackThisOrderClub(row.order_id)
  } else if (order_id_cut == 'RE') {
    trackThisOrderReloadly(row.order_id)
  } else if (order_id_cut == 'SM' || order_id_cut == 'TT' || order_id_cut == 'TC'){
    trackThisOrderEminence(row.order_id)
  } else if (order_id_cut == 'PS' && (row.type == 'data' || row.type == 'electricity' || row.type == 'cable' || row.type == 'airtime_to_wallet' || row.type == 'bulk_sms' || row.type == 'router')) {
    trackThisOrderPayscribe(row.order_id)
  } else if (row.sub_type == 'payscribe_epin') {
    trackThisOrderPayscribeEpin(row.order_id)
  } else if (row.sub_type == 'educational_voucher_epin') {
    trackThisOrderPayscribeEducationalEpin(row.order_id)
  }
                              
};
</script>

