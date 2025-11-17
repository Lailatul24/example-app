<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps<{ facilities: any[], categories: any[], exportUrl : any }>()

const showModal = ref(false)
const editMode = ref(false)
const toast = ref<string | null>(null)

// Ambil flash message dari Laravel
const page = usePage()
watch(() => page.props.flash?.success, (val: string) => {
  if (val) {
    toast.value = val
    setTimeout(() => (toast.value = null), 3000)
  }
})

// Inertia form
const form = useForm({
  id: null,
  code: '',
  name: '',
  category_ids: [],
  quantity_total: '',
  quantity_available: '',
  condition: '',
  description: ''
})

function openModal(facility: any = null) {
  editMode.value = !!facility
  if (facility) {
    form.id = facility.id
    form.code = facility.code
    form.name = facility.name
    form.quantity_total = facility.quantity_total
    form.quantity_available = facility.quantity_available
    form.condition = facility.condition
    form.description = facility.description
  } else {
    form.reset()
    form.id = null
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  form.reset()
  form.clearErrors()
}

function saveFacility() {
  if (editMode.value && form.id) {
    form.put(`/facilities/${form.id}`, {
       preserveScroll: true,
       onSuccess: () => {
           closeModal()
           router.reload({ only: ['facilities'] })
        },
    })
  } else {
    form.post(('/facilities'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal()
            router.reload({ only: ['facilities'] })
            form.reset()
        },
    })
  }
}

function deleteFacility(id: number) {
  if (confirm('Yakin ingin menghapus fasilitas ini?')) {
    router.delete(`/facilities/${id}`)
  }
}

const selectedCategory = ref('')

function exportData() {
 return `/facilities/export?category_id=${selectedCategory.value}`
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
        <h1 class="text-2xl font-bold">Manajemen Fasilitas</h1>
        <div class="flex space-x-2">
            <select v-model="selectedCategory" class="border rounded p-2">
            <option value="">Semua Kategori</option>
            <option v-for="cat in props.categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
            </option>
            </select>

            <a
             :href="`${exportUrl}?category_id=${selectedCategory}`"
             class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
            >
             Export XLSX
            </a>
            <button
            @click="openModal()"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
            + Tambah Fasilitas
            </button>
        </div>
      </div>

      <!-- Table -->
      <table class="w-full bg-white rounded-lg shadow">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="px-4 py-2">Nama</th>
            <th class="px-4 py-2">Qty</th>
            <th class="px-4 py-2">Qty available</th>
            <th class="px-4 py-2">Kondisi</th>
            <th class="px-4 py-2">Deskripsi</th>
            <th class="px-4 py-2 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="facility in props.facilities"
            :key="facility.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="px-4 py-2">{{ facility.name }}</td>
            <td class="px-4 py-2">{{ facility.quantity_total }}</td>
            <td class="px-4 py-2">{{ facility.quantity_available }}</td>
            <td class="px-4 py-2">{{ facility.condition }}</td>
            <td class="px-4 py-2">{{ facility.description }}</td>
            <td class="px-4 py-2 text-right space-x-2">
              <button
                @click="openModal(facility)"
                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
              >
                Edit
              </button>
              <button
                @click="deleteFacility(facility.id)"
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
            {{ editMode ? 'Edit Fasilitas' : 'Tambah Fasilitas' }}
          </h2>

          <form @submit.prevent="saveFacility" class="space-y-4">
            <div v-if="editMode">
                <label class="block text-sm font-medium">Kode Inventaris</label>
                <input
                   v-model="form.code"
                   type="text"
                   class="w-full border rounded p-2 bg-gray-100 text-gray-600 cursor-not-allowed"
                   readonly
                />
            </div>

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

            <div>
                <label class="block text-sm text-black font-medium">Kategori</label>
                <select
                    v-model="form.category_ids"
                    multiple
                    class="w-full border rounded p-2"
                >
                    <option
                    v-for="category in props.categories"
                    :key="category.id"
                    :value="category.id"
                    >
                    {{ category.name }}
                    </option>
                </select>
                <p v-if="form.errors.category_ids" class="text-red-500 text-sm mt-1">
                    {{ form.errors.category_ids }}
                </p>
            </div>

            <div>
              <label class="block text-sm font-medium">Qty Total</label>
              <input
                v-model="form.quantity_total"
                type="number"
                placeholder="0"
                class="w-full border rounded p-2"
                :class="form.errors.quantity_total ? 'border-red-500' : 'border-gray-300'"
              />
              <p v-if="form.errors.quantity_total" class="text-red-500 text-sm mt-1">
                {{ form.errors.quantity_total }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium">Qty Available</label>
              <input
                v-model="form.quantity_available"
                type="number"
                class="w-full border rounded p-2"
                placeholder="0"
                :class="form.errors.quantity_available ? 'border-red-500' : 'border-gray-300'"
              />
              <p v-if="form.errors.quantity_available" class="text-red-500 text-sm mt-1">
                {{ form.errors.quantity_available }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium">Kondisi</label>
              <div class="flex gap-4">
                <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="radio"
                    value="baik"
                    v-model="form.condition"
                    class="text-blue-600 focus:ring-blue-500"
                />
                <span>Baik</span>
                </label>

                <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="radio"
                    value="rusak_ringan"
                    v-model="form.condition"
                    class="text-yellow-500 focus:ring-yellow-400"
                />
                <span>Rusak Ringan</span>
                </label>

                <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="radio"
                    value="rusak_berat"
                    v-model="form.condition"
                    class="text-red-600 focus:ring-red-500"
                />
                <span>Rusak Berat</span>
                </label>
              </div>
              <p v-if="form.errors.condition" class="text-red-500 text-sm mt-1">
                {{ form.errors.condition }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium">Deskripsi</label>
              <textarea
                v-model="form.description"
                class="w-full border rounded p-2"
              />
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
