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
  background-image: linear-gradient(to right bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('/public/img/compemsation.jpg');
  background-size: 100% 100%;
  background-repeat: no-repeat;
  background-position: left top;


}

/* Custom code for the demo */
</style>

<template>
  <div class=" leading-relaxed tracking-wide flex flex-col">
    <!--Nav-->
    <MainPageNav selectedPage="compensation_plan" />

    <div class="small-banner">

    </div>

    <section class="bg-white border-b py-8">
      <div class="container mx-auto flex flex-wrap pt-4 pb-12">
        <h2 class="w-full my-2 text-4xl font-black leading-tight text-center text-gray-800">
          COMPENSATION PLAN
        </h2>
        <div class="w-full mb-4">
          <div class="h-1 mx-auto gradient w-64 opacity-25 my-0 py-0 rounded-t"></div>
        </div>


        <div class="text-gray-600 mb-8 font-semibold text-lg pt-9">
          <p class="my-4 mx-2">Go-Easybiz 2*24 potential networking system comes with great initiative.
            Sponsor bonus = 42%, Placement bonus = 2% times 24 Generation.
            We have team support bonus which makes increase dependable weekly earning 100% possible and non-stop.

          </p>

          <p class="my-4 mx-2">Though you start earning from the week you join the system, but your weekly withdrawal is possible only after you have
          invited at least five persons to the community.
          </p>

          <p class="my-4 mx-2">It's easy to grow with the community this way...</p>
          
          <p>
          1st week.....U invite 2 <br>
          2nd week....2 invite 4<br>
          3rd week.....4 invite 8<br>
          4th week.....8 invite 16<br>
          5th week...16 invite 32<br>
          6th week...32 invite 64 Etc.....(24 weeks / Generation).<br></p>

          <p class="my-4 mx-2">If it takes you one whole week to invite two, and each of them use one week to invite two each to join the
          community..... Within 24 weeks...the system would have earned you over N300 Million (N300,000,000) from Sponsorship,
          Placement and system support income and it will be consistent almost weekly. The system creates you Active and Passive
          Income. You withdraw at anytime any day straight to your account.</p>

          <p class="my-4 mx-2">Go-Easybiz <br>
          ...Easy! Amazing! & Reliable!</p>

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