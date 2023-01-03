<style>
  
</style>
<template>
  <LayoutGuest>
    <Head title="Join and enjoy Go-Easybiz telecoms cash. It's Quick! Easy! Reliable!" />

    <SectionFullScreen
      v-slot="{ cardClass }"
      bg="bg-login-background"
    >
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

      <div v-if="pages.page_one_open" class="w-full mx-3 sm:w-6/12 my-5 rounded-md shadow-lg bg-white ">
      
        <div class="px-5 py-4">
          <div class="logo-icon">
            <img class="w-9 h-9 inline-block mr-2" :src="Logo" alt="">
            <span class="text-black font-bold text-lg">Go-Easybiz</span>
          </div>
          <p class="text-gray-400 text-sm font-semibold my-2">Sign up to go-easybiz today</p>
          <small class="text-sm font-semibold text-gray-500">Enter your sponsors phone number</small>
          <form @submit.prevent="submitPageOneForm" class="pt-3">
            <!-- <flash-messages /> -->
            <div class="">

              <div class="grid grid-cols-12 gap-6 my-3">
                
                <select class="col-span-4 mb-[7px]" id="country" v-model="page_one_form.country"
                  :class="page_one_form.errors.country ? 'login-form-input-error' : 'login-form-input'">
                  <option v-for="country in countries" :value="country.id" :key="country.id" v-html="`${country.name} (${country.phone_code})`"></option>
                </select>
      
                <div class="col-span-8">
                  <text-input v-model="page_one_form.sponsor_phone_number"
                    :error="page_one_form.errors.sponsor_phone_number" type="number" label="Sponsors Phone Number" id="sponsor_phone_number"
                    placeholder="" />
                </div>
              </div>

              <div class="my-4 px-1">
                <!-- <input type="checkbox" class="login-checkbox" id="terms" v-model="form.terms" />
                <label for="terms" class="login-checkbox-label text-black">I accept the </label>
                <span class="login-checkbox-label m-0 text-primary ">terms and conditions</span> -->
                <span class="mb-5 login-checkbox-label mt-1 float-right text-black">Already registered? <Link :href="route('login')" class=" text-primary">Login</Link></span>
              </div>
              
              <button :class="page_one_form.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="login_btn_hovered = false"
                @mouseover="login_btn_hovered = true" type="submit" class="login-button">
                Proceed
                <img v-if="page_one_form.processing" class="inline-block w-7 h-6 float-right"
                  :src="login_btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
              </button>
            </div>
          </form>
        </div>
      </div>

      <div v-if="pages.page_two_open" class="w-full mx-3 sm:w-6/12 my-5 rounded-md shadow-lg bg-white ">
      
        <div class="px-5 py-4">
          <div class="logo-icon">
            <img class="w-9 h-9 inline-block mr-2" :src="Logo" alt="">
            <span class="text-black font-bold text-lg">Go-Easybiz</span>
          </div>
          <p class="text-gray-400 text-sm font-semibold my-2">Sign up to go-easybiz today</p>
          <small class="text-sm font-semibold text-gray-500">Enter your personal details</small>
          <form @submit.prevent="submitPageTwoForm" class="pt-3">
            <!-- <flash-messages /> -->
            <div class="">

              <text-input v-model="page_two_form.name" :error="page_two_form.errors.name" type="text" label="Full Name" id="name" placeholder=""
                class="" />
              <text-input v-model="page_two_form.email" :error="page_two_form.errors.email" type="email" label="Email Address" id="email" placeholder=""
                class="" />
              
      
              <div class="grid grid-cols-12 gap-6 my-3">
      
                <select class="col-span-4 mb-[7px]" id="country" v-model="page_two_form.country"
                  :class="page_two_form.errors.country ? 'login-form-input-error' : 'login-form-input'">
                  <option v-for="country in countries" :value="country.id" :key="country.id"
                    v-html="`${country.name} (${country.phone_code})`"></option>
                </select>
      
                <div class="col-span-8">
                  <text-input  v-model="page_two_form.phone"
                    :error="page_two_form.errors.phone" type="number" label="Phone Number"
                    id="phone_number" placeholder="E.g 07051942325" />
                </div>
              </div>
      
              <div class="my-4 px-1">
                
                <span class="mb-5 login-checkbox-label mt-1 float-right text-black">Already registered?
                  <Link :href="route('login')" class=" hover:text-primary">Login</Link>
                </span>
              </div>
      
              <button :class="page_two_form.processing ? 'opacity-80 cursor-not-allowed' : ''"
                @mouseleave="login_btn_hovered = false" @mouseover="login_btn_hovered = true" type="submit"
                class="login-button">
                Proceed
                <img v-if="page_two_form.processing" class="inline-block w-7 h-6 float-right"
                  :src="login_btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
              </button>
            </div>
          </form>
        </div>
      </div>

      <div v-if="pages.page_three_open" class="w-full mx-3 sm:w-6/12 my-5 rounded-md shadow-lg bg-white ">
      
        <div class="px-5 py-4">
          <div class="logo-icon">
            <img class="w-9 h-9 inline-block mr-2" :src="Logo" alt="">
            <span class="text-black font-bold text-lg">Go-Easybiz</span>
          </div>
          <p class="text-gray-400 text-sm font-semibold my-2">Sign up to go-easybiz today</p>
          <small class="text-sm font-semibold  text-gray-500">Enter your authentication details</small>
          <form @submit.prevent="submit" class="pt-3">
            <!-- <flash-messages /> -->
            <div class="">
      
              <text-input v-model="form.password" :error="form.errors.password" type="password" label="Password" id="password"
                placeholder="" class="" />
              
              <text-input v-model="form.password_confirmation" :error="form.errors.password_confirmation" type="password"
                label="Confirm Password" id="password_confirmation" placeholder="" class="" />
      
              <div class="my-4 px-1">
                <input type="checkbox" class="login-checkbox" id="terms" v-model="form.terms" />
                <label for="terms" class="login-checkbox-label text-black">I accept the </label>
                <span class="login-checkbox-label m-0 text-primary ">terms and conditions</span>

                <span class="mb-5 login-checkbox-label mt-1 float-right text-black">Already registered?
                  <Link :href="route('login')" class=" hover:text-primary">Login</Link>
                </span>
              </div>
      
              <button :class="form.processing ? 'opacity-80 cursor-not-allowed' : ''"
                @mouseleave="login_btn_hovered = false" @mouseover="login_btn_hovered = true" type="submit"
                class="login-button">
                Submit
                <img v-if="form.processing" class="inline-block w-7 h-6 float-right"
                  :src="login_btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
              </button>
            </div>
          </form>
        </div>
      </div>




      <!-- <div v-if="false" class="w-full mx-3 sm:w-6/12 my-5 rounded-md shadow-lg bg-white ">
        
        <div class="px-5 py-4">
          <div class="logo-icon">
            <img class="w-9 h-9 inline-block mr-2" :src="Logo" alt="">
            <span class="text-black font-bold text-lg">Easybizu</span>
          </div>
          <p class="text-gray-400 text-sm font-semibold my-2">Sign up to easybizu today</p>
          <div class="mt-4 px-2">
            <button class="font-semibold bg-facebook-blue text-white text-center px-4 py-2 rounded"><i class="text-lg fab fa-facebook-f"></i><span class="text-xs  ml-2">Sign up with facebook</span></button>

            <button class="text-black font-semibold text-center px-4 py-2 rounded border border-gray-300 float-right"> <img class="w-5 h-5 inline-block" :src="GoogleIcon" alt="Google Icon"> <span class="text-xs  ml-2">Sign up with google</span></button>
          </div>
          <div class="my-3 flex justify-center">
            <div class="bg-gray-700 w-5 h-[2px] inline-block mt-2"></div>
            <span class="text-gray-700 text-xs mx-2">OR</span>
            <div class="bg-gray-700 w-5 h-[2px] inline-block mt-2"></div>
          </div>

          


          <form @submit.prevent="submit" class="pt-3">
            
            <div class="">
              
              <text-input @focusout="getSponsorUserInfo" v-model="form.sponsor_user_name" :error="form.errors.sponsor_user_name" type="text" label="Sponsor Username" id="sponsor_user_name" placeholder=""
                class="" />

              <div v-if="sponsor_info.user_name != ''" class="card" id="sponsor-info-card">
                <div class='container'>
                  <h3 class='text-lg font-bold text-center'>Sponsor Details</h3>
                  <div class='grid grid-cols-12 gap-6 mt-8'>
                    <UserAvatar class="col-span-3" :username="sponsor_info.full_name" api="initials" />
                    <div class='col-span-9'>
                      <p class='text-left text-sm font-semibold'>Full Name: <em
                          class='text-primary'>{{ sponsor_info.full_name }}</em></p>
                      <p class='text-left text-sm font-semibold'>User Name: <em
                          class='text-primary'>{{ sponsor_info.user_name }}</em></p>
                      <p class='text-left text-sm font-semibold'>Phone Number: <em
                          class='text-primary'>{{ sponsor_info.phone_number }}</em></p>
                      <p class='text-left text-sm font-semibold'>Email Adress: <em
                          class='text-primary'>{{ sponsor_info.email }}</em></p>
                      </div>
                    </div>
                  </div>
              </div>
              
              <text-input v-model="form.name" :error="form.errors.name" type="text" label="Full Name" id="name" placeholder=""
                class="" />
              <text-input v-model="form.email"  :error="form.errors.email" type="email" label="Email Address" id="email"
                placeholder="" class="" />

              <text-input v-model="form.phone" :error="form.errors.phone" type="number" label="Phone Number" id="phone" placeholder="E.g 07051942325"
                class="" />

              

              <div class="relative w-full mb-3">
                <label class="login-form-label" :for="'country'">Country:</label>
              
                <select  id="country" v-model="form.country"
                  :class="form.errors.country ? 'login-form-input-error' : 'login-form-input'">
                  <option v-for="country in countries" :value="country.id" :key="country.id">{{ country.name }}</option>
                </select>
              
                <div v-if="form.errors.country" class="login-form-error">{{ form.errors.country }}</div>
              </div>
              
              <div class="relative w-full mb-3">
                <label class="login-form-label" :for="'region'">Region:</label>
              
                <select id="region" v-model="form.region"
                  :class="form.errors.region ? 'login-form-input-error' : !region_disabled ? 'login-form-input' : 'login-form-input opacity-50 cursor-not-allowed'">
                  <option v-for="region in regions" :value="region.id" :key="region.id">{{ region.name }}</option>
                </select>
              
                <div v-if="form.errors.region" class="login-form-error">{{ form.errors.region }}</div>
              </div>
              

              
            
              <text-input v-model="form.user_name" :error="form.errors.user_name" type="text" label="Username" id="user_name"
                placeholder="" class="" />

              <text-input v-model="form.password" :error="form.errors.password" type="password" label="Password" id="password"
                placeholder="" class="" />
              
              <text-input v-model="form.password_confirmation" :error="form.errors.password_confirmation" type="password"
                label="Confirm Password" id="password_confirmation" placeholder="" class="" />
            </div>
            <div class="mt-4 px-1">
              <input type="checkbox" class="login-checkbox" id="terms" v-model="form.terms"/>
              <label for="terms" class="login-checkbox-label text-black">I accept the </label>
              <span class="login-checkbox-label m-0 text-primary ">terms and conditions</span>
              <Link :href="route('login')" class="login-checkbox-label mt-1 float-right hover:text-primary text-black">Already registered?</Link>
            </div>
            
            <button :class="form.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="login_btn_hovered = false" @mouseover="login_btn_hovered = true" type="submit" class="login-button">
              Submit
              <img v-if="form.processing" class="inline-block w-7 h-6 float-right" :src="login_btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
            </button>
          </form>
        </div>
          
        
      </div> -->

      <div v-if="pages.page_two_open" @click="switch_pages('page_one_open')">
        <floating-action-button  :title="'Go Back'">
          
          <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
        </floating-action-button>
      </div>

      <div v-if="pages.page_three_open" @click="switch_pages('page_two_open')">
        <floating-action-button :title="'Go Back'">
      
          <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
        </floating-action-button>
      </div>
    </SectionFullScreen>
  </LayoutGuest>
