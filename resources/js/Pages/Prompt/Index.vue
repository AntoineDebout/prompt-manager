<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Gestionnaire de Prompts</h1>
      </div>
      
      <div class="flex gap-6">
        <!-- Colonne principale - Messages (3/4) -->
        <div class="flex-1">
          <!-- Liste des prompts -->
          <div class="space-y-4">
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

          <!-- Boutons d'action -->
          <div class="mt-6 flex justify-between">
            <button
              @click="addNewItem"
              class="btn btn-primary"
            >
              Ajouter un message
            </button>

            <div class="space-x-3">
              <button
                @click="openTestModalForCurrentPrompt"
                class="btn btn-outline"
              >
                Tester le prompt
              </button>

              <button
                @click="exportPrompt"
                class="btn btn-outline"
              >
                Exporter le prompt
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar droite -->
        <div class="w-80 flex-none">
          <div class="space-y-4 sticky top-8">
            <!-- Sélecteur de prompt -->
            <PromptSelector
              v-model="selectedPromptSlug"
              :prompts="templatePrompts"
            />
            <!-- Liste des variables -->
            <VariablesList

            />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal d'export -->
    <ExportModal
      v-model="isExportModalOpen"
      :content="currentPromptItems"
    />

    <TestPromptModal
      v-model="showTestModal"
      :prompt="selectedPrompt?.messages"
      :variables="promptVariables"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import PromptItem from './partials/Components/PromptItem.vue'
import PromptSelector from './partials/Components/PromptSelector.vue'
import VariablesList from './partials/Components/VariablesList.vue'
import ExportModal from './partials/Components/ExportModal.vue'
import TestPromptModal from './partials/Components/TestPromptModal.vue'

// Props
const props = defineProps({
  templatePrompts: {
    type: Object,
    required: true,
  },
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

const openTestModalForCurrentPrompt = () => {
  // Envoyer le tableau de messages complet
  openTestModal({
    messages: currentPromptItems.value
  })
}

const openTestModal = (prompt) => {
  selectedPrompt.value = prompt
  promptVariables.value = getDefaultVariables()
  showTestModal.value = true
}

const closeTestModal = () => {
  showTestModal.value = false
  selectedPrompt.value = null
  promptVariables.value = {}
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
