<style>

</style>
<template>
  <LayoutGuest>

    <Head title="Register" />

    <SectionFullScreen v-slot="{ cardClass }" bg="bg-login-background">
      <!-- <CardBox
        :class="cardClass"
        class="my-24"
        is-form
        @submit.prevent="submit"
      >
        <FormValidationErrors />

        <FormField
          label="Name"
          label-for="name"
          help="Please enter your name"
        >
          <FormControl
            v-model="form.name"
            id="name"
            :icon="mdiAccount"
            autocomplete="name"
            type="text"
            required
          />
        </FormField>

        <FormField
          label="Email"
          label-for="email"
          help="Please enter your email"
        >
          <FormControl
            v-model="form.email"
            id="email"
            :icon="mdiEmail"
            autocomplete="email"
            type="email"
            required
          />
        </FormField>

        <FormField
          label="Password"
          label-for="password"
          help="Please enter new password"
        >
          <FormControl
            v-model="form.password"
            id="password"
            :icon="mdiFormTextboxPassword"
            type="password"
            autocomplete="new-password"
            required
          />
        </FormField>

        <FormField
          label="Confirm Password"
          label-for="password_confirmation"
          help="Please confirm your password"
        >
          <FormControl
            v-model="form.password_confirmation"
            id="password_confirmation"
            :icon="mdiFormTextboxPassword"
            type="password"
            autocomplete="new-password"
            required
          />
        </FormField>

        <FormCheckRadioGroup
          v-if="hasTermsAndPrivacyPolicyFeature"
          v-model="form.terms"
          name="remember"
          :options="{ agree: 'I agree to the Terms' }"
        />

        <BaseDivider />

        <BaseButtons>
          <BaseButton
            type="submit"
            color="info"
            label="Register"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          />
          <BaseButton
            route-name="login"
            color="info"
            outline
            label="Login"
          />
        </BaseButtons>
      </CardBox> -->

      <div class="w-full mx-3 sm:w-6/12 my-5 rounded-md shadow-lg bg-white text-black">

        <div class="px-5 py-4">
          <div class="logo-icon">
            <img class="w-9 h-9 inline-block mr-2" :src="Logo" alt="">
            <span class="text-black font-bold text-lg">Easybizu</span>
          </div>

          <div v-if="balance > 0" class="">
            <p class="text-gray-400 text-sm font-semibold my-2">Step 2 of registration</p>
            <small class="text-sm font-semibold">Complete registration payment</small>
            
            <h5 class="text-center text-sm font-bold mt-[30px]">Your details: </h5>
            <table class="table ">
              <tbody>
                <tr>
                  <td>First Commitment Fee</td>
                  <td><em class="text-primary" v-html="'₦' + first_commitment_fee"></em></td>
                </tr>
                <tr>
                  <td>Email</td>
                  <td><em class="text-primary">{{ user.email }}</em></td>
                </tr>
                <tr>
                  <td>Phone Number</td>
                  <td><em class="text-primary" v-html="`${user.phone_code}${user.phone}`"></em></td>
                </tr>
                <!-- <tr>
                  <td>Reg. Expiry Date</td>
                  <td><em class="text-primary">{{ $account_expiry_date }}</em></td>
                </tr> -->
                <tr>
                  <td>Balance</td>
                  <td><em class="text-primary" v-html="'₦' + balance"></em></td>
                </tr>
              </tbody>
            </table>

            <h5 class="text-center text-sm font-bold mt-[30px]">Deposit your personalized account: </h5>
            <small class="text-xs text-gray-400">₦20 charge for instant crediting</small>
            
            <!-- <table class="table">
              <tbody>
                <tr>
                  <td>Bank Name</td>
                  <td>Providus Bank</td>
                </tr>
                <tr>
                  <td>A/c Name</td>
                  <td><em class="text-primary">{{ user.providus_account_name }}</em></td>
                </tr>
                <tr>
                  <td>A/c No</td>
                  <td><em class="text-primary">{{ user.providus_account_number }}</em></td>
                </tr>
                
              </tbody>
            </table> -->

            <table class="table">
              <thead>
                <tr>
            
                  <th></th>
            
                  <th>Bank Name</th>
                  <th>Account Name</th>
                  <th>Account Number</th>
                </tr>
              </thead>
              <tbody>
            
                <tr v-if="user.providus_account_number != null">
                  <td></td>
                  <td>Providus Bank</td>
                  <td>{{ user.providus_account_name }}</td>
                  <td>{{ user.providus_account_number }}</td>
                </tr>
            
                <!-- <tr v-if="monnify_details.wema_account_number != null">
                  <td></td>
                  <td>Wema Bank</td>
                  <td>{{ monnify_details.wema_account_name }}</td>
                  <td>{{ monnify_details.wema_account_number }}</td>
                </tr>
            
                <tr v-if="monnify_details.sterling_account_number != null">
                  <td></td>
                  <td>Sterling Bank</td>
                  <td>{{ monnify_details.sterling_account_name }}</td>
                  <td>{{ monnify_details.sterling_account_number }}</td>
                </tr>
            
                <tr v-if="monnify_details.fidelity_account_number != null">
                  <td></td>
                  <td>Fidelity Bank</td>
                  <td>{{ monnify_details.fidelity_account_name }}</td>
                  <td>{{ monnify_details.fidelity_account_number }}</td>
                </tr>
            
                <tr v-if="monnify_details.moniepoint_account_number != null">
                  <td>Moniepoint</td>
                  <td>{{ monnify_details.moniepoint_account_name }}</td>
                  <td>{{ monnify_details.moniepoint_account_number }}</td>
                </tr> -->
              </tbody>
            </table>

            <!-- <h5 class="text-center font-bold text-sm mt-[30px]">Deposit To De Meet Global Resources: </h5>
            
            <table class="table">
              <tbody>
                <tr>
                  <td>Bank Name</td>
                  <td>GTBank</td>
                </tr>
                <tr>
                  <td>A/c No</td>
                  <td><em class="text-primary">0597824729</em></td>
                </tr>
                <tr>
                  <td>Bank Name</td>
                  <td>Access Bank</td>
                </tr>
                <tr>
                  <td>A/c No</td>
                  <td><em class="text-primary">0103304419</em></td>
                </tr>
              </tbody>
            </table> -->
            
            <!-- <h5 class="text-center mt-[30px] mb-[20px] text-sm font-bold">Submit Proof Of Payment</h5>

            <form @submit.prevent="submit" class="pt-3">
              
              <div class="">
            
                <text-input v-model="form.amount" :error="form.errors.amount" type="number"
                  label="Amount" id="amount" placeholder="" class="" />

                <text-input v-model="form.depositors_name" :error="form.errors.depositors_name" type="text" label="Depositors Name" id="depositors_name" placeholder=""
                  class="" />

                <text-input v-model="form.date_of_payment" :error="form.errors.date_of_payment" type="date" label="Date of payment"
                  id="depositors_name" placeholder="" class="" />

              

                <div class="mb-2 transition-all ease-linear duration-200">
                  <label class="login-form-label" for="image">Choose Image: </label>
                
                  <input id="image" ref="image" class="login-form-input"
                    :class="form.errors.image ? 'login-form-input-error' : 'login-form-input'" type="file"
                    />
                  <div v-if="form.errors.image" class="login-form-error">{{ form.errors.image }}</div>
                </div>

                

                <div class="progress">
                  <div v-if="form.progress" class="progress-bar progress-bar-primary" role="progressbar"
                    :aria-valuenow="form.progress.percentage" aria-valuemin="0" aria-valuemax="100"
                    :style="'width:'+ form.progress.percentage +'%'">
                    <span class="sr-only" v-html="form.progress.percentage + '% Complete'"></span>
                  </div>
                </div>
            
                <button :class="form.processing ? 'opacity-80 cursor-not-allowed' : ''"
                  @mouseleave="login_btn_hovered = false" @mouseover="login_btn_hovered = true" type="submit" class="login-button">
                  Submit
                  <img v-if="form.processing" class="inline-block w-7 h-6 float-right"
                    :src="login_btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
                </button>
              </div>
            </form> -->
          </div>

          <div v-else class="">
            <div v-if="pages.page_one_open" class="">
              <p class="text-gray-400 text-sm font-semibold my-2">Step 1</p>
              <!-- <small class="text-sm font-semibold">Enter your placement details</small> -->

              <small class="text-sm font-semibold text-gray-500">Enter your placements phone number</small>
              <form @submit.prevent="submitPageOneForm" class="pt-3">
                
                <div class="">
              
                  <div class="grid grid-cols-12 gap-6 my-3">
              
                    <select class="col-span-4 mb-[7px]" id="country" v-model="page_one_form.country"
                      :class="page_one_form.errors.country ? 'login-form-input-error' : 'login-form-input'">
                      <option v-for="country in countries" :value="country.id" :key="country.id"
                        v-html="`${country.name} (${country.phone_code})`"></option>
                    </select>
              
                    <div class="col-span-8">
                      <text-input v-model="page_one_form.placement_phone_number" :error="page_one_form.errors.placement_phone_number"
                        type="number" label="Placement Phone Number" id="placement_phone_number" placeholder="" />
                    </div>
                  </div>
              
                  <div class="my-4 px-1">
                  
                    <span class="mb-5 login-checkbox-label mt-1 float-right text-black">No placement?
                      <span @click="processNoPlacement" class=" hover:text-primary">Click Here</span>
                    </span>
                  </div>
              
                  <button :class="page_one_form.processing ? 'opacity-80 cursor-not-allowed' : ''"
                    @mouseleave="login_btn_hovered = false" @mouseover="login_btn_hovered = true" type="submit" class="login-button">
                    Proceed
                    <img v-if="page_one_form.processing" class="inline-block w-7 h-6 float-right"
                      :src="login_btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
                  </button>
                </div>
              </form>
            </div>

            <div v-if="pages.page_two_open" class="">
              <p class="text-gray-400 text-sm font-semibold my-2">Step 2</p>
              <!-- <small class="text-sm font-semibold">Enter your placement details</small> -->
            
              <small class="text-sm font-semibold text-gray-500">Click To select placement position</small>
              
              <div v-if="page_two_form.available_position == 'left'" class="">
                <table class="table" id="select-placement-position-table" cellspacing="0" width="100%" style="width:100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Available Positions</th>
                
                    </tr>
                  </thead>      
                  <tbody>
                    <tr @click="selectThisPositionPlacement('left')" class="cursor-pointer">
                      <td>1</td>
                      <td>Left</td>
                    </tr>
                  </tbody>        
                </table>
              </div>

              <div v-if="page_two_form.available_position == 'right'" class="">
                <table class="table" id="select-placement-position-table" cellspacing="0" width="100%" style="width:100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Available Positions</th>
              
                    </tr>
                  </thead>
                  <tbody>
                    <tr @click="selectThisPositionPlacement('right')" class="cursor-pointer">
                      <td>1</td>
                      <td>Right</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-if="page_two_form.available_position == 'both'" class="">
                <table class="table" id="select-placement-position-table" cellspacing="0" width="100%" style="width:100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Available Positions</th>
              
                    </tr>
                  </thead>
                  <tbody>

                    <tr @click="selectThisPositionPlacement('left')" class="cursor-pointer">
                      <td>1</td>
                      <td>Left</td>
                    </tr>
                    <tr @click="selectThisPositionPlacement('right')" class="cursor-pointer">
                      <td>2</td>
                      <td>Right</td>
                    </tr>
                  </tbody>
                </table>
              </div>



            </div>
          </div>
        </div>
      </div>

      
      <div v-if="pages.page_two_open" @click="switch_pages('page_one_open')">
        <floating-action-button :title="'Go Back'">
      
          <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
        </floating-action-button>
      </div>

      
     
    </SectionFullScreen>
  </LayoutGuest>
