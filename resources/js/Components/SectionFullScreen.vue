<script setup>
import { computed } from "vue";
import { useStyleStore } from "@/stores/style";
import {
  gradientBgPurplePink,
  gradientBgDark,
  gradientBgPinkRed,
  gradientBgCustom,
} from "@/colors";

const props = defineProps({
  bg: {
    type: String,
    required: true,
    validator: (value) => ["custom","purplePink", "pinkRed","bg-login-background"].includes(value),
  },
});

const colorClass = computed(() => {
  if (props.bg == "bg-login-background") {
    return "bg-login-background";
  }

  if (useStyleStore().darkMode) {
    return gradientBgDark;
  }

  switch (props.bg) {
    case "purplePink":
      return gradientBgPurplePink;
    case "pinkRed":
      return gradientBgPinkRed;
    case "custom":
      return gradientBgCustom;

    // default :
    //   return props.bg;
  }

  


  // return "bg-login-background";
});
</script>

<template>
  <div
    class="flex min-h-screen items-center justify-center bg-cover"
    :class="colorClass"
  >
    <slot card-class="w-11/12 md:w-7/12 lg:w-6/12 xl:w-4/12 shadow-2xl" />
  </div>
</template>
