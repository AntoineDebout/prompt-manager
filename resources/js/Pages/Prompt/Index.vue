<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-6">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-semibold text-gray-900">Gestionnaire de Prompts</h1>
          <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500">Configuration du:</span>
            <div class="flex items-center justify-between gap-2 px-1 py-1 bg-gray-100 rounded-lg">
              <button
                @click="currentView = 'prompt'"
                type="button"
                class="text-sm px-3 py-1.5 rounded-md transition-colors relative focus:outline-none"
                :class="[
                  currentView === 'prompt' 
                    ? 'text-white bg-indigo-600 shadow-sm' 
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                ]"
              >
                Prompt
              </button>
              <button
                @click="currentView = 'schema'"
                type="button"
                class="text-sm px-3 py-1.5 rounded-md transition-colors relative focus:outline-none"
                :class="[
                  currentView === 'schema' 
                    ? 'text-white bg-indigo-600 shadow-sm' 
                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                ]"
              >
                Schema
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div class="flex gap-6">
        <div class="flex-1">
          <div v-if="currentView === 'prompt'" class="space-y-4 mb-6">
            <TransitionGroup name="list">
              <PromptItem
                v-for="(item, index) in currentPromptItems"
                :key="index"
                v-model="currentPromptItems[index]"
                :index="index"
                :is-first="index === 0"
                :is-last="index === currentPromptItems.length - 1"
                @move-up="moveItem(index, index - 1)"
                @move-down="moveItem(index, index + 1)"
                @delete="deleteItem(index)"
                @test="openTestModal(item)"
              />
            </TransitionGroup>
          </div>

          <div v-else-if="currentView === 'schema'" class="mb-6">
            <JsonSchemaBuilder
              v-model="schema"
              class="w-full"
            />
          </div>

          <!-- Boutons d'action toujours visibles -->
          <div class="flex justify-between border-t pt-6">
            <div>
              <button
                v-if="currentView === 'prompt'"
                @click="addNewItem"
                class="btn btn-primary"
              >
                Ajouter un message
              </button>
            </div>

            <div class="space-x-3">
              <button
                @click="openTestModalForCurrentPrompt"
                class="btn btn-outline"
                :disabled="!canTest"
              >
                Tester le prompt
              </button>

              <button
                @click="exportPrompt"
                class="btn btn-outline"
                :disabled="!canTest"
              >
                Exporter le prompt
              </button>
            </div>
          </div>
        </div>

        <div v-if="currentView === 'prompt'" class="w-80 flex-none">
          <div class="space-y-4 sticky top-8">
            <PromptSelector
              v-model="selectedPromptSlug"
              :prompts="templatePrompts"
            />
            <VariablesList />
          </div>
        </div>
      </div>
    </div>
  </div>

  <ExportModal 
    v-model="isExportModalOpen"
    :messages="currentPromptItems"
    :schema="schema"
    @close="closeExportModal"
  />

  <TestPromptModal
    v-model="showTestModal"
    :messages="selectedPrompt?.messages"
    :variables="promptVariables"
    :schema="schema"
    @close="closeTestModal"
  />
</template>

<script setup>
import { ref, computed } from 'vue'
import PromptItem from './partials/Components/PromptItem.vue'
import PromptSelector from './partials/Components/PromptSelector.vue'
import VariablesList from './partials/Components/VariablesList.vue'
import ExportModal from './partials/Components/ExportModal.vue'
import TestPromptModal from './partials/Components/TestPromptModal.vue'
import JsonSchemaBuilder from './partials/Components/JsonSchemaBuilder.vue'

// Props
const props = defineProps({
  templatePrompts: {
    type: Object,
    required: true,
  },
  defaultSchema: {
    type: Object,
    required: true
  }
})

// État local
const selectedPromptSlug = ref(Object.keys(props.templatePrompts)[0])
const isExportModalOpen = ref(false)


// État local pour les prompts
const localPrompts = ref({ ...props.templatePrompts })

// Computed pour le prompt actuel
const currentPromptItems = computed({
  get: () => localPrompts.value[selectedPromptSlug.value] || [],
  set: (newValue) => {
    localPrompts.value[selectedPromptSlug.value] = [...newValue]
  }
})

// Méthodes

const addNewItem = () => {
  currentPromptItems.value.push({
    content: '',
    role: 'system'
  });
}

const deleteItem = (index) => {
  const newPrompt = [...currentPromptItems.value]
  newPrompt.splice(index, 1)
  currentPromptItems.value = newPrompt
}

const moveItem = (fromIndex, toIndex) => {
  if (toIndex < 0 || toIndex >= currentPromptItems.value.length) return
  const newPrompt = [...currentPromptItems.value]
  const item = newPrompt.splice(fromIndex, 1)[0]
  newPrompt.splice(toIndex, 0, item)
  currentPromptItems.value = newPrompt
}



const exportPrompt = () => {
  isExportModalOpen.value = true
}

const showTestModal = ref(false)
const selectedPrompt = ref(null)
const promptVariables = ref({})

// Garde les boutons actifs selon la vue
const canTest = computed(() => {
  if (currentView.value === 'prompt') {
    return currentPromptItems.value.length > 0
  }
  return true
})

const openTestModalForCurrentPrompt = () => {
  console.log('Opening test modal for current prompt')
  selectedPrompt.value = {
    messages: [...currentPromptItems.value]
  }
  promptVariables.value = getDefaultVariables()
  showTestModal.value = true
}

const openTestModal = (item) => {
  console.log('Opening test modal for single item')
  selectedPrompt.value = {
    messages: [{ ...item }]
  }
  promptVariables.value = getDefaultVariables()
  showTestModal.value = true
}

const closeTestModal = () => {
  showTestModal.value = false
  selectedPrompt.value = null
  promptVariables.value = {}
}

// État local pour l'export
const closeExportModal = () => {
  isExportModalOpen.value = false
}

import { variableLabels } from './partials/config/variables'

const getDefaultVariables = () => {
  return Object.keys(variableLabels).reduce((acc, key) => {
    // Enlever les accolades de la clé
    const cleanKey = key.replace(/[{}]/g, '')
    acc[cleanKey] = ''
    return acc
  }, {})
}

const currentView = ref('prompt')
const schema = ref(JSON.parse(JSON.stringify(props.defaultSchema)))
</script>

<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.list-leave-active {
  position: absolute;
}
</style>
