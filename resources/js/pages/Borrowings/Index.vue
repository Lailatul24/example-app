<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps<{ borrowings: any[], facilities: any[] }>()
const showModal = ref(false)

const form = useForm({
  borrower_name: '',
  borrow_date: '',
  facility_ids: [] as number[],
})

function submit() {
  form.post('/borrowings', {
    onSuccess: () => {
      showModal.value = false
      form.reset()
    }
  })
}

function returnItem(id: number) {
  router.put(`/borrowings/${id}`, {}, {
    onSuccess: () => {
      // otomatis reload daftar
      router.reload({ only: ['borrowings'] })
    }
  })
}
</script>

<template>
  <AppLayout>
    <div class="p-6">
      <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-bold">Manajemen Peminjaman Barang</h1>
        <button @click="showModal = true"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          + Tambah Peminjaman
        </button>
      </div>

      <table class="w-full bg-white shadow rounded">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2">Peminjam</th>
            <th class="px-4 py-2">Tanggal</th>
            <th class="px-4 py-2">Barang</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="b in props.borrowings" :key="b.id" class="border-t">
            <td class="px-4 py-2">{{ b.borrower_name }}</td>
            <td class="px-4 py-2">{{ b.borrow_date }}</td>
            <td class="px-4 py-2">
              <ul>
                <li v-for="f in b.facilities" :key="f.id">
                  {{ f.name }} ({{ f.pivot.quantity }})
                </li>
              </ul>
            </td>
            <td class="px-4 py-2">
                <span
                    v-if="b.status === 'dipinjam'"
                    class="text-yellow-600 font-medium"
                >
                    Dipinjam
                </span>

                <span
                    v-else
                    class="text-green-600 font-medium"
                >
                    Dikembalikan
                </span>
            </td>

            <td class="px-4 py-2 text-center">
            <button
                v-if="b.status === 'dipinjam'"
                @click="returnItem(b.id)"
                class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
            >
                Kembalikan
            </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg">
          <h2 class="text-xl font-bold mb-4">Tambah Peminjaman</h2>

          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium">Nama Peminjam</label>
              <input v-model="form.borrower_name" type="text" class="w-full border rounded p-2" />
            </div>

            <div>
              <label class="block text-sm font-medium">Tanggal Pinjam</label>
              <input v-model="form.borrow_date" type="date" class="w-full border rounded p-2" />
            </div>

            <div>
              <label class="block text-sm font-medium">Barang yang Dipinjam</label>
              <select v-model="form.facility_ids" multiple class="w-full border rounded p-2">
                <option v-for="f in props.facilities" :key="f.id" :value="f.id">{{ f.name }}</option>
              </select>
            </div>

            <div class="flex justify-end gap-2">
              <button type="button" @click="showModal=false" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
              <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
