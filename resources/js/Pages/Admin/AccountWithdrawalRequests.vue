<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head :title="`Account Withdrawal Requests`" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiCreditCardMinus" :title="`Account Withdrawal Requests`" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->
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
          <FormField class="sm:col-span-4 capitalize" label="Role" wrap-body>
            <FormCheckRadioGroup v-model="form.role" name="role" :options="roleOptions" type="radio" />
          </FormField>
          <FormField class="sm:col-span-4" label="Full Name">
            <FormControl v-model="form.name" />
          </FormField>
          <FormField class="sm:col-span-4" label="Phone Number">
            <FormControl v-model="form.phone" />
          </FormField>
          <FormField class="sm:col-span-4" label="Email">
            <FormControl v-model="form.email" />
          </FormField>

          <FormField class="sm:col-span-4" label="Amount">
            <FormControl v-model="form.amount" type="number"/>
          </FormField>
          

          <FormField class="sm:col-span-4" label="Request Date">
            <FormControl v-model="form.date" type="date" />
          </FormField>
          <FormField class="sm:col-span-4" label="Debited Date">
            <FormControl v-model="form.debited_date_time" type="date" />
          </FormField>
          <FormField class="sm:col-span-4" label="Dismissed Date">
            <FormControl v-model="form.dismissed_date_time" type="date" />
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

        <div v-if="requests.data.length > 0" class="">
          <table>
            <thead class="text-sm">
              <tr>

                <th></th>
                <th>Actions</th>
                <th>Status</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Amount (₦)</th>
                <th>Bank Name</th>
                <th>Account Number</th>
                <th>Account Name</th>
                <th>Request Date / Time</th>
                <th>Debited Date / Time</th>
                <th>Dismissed Date / Time</th>
               </tr> 
            </thead>
            <tbody class="text-sm">
              <tr v-for="(row,index) in requests.data" :key="row.id">

                <td v-html="`${(index + 1) +((requests.current_page - 1) * form.length)}.`"></td>
                <td v-if="row.debited == 0 && row.dismissed == 0" class="before:hidden lg:w-1 whitespace-nowrap">
                  <BaseButtons type="justify-start lg:justify-end" no-wrap>
                    <BaseButton color="success" :icon="mdiCheckBold" small @click="creditThisUser(row,index)" />
                    <BaseButton color="danger" :icon="mdiCloseThick" small @click="dismissThisRequest(row,index)" />
                  </BaseButtons>
                </td>

                <td v-else class="before:hidden lg:w-1 whitespace-nowrap">
                  <!-- <BaseButtons type="justify-start lg:justify-end" no-wrap>
                    <BaseButton color="success" :icon="mdiCheckBold" small @click="processShowPerformActionModal(row,index)" />
                    <BaseButton color="danger" :icon="mdiCloseThick" small @click="isModalDangerActive = true" />
                  </BaseButtons> -->
                </td>

                <td v-html="row.debited == 0 && row.dismissed == 0 ? 'Pending' : row.debited == 1 && row.dismissed == 0 ? 'Debited' : row.debited == 0 && row.dismissed == 1 ? 'Dismissed': ''">

                </td>

                <td class="capitalize" data-label="Full Name">
                  {{ row.name }}
                </td>

                <td class="text-xs text-primary italic" data-label="Email">
                  {{ row.email }}
                </td>

                <td class="text-xs text-primary italic" data-label="Phone Number">
                  {{ row.phone }}
                </td>

                <td data-label="Amount" v-html="mainStore.addCommas((row.amount - 0).toFixed(2))">

                </td>

                <td class="capitalize text-sm" data-label="Bank Name">
                  {{ row.real_bank_name }}
                </td>

                <td class="italic " data-label="Account Number">
                  {{ row.account_number }}
                </td>

                <td class="capitalize " data-label="Account Name">
                  {{ row.account_name }}
                </td>

                <td data-label="Date / Time" v-html="`${row.date} ${row.time}`">

                </td>

                <td data-label="Debited / Time" v-html="row.debited_date_time != null ? row.debited_date_time : ''">
                
                </td>

                <td data-label="Dismissed / Time" v-html="row.dismissed_date_time != null ? row.dismissed_date_time : ''">
                
                </td>

                

              </tr>
            </tbody>
          </table>
          <div class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
            <BaseLevel>
              <BaseButtons>
                <BaseButton v-for="page in requests.links" :key="page" :active="page.active" :label="page.label"
                  :color="page.active ? 'lightDark' : 'whiteDark'" small @click="currentPage = page"
                  :href="page.url != null ? page.url : ''" :disabled="page.url === null" />
              </BaseButtons>
              <small>Page {{ requests.current_page }} of {{ requests.last_page }}</small>
            </BaseLevel>
          </div>
        </div>
      </CardBox>

      <CardBoxModal v-model="showCreditThisUserModal" button="danger" buttonLabel="Close"
        :title="`Verify Users Account Credit`">
        <h4 class="text-lg font-semibold text-primary"
          v-html="`Wallet Balance: ₦${mainStore.addCommas((selected_user.total_income - selected_user.withdrawn).toFixed(2))}`">
        </h4>
        
        <div class="">
          Bank Name: <em class='text-primary'>{{ selected_bank_name }}</em>
          <br>
          Account Number: <em
            class='text-primary'>{{ selected_account_number  }}</em>
            <br>
          Account Name: <em class='text-primary'>{{ selected_account_name }}</em>
        </div>
        <BaseButton @click="verifyAccountCredit" label="Verify Account Credit" color="success" rounded />
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
  mdiCreditCardMinus,
  mdiCheckBold ,
  mdiCloseThick,
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
import CardBoxModal from "@/components/CardBoxModal.vue";