</template>
<script setup>
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import { computed, ref } from 'vue'
import { mdiAccount, mdiEmail, mdiFormTextboxPassword } from '@mdi/js'
import LayoutGuest from '@/layouts/LayoutGuest.vue'
import SectionFullScreen from '@/components/SectionFullScreen.vue'
import CardBox from '@/components/CardBox.vue'
import FormCheckRadioGroup from '@/components/FormCheckRadioGroup.vue'
import FormField from '@/components/FormField.vue'
import FormControl from '@/components/FormControl.vue'
import BaseDivider from '@/components/BaseDivider.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseButtons from '@/components/BaseButtons.vue'
import FormValidationErrors from '@/components/FormValidationErrors.vue'
// import Logo from '@/Icons/onehealth_logo_icon.svg'
import GoogleIcon from '@/Icons/google_icon.svg'
import FacilityIcon from '@/Icons/facility_icon.svg'
import PatientIcon from '@/Icons/patient_icon.svg'
import TextInput from '@/Components/TextInput.vue'
import FileInput from '@/Components/FileInput.vue'
import FormLoaderDark from '@/Loaders/form_loader_dark.gif'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'
import FlashMessages from '@/Components/FlashMessages.vue'
import Logo from '@/Icons/easybizu_logo.jpeg'
import UserAvatar from '@/components/UserAvatar.vue'
import FloatingActionButton from '@/components/FloatingActionButton.vue'




