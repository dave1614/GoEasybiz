<template inheritAttrs="false">
  <LayoutAuthenticated>

    <Head title="Members List" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiClipboardTextClock" title="Members List" main>
        <!-- <BaseButton href="https://github.com/justboil/admin-one-vue-tailwind" target="_blank" :icon="mdiGithub"
          label="Star on GitHub" color="contrast" rounded-full small /> -->
      </SectionTitleLineWithButton>
      <!-- <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Responsive table.</b> Collapses on mobile
      </NotificationBar> -->

      <!-- <CardBox v-if="useSearchBtn" form @submit.prevent="submitFilterForm" class=""> -->
      <div v-if="!showHistoriesModal">
        <CardBox isForm @submit.prevent="submitFilterForm" class="">
          <div class="sm:grid sm:grid-cols-12 sm:gap-6">
            <FormField class="sm:col-span-4" label="Length">
              <FormControl v-model="form.length" :options="lengthOptions" />
            </FormField>
            <FormField class="sm:col-span-4" label="Full Name">
              <FormControl v-model="form.name" type="text" />
            </FormField>
            <FormField class="sm:col-span-4" label="Country">
              <FormControl v-model="form.country" :options="countries" />
            </FormField>
            <FormField class="sm:col-span-4" label="Phone Number">
              <FormControl v-model="form.phone" type="text" />
            </FormField>
            <FormField class="sm:col-span-4" label="Email Address">
              <FormControl v-model="form.email" type="email" />
            </FormField>
            <FormField class="sm:col-span-4" label="Registration Date">
              <FormControl v-model="form.created_date" type="date" />
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

          <div v-if="users.data.length > 0" class="">
            <table>
              <thead>
                <tr>

                  <th>#</th>
                  <th>Full Name</th>
                  <th>Country</th>
                  <th>Phone Number</th>
                  <th>Date Of Registration</th>
                  <th>Email Address</th>
                  <th>Wallet Balance</th>
                  <th>Total Earnings</th>
                  <th>Address</th>
                  <th>Actions</th>

                </tr>
              </thead>
              <tbody>
                <tr v-for="(row,index) in users.data" :key="row.id">

                  <!-- <td>{{ row.index }}. </td> -->

                  <td v-html="`${(index + 1) +((users.current_page - 1) * form.length)}.`"></td>
                  <td class="capitalize" data-label="Full Name">
                    {{ row.name }}
                  </td>
                  <td class="capitalize" data-label="Country">
                    {{ row.country_name }}
                  </td>
                  <td  data-label="Phone Number">
                    {{ row.phone }}
                  </td>
                  <td  data-label="Registration Date" v-html="`${row.created_date} ${row.created_time}`">
                    
                  </td>
                  <td data-label="Email Address" v-html="`${row.email}`">

                  </td>
                  <td data-label="Wallet Balance" v-html="mainStore.addCommas((row.total_income - row.withdrawn).toFixed(2))">

                  </td>

                  <td data-label="Total Earnings" v-html="mainStore.addCommas((row.total_earnings - 0).toFixed(2))">
                  
                  </td>

                  <td data-label="Address" >
                    {{ row.address }}
                  </td>
                  <td class="before:hidden lg:w-1 whitespace-nowrap">
                    <BaseButtons type="justify-start lg:justify-end" no-wrap>
                      <BaseButton color="info" :icon="mdiAccountCog" small @click="processShowPerformActionModal(row,index)" />
                      <!-- <BaseButton color="danger" :icon="mdiTrashCan" small @click="isModalDangerActive = true" /> -->
                    </BaseButtons>
                  </td>

                </tr>
              </tbody>
            </table>
            <div class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
              <BaseLevel>
                <BaseButtons>
                  <BaseButton v-for="page in users.links" :key="page" :active="page.active" :label="page.label"
                    :color="page.active ? 'lightDark' : 'whiteDark'" small @click="currentPage = page"
                    :href="page.url != null ? page.url : ''" :disabled="page.url === null" />
                </BaseButtons>
                <small>Page {{ users.current_page }} of {{ users.last_page }}</small>
              </BaseLevel>
            </div>
          </div>
        </CardBox>

        <CardBoxModal v-model="showPerformActionmModal" button="danger" buttonLabel="Close" :title="`Choose action to perform on ${selected_user.name}`">
          <table class="mt-8">
            
            <tbody>
              <tr class="cursor-pointer" @click="editUserProfile">
                <td>1.</td>
                <td class="text-primary">Edit Users Profile</td>
              </tr>
            
              <tr class="cursor-pointer" @click="creditUsersWallet">
                <td>2.</td>
                <td class="text-primary">Credit Users Wallet</td>
              </tr>
              <tr class="cursor-pointer" @click="debitUsersWallet">
                <td>3.</td>
                <td class="text-primary">Debit Users Wallet</td>
              </tr>
              <tr class="cursor-pointer" @click="viewUsersHistory">
                <td>4.</td>
                <td class="text-primary">View Users History</td>
              </tr>
              
              
            
            </tbody>
          </table>
        </CardBoxModal>

        <CardBoxModal v-model="showCreditUserModal" button="danger" buttonLabel="Close"
          :title="`Enter amount to credit ${selected_user.name}`">
          <h4 class="text-lg font-semibold text-primary" v-html="`Wallet Balance: ₦${mainStore.addCommas((selected_user.total_income - selected_user.withdrawn).toFixed(2))}`"></h4>

          <form @submit.prevent="creditThisUser">
            <FormField  label="Enter Amount">
              <FormControl v-model="credit_user_form.amount" :icon="mdiAccountCash" type="number" required/>
            
            </FormField>

            <button :class="credit_user_form.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="btn_hovered = false"
              @mouseover="btn_hovered = true" type="submit" class="app-form-button">
              Submit
              <img v-if="credit_user_form.processing" class="inline-block w-7 h-6 float-right"
                :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
            </button>
          </form>
        </CardBoxModal>

        <CardBoxModal v-model="showDebitUserModal" button="danger" buttonLabel="Close"
          :title="`Enter amount to debit ${selected_user.name}`">
          <h4 class="text-lg font-semibold text-primary"
            v-html="`Wallet Balance: ₦${mainStore.addCommas((selected_user.total_income - selected_user.withdrawn).toFixed(2))}`">
          </h4>
        
          <form @submit.prevent="debitThisUser">
            <FormField label="Enter Amount">
              <FormControl v-model="debit_user_form.amount" :icon="mdiAccountCash" type="number" required />
        
            </FormField>
        
            <button :class="debit_user_form.processing ? 'opacity-80 cursor-not-allowed' : ''"
              @mouseleave="btn_hovered = false" @mouseover="btn_hovered = true" type="submit" class="app-form-button">
              Submit
              <img v-if="debit_user_form.processing" class="inline-block w-7 h-6 float-right"
                :src="btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
            </button>
          </form>
        </CardBoxModal>
      </div>

      <CardBox v-if="showHistoriesModal" class="mb-6" has-table>
        <h3 class="text-xl font-bold mx-3 my-5" v-html="`Choose ${selected_user.name}'s history to view`"></h3>
        <table class="mt-8">
        
          <tbody class="">
            <tr class="cursor-pointer" @click="Inertia.visit(route('users_account_credit_history',selected_user_id))">
              <td>1.</td>
              <td class="text-primary">Account Credit History</td>
            </tr>
        
            <tr class="cursor-pointer" @click="Inertia.visit(route('users_withdrawal_history',selected_user_id))">
              <td>2.</td>
              <td class="text-primary">Withdrawal History</td>
            </tr>
            <tr class="cursor-pointer" @click="Inertia.visit(route('users_vtu_history',selected_user_id))">
              <td>3.</td>
              <td class="text-primary">VTU History</td>
            </tr>
            <tr class="cursor-pointer" @click="Inertia.visit(route('users_transfer_history',selected_user_id))">
              <td>4.</td>
              <td class="text-primary">Transfer History</td>
            </tr>
            <tr class="cursor-pointer" @click="Inertia.visit(route('users_admin_credit_history',selected_user_id))">
              <td>5.</td>
              <td class="text-primary">Admin Credit History</td>
            </tr>
        
            <tr class="cursor-pointer" @click="Inertia.visit(route('users_admin_debit_history',selected_user_id))">
              <td>6.</td>
              <td class="text-primary">Admin Debit History</td>
            </tr>
        
            <tr class="cursor-pointer" @click="Inertia.visit(route('users_account_statement',selected_user_id))">
              <td>7.</td>
              <td class="text-primary">Account Statement</td>
            </tr>
            <tr class="cursor-pointer" @click="Inertia.visit(route('users_earnings_to_wallet',selected_user_id))">
              <td>8.</td>
              <td class="text-primary">Earnings To Wallet History</td>
            </tr>
            <tr class="cursor-pointer" @click="Inertia.visit(route('users_easy_loan_history',selected_user_id))">
              <td>9.</td>
              <td class="text-primary">Easy Loan History</td>
            </tr>
          </tbody>
        </table>
      </CardBox>




    </SectionMain>
    <FloatingActionButton v-if="showHistoriesModal" :title="'Go Back'" @click="showHistoriesModal = false">
    
      <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
    </FloatingActionButton>
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
  mdiClipboardTextClock,
  mdiAccountCog,
  mdiAccountCash,
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
const showPerformActionmModal = ref(false);
const showCreditUserModal = ref(false);
const showDebitUserModal = ref(false);
const showHistoriesModal = ref(false);

