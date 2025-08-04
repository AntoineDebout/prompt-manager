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
            <DialogPanel class="w-[90vw] h-[85vh] transform overflow-hidden rounded-2xl bg-white shadow-xl transition-all">
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

              <div class="flex h-[calc(85vh-4rem)]">
                <!-- Panneau de gauche avec le formulaire -->
                <form @submit.prevent="testPrompt" class="w-[70%] p-8 overflow-y-auto border-r">
                  <div class="space-y-6 max-w-4xl mx-auto">
                    <div class="grid grid-cols-2 gap-6">
                      <!-- Sélecteur de cas de test -->
                      <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                        <h3 class="font-medium text-gray-900 flex items-center">
                          <BeakerIcon class="h-5 w-5 mr-2 text-gray-500" />
                          Cas de test
                        </h3>
                        <div class="flex gap-4 items-center">
                          <div class="relative flex-1">
                            <select
                              id="testCase"
                              v-model="selectedTestCase"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm appearance-none pr-10 pl-3 py-2 truncate"
                              @change="applyTestCase"
                            >
                              <option value="">Sélectionnez un cas de test</option>
                              <option v-for="(testCase, id) in testCases" :key="id" :value="id">
                                {{ id }}
                              </option>
                            </select>
                            <ChevronDownIcon class="h-5 w-5 text-gray-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" />
                          </div>
                          <button
                            type="button"
                            class="inline-flex items-center p-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shrink-0"
                            @click="resetFields"
                          >
                            <ArrowPathIcon class="h-4 w-4" />
                          </button>
                        </div>
                      </div>

                      <!-- Configuration -->
                      <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                        <h3 class="font-medium text-gray-900 flex items-center">
                          <Cog6ToothIcon class="h-5 w-5 mr-2 text-gray-500" />
                          Configuration
                        </h3>
                        <div class="grid grid-cols-2 gap-6">
                          <!-- Sélection du modèle -->
                          <div class="space-y-2">
                            <label for="model" class="block text-sm font-medium text-gray-700">Modèle</label>
                            <div class="relative">
                              <select
                                id="model"
                                v-model="form.model"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm appearance-none pr-10 pl-3 py-2 truncate"
                              >
                                <option value="gpt-4o-mini">GPT-4o Mini</option>
                                <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                                <option value="gpt-4">GPT-4</option>
                                <option value="gpt-4-turbo">GPT-4 Turbo</option>
                              </select>
                              <ChevronDownIcon class="h-5 w-5 text-gray-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" />
                            </div>
                          </div>

                          <!-- Température -->
                          <div class="space-y-2">
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
                    </div>

                    <!-- Variables du prompt -->
                    <div v-if="Object.keys(props.variables).length > 0" class="bg-gray-50 rounded-lg p-6 space-y-6">
                      <h3 class="font-medium text-gray-900 flex items-center">
                        <AdjustmentsHorizontalIcon class="h-5 w-5 mr-2 text-gray-500" />
                        Variables
                      </h3>
                      <div class="grid grid-cols-3 gap-6">
                        <div v-for="(value, key) in props.variables" :key="key" class="space-y-2">
                          <label :for="key" class="block text-sm font-medium text-gray-700">{{ computeLabel(key) }}</label>
                          <input
                            :id="key"
                            :name="key"
                            v-model="props.variables[key]"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2"
                            @focus="clearFieldError(key)"
                          />
                          <p v-if="fieldErrors[key]" class="mt-1 text-sm text-red-600">{{ fieldErrors[key] }}</p>
                        </div>
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

              <!-- Panneau de droite avec le résultat -->
              <div v-if="result" class="w-[30%] flex flex-col bg-gray-50 p-8">
                <div class="flex items-center justify-between mb-4">
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
                <div class="flex-1 relative overflow-auto">
                  <pre
                    class="w-full h-full font-mono text-sm p-4 bg-white border border-gray-200 rounded-lg whitespace-pre-wrap"
                  >{{ formattedResult }}</pre>
                </div>
              </div>
              <div v-else class="w-[30%] flex items-center justify-center bg-gray-50">
                <div class="text-center text-gray-500">
                  <BeakerIcon class="h-12 w-12 mx-auto mb-4" />
                  <p>Le résultat du test s'affichera ici</p>
                </div>
              </div>
            </div>
          </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
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
import { useStore } from 'vuex'

const store = useStore()
const props = defineProps({
  modelValue: Boolean,
  prompt: {
    type: Array,
    default: () => []
  },
  variables: {
    type: Object,
    default: () => ({})
  }
})

// Computed pour le schema et les messages
const schema = computed(() => {
  const value = store.getters.rawSchema
  return value
})

const messages = computed(() => {
  return props.prompt || []
})

// Debug des valeurs au montage du composant
onMounted(() => {
  loadTestCases()
})

const emit = defineEmits(['update:modelValue'])

const form = ref({
  model: 'gpt-4o-mini',
  temperature: 0.0
})

const result = ref('')

// Computed pour le formatage intelligent du résultat
const formattedResult = computed(() => {
  if (!result.value) return ''
  
  try {
    // Essayer de parser comme JSON
    const parsed = JSON.parse(result.value)
    // Si c'est un JSON valide, le formater joliment
    return JSON.stringify(parsed, null, 2)
  } catch (e) {
    // Si ce n'est pas du JSON, retourner tel quel
    return result.value
  }
})

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
    console.error('Erreur lors du chargement des cas de test:')
  }
}

// Appliquer le cas de test sélectionné
const applyTestCase = () => {
  if (!selectedTestCase.value) return
  
  const testCase = testCases.value[selectedTestCase.value]
  if (!testCase) return

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
    const currentSchema = schema.value
    const currentMessages = messages.value
    
    if (!currentSchema) {
      throw new Error('Schema non disponible')
    }

    if (!currentMessages?.length) {
      throw new Error('Messages du prompt non disponibles')
    }

    const response = await axios.post('/api/prompts/test', {
      messages: currentMessages,
      variables: props.variables,
      schema: currentSchema,
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
