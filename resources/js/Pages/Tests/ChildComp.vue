<script setup>
import { ref, inject }  from 'vue';
var props = defineProps({
  count: {
    type: Number,
    default: 0,
  }
});

var num = ref(null);

var emit = defineEmits(['increment']);

const increment = (event) => {
  emit('increment',num.value)
};

const message = inject("message");
console.log(message)

</script>

<template>
  <div>
    <h1 class="text-2xl font-bold my-2">Child Count: </h1>
    <h2 class="text-xl font-bold my-2">{{ count }}</h2>

    <input type="number" v-model="num">

    <button @click="increment" class="text-white rounded py-2 px-4 text-center bg-blue-500 shadow-sm border border-blue-600">Increment</button>

    <slot name="header">

    </slot>
    <slot>
      Nothing was sent here
    </slot>

    <slot name="footer">
    
    </slot>
  </div>
</template>