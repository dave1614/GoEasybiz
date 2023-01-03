<script setup>
import ChildComp from './ChildComp.vue';
import { ref, provide } from 'vue';
import { useMouse } from '@/Composables/mouse';

const count = ref(0);
const {x, y} = useMouse();

const increment = (num) => {
  // console.log(num.value)
  count.value += num !== null ? num : 1;
};

provide('message','hello');


</script>
<template>
  <div>
    <h1 class="text-3xl font-bold my-2">Mouse position is at: {{ x }}, {{ y }}</h1>
    <h1 class="text-2xl font-bold my-2">Parent Count: </h1>
    <h2 class="text-xl font-bold my-2">{{ count }}</h2>
    <ChildComp @increment="increment" :count="count">
      <template #header>
        <div class="bg-slate-800 text-white text-center">
          This is the header
        </div>
      </template>

      <!-- <template #default> -->
        <div class="h-[150px] bg-orange-500 rounded my-3">
          <p class="text-center">This was embedded with slots</p>
        </div>
      <!-- </template> -->

      <template #footer>
        <div class="bg-slate-800 text-white text-center">
          This is the footer
        </div>
      </template>
    </ChildComp>
  </div>
</template>