const page = usePage()
const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)


const monnify_details = page.props.value?.monnify_details;
console.log(monnify_details)
const countries = page.props.value?.countries;
const user = page.props.value?.user;
const balance = page.props.value?.balance; 
const first_commitment_fee = page.props.value?.first_commitment_fee;


const login_btn_hovered = ref(false);
const image = ref(null);

const main_form = useForm({
  placement_user_id: null,
  positioning: null,
  mlm_db_id: null,
  position_selected: false,
});

const selectThisPositionPlacement = (position = null) => {
  if (!main_form.processing) {
    main_form.positioning = position 
    main_form.position_selected = (position == null) ? false : true;
    switch_pages("page_one_open",true)
    Swal.fire({
      title: 'Processing Registration',
      html: 'Placement selected. Completing your registration. Please do not reload page.',
      icon: 'success',
      showCancelButton: false,
      showConfirmButton: false,
      allowEscapeKey: false,
      allowOutsideClick: false,
    })
    main_form.post(route('complete_registration_step_2'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        if (response.success) {
          
          // Swal.fire({
          //   title: 'Success',
          //   html: 'Your account has been successfully created. Redirecting you now',
          //   icon: 'success',
          // })

          // setTimeout
        } 
      }, onError: (errors) => {
        var size = Object.keys(errors).length;
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: `Sorry something went wrong`,
        })
      },
    });
  }
}

