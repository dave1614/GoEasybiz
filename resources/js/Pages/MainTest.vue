<script setup>
import { watch, ref, onMounted } from 'vue';

const question = ref('');
const answer = ref('Questions usually contain a question mark. ;-)');

const list = ref([
  'one',
  'two',
  'three',
  'four',
  'five'
]);

const itemRefs = ref([]);

onMounted(() => {
  console.log(itemRefs.value)
});
watch(question, async (newQuestion, oldQuestion) => {
  if(newQuestion.indexOf('?') > -1){
    answer.value = 'Thinking...';
  }

  try{
    const res = await fetch('https://yesno.wtf/api');
    answer.value = (await res.json()).answer;
  }catch(error){
    answer.value = 'Error! could not rerach the API ' + error;
  }
});
</script>
<template>
  <ul>
    <li v-for="(item, index) in list" ref="itemRefs" :key="index">
      {{ item }}
    </li>
  </ul>
  <p>
    Ask a yes/no question:

    <input type="text" v-model="question">
  </p>
  <p>{{ answer }}</p>
</template>