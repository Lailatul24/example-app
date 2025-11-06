<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps<{ categories: any[] }>()
const showModal = ref(false)
const editMode = ref(false)
const toast = ref<string | null>(null)

const page = usePage()
watch(() => page.props.flash?.success, (val: string) => {
  if (val) {
    toast.value = val
    setTimeout(() => (toast.value = null), 3000)
  }
})

// Form Inertia
const form = useForm({
  id: null,
  name: '',
})

function openModal(category: any = null) {
  editMode.value = !!category
  if (category) {
    form.id = category.id
    form.name = category.name
  } else {
    form.reset()
  }
  showModal.value = true
}

function closeModal() {
  form.reset()
  form.clearErrors()
  showModal.value = false
}

function saveCategory() {
  if (editMode.value && form.id) {
    form.put(`/categories/${form.id}`, {
      onSuccess: () => {
        closeModal()
        router.reload({ only: ['categories'] })
      },
    })
  } else {
    form.post('/categories', {
      onSuccess: () => {
        closeModal()
        router.reload({ only: ['categories'] })
      },
    })
  }
}

function deleteCategory(id: number) {
  if (confirm('Yakin ingin menghapus kategori ini?')) {
    router.delete(`/categories/${id}`, {
      onSuccess: () => router.reload({ only: ['categories'] }),
    })
  }
}
</script>

<template>
  <AppLayout>
    <div class="p-6 relative">
      <!-- Toast -->
      <transition name="fade">
        <div
          v-if="toast"
          class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50"
        >
          {{ toast }}
        </div>
      </transition>

      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manajemen Kategori</h1>
        <button
          @click="openModal()"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          + Tambah Kategori
        </button>
      </div>

      <!-- Table -->
      <table class="w-full bg-white rounded-lg shadow">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="px-4 py-2">Nama</th>
            <th class="px-4 py-2">Deskripsi</th>
            <th class="px-4 py-2 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="category in props.categories"
            :key="category.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="px-4 py-2">{{ category.name }}</td>
            <td class="px-4 py-2">{{ category.description }}</td>
            <td class="px-4 py-2 text-right space-x-2">
              <button
                @click="openModal(category)"
                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
              >
                Edit
              </button>
              <button
                @click="deleteCategory(category.id)"
                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
              >
                Hapus
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
      >
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
          <h2 class="text-xl font-bold mb-4">
            {{ editMode ? 'Edit Kategori' : 'Tambah Kategori' }}
          </h2>

          <form @submit.prevent="saveCategory" class="space-y-4">
            <div>
              <label class="block text-sm font-medium">Nama</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border rounded p-2"
                :class="form.errors.name ? 'border-red-500' : 'border-gray-300'"
              />
              <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                {{ form.errors.name }}
              </p>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
              >
                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
