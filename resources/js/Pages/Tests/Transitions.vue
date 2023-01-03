<script setup>
import { ref } from 'vue';

const show = ref(false);

</script>

<template>
  <div class=" mx-3 my-4">
    <div>
      <button @click="show = !show" class="px-4 py-2 bg-orange-500 rounded text-white">Toggle</button>
    </div>

    <transition name="slide-fade">
      <p v-if="show">hello</p>
    </transition>
  </div>
</template>

<style scoped>
  .slide-fade-enter-active {
    transition: all 0.3s ease-out;
  }

  .slide-fade-leave-active {
    transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
  }

  .slide-fade-enter-from,
  .slide-fade-leave-to {
    transform: translateX(20px);
    opacity: 0;
  }
</style>