</template>
<script setup>
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import { computed,ref } from 'vue'
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
import FormLoaderDark from '@/Loaders/form_loader_dark.gif'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'
import FlashMessages from '@/Components/FlashMessages.vue'
import Logo from '@/Icons/easybizu_logo.jpeg'
import UserAvatar from '@/components/UserAvatar.vue'
import FloatingActionButton from '@/components/FloatingActionButton.vue'
    



const page = usePage()
const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const prop_country = page.props.value?.prop_country;
// console.log(prop_country)
const prop_phone = page.props.value?.prop_phone;

const countries = page.props.value?.countries;
const regions = ref(page.props.value?.regions);
const region_disabled = ref(false);
const login_btn_hovered = ref(false);
const pages = ref({
  page_one_open: true,
  page_two_open: false,
  page_three_open: false,
  page_four_open: false,
});

const switch_pages = (page) => {
  Object.keys(pages.value).forEach(key => {
    pages.value[key] = false;
  });
  pages.value[page] = true;

  console.log(pages.value)
}

const sponsor_info = ref({
  full_name: '',
  user_name: '',
  phone_number: '',
  email: ''
});

const page_one_form = useForm({
  country: prop_country == null ? 151 : prop_country,
  sponsor_phone_number: prop_phone == null ? '' : prop_phone,
});