const checkIfAnyPositionIsAvailableUnderPlacement = () => {
  if (!main_form.processing) {
    main_form.post(route('select_positioning_for_mlm_registration'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        if (response.success && response.available_position != "" && response.mlm_db_id != "") {
          page_two_form.available_position = response.available_position;
          main_form.mlm_db_id = response.mlm_db_id;
          switch_pages("page_two_open");
          console.log(main_form)
        } else if (response.invalid_placement) {
          Swal.fire({
            icon: 'error',
            title: 'Invalid Placement',
            text: `Placement selected is invalid`,
          })
        } else if (response.no_available_position) {
          Swal.fire({
            icon: 'error',
            title: 'No Available Position',
            text: `Sorry No Available Position Under This Account.`,
          })
        } else{
          Swal.fire({
            icon: 'error',
            title: 'Ooops!',
            text: `Sorry something went wrong`,
          })
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
}

const form = useForm({

  amount: null,
  depositors_name: null,
  date_of_payment: null,
  image: null,
})

const submit = () => {
  form.image = image.value.files[0];
  if (!form.processing) {
    form.post(route('submit_proof_of_payment_to_admin'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        if (response.success) {

          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Your Request Has Been Sent To The Admin. You Will Soon Be Credited.',
          }).then((result) => {
            if (result.isConfirmed) {
              document.location.reload()
            }
          })

        } else if (response.empty) {
          Swal.fire({
            title: 'Ooops',
            text: "You Must Select An Image To Upload For Payment Proof",
            icon: 'error',
          })
        } else if (!response.only_one_image) {
          Swal.fire({
            title: 'Ooops',
            text: "You Can Only Select One Image To Upload As Payment Proof",
            icon: 'error',
          })
        } else if (response.errors != "") {

          Swal.fire({
            title: 'Error',
            html: response.errors,
            icon: 'error',

          })
        } else {

          Swal.fire({
            title: 'Ooops!',
            html: 'Something Went Wrong. Please Try Again',
            icon: 'error',

          })
        }

      }, onError: (errors) => {
        console.log(errors)
        var size = Object.keys(errors).length;
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: `There are ${size} form errors. Please fix them`,
        })
      },
    });
  }
}

