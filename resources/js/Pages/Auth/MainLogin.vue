<style>
  
</style>
<template>
  <LayoutGuest class="font-montserrat">
    <Head title="Register" />

    <SectionFullScreen
      v-slot="{ cardClass }"
      bg="bg-login-background"
    >
      

      <div class="w-full mx-3 sm:w-5/12 my-5 rounded-md shadow-lg bg-white ">
        
        <div class="px-5 py-4">
          <div class="logo-icon">
            <img class="w-9 h-9 inline-block mr-2" :src="Logo" alt="">
            <span class="text-black font-bold text-lg">Go-Easybiz</span>
          </div>
          <p class="text-gray-400 text-sm font-semibold my-2">Login to your go-easybiz account</p>
          <!-- <div class="mt-4 px-2">
            <button class="font-semibold bg-facebook-blue text-white text-center px-4 py-2 rounded"><i class="text-lg fab fa-facebook-f"></i><span class="text-xs  ml-2">Login with facebook</span></button>

            <button class="text-black font-semibold text-center px-4 py-2 rounded border border-gray-300 float-right"> <img class="w-5 h-5 inline-block" :src="GoogleIcon" alt="Google Icon"> <span class="text-xs  ml-2">Login with google</span></button>
          </div>
          <div class="my-3 flex justify-center">
            <div class="bg-gray-700 w-5 h-[2px] inline-block mt-2"></div>
            <span class="text-gray-700 text-xs mx-2">OR</span>
            <div class="bg-gray-700 w-5 h-[2px] inline-block mt-2"></div>
          </div> -->



          <form @submit.prevent="submit" class="pt-3">
            <flash-messages />
            <div class="max-h-[260px] overflow-x-hidden">
              

              <!-- <text-input v-model="form.login" :error="form.errors.login" type="text" label="Username or email" id="user_name"
                placeholder="" class="" /> -->

              <div class="grid grid-cols-12 gap-6 my-3">
              
                <select class="col-span-4 mb-[7px]" id="country" v-model="form.country"
                  :class="form.errors.country ? 'login-form-input-error' : 'login-form-input'">
                  <option v-for="country in countries" :value="country.id" :key="country.id"
                    v-html="`${country.name} (${country.phone_code})`"></option>
                </select>
              
                <div class="col-span-8">
                  <text-input v-model="form.phone" :error="form.errors.phone"
                    type="number" name="phone" label="Phone Number" id="phone" placeholder="" />
                </div>
              </div>

              <text-input v-model="form.password" name="password" :error="form.errors.password" type="password" label="Password" id="password"
                placeholder="" class="" />
              
              
            </div>
            <div class="mt-4 px-1">
              <input type="checkbox" name="remember_me" class="login-checkbox" id="terms" v-model="form.remember_me"/>
              <label for="terms" class="login-checkbox-label text-black">Remember me</label>
              
              
            </div>
            <div class="mt-4 mb-3 px-1">
              <Link :href="route('register')" class="login-checkbox-label mt-1 hover:text-primary text-primary">New?
              Register Here</Link>

              <Link :href="route('password.request')" class="login-checkbox-label mt-1 float-right hover:text-primary text-primary">Forgot Password?</Link>
            </div>
            
            <button name="login-btn" :class="form.processing ? 'opacity-80 cursor-not-allowed' : ''" @mouseleave="login_btn_hovered = false" @mouseover="login_btn_hovered = true" type="submit" class="login-button">
              Login
              <img v-if="form.processing" class="inline-block w-7 h-6 float-right" :src="login_btn_hovered ? FormLoaderDark : FormLoaderLight" alt="">
            </button>
          </form>
        </div>
          
        
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
import Logo from '@/Icons/easybizu_logo.jpeg'
import GoogleIcon from '@/Icons/google_icon.svg'
import FacilityIcon from '@/Icons/facility_icon.svg'
import PatientIcon from '@/Icons/patient_icon.svg'
import TextInput from '@/Components/TextInput.vue'
import FormLoaderDark from '@/Loaders/form_loader_dark.gif'
import FormLoaderLight from '@/Loaders/form_loader_light.gif'
import FlashMessages from '@/Components/FlashMessages.vue'




const page = usePage()
const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)

const countries = page.props.value?.countries;

const login_btn_hovered = ref(false);

const form = useForm({
  // login: '',
  country: 151,
  phone: '',
  password: '',
  remember_me: false,
})


const submit = () => {

  if (!form.processing) {
    
    form.post(route('login'), {
      preserveScroll: true,
    });
  }
}


</script>
