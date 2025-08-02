<template>
  <div class="bg-white rounded-lg border border-gray-200 p-4">
    <h2 class="text-lg font-medium text-gray-700 mb-3">Variables disponibles</h2>
    <div class="flex flex-col gap-2">
      <button
        v-for="variable in availableVariables"
        :key="variable.key"
        class="px-3 py-2 text-sm text-gray-700 bg-white hover:bg-gray-50 rounded border border-gray-300 cursor-pointer text-center transition-colors duration-150 ease-in-out"
        draggable="true"
        @dragstart="handleDragStart($event, variable)"
        @dblclick="$emit('variable-click', variable)"
      >
        {{ variable.label }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { variableLabels } from '../config/variables.js'

// Calcul des variables disponibles à partir de la configuration
const availableVariables = computed(() => {
  return Object.entries(variableLabels)
    .map(([key, label]) => ({ key, label }))
    .sort((a, b) => a.label.localeCompare(b.label, 'fr', { sensitivity: 'base' }))
})

defineEmits(['variable-click'])

const handleDragStart = (event, variable) => {
  event.dataTransfer.setData('text/plain', variable.key)
  event.dataTransfer.setData('text/label', variable.label)
}
</script>