const pages = ref({
  page_one_open: false,
  page_two_open: false,
  page_three_open: false,
  page_four_open: false,
});

const switch_pages = (page = "page_one_open",close_all = false) => {
  Object.keys(pages.value).forEach(key => {
    pages.value[key] = false;
  });
  pages.value[page] = (close_all) ? false : true;

  // console.log(pages.value)
}

const page_one_form = useForm({
  country: 151,
  placement_phone_number: '',
});

const submitPageOneForm = () => {
  if (!page_one_form.processing) {
    page_one_form.post(route('get_reg_placement_info'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        if (response.success) {

          Swal.fire({
            icon: 'success',
            title: `Do you select ${response.placement.name} as your placement?`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Proceed!',
            cancelButtonText: 'No',
            html: `<div class=''>
              <p class='text-left text-sm font-semibold'>Full Name: <em
                  class='text-primary'>${response.placement.name}</em></p>
              
              <p class='text-left text-sm font-semibold'>Phone Number: <em
                  class='text-primary'>${response.placement.phone_code}${response.placement.phone}</em></p>
              <p class='text-left text-sm font-semibold'>Email Adress: <em
                  class='text-primary'>${response.placement.email}</em></em></p>
              </div>
            </div>`,
          }).then((result) => {
            if (result.isConfirmed) {
              main_form.placement_user_id = response.placement.id
              // switch_pages('page_two_open')
              checkIfAnyPositionIsAvailableUnderPlacement();
            }
          })
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `This placement does not exist`,
          })
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

const page_two_form = useForm({
  available_position: null,
  
});


const processNoPlacement = () => {
  switch_pages("page_one_open",true)
  console.log('getting you a placement..')

  main_form.placement_user_id = null;
  main_form.positioning = null;
  main_form.mlm_db_id = null;
  main_form.position_selected = false;
  selectThisPositionPlacement()
  
}

if (balance <= 0){
  const onConfirmRefresh = function (event) {
    event.preventDefault();
    return event.returnValue = "Are you sure you want to leave the page?";
  }

  window.addEventListener("beforeunload", onConfirmRefresh, { capture: true });

  console.log(balance)
  Swal.fire({
    title: 'Commitment Fee Successful',
    html: 'Do You Have A Placement?',
    icon: 'success',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes',
    cancelButtonText: "No",
    allowEscapeKey: false,
    allowOutsideClick: false,
  }).then((result) => {
    if (result.isConfirmed) {
      switch_pages("page_one_open");
    } else if (
      /* Read more about handling dismissals below */
      result.dismiss === Swal.DismissReason.cancel
    ) {
      processNoPlacement();
    }
  });
}










</script>
