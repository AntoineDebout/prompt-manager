<template>
  <div class="bg-white rounded-lg p-4 border border-gray-200 hover:border-primary-300 transition-all duration-200">
    <div class="flex items-start gap-4">
      <!-- Sélection du rôle -->
      <div class="w-48">
        <select
          v-model="modelValue.role"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 py-2.5 px-3 text-sm"
          @change="$emit('update:modelValue', { ...modelValue, role: $event.target.value })"
        >
          <option value="system">System</option>
          <option value="developer">Developer</option>
          <option value="user">User</option>
        </select>
      </div>

      <!-- Zone de texte -->
      <div class="flex-1">
        <textarea
          v-model="modelValue.content"
          v-auto-resize
          :data-index="index"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 min-h-[6rem] px-4 py-2"
          @dragover.prevent
          @drop="handleDrop"
          @click="$event.target.focus()"
          @focus="$emit('focus', index)"
          @input="handleInput"
          @keydown="handleKeydown"
        ></textarea>
      </div>

      <!-- Boutons d'actions -->
      <div class="flex flex-col gap-2">
        <button
          @click="$emit('move-up')"
          :disabled="isFirst"
          class="p-2 text-gray-500 hover:text-primary-600 disabled:opacity-50"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
          </svg>
        </button>
        <button
          @click="$emit('delete')"
          class="p-2 text-red-500 hover:text-red-600"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
        <button
          @click="$emit('move-down')"
          :disabled="isLast"
          class="p-2 text-gray-500 hover:text-primary-600 disabled:opacity-50"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { nextTick } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  index: {
    type: Number,
    required: true
  },
  isFirst: {
    type: Boolean,
    default: false
  },
  isLast: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'move-up', 'move-down', 'delete', 'focus'])

// Directive pour auto-resize
const vAutoResize = {
  mounted: (el) => {
    el.style.overflow = 'hidden'
    el.style.resize = 'none'
    
    const resize = () => {
      el.style.height = 'auto'
      el.style.height = el.scrollHeight + 'px'
    }
    
    el.addEventListener('input', resize)
    window.addEventListener('resize', resize)
    
    // Initial resize
    nextTick(resize)
    
    // Cleanup
    el._cleanup = () => {
      window.removeEventListener('resize', resize)
    }
  },
  updated: (el) => {
    nextTick(() => {
      el.style.height = 'auto'
      el.style.height = el.scrollHeight + 'px'
    })
  },
  unmounted: (el) => {
    if (el._cleanup) el._cleanup()
  }
}

const handleDrop = (event) => {
  event.preventDefault()
  const variable = event.dataTransfer.getData('text/plain')
  insertVariable(variable, event.target.selectionStart)
}

const handleInput = (event) => {
  emit('update:modelValue', { ...props.modelValue, content: event.target.value })
}

const handleKeydown = (event) => {
  const textarea = event.target;
  const cursorPos = textarea.selectionStart;
  const content = props.modelValue.content;

  // Recherche des variables dans le texte
  const variablePattern = /{{([^}]+)}}/g;
  const matches = [...content.matchAll(variablePattern)];
  
  // Vérifie si le curseur est dans une variable
  for (const match of matches) {
    const start = match.index;
    const end = start + match[0].length;
    
    if (cursorPos >= start && cursorPos <= end) {
      // Si c'est la touche supprimer ou retour arrière
      if (event.key === 'Backspace' || event.key === 'Delete') {
        event.preventDefault();
        // Supprime toute la variable
        const beforeVariable = content.substring(0, start);
        const afterVariable = content.substring(end);
        emit('update:modelValue', { 
          ...props.modelValue, 
          content: beforeVariable + afterVariable 
        })
        
        // Replace le curseur au début de où était la variable
        nextTick(() => {
          textarea.selectionStart = textarea.selectionEnd = start;
        });
      } else {
        // Empêche toute autre modification
        event.preventDefault();
      }
      return;
    }
  }
}

const insertVariable = (variable, position) => {
  const content = props.modelValue.content
  const needsSpaceBefore = position > 0 && content[position - 1] !== ' '
  const needsSpaceAfter = position < content.length && content[position] !== ' '
  
  const textToInsert = (needsSpaceBefore ? ' ' : '') + variable + (needsSpaceAfter ? ' ' : '')
  const newContent = content.substring(0, position) + textToInsert + content.substring(position)
  
  emit('update:modelValue', { ...props.modelValue, content: newContent })
  
  // Mettre à jour la position du curseur après l'insertion
  nextTick(() => {
    const textarea = document.querySelector(`textarea[data-index="${props.index}"]`)
    if (textarea) {
      const newPosition = position + textToInsert.length
      textarea.focus()
      textarea.setSelectionRange(newPosition, newPosition)
    }
  })
}
</script>

<style>
select option {
  padding: 8px 12px;
}
</style>
