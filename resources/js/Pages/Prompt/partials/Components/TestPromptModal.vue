<template>
  <TransitionRoot appear :show="modelValue" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-10">
      <TransitionChild
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <TransitionChild
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="w-8xl transform overflow-hidden rounded-2xl bg-white shadow-xl transition-all">
              <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <BeakerIcon class="h-6 w-6 text-white" />
                    <h2 class="ml-3 text-lg font-medium text-white">Tester le Prompt</h2>
                  </div>
                  <button
                    type="button"
                    class="rounded-md bg-indigo-600 text-gray-300 hover:text-white focus:outline-none"
                    @click="closeModal"
                  >
                    <span class="sr-only">Fermer</span>
                    <XMarkIcon class="h-6 w-6" />
                  </button>
                </div>
              </div>

              <form @submit.prevent="testPrompt" class="p-8">
                <div class="space-y-8">
                  <!-- Sélecteur de cas de test -->
                  <div class="bg-gray-50 rounded-lg p-6 space-y-6">
                    <h3 class="font-medium text-gray-900 flex items-center">
                      <BeakerIcon class="h-5 w-5 mr-2 text-gray-500" />
                      Cas de test
                    </h3>
                    <div class="flex gap-4 items-center">
                      <div class="relative">
                      <select
                        id="testCase"
                        v-model="selectedTestCase"
                        class="mt-1 block w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base appearance-none px-4 py-3"
                        @change="applyTestCase"
                      >
                        <option value="">Sélectionnez un cas de test</option>
                        <option v-for="(testCase, id) in testCases" :key="id" :value="id">
                          {{ id }}
                        </option>
                      </select>
                      <ChevronDownIcon class="h-5 w-5 text-gray-400 absolute right-3 top-3.5 pointer-events-none" />
                    </div>
                      <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        @click="resetFields"
                      >
                        <ArrowPathIcon class="h-4 w-4 mr-1.5" />
                        Réinitialiser
                      </button>
                    </div>
                  </div>

                  <!-- Variables du prompt -->
                  <div v-if="Object.keys(props.variables).length > 0" class="bg-gray-50 rounded-lg p-6 space-y-6">
                    <h3 class="font-medium text-gray-900 flex items-center">
                      <AdjustmentsHorizontalIcon class="h-5 w-5 mr-2 text-gray-500" />
                      Variables
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                      <div v-for="(value, key) in props.variables" :key="key" class="space-y-2">
                        <label :for="key" class="block text-sm font-medium text-gray-700">{{ computeLabel(key) }}</label>
                        <input
                          :id="key"
                          :name="key"
                          v-model="props.variables[key]"
                          type="text"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base px-4 py-3"
                          @focus="clearFieldError(key)"
                        />
                        <p v-if="fieldErrors[key]" class="mt-1 text-sm text-red-600">{{ fieldErrors[key] }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Configuration -->
                  <div class="bg-gray-50 rounded-lg p-6 space-y-6">
                    <h3 class="font-medium text-gray-900 flex items-center">
                      <Cog6ToothIcon class="h-5 w-5 mr-2 text-gray-500" />
                      Configuration
                    </h3>
                    <div class="flex gap-6">
                      <!-- Sélection du modèle -->
                      <div class="space-y-2 flex-1">
                        <label for="model" class="block text-sm font-medium text-gray-700">Modèle</label>
                        <div class="relative">
                          <select
                            id="model"
                            v-model="form.model"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base appearance-none px-4 py-3"
                          >
                            <option value="gpt-4o-mini">GPT-4o Mini</option>
                            <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                            <option value="gpt-4">GPT-4</option>
                            <option value="gpt-4-turbo">GPT-4 Turbo</option>
                          </select>
                          <ChevronDownIcon class="h-5 w-5 text-gray-400 absolute right-3 top-3.5 pointer-events-none" />
                        </div>
                      </div>

                      <!-- Température -->
                      <div class="space-y-2 flex-1">
                        <label for="temperature" class="block text-sm font-medium text-gray-700">
                          Température
                        </label>
                        <div class="flex items-center space-x-4">
                          <input
                            id="temperature"
                            v-model="form.temperature"
                            type="range"
                            min="0"
                            max="2"
                            step="0.1"
                            class="flex-1"
                          />
                          <span class="text-sm text-gray-500 w-12 text-right">{{ form.temperature }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Résultat -->
                  <div v-if="result" class="space-y-2">
                    <div class="flex items-center justify-between">
                      <h3 class="font-medium text-gray-900 flex items-center">
                        <DocumentTextIcon class="h-5 w-5 mr-2 text-gray-500" />
                        Résultat
                      </h3>
                      <button
                        type="button"
                        class="text-sm text-gray-500 hover:text-gray-700"
                        @click="copyToClipboard"
                      >
                        <div class="flex items-center">
                          <DocumentDuplicateIcon class="h-4 w-4 mr-1" />
                          Copier
                        </div>
                      </button>
                    </div>
                    <div class="relative">
                      <textarea
                        v-model="result"
                        readonly
                        rows="6"
                        class="mt-1 block w-full rounded-md bg-gray-50 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono px-4 py-3"
                      ></textarea>
                    </div>
                  </div>

                  <!-- Message d'erreur -->
                  <div v-if="error" class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                      <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
                      <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Une erreur est survenue</h3>
                        <div class="mt-2 text-sm text-red-700">{{ error }}</div>
                      </div>
                    </div>
                  </div>

                  <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button
                      type="button"
                      class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                      @click="closeModal"
                    >
                      <XMarkIcon class="h-4 w-4 mr-1.5" />
                      Fermer
                    </button>
                    <button
                      type="submit"
                      class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                      :disabled="isLoading"
                    >
                      <BeakerIcon v-if="!isLoading" class="h-4 w-4 mr-1.5" />
                      <SparklesIcon v-else class="h-4 w-4 mr-1.5 animate-spin" />
                      {{ isLoading ? 'Test en cours...' : 'Tester' }}
                    </button>
                  </div>
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { 
  BeakerIcon, 
  XMarkIcon, 
  ArrowPathIcon,
  AdjustmentsHorizontalIcon,
  Cog6ToothIcon,
  ChevronDownIcon,
  DocumentTextIcon,
  DocumentDuplicateIcon,
  ExclamationCircleIcon,
  SparklesIcon
} from '@heroicons/vue/24/outline'
import axios from 'axios'
import { variableLabels } from '../config/variables'

const props = defineProps({
  modelValue: Boolean,
  prompt: String,
  variables: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update:modelValue'])

const form = ref({
  model: 'gpt-4o-mini',
  temperature: 0.0
})

const result = ref('')
const error = ref('')
const isLoading = ref(false)
const fieldErrors = ref({})
const testCases = ref({})
const selectedTestCase = ref('')
const originalValues = ref({})

// Charger les cas de test
const loadTestCases = async () => {
  try {
    const response = await axios.get('/api/prompts/test-cases')
    testCases.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement des cas de test:', error)
  }
}

// Appliquer le cas de test sélectionné
const applyTestCase = () => {
  if (!selectedTestCase.value) return
  
  const testCase = testCases.value[selectedTestCase.value]
  if (!testCase) return

  console.log('Variables disponibles:', Object.keys(props.variables))
  console.log('Cas de test:', testCase)

  // Sauvegarder les valeurs originales si pas encore fait
  if (Object.keys(originalValues.value).length === 0) {
    Object.keys(props.variables).forEach(key => {
      originalValues.value[key] = props.variables[key]
    })
  }

  // Appliquer les nouvelles valeurs
  Object.keys(props.variables).forEach(key => {
    if (key in testCase) {
      props.variables[key] = testCase[key]
    }
  })
}

// Réinitialiser les champs
const resetFields = () => {
  selectedTestCase.value = ''
  // Restaurer les valeurs originales
  Object.keys(originalValues.value).forEach(key => {
    if (key in props.variables) {
      props.variables[key] = originalValues.value[key]
    }
  })
}

const resetModal = () => {
  result.value = ''
  error.value = ''
  fieldErrors.value = {}
  form.value = {
    model: 'gpt-4o-mini',
    temperature: 0.0
  }
  // Réinitialiser les variables
  Object.keys(props.variables).forEach(key => {
    props.variables[key] = ''
  })
  // Réinitialiser le cas de test sélectionné
  selectedTestCase.value = ''
  originalValues.value = {}
}

const closeModal = () => {
  resetModal()
  emit('update:modelValue', false)
}

const computeLabel = (key) => {
  const fullKey = `{{${key}}}`
  return variableLabels[fullKey] || key
}

const clearFieldError = (field) => {
  if (fieldErrors.value[field]) {
    fieldErrors.value[field] = ''
  }
}

const testPrompt = async () => {
  error.value = ''
  fieldErrors.value = {}
  isLoading.value = true
  
  try {
    const response = await axios.post('/api/prompts/test', {
      prompt: props.prompt,
      variables: props.variables,
      model: form.value.model,
      temperature: parseFloat(form.value.temperature)
    })
    
    result.value = response.data.result
  } catch (e) {
    if (e.response?.status === 422) {
      // Gestion des erreurs de validation
      const validationErrors = e.response.data.errors
      if (validationErrors?.variables) {
        validationErrors.variables.forEach(error => {
          const match = error.match(/La variable '(.+)' doit être remplie/)
          if (match) {
            const field = match[1]
            fieldErrors.value[field] = error
          }
        })
      }
      error.value = 'Veuillez remplir tous les champs requis.'
    } else {
      error.value = e.response?.data?.error || 'Une erreur est survenue'
    }
  } finally {
    isLoading.value = false
  }
}

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(result.value)
  } catch (err) {
    console.error('Failed to copy text: ', err)
  }
}

// Charger les cas de test au montage du composant
onMounted(() => {
  loadTestCases()
})
</script>
