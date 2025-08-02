<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 mb-6">Gestionnaire de Prompts</h1>
      
      <div class="flex gap-6">
        <!-- Colonne principale - Messages (3/4) -->
        <div class="flex-1">
          <!-- Liste des prompts -->
          <div class="space-y-4">
            <TransitionGroup name="list">
              <PromptItem
                v-for="(item, index) in promptItems"
                :key="index"
                v-model="promptItems[index]"
                :index="index"
                :is-first="index === 0"
                :is-last="index === promptItems.length - 1"
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

        <!-- Liste des variables -->
        <VariablesList
          @variable-click="handleVariableClick"
        />
      </div>
    </div>

    <!-- Modal d'export -->
    <ExportModal
      v-model="isExportModalOpen"
      :content="promptItems"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { getVariableLabel } from './partials/config/variables.js'
import PromptItem from './partials/Components/PromptItem.vue'
import VariablesList from './partials/Components/VariablesList.vue'
import ExportModal from './partials/Components/ExportModal.vue'

// Props
const props = defineProps({
  templatePrompt: {
    type: Array,
    required: true,
  },
})

// État local
const promptItems = ref([...props.templatePrompt])
const isExportModalOpen = ref(false)
const activeTextareaIndex = ref(null)

// Plus besoin de extractVariablesWithLabels car les variables sont maintenant gérées directement dans le composant VariablesList

// Méthodes
const addNewItem = () => {
  promptItems.value.push({
    role: 'system',
    content: '',
  })
}

const deleteItem = (index) => {
  promptItems.value.splice(index, 1)
}

const moveItem = (fromIndex, toIndex) => {
  if (toIndex < 0 || toIndex >= promptItems.value.length) return
  const item = promptItems.value.splice(fromIndex, 1)[0]
  promptItems.value.splice(toIndex, 0, item)
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
  const content = promptItems.value[index].content
  const needsSpaceBefore = position > 0 && content[position - 1] !== ' '
  const needsSpaceAfter = position < content.length && content[position] !== ' '
  
  const textToInsert = (needsSpaceBefore ? ' ' : '') + variable + (needsSpaceAfter ? ' ' : '')
  promptItems.value[index].content = content.substring(0, position) + textToInsert + content.substring(position)
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
