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
  background-image: linear-gradient(to right bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('/public/img/faq.jpg');
  background-size: 100% 100%;
  background-repeat: no-repeat;
  background-position: left top;


}

/* Custom code for the demo */
</style>

<template>
  <div class=" leading-relaxed tracking-wide flex flex-col">
    <!--Nav-->
    <MainPageNav selectedPage="faqs" />

    <div class="small-banner">

    </div>



    <section class="bg-white border-b py-1 px-4">
      <div class="container mx-auto flex flex-wrap items-center justify-between pb-12">
        <h2 class="text-4xl w-full my-4 font-black leading-tight text-center text-gray-800 lg:mt-8">
          Frequently Asked Questions
        </h2>
        <div class="w-full mb-12">
          <div class="h-1 mx-auto gradient w-64 opacity-25 my-0 py-0 rounded-t"></div>
        </div>

        <div class="text-gray-600 mb-8 font-semibold text-lg">

          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">How can I purchase data from you?</h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                Please follow the following steps to purchase MTN, 9mobile, GLO, Airtel Data. <br>
                Step 1: Create or login to your account, if you already have.<br>
                Step 2: Fund your wallet.<br>
                Step 3: Fill the data form.<br>
                Step 4: Wait for less than 2 minutes, the recipent(s) will receive notification(s) of data recharge(Except for
                9mobile(SME), which should be confirmed with its balance code)..
              </p>
            </template>
          </app-accordion>
          
          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">What are the codes to check for data balance are?</h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                MTN: *461*4#<br>
                9MOBILE: *228#<br>
                GLO: *127*0#<br>
                AIRTEL: *140#</p>
            </template>
          </app-accordion>

          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">What are the other products/services you offer?</h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                Trade Giftcard with Go-Easybiz, convert your excess airtime to cash, Airtime VTU on MTN, 9MOBILE, GLO and AIRTEL,
                Bills
                Payment (GOTV, DSTV, STARTIMES), ELECTRICITY BILLS, PRINTING RECHARGE CARD, buy and sell eTicket and BULK SMS.</p>
            </template>
          </app-accordion>


          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">How can I fund my wallet?</h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                You can fund your wallet using any of the three payment means.
                Bank Payment (Transfer) Instant Funding( With your personalized wallet funding account).
                Payment with your earned wallet income.</p>
            </template>
          </app-accordion>

          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">Can I be a reseller or an agent?</h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                Our Products can be retailed, as they are affordable. You can retail our Products to others and make profit. Just make
                sure your wallet is funded, then you can easily purchase for anyone you wish.</p>
            </template>
          </app-accordion>

          
          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">Are your data plans legit?</h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                Yea, we are third party data vendors from Telcos. We buy in bulk from network providers and resell.</p>
            </template>
          </app-accordion>

          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">I paid into your bank account but my wallet was not autocredited?</h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                Please chat customers service via whatsapp, send the amount paid, bank paid into, date of payment and your username.
              </p>
            </template>
          </app-accordion>

          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">What's your working period?</h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                We offer 24/7 services.
              </p>
            </template>
          </app-accordion>

          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">Is this data plan compatible with all devices?</h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                Yes, it is compatible with all devices.
              </p>
            </template>
          </app-accordion>

          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">Are there any additional charges For DSTV, GOTV, STARTIMES or electricity payment?
              </h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                No we don't have any additional charge or hidden charges, as a matter of fact, we give commission for using those
                services.
              </p>
            </template>
          </app-accordion>

          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">Can I communicate with you in case I have any enquiry?
              </h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                Yes, you can always reach out to us through our customer care line via call/sms/whatsapp 2348174622455
              </p>
            </template>
          </app-accordion>

          <app-accordion class="my-4 mx-2">
            <template v-slot:title>
              <h6 class="font-bold text-lg my-2">Does the recharge card printing really works?
              </h6>
            </template>
            <template v-slot:content>
              <p class="text-sm my-5">
                Yes, the recharge card printing works perfectly and it works for all network.
              </p>
            </template>
          </app-accordion>
          



        </div>
      </div>
    </section>

    <!--Footer-->
    <MainFooter />

  </div>

</template>

<script setup>
import AppAccordion from "@/components/AppAccordion.vue";
import MainFooter from "@/components/MainFooter.vue";
import MainPageNav from "@/components/MainPageNav.vue";
import { Inertia } from '@inertiajs/inertia';
import { useMainStore } from "@/stores/main";

const mainStore = useMainStore();
</script>