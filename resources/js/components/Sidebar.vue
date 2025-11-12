<template>
  <aside class="bg-white border-r border-gray-200 p-4">
    <h1 class="text-xl font-bold mb-6">Inventaris</h1>
    <nav class="flex flex-col space-y-2">
      <a href="/dashboard" class="hover:bg-gray-100 rounded px-3 py-2" >Dashboard</a>
      <a href="/facilities" class="hover:bg-gray-100 rounded px-3 py-2" >Fasilitas</a>
      <a href="/categories" class="hover:bg-gray-100 rounded px-3 py-2" >Kategori</a>
      <a href="/borrowings" class="hover:bg-gray-100 rounded px-3 py-2" >Peminjaman</a>
    </nav>
  </aside>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const active = computed(() => window.location.pathname.includes('facilities') ? 'facilities' : 'dashboard')
</script>
