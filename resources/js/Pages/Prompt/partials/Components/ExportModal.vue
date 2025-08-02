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
            <DialogPanel class="w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all">
              <DialogTitle as="h3" class="text-xl font-semibold leading-6 text-gray-900 mb-1">
                Export du Prompt
              </DialogTitle>
              <p class="text-sm text-gray-500 mb-4">
                Le prompt est exporté au format JSON pour pouvoir être importé dans d'autres outils.
              </p>
              <div class="mt-4">
                <pre class="bg-gray-900 text-gray-100 p-6 rounded-lg overflow-y-auto max-h-[60vh] text-sm font-mono whitespace-pre-wrap break-all"><code>{{ JSON.stringify(content, null, 2) }}</code></pre>
              </div>
              <div class="mt-6 flex justify-end gap-3">
                <button
                  @click="copyToClipboard"
                  class="btn btn-primary inline-flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
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
import { ref } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

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

defineEmits(['update:modelValue'])

const copied = ref(false)

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(JSON.stringify(props.content, null, 2))
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (err) {
    console.error('Failed to copy: ', err)
  }
}
</script>
