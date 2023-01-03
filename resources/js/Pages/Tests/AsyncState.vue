<script setup>
import {ref} from 'vue';
import { useFetch } from '@/Composables/fetch';

const url = ref("https://vps.providusbank.com/vps/api/PiPCreateReservedAccountNumber");

const { data, error } = useFetch(url);

setTimeout(() => {
  url.value = "https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts"
}, 10000);


</script>
<template>
  <div v-if="error" class="">Ooops! Error encountered: {{ error }}</div>
  <div v-else-if="data" class="">
    Data Loaded: 
    <pre>{{ data }}</pre>
  </div>
  <div v-else class="">Loading...</div>
</template>