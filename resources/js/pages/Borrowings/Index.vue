<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps<{ loans: any[], facilities: any[] }>()
const showModal = ref(false)

const form = useForm({
  borrower_name: '',
  borrowed_at: new Date().toISOString().split('T')[0],
  items: [{ facility_id: '', quantity: 1 }]
})

function addItem() {
  form.items.push({ facility_id: '', quantity: 1 })
}

function getMaxQty(facility_id: string) {
  const f = props.facilities.find(f => f.id === facility_id)
  return f ? f.quantity_available : 0
}

function submit() {
  form.post('/loans', {
    onSuccess: () => {
      showModal.value = false
      form.reset()
    }
  })
}

function returnItem(id: number) {
  router.post(`/loans/${id}/return`, {}, {
    onSuccess: () => {
      // otomatis reload daftar
      router.reload({ only: ['loans'] })
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

      <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full border-collapse">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left">Peminjam</th>
              <th class="px-4 py-2 text-left">Tanggal Pinjam</th>
              <th class="px-4 py-2 text-left">Jatuh Tempo</th>
              <th class="px-4 py-2 text-left">Barang Dipinjam</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="loan in props.loans"
              :key="loan.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="px-4 py-2">{{ loan.borrower_name }}</td>
              <td class="px-4 py-2">{{ loan.borrowed_at }}</td>
              <td class="px-4 py-2">{{ loan.returned_at ? loan.returned_at  : '-'  }}</td>
              <td class="px-4 py-2">
                <ul>
                  <li
                    v-for="item in loan.items"
                    :key="item.facility_name"
                    class="text-sm"
                  >
                    {{ item.facility_name }} — {{ item.quantity }} unit
                  </li>
                </ul>
              </td>
              <td class="px-4 py-2">
                <span
                  v-if="loan.returned_at"
                  class="px-2 py-1 text-xs rounded bg-green-100 text-green-700"
                >
                  Sudah Dikembalikan
                </span>
                <span
                  v-else
                  class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700"
                >
                  Belum Dikembalikan
                </span>
              </td>
              <td class="px-4 py-2 text-right">
                <button
                   v-if="!loan.returned_at"
                  @click="returnItem(loan.id)"
                  class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                  Kembalikan
                </button>

                <span v-else class="text-green-600 font-semibold">
                    Sudah Dikembalikan
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg">
          <h2 class="text-xl font-bold mb-4">Tambah Peminjaman</h2>

          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium">Nama Peminjam</label>
              <input v-model="form.borrower_name" type="text" class="w-full border rounded p-2" />
            </div>

            <div v-for="(item, index) in form.items" :key="index" class="mb-3 border p-2 rounded">
              <select v-model="item.facility_id" class="input">
                <option value="">Pilih Barang</option>
                <option v-for="f in props.facilities" :key="f.id" :value="f.id">
                    {{ f.name }} (Tersedia: {{ f.quantity_available }})
                </option>
              </select>

              <input
                type="number"
                v-model.number="item.quantity"
                :max="getMaxQty(item.facility_id)"
                class="input mt-1"
                placeholder="Jumlah"
              />
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
