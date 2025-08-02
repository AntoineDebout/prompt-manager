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
                @focus="activeTextareaIndex = index"
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

            <button
              @click="exportPrompt"
              class="btn btn-outline"
            >
              Exporter le prompt
            </button>
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
              @variable-click="handleVariableClick"
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
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import PromptItem from './partials/Components/PromptItem.vue'
import PromptSelector from './partials/Components/PromptSelector.vue'
import VariablesList from './partials/Components/VariablesList.vue'
import ExportModal from './partials/Components/ExportModal.vue'

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
const activeTextareaIndex = ref(null)

// Computed pour le prompt actuel
const currentPromptItems = computed({
  get: () => props.templatePrompts[selectedPromptSlug.value] || [],
  set: (newValue) => {
    // On crée une copie de l'objet pour maintenir la réactivité
    const updatedPrompts = { ...props.templatePrompts }
    updatedPrompts[selectedPromptSlug.value] = newValue
    // Ici, vous pourriez ajouter une logique pour sauvegarder les changements
  }
})

// Méthodes

const addNewItem = () => {
  const newPrompt = [...currentPromptItems.value]
  newPrompt.push({
    role: 'system',
    content: '',
  })
  currentPromptItems.value = newPrompt
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

const handleVariableClick = (variable) => {
  if (activeTextareaIndex.value !== null) {
    const textarea = document.querySelector(`textarea[data-index="${activeTextareaIndex.value}"]`)
    if (textarea) {
      insertVariable(variable.key, activeTextareaIndex.value, textarea.selectionStart)
    }
  }
}

const insertVariable = (variable, index, position) => {
  const newPrompt = [...currentPromptItems.value]
  const content = newPrompt[index].content
  const needsSpaceBefore = position > 0 && content[position - 1] !== ' '
  const needsSpaceAfter = position < content.length && content[position] !== ' '
  
  const textToInsert = (needsSpaceBefore ? ' ' : '') + variable + (needsSpaceAfter ? ' ' : '')
  newPrompt[index].content = content.substring(0, position) + textToInsert + content.substring(position)
  currentPromptItems.value = newPrompt
}

const exportPrompt = () => {
  isExportModalOpen.value = true
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
