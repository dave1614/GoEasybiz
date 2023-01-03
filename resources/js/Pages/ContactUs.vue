<style>
@import url("https://rsms.me/inter/inter.css");

html {
  font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI",
    Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif,
    "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol",
    "Noto Color Emoji";
}

.gradient {
  /* background-image: linear-gradient(-225deg, #cbbacc 0%, #2580b3 100%); */
  background-image: linear-gradient(to right bottom, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/public/img/pexels-ibrahim-boran-613021 (2).jpg');
  /* background-size: contain; */
}

/* button,
.gradient2 {
  background-color: #f39f86;
  background-image: linear-gradient(315deg, #f39f86 0%, #f9d976 74%);
  
} */

/* Browser mockup code
 * Contribute: https://gist.github.com/jarthod/8719db9fef8deb937f4f
 * Live example: https://updown.io
 */

.browser-mockup {
  border-top: 2em solid rgba(230, 230, 230, 0.7);
  position: relative;
  height: 60vh;
}

.browser-mockup:before {
  display: block;
  position: absolute;
  content: "";
  top: -1.25em;
  left: 1em;
  width: 0.5em;
  height: 0.5em;
  border-radius: 50%;
  background-color: #f44;
  box-shadow: 0 0 0 2px #f44, 1.5em 0 0 2px #9b3, 3em 0 0 2px #fb5;
}

.browser-mockup>* {
  display: block;
}

.small-banner {
  height: 270px;
  background-image: linear-gradient(to right bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('/public/img/contact_us.jpg');
  background-size: 100% 100%;
  background-repeat: no-repeat;
  background-position: left top;


}

/* Custom code for the demo */
</style>

<template>
  <div class=" leading-relaxed tracking-wide flex flex-col">
    <!--Nav-->
    <MainPageNav selectedPage="contact_us" />

    <div class="small-banner">

    </div>

    <section class="bg-gray-200 border-b py-8">
      <div class="container mx-auto flex flex-wrap pt-4 pb-12">
        <h2 class="w-full my-2 text-4xl font-black leading-tight text-center text-gray-800">
          Connect With Us
        </h2>
        <div class="w-full mb-4">
          <div class="h-1 mx-auto gradient w-64 opacity-25 my-0 py-0 rounded-t"></div>
        </div>

        <div class="">
          <p class="text-sm font-semibold my-7 mx-7">We would love to respond to your queries and help you succeed. Feel free to get in touch with us.</p>
        </div>

        <div class="w-10/12 bg-white mx-auto my-0">
          
          <div class="sm:grid sm:grid-cols-12 sm:gap-1 h-full">
            <div class="col-span-7 px-9 py-8 ">
              <h4 class="text-xl text-blue-700 font-bold">Send your request</h4>
              <form @submit.prevent="submit" class="pt-9">
                
                <div class="grid grid-cols-12 gap-6 mb-5">
                  <div class="col-span-6">
                    <ContactTextInput v-model="form.name" :error="form.errors.name" type="text" label="Name" id="name"
                        placeholder="John Doe" class="" />
                  </div>
                  
                  <div class="col-span-6">
                    <ContactTextInput v-model="form.phone" :error="form.errors.phone" type="text" label="Phone" id="phone"
                    placeholder="+1 345 666 67"  />
                  </div>

                  <div class="col-span-6">
                    <ContactTextInput v-model="form.email" :error="form.errors.email" type="email" label="Email" id="email" placeholder="johndoe@myemail.com"/>
                  </div>
                  
                  <div class="col-span-6">
                    <ContactTextInput v-model="form.subject" :error="form.errors.subject" type="text" label="Subject" id="subject" placeholder="Your Subject"/>
                  </div>
                  
                  <div class="col-span-12">
                    <!-- <ContactTextAreaInput v-model="form.message" :error="form.errors.message" type="text" label="Message" id="message"
                      /> -->

                    <label class="contact-form-label" for="message">Message</label>
                    <textarea :class="form.errors.message ? 'login-form-input-error' : 'contact-form-input'" v-model="form.message" id="message" cols="30" rows="10" placeholder="Your Message" ></textarea>
                    <div v-if="form.errors.message" class="login-form-error">{{ form.errors.message }}</div>
                  </div>
              
                </div>
                
                
              
                <button type="submit" class="rounded-full bg-blue-700 text-white px-9 py-2 font-semibold text-lg">Send</button>
              </form>
            </div>
            <div class="col-span-5 bg-blue-700 px-9 py-8 text-white">
              <h4 class="text-xl font-bold">Reach Us</h4>

              <div class="mt-[70px] text-sm">
                <div class="grid grid-cols-12 gap-6 w-full">

                  <div class="col-span-6">
                    <p>Email</p>
                  </div>

                  <div class="col-span-6">
                    <p>info@goeasybiz.com, goessybiz@gmail.com</p>
                  </div>

                  <div class="col-span-6">
                    <p>Phone</p>
                  </div>
                  
                  <div class="col-span-6">
                    <p> <a class="underline" href="https://wa.me/2349153376143?text=I want to reach out to you about...">09153376143</a></p>
                  </div>

                  <div class="col-span-6">
                    <p>Whatsapp</p>
                  </div>
                  
                  <div class="col-span-6">
                    <p>08174622455</p>
                  </div>

                  <div class="col-span-6">
                    <p>Address</p>
                  </div>
                  
                  <div class="col-span-6">
                    <p>Abam Arochukwu Abia Nigeria</p>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!--Footer-->
    <MainFooter />

  </div>

</template>
<script setup>
import {
  mdiWhatsapp,
} from "@mdi/js";
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import { computed, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useMainStore } from "@/stores/main";
import ContactTextInput from '@/Components/ContactTextInput.vue'
import ContactTextAreaInput from '@/Components/ContactTextAreaInput.vue'
import MainFooter from "@/components/MainFooter.vue";
import MainPageNav from "@/components/MainPageNav.vue";



const mainStore = useMainStore();


const page = usePage()
const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)


const form = useForm({
  
  name: null,
  phone: null,
  email: null,
  subject: null,
  message: null,
})


const submit = () => {
  if (!form.processing) {
    
    form.post(route('submit_contact_info'), {
      preserveScroll: true,
      onSuccess: (page) => {
        console.log(page)
        var response = page.props.flash.data;
        console.log(response)

        if (response.success) {
          

          Swal.fire({
            title: 'Success',
            icon: 'success',
            html: `Your Message Has Been Sent Successfully`,
            allowEscapeKey: false,
            allowOutsideClick: false,

          }).then((result) => {
            Inertia.visit(route('contact_us'));

          });

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
  }
};
</script>
