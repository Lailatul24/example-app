/// <reference types="vite/client" />

// Biarkan TypeScript mengenali macro Vue seperti defineProps, defineEmits, dll
/// <reference types="vue/macros-global" />

// Deklarasi untuk file .vue
declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}
