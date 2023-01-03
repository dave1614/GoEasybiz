<template>
<footer class="bg-white ">
  <div class="container mx-auto mt-8 px-8">
    <div class="w-full flex flex-col md:flex-row py-6">
      <div class="flex-1 mb-6">
        <a class="text-orange-600 no-underline hover:no-underline font-bold text-2xl lg:text-4xl" href="#">
          <svg class="h-6 w-6 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M13 8V0L8.11 5.87 3 12h4v8L17 8h-4z" />
          </svg>
          Go-Easybiz
        </a>
      </div>

      <div class="flex-1">
        <p class="uppercase font-extrabold text-gray-500 md:mb-6">
          Company
        </p>
        <ul class="list-reset mb-6">
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a :href="route('our_services')"
              class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Our Services</a>
          </li>
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a :href="route('compensation_plan')"
              class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Compensation Plan</a>
          </li>
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a :href="route('about_us')"
              class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">About
              Us</a>
          </li>
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a :href="route('contact_us')"
              class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Contact Us</a>
          </li>
        </ul>
      </div>

      <!-- <div class="flex-1">
            <p class="uppercase font-extrabold text-gray-500 md:mb-6">Legal</p>
            <ul class="list-reset mb-6">
              <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                <a href="#" class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Terms</a>
              </li>
              <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                <a href="#" class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Privacy</a>
              </li>
            </ul>
          </div> -->
      <div class="flex-1">
        <p class="uppercase font-extrabold text-gray-500 md:mb-6">Social</p>
        <ul class="list-reset mb-6">
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a href="#" class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Facebook</a>
          </li>
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a href="#" class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Linkedin</a>
          </li>
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a href="https://twitter.com/GoEasybiz" target="_blank" class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Twitter</a>
          </li>
        </ul>
      </div>

      <div class="flex-1">
        <p class="uppercase font-extrabold text-gray-500 md:mb-6">Links</p>
        <ul class="list-reset mb-6">
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a :href="route('faqs')"
              class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">FAQs</a>
          </li>
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a :href="route('register')"
              class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Register</a>
          </li>
          <li class="mt-2 inline-block mr-2 md:block md:mr-0">
            <a :href="route('login')"
              class="font-light no-underline hover:underline text-gray-800 hover:text-orange-500">Login</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</footer>
</template>