const submitPageOneForm = () => {
  if (!page_one_form.processing){
    page_one_form.post(route('get_reg_sponsor_info'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        if (response.success) {

          Swal.fire({
            icon: 'success',
            title: 'Is this your sponsor?',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Proceed!',
            cancelButtonText: 'No',
            html: `<div class=''>
              <p class='text-left text-sm font-semibold'>Full Name: <em
                  class='text-primary'>${response.sponsor.name }</em></p>
              
              <p class='text-left text-sm font-semibold'>Phone Number: <em
                  class='text-primary'>${response.sponsor.phone_code}${response.sponsor.phone }</em></p>
              <p class='text-left text-sm font-semibold'>Email Adress: <em
                  class='text-primary'>${response.sponsor.email}</em></em></p>
              </div>
            </div>`,
          }).then((result) => {
            if (result.isConfirmed) {
              form.sponsor_phone_number = page_one_form.sponsor_phone_number
              form.sponsor_country = page_one_form.country

              switch_pages('page_two_open')
            }
          })
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `This sponsor does not exist`,
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
  country: 151,
  name: '',
  email: '',
  phone: '',
})

const submitPageTwoForm = () => {
  if (!page_two_form.processing) {
    page_two_form.post(route('process_registr_page_two'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        if (response.success) {

          
          form.country = page_two_form.country;
          form.name = page_two_form.name;
          form.email = page_two_form.email;
          form.phone = page_two_form.phone;

          console.log(form)

          switch_pages('page_three_open')
            
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `Something went wrong`,
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
};


const form = useForm({
  
  sponsor_country : 151,
  sponsor_phone_number: '',
  country: 151,
  // region: 1,
  name: '',
  email: '',
  phone: '',
  
  password: '',
  password_confirmation: '',
  terms: false,
})


const submit = () => {

  if (!form.processing) {
    form.post(route('register'), {
      preserveScroll: true,
      onSuccess: (page) => {

        var response = page.props.flash.data;
        if (response.success)
          console.log('success')
        else if (response.terms_unaccepted)
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Terms must be accepted to proceed with registration',
          })
        else
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong.',
          })
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

const sponsr_info_request = useForm({

  sponsor_user_name: '',
})



const load_regions_for_country_request = useForm({
  country: null,
});

// console.log(load_regions_for_country_request.country)
console.log(form.processing)

// const submit = () => {
//   if(!form.processing){
//     form
//       .transform(data => ({
//         ...data,
//         terms: form.terms && form.terms.length
//       }))
//       .post(route('register'), {
//         onFinish: () => form.reset('password', 'password_confirmation'),
//       })
//   }
// }

const getSponsorUserInfo = () => {
  sponsr_info_request.sponsor_user_name = form.sponsor_user_name
  sponsr_info_request.post(route('get_reg_sponsor_info'), {
    preserveScroll: true,
    onSuccess: (page) => {

      var response = page.props.flash.data;
      if (response.success) {
        
        sponsor_info.value.full_name = response.sponsor_full_name;
        sponsor_info.value.phone_number = response.sponsor_phone_num;
        sponsor_info.value.email = response.sponsor_email_address;
        sponsor_info.value.user_name = form.sponsor_user_name;

        
      }else{
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: `This sponsor username does not exist`,
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



const loadRegionsForCountry = () => {
  
  load_regions_for_country_request.country = form.country
  // console.log(self.load_regions_for_country_request.country)
  region_disabled.value = true;
  form.processing = true;

  load_regions_for_country_request.post(route('load_regions_for_country'), {
    preserveScroll: true,
    onSuccess: (page) => {
      console.log(page)
      var response = page.props.flash.data;
      console.log(response)

      if (response.regions.length) {
        
        regions.value = response.regions;
        form.region = response.first_region_id;

      }
      region_disabled.value = false;
      form.processing = false;
    }, onError: (errors) => {

    },
  });
}
</script>
