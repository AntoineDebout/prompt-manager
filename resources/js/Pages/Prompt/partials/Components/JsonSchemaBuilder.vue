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
              @click="formatSchema"
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
            v-model="localContent"
            @input="validateJson"
            @blur="updateSchema"
            class="font-mono w-full h-[calc(100vh-16rem)] resize-none p-4 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            :class="{ 'border-red-500': !isValid }"
            spellcheck="false"
          ></textarea>

          <div 
            v-if="!isValid" 
            class="absolute bottom-4 right-4 text-sm text-red-600 bg-white px-2 py-1 rounded-md shadow-sm border border-red-200"
          >
            {{ duplicateKeyError || 'JSON invalide' }}
          </div>

          <div 
            v-if="duplicateKeyError" 
            class="absolute bottom-4 right-20 text-sm text-red-600 bg-white px-2 py-1 rounded-md shadow-sm border border-red-200"
          >
            {{ duplicateKeyError }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const textarea = ref(null)

// État local
const isValid = ref(true)
const duplicateKeyError = ref(null)

// Computed pour accéder au schema formatté depuis le store
const currentSchema = computed(() => store.getters.rawSchema)

// Contenu local du textarea
const localContent = ref('')

// Initialiser et synchroniser le contenu local
watch(currentSchema, (newSchema) => {
  if (!newSchema) {
    return
  }
  
  try {
    const newFormatted = JSON.stringify(newSchema, null, 2)
    
    // Ne mettre à jour que si le contenu est réellement différent
    if (localContent.value.trim() !== newFormatted.trim()) {
      localContent.value = newFormatted
    } 
  } catch (e) {
    console.error('Erreur lors de la mise à jour du contenu local:')
  }
}, { immediate: true })

// Vérifier les doublons dans un objet JSON
const checkDuplicateKeys = (obj, parentPath = '') => {
  if (typeof obj !== 'object' || obj === null) return null;

  const keys = new Set();
  for (const key of Object.keys(obj)) {
    // Vérifier les doublons au niveau actuel
    if (keys.has(key)) {
      return parentPath ? `${parentPath}.${key}` : key;
    }
    keys.add(key);

    // Vérifier récursivement les objets imbriqués
    const newPath = parentPath ? `${parentPath}.${key}` : key;
    if (typeof obj[key] === 'object' && obj[key] !== null) {
      const duplicate = checkDuplicateKeys(obj[key], newPath);
      if (duplicate) {
        // Ne retourner que si c'est un doublon au même niveau
        if (duplicate.split('.').slice(-2, -1)[0] === duplicate.split('.').slice(-1)[0]) {
          return duplicate;
        }
      }
    }
  }
  return null;
}

// Valider le JSON sans mettre à jour le store
const validateJson = () => {
  if (!localContent.value?.trim()) {
    isValid.value = false
    return
  }
  
  try {
    const parsed = JSON.parse(localContent.value)
    if (!parsed || typeof parsed !== 'object') {
      throw new Error('Le contenu doit être un objet')
    }
    isValid.value = true
  } catch (e) {
    isValid.value = false
  }
}

// Mettre à jour le store quand le contenu change et est valide
const updateSchema = () => {

  if (!isValid.value || !localContent.value?.trim()) {
    return
  }
  
  try {
    const parsed = JSON.parse(localContent.value)
    
    store.dispatch('updateSchema', parsed)
  } catch (e) {
    console.error('Erreur lors de la mise à jour du schema:')
  }
}

// Formater le JSON
const formatSchema = () => {
  if (isValid.value) {
    const cursorPosition = textarea.value?.selectionStart || 0
    
    try {
      const parsed = JSON.parse(localContent.value)
      localContent.value = JSON.stringify(parsed, null, 2)
      store.dispatch('updateSchema', parsed)
      
      // Restaurer la position du curseur au prochain tick
      setTimeout(() => {
        textarea.value?.setSelectionRange(cursorPosition, cursorPosition)
      }, 0)
    } catch (e) {
      console.error('Erreur lors du formatage:', e)
    }
  }
}
</script>