const selected_user_id = ref(null);
const selected_user = ref({});
const selected_user_index = ref(0);
const btn_hovered = ref(false);

// const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const props = page.props;
const users = props.value?.users;
const countries = page.props.value?.countries;
const selected_country = page.props.value?.selected_country;
const useSearchBtn = mainStore.useSearchBtn;
console.log(users)

const lengthOptions = ref([
  10,
  20,
  50,
  100
]);




const form = useForm({
  length: props.value?.length,
  name: props.value?.name,
  country: props.value?.country,
  phone: props.value?.phone,
  email: props.value?.email,
  created_date: props.value?.created_date,
  start_date: props.value?.start_date,
  end_date: props.value?.end_date,

})

const debit_user_form = useForm({
  amount: null,
})

const credit_user_form = useForm({
  amount: null,
})

const user_form = useForm({
  user_id: selected_user_id.value
})
// console.log(form)

const clearFilterForm = () => {
  form.length = 10
  form.name = null
  form.country = countries[selected_country],
  form.phone = null
  form.email = null
  form.created_date = null
  form.start_date = null
  form.end_date = null

  

  // console.log(form)
}



const submitFilterForm = () => {
  console.log('test')
  let query = _.pickBy(form);
  let queryRoute = route('view_members_list', Object.keys(query).length ? query : {
    remember: 'forget'
  });


  Inertia.get(queryRoute, {}, {
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

const viewUsersHistory = () => {
  showHistoriesModal.value = true
}

const debitThisUser = () => {
  Swal.fire({
    title: `Are You Sure You Want To Debit <em class='text-primary'>${selected_user.value.name}</em>  <em class='text-primary'>₦${mainStore.addCommas(debit_user_form.amount)}</em> ?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes Proceed!',
  }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
      debit_user_form.post(route('debit_user', selected_user_id.value), {
        preserveScroll: true,
        onSuccess: (page) => {

          var response = page.props.flash.data;
          console.log(response)
          if (response.success) {

            Swal.fire({
              icon: 'success',
              title: 'Success',
              html: `You have successfully debited <em class='text-primary'>${selected_user.value.name}</em>  <em class='text-primary'>₦${mainStore.addCommas(debit_user_form.amount)}</em`,
            })
            debit_user_form.amount = null
            refreshUserData()
          } else if (response.max) {

            
            Swal.fire({
              icon: 'error',
              title: 'Ooops',
              html: `<em class='text-primary'>${selected_user.value.name}</em> Wallet Balance Is <em class='text-primary'>${response.wallet_balance.toFixed(2)}</em>. You Cannot Debit Him Beyond This.`,
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

const creditThisUser = () => {
  Swal.fire({
    title: `Are You Sure You Want To Credit <em class='text-primary'>${selected_user.value.name}</em>  With <em class='text-primary'>₦${mainStore.addCommas(credit_user_form.amount)}</em> ?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes Proceed!',
  }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
      credit_user_form.post(route('credit_user',selected_user_id.value), {
        preserveScroll: true,
        onSuccess: (page) => {

          var response = page.props.flash.data;
          console.log(response)
          if (response.success){

            Swal.fire({
              icon: 'success',
              title: 'Success',
              html: `You have successfully credited <em class='text-primary'>${selected_user.value.name}</em>  With <em class='text-primary'>₦${mainStore.addCommas(credit_user_form.amount)}</em`,
            })
            credit_user_form.amount = null
            refreshUserData()
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

const debitUsersWallet = () => {
  refreshUserData()
  showDebitUserModal.value = true
}

const creditUsersWallet = () => {
  refreshUserData()
  showCreditUserModal.value = true
}

const editUserProfile = () => {
  var user_id = selected_user.value.id;
  // showPerformActionmModal.value = false
  setTimeout(() => {
    Inertia.visit(route('admin_edit_user_profile', user_id))
  }, 100);
}

const processShowPerformActionModal = (user,index) => {
  selected_user_id.value = user.id
  selected_user.value = user
  showPerformActionmModal.value = true
  selected_user_index.value = index
}

async function refreshUserData () {
  
  

  await axios
    .post(route('get_user_data', selected_user_id.value))
    .then((response) => {
      
      console.log(response)
      if (response.data.success){
        selected_user.value = response.data.user
        users.data[selected_user_index.value] = selected_user.value
      }
    })
    .catch((error) => {
      alert(error.message);
    });

}

</script>

