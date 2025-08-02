<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Gestionnaire de Prompts</h1>
        
        <!-- Variables disponibles -->
        <div class="mb-8">
          <h2 class="text-lg font-medium text-gray-700 mb-3">Variables disponibles</h2>
          <div class="flex flex-wrap gap-2">
            <div
              v-for="variable in extractVariables"
              :key="variable"
              class="px-3 py-1.5 bg-primary-50 text-primary-700 rounded-md border border-primary-200 cursor-move"
              draggable="true"
              @dragstart="handleDragStart($event, variable)"
            >
              {{ variable }}
            </div>
          </div>
        </div>

        <!-- Liste des prompts -->
        <div class="space-y-4">
          <TransitionGroup name="list">
            <div
              v-for="(item, index) in promptItems"
              :key="index"
              class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-primary-300 transition-all duration-200"
            >
              <div class="flex items-start gap-4">
                <!-- Sélection du rôle -->
                <div class="w-48">
                  <select
                    v-model="item.role"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                  >
                    <option value="system">System</option>
                    <option value="user">User</option>
                    <option value="assistant">Assistant</option>
                    <option value="developer">Developer</option>
                  </select>
                </div>

                <!-- Zone de texte -->
                <div class="flex-1">
                  <textarea
                    v-model="item.content"
                    rows="4"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    @dragover.prevent
                    @drop="handleDrop($event, index)"
                  ></textarea>
                </div>

                <!-- Boutons d'actions -->
                <div class="flex flex-col gap-2">
                  <button
                    @click="moveItem(index, index - 1)"
                    :disabled="index === 0"
                    class="p-2 text-gray-500 hover:text-primary-600 disabled:opacity-50"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                  </button>
                  <button
                    @click="deleteItem(index)"
                    class="p-2 text-red-500 hover:text-red-600"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                  <button
                    @click="moveItem(index, index + 1)"
                    :disabled="index === promptItems.length - 1"
                    class="p-2 text-gray-500 hover:text-primary-600 disabled:opacity-50"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
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

        <!-- Modal d'export -->
        <TransitionRoot appear :show="isExportModalOpen" as="template">
          <Dialog as="div" @close="isExportModalOpen = false" class="relative z-10">
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
                  <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all">
                    <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                      Export du Prompt
                    </DialogTitle>
                    <div class="mt-4">
                      <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-auto max-h-96"><code>{{ JSON.stringify(promptItems, null, 2) }}</code></pre>
                    </div>
                    <div class="mt-4 flex justify-end">
                      <button
                        @click="copyToClipboard"
                        class="btn btn-primary mr-2"
                      >
                        Copier
                      </button>
                      <button
                        @click="isExportModalOpen = false"
                        class="btn btn-outline"
                      >
                        Fermer
                      </button>
                    </div>
                  </DialogPanel>
                </TransitionChild>
              </div>
            </div>
          </Dialog>
        </TransitionRoot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

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

// Computed
const extractVariables = computed(() => {
  const variables = new Set()
  promptItems.value.forEach(item => {
    const matches = item.content.match(/{{(.*?)}}/g)
    if (matches) {
      matches.forEach(match => {
        variables.add(match.trim())
      })
    }
  })
  return Array.from(variables)
})

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

const handleDragStart = (event, variable) => {
  event.dataTransfer.setData('text/plain', variable)
}

const handleDrop = (event, index) => {
  const variable = event.dataTransfer.getData('text/plain')
  const textarea = event.target
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const content = promptItems.value[index].content
  promptItems.value[index].content = content.substring(0, start) + variable + content.substring(end)
}

const exportPrompt = () => {
  isExportModalOpen.value = true
}

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(JSON.stringify(promptItems.value, null, 2))
    // On pourrait ajouter une notification de succès ici
  } catch (err) {
    console.error('Failed to copy: ', err)
  }
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
