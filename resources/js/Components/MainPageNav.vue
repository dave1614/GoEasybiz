<template>
  <nav :class="isMainPage ? '' : 'bg-slate-700'" id="header" class="w-full z-30 top-0 text-white py-1">
    <div class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 px-2 py-2 lg:py-6">
      <div class="pl-4 flex items-center">
        <a class="text-white no-underline hover:no-underline font-bold text-2xl lg:text-4xl" :href="route('main_page')">
          <svg class="h-6 w-6 inline-block fill-current text-yellow-700" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20">
            <path d="M13 8V0L8.11 5.87 3 12h4v8L17 8h-4z" />
          </svg>
          Go-Easybiz
        </a>
      </div>
  
      <div class="block lg:hidden pr-4">
        <button @click="mainStore.toggleMainNavMenu()" id="nav-toggle"
          class="flex items-center px-3 py-2 border rounded text-gray-500 border-gray-600 hover:text-gray-800 hover:border-green-500 appearance-none focus:outline-none">
          <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <title>Menu</title>
            <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
          </svg>
        </button>
      </div>
  
      <div :class="mainStore.show_nav_menu ? '' : 'hidden'"
        class="w-full flex-grow lg:flex lg:items-center lg:w-auto  lg:block mt-2 lg:mt-0 text-black p-4 lg:p-0 z-20"
        id="nav-content">
        <ul class="list-reset lg:flex justify-end flex-1 items-center">
          <li class="mr-3">
            <a :class="selectedPage == 'home' ? selected_link_classes : link_classes" :href="route('main_page')">Home</a>
          </li>
          <li class="mr-3">
            <a :class="selectedPage == 'about_us' ? selected_link_classes : link_classes"
              :href="route('about_us')">About Us</a>
          </li>
          <li class="mr-3">
            <a :class="selectedPage == 'our_services' ? selected_link_classes : link_classes"
              :href="route('our_services')">Our Services</a>
          </li>
          <li class="mr-3">
            <a :class="selectedPage == 'faqs' ? selected_link_classes : link_classes"
              :href="route('faqs')">Faqs</a>
          </li>
          <li class="mr-3">
            <a :class="selectedPage == 'contact_us' ? selected_link_classes : link_classes"
              :href="route('contact_us')">Contact Us</a>
          </li>
          <li class="mr-3">
            <a :class="selectedPage == 'register' ? selected_link_classes : link_classes"
              :href="route('register')">Register</a>
          </li>
          <li class="mr-3">
            <a :class="selectedPage == 'login' ? selected_link_classes : link_classes"
              :href="route('login')">Sign In</a>
          </li>
        </ul>
        <!-- <button id="navAction"
          class="mx-auto lg:mx-0 hover:underline text-gray-800 font-extrabold rounded mt-4 lg:mt-0 py-4 px-8 shadow opacity-75">
          Action
        </button> -->
      </div>
    </div>
  </nav>
</template>
<script setup>
import { Inertia } from '@inertiajs/inertia';
import { useMainStore } from "@/stores/main";
import { computed, useSlots, ref, reactive, watch } from "vue";


const mainStore = useMainStore();
const link_classes = ref("inline-block text-white no-underline hover:text-gray-800 hover:text-underline py-2 px-4");
const selected_link_classes = ref("inline-block py-2 px-4 text-white font-bold no-underline");


defineProps({
  isMainPage: {
    type: Boolean,
    default: false,
  },
  selectedPage: {
    type: String,
    default: "home",
  }
  
});
</script>
