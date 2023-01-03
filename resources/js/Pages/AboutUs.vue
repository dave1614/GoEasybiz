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

.small-banner{
  height: 270px;
  background-image: linear-gradient(to right bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('/public/img/about-us.jpg') ;
  background-size: 100% 100%;
    background-repeat: no-repeat;
    background-position: left top;

  
}

/* Custom code for the demo */
</style>

<template>
  <div class=" leading-relaxed tracking-wide flex flex-col">
    <!--Nav-->
    <MainPageNav selectedPage="about_us"/>

    <div class="small-banner">

    </div>

   

    <section class="bg-white border-b py-1 px-4">
      <div class="container mx-auto flex flex-wrap items-center justify-between pb-12">
        <h2 class="text-4xl w-full my-4 font-black leading-tight text-center text-gray-800 lg:mt-8">
          About Us
        </h2>
        <div class="w-full mb-12">
          <div class="h-1 mx-auto gradient w-64 opacity-25 my-0 py-0 rounded-t"></div>
        </div>
    
        <div class="text-gray-600 mb-8 font-semibold text-lg">
          <p class="my-4 mx-2">Go-Easybiz, a community of telecoms services users is a unique with proven system that makes telecoms services cheaper
          and it's networking success easier than ever before. It's a business that helps people earn passive income easily and
          improve their financial standard.
          Buy very cheap as a member and earn massively when you refer.
          
          </p>

          <div class="mt-9 mb-5">
            <h5 class="font-bold text-xl">The Community Business Benefits.</h5>
            <ul class="font-[400] my-3 text-lg">
              <li> * We deal in an industry that it's services are always in high demand.</li>
              
              <li> * Your membership is a partnership with a community that is making an impact in the world.</li>
              
              <li> * Opportunity to earn Free N500 airtime weekly once you refer upto Five persons.</li>
              
              <li> * Our business doesn’t require any previous experience. If you love helping people, you can do well at this.</li>
              
              <li> * You’ll succeed faster with this business because you don’t need to create, source, or manufacture your own product or
              service to get started.</li>
              
              <li> * With our "Invite Five" strategy, You can grow this business from four to seven figures per week within few months of
              your membership.</li>
              
              <li> * This business is shown to thrive regardless of the state of the economy.</li>

              <li> * This business allows you to create time and money freedom in your life so you can do the things you want to.
              </li>

              <li> * This is a business where you’re in business for yourself but not by yourself. You will be trained, mentored, and
              supported by people around the world.</li>
              
              <li> * The growth you will experience and the confidence you will build in this business can change many things for you.</li>
              
            </ul>
          </div>

          <div class="my-4 mt-9">
            <h5 class="font-bold text-xl">Partnership at what Cost ?</h5>
            <p class="font-[400] ml-3">Your one time membership fee of N1100 and your "Invite FIVE" effort to the business community.</p>
          </div>

          <div class="my-4 mt-9">
            
            <p class="">Go-Easybiz.
            ...Easy! Amazing! Reliable!</p>
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