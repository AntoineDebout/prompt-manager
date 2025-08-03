<template>
  <div class="flex-1">
    <div class="bg-white shadow sm:rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Configuration du Schema JSON</h3>
          <div class="flex items-center gap-2">
            <button
              type="button"
              class="text-sm text-gray-600 hover:text-gray-900"
              @click="formatJson"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
              </svg>
              <span class="ml-1">Formater</span>
            </button>
          </div>
        </div>
        
        <div class="relative">
          <textarea
            ref="textarea"
            v-model="jsonContent"
            @input="onInput"
            class="font-mono w-full h-[calc(100vh-16rem)] resize-none p-4 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            :class="{ 'border-red-500': !isValid }"
            spellcheck="false"
          ></textarea>

          <div 
            v-if="!isValid" 
            class="absolute bottom-4 right-4 text-sm text-red-600 bg-white px-2 py-1 rounded-md shadow-sm border border-red-200"
          >
            JSON invalide
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:modelValue'])
const textarea = ref(null)
const isValid = ref(true)
const jsonContent = ref(JSON.stringify(props.modelValue, null, 2))

const onInput = () => {
  try {
    const parsed = JSON.parse(jsonContent.value)
    isValid.value = true
    emit('update:modelValue', parsed)
  } catch (e) {
    isValid.value = false
  }
}

const formatJson = () => {
  try {
    const parsed = JSON.parse(jsonContent.value)
    jsonContent.value = JSON.stringify(parsed, null, 2)
    isValid.value = true
  } catch (e) {
    // Ne rien faire si le JSON est invalide
  }
}
</script>
