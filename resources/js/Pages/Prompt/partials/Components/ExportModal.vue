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
              <div class="bg-gray-900 rounded-lg overflow-hidden">
                <pre class="text-sm p-4 text-gray-300 overflow-auto max-h-[400px]"><code>{{ exportContent }}</code></pre>
              </div>
              
              <div class="mt-6 flex justify-end gap-3">
                <button
                  @click="copyToClipboard"
                  class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-12a2 2 0 00-2-2h-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  {{ copied ? 'Copié !' : 'Copier' }}
                </button>
                <button
                  @click="$emit('update:modelValue', false)"
                  class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
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
import { ref, computed } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { useStore } from 'vuex'

const store = useStore()
const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true
  },
  messages: {
    type: Array,
    required: true
  }
})

defineEmits(['update:modelValue'])

const copied = ref(false)

const exportContent = computed(() => {
  const content = {
    messages: props.messages,
    schema: store.state.schema
  }
  return JSON.stringify(content, null, 2)
})

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(exportContent.value)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2000)
  } catch (err) {
    console.error('Failed to copy: ', err)
  }
}
</script>
