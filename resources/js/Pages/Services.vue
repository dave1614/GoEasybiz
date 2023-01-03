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
  background-image: linear-gradient(to right bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('/public/img/our_services.jpg');
  background-size: 100% 100%;
  background-repeat: no-repeat;
  background-position: left top;


}
/* Custom code for the demo */
</style>

<template>
  <div class=" leading-relaxed tracking-wide flex flex-col">
    <!--Nav-->
    <MainPageNav selectedPage="our_services" />

    <div class="small-banner">
    
    </div>

  <section class="bg-white border-b py-8">
    <div class="container mx-auto flex flex-wrap pt-4 pb-12">
      <h2 class="w-full my-2 text-4xl font-black leading-tight text-center text-gray-800">
        OUR SERVICES
      </h2>
      <div class="w-full mb-4">
        <div class="h-1 mx-auto gradient w-64 opacity-25 my-0 py-0 rounded-t"></div>
      </div>

      
      <div class="text-gray-600 mb-8 font-semibold text-lg text-center">
        <p class="my-4 mx-2">Go-Easybiz services is an online based service where you can conveniently buy airtime, print recharge cards, buy cheap
        Internet data, and get amazing chargeback.
        You can also renew your dstv, gotv, startimes, pay electricity bills, pay smile internet data, send bulk SMS.

        </p>
      
      
      
      </div>
  
      <div class="w-full md:w-1/3 p-6 flex flex-col flex-grow flex-shrink">
        <div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden shadow">
          <a href="#" class="flex flex-wrap no-underline hover:no-underline">
            <!-- <p class="w-full text-gray-600 text-xs md:text-sm px-6 mt-6">
              GETTING STARTED
            </p> -->
            <div class="w-full font-bold text-xl text-gray-800 px-6  py-6">
              Airtime Purchase (VTU)
            </div>
            <p class="text-gray-600 text-base px-6 mb-5">
              Enjoy much discount on airtime purchase on our platform.
              We offer VTU airtime for MTN, Glo, Airtel and 9mobile.
            </p>
          </a>
        </div>
        <div class="flex-none mt-auto bg-white rounded-b rounded-t-none overflow-hidden shadow p-6">
          <div class="flex items-center justify-start">
            <button
              class="main-page-btn text-sm">
              Click to Purchase
            </button>
          </div>
        </div>
      </div>
  
      <div class="w-full md:w-1/3 p-6 flex flex-col flex-grow flex-shrink">
        <div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden shadow">
          <a href="#" class="flex flex-wrap no-underline hover:no-underline">
            <!-- <p class="w-full text-gray-600 text-xs md:text-sm px-6 mt-6">
              GETTING STARTED
            </p> -->
            <div class="w-full font-bold text-xl text-gray-800 px-6 py-6">
              Cable TV
            </div>
            <p class="text-gray-600 text-base px-6 mb-5">
              Subscribe your Cable TV (DSTv, GOTv & Startimes) at the cheapest price and get instant activation.
              Get discount while doing so.
            </p>
          </a>
        </div>
        <div class="flex-none mt-auto bg-white rounded-b rounded-t-none overflow-hidden shadow p-6">
          <div class="flex items-center justify-start">
            <button
              class="main-page-btn text-sm">
              Click to Subscribe
            </button>
          </div>
        </div>
      </div>
  
      <div class="w-full md:w-1/3 p-6 flex flex-col flex-grow flex-shrink">
        <div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden shadow">
          <a href="#" class="flex flex-wrap no-underline hover:no-underline">
            <!-- <p class="w-full text-gray-600 text-xs md:text-sm px-6 mt-6">
              Print Recharge Cards Online
            </p> -->
            <div class="w-full font-bold text-xl text-gray-800 px-6 py-6">
              Print Recharge Cards Online
            </div>
            <p class=" text-gray-600 text-base px-6 mb-5">
              Print recharge card online that works for MTN, Glo, Airtel, 9mobile.
              Our universal pin works for effectively for all networks.
            </p>
          </a>
        </div>
        <div class="flex-none mt-auto bg-white rounded-b rounded-t-none overflow-hidden shadow p-6">
          <div class="flex items-center justify-start">
            <button
              class="main-page-btn text-sm">
              Click to Print recharge card
            </button>
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
import MainFooter from "@/components/MainFooter.vue";
import MainPageNav from "@/components/MainPageNav.vue";
import { Inertia } from '@inertiajs/inertia';
import { useMainStore } from "@/stores/main";

const mainStore = useMainStore();
</script>