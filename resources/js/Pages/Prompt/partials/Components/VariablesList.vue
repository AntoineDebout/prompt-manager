<template>
  <div class="w-1/4">
    <div class="bg-white rounded-lg border border-gray-200 p-4 sticky top-8">
      <h2 class="text-lg font-medium text-gray-700 mb-3">Variables disponibles</h2>
      <div class="flex flex-wrap gap-2">
        <div
          v-for="variable in availableVariables"
          :key="variable.key"
          class="px-3 py-1.5 bg-primary-50 text-primary-700 rounded-md border border-primary-200 cursor-pointer select-none"
          draggable="true"
          @dragstart="handleDragStart($event, variable)"
          @dblclick="$emit('variable-click', variable)"
        >
          {{ variable.label }}
        </div>
      </div>
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
