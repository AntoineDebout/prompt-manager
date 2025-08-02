<template>
  <TransitionRoot appear :show="modelValue" as="template">
    <Dialog as="div" @close="$emit('update:modelValue', false)" class="relative z-10">
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
                <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-auto max-h-96"><code>{{ JSON.stringify(content, null, 2) }}</code></pre>
              </div>
              <div class="mt-4 flex justify-end">
                <button
                  @click="copyToClipboard"
                  class="btn btn-primary mr-2"
                  :class="{ 'opacity-50': copied }"
                >
                  {{ copied ? 'Copié !' : 'Copier' }}
                </button>
                <button
                  @click="$emit('update:modelValue', false)"
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
</template>

<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true
  },
  content: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['update:modelValue'])

// Variable pour suivre l'état de la copie
const copied = ref(false)

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(JSON.stringify(props.content, null, 2))
    copied.value = true
    // Réinitialiser le message après 2 secondes
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (err) {
    console.error('Failed to copy: ', err)
  }
}
</script>