import { useMainStore } from "@/stores/main";
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { computed, ref, reactive, watch } from 'vue'
import axios from "axios";
import _ from 'lodash';

const page = usePage();
const mainStore = useMainStore();

const selected_request_id = ref(null);
const selected_user_id = ref(null);
const selected_user = ref({});
const selected_user_index = ref(0);
const selected_bank_name = ref(""); 
const selected_account_number = ref("");
const selected_account_name = ref("");

const showCreditThisUserModal = ref(false);

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const user = props.value?.user;
const requests = props.value?.requests;
const useSearchBtn = mainStore.useSearchBtn;
console.log(requests)

const lengthOptions = ref([
  10,
  20,
  50,
  100
]);

const roleOptions = ref({
  dismissed: 'dismissed',
  debited: 'debited',
  pending: 'pending',
  all: 'all',
});

const verify_account_credit_form = useForm({
  id: null,
})

const verify_account_dismiss_form = useForm({
  id: null,
})
  
const dismissThisRequest = (row) => {
  Swal.fire({
    title: `Are You Sure You Want To Dismiss <em class='text-primary'>${row.name}</em>'s Withdrawal Request?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes Proceed!',
  }).then((result) => {
    verify_account_dismiss_form.id = row.id

    if (result.isConfirmed) {
      verify_account_dismiss_form.post(route('dismiss_account_credit_withdrawal'), {
        preserveScroll: true,
        onSuccess: (page) => {

          var response = page.props.flash.data;
          console.log(response)
          if (response.success) {

            Swal.fire({
              icon: 'success',
              title: 'Success',
              html: `Successfully Dismissed`,
            }).then((result) => {

              if (result.isConfirmed) {
                document.location.reload();
              }
            });

          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: `Something went wrong`,
            })
          }
        }, onError: (errors) => {
          var size = Object.keys(errors).length;
          errors_size.value = size;
          console.log(errors_size.value)
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `Something went wrong`,
          })
        },
      });

    } else if (result.isDenied) {
      // Swal.fire('Changes are not saved', '', 'info')
    }
  })
}


const verifyAccountCredit = () => {
  Swal.fire({
    title: `Are You Sure You Want To Mark <em class='text-primary'>${selected_user.value.name}</em>'s Withdrawal Request As Paid ?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes Proceed!',
  }).then((result) => {
    verify_account_credit_form.id = selected_request_id.value

    if (result.isConfirmed) {
      verify_account_credit_form.post(route('verify_account_credit_withdrawal'), {
        preserveScroll: true,
        onSuccess: (page) => {

          var response = page.props.flash.data;
          console.log(response)
          if (response.success) {

            Swal.fire({
              icon: 'success',
              title: 'Success',
              html: `Successfully Marked As Debited`,
            }).then((result) => {

              if (result.isConfirmed) {
                document.location.reload();
              }
            });
            
          }else{
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: `Something went wrong`,
            })
          }
        }, onError: (errors) => {
          var size = Object.keys(errors).length;
          errors_size.value = size;
          console.log(errors_size.value)
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `Something went wrong`,
          })
        },
      });

    } else if (result.isDenied) {
      // Swal.fire('Changes are not saved', '', 'info')
    }
  })
}



const form = useForm({
  length: props.value?.length,

  name: props.value?.name,
  phone: props.value?.phone,
  email: props.value?.email,
  country_name: props.value?.country_name,

  role: roleOptions.value[props.value?.role],

  amount: props.value?.amount,

  date: props.value?.date,
  debited_date_time: props.value?.debited_date_time,
  dismissed_date_time: props.value?.dismissed_date_time,
  start_date: props.value?.start_date,
  end_date: props.value?.end_date,

})
// console.log(form)

const clearFilterForm = () => {
  form.length = 10

  form.name = null
  form.phone = null
  form.email = null
  form.country_name = null


  form.role = "pending"

  form.amount = null

  form.date = null
  form.debited_date_time = null
  form.dismissed_date_time = null
  form.start_date = null
  form.end_date = null


  // console.log(form)
}



const submitFilterForm = () => {
  // console.log()
  let query = _.pickBy(form);
  let queryRoute = route('account_withdrawal_requests');
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

const creditThisUser = (row,index) =>{
  selected_request_id.value = row.id;
  selected_bank_name.value = row.real_bank_name;
  selected_account_number.value = row.account_number;
  selected_account_name.value = row.account_name;
  selected_user_id.value = row.user_id
  selected_user.value = row
  refreshUserData()

  setTimeout(() => {
    showCreditThisUserModal.value = true
  }, 2000);
}

async function refreshUserData() {



  await axios
    .post(route('get_user_data', selected_user_id.value))
    .then((response) => {

      console.log(response)
      if (response.data.success) {
        selected_user.value = response.data.user
        // users.data[selected_user_index.value] = selected_user.value
      }
    })
    .catch((error) => {
      alert(error.message);
    });

}

const submit = () => {

};
</script>

