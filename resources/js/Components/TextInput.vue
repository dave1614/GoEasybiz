<template>
  <div class="mb-2 transition-all ease-linear duration-200">
    <label v-if="label" class="login-form-label" :for="id">{{ label }}:</label>

    <input :id="id" ref="input" v-bind="$attrs" class="login-form-input" :class="error ? 'login-form-input-error' : 'login-form-input'"
      :type="type" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" />
    <div v-if="error" class="login-form-error">{{ error }}</div>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `text-input-${this._uid}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    modelValue: {
      type: [String, Number],
      default: "",
    },
    label: String,
    error: String,
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
  },
}
</script>
