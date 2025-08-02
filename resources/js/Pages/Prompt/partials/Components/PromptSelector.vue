<template>
  <div class="bg-white rounded-lg border border-gray-200 p-4">
    <h2 class="text-lg font-medium text-gray-700 mb-3">Sélection du prompt</h2>
    <select
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-3"
    >
      <option
        v-for="(prompt, slug) in prompts"
        :key="slug"
        :value="slug"
        class="py-2"
      >
        {{ slug }}
      </option>
    </select>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    required: true
  },
  prompts: {
    type: Object,
    required: true
  }
})

defineEmits(['update:modelValue'])
</script>

<style>
select option {
  padding: 8px 12px;
}
</style>
