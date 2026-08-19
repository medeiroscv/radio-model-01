<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface MediaFile {
    path: string
    name: string
    url: string
    size: number
    modified: number
}

const props = defineProps<{
    files: MediaFile[]
}>()

const uploading = ref(false)
const fileInput = ref<HTMLInputElement | null>(null)

function formatSize(bytes: number): string {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB'
    return Math.round(bytes / 1024) + ' KB'
}

function formatDate(ts: number): string {
    return new Date(ts * 1000).toLocaleString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement
    if (!input.files?.length) return
    uploading.value = true
    const form = new FormData()
    form.append('file', input.files[0])
    router.post('/admin/media', form, {
        preserveScroll: true,
        onSuccess: () => {
            uploading.value = false
            if (fileInput.value) fileInput.value.value = ''
        },
        onError: () => {
            uploading.value = false
            if (fileInput.value) fileInput.value.value = ''
        },
    })
}

function removeFile(file: MediaFile) {
    if (!confirm(`Excluir ${file.name}?`)) return
    router.delete('/admin/media', {
        data: { path: file.path },
        preserveScroll: true,
    })
}

function copyUrl(file: MediaFile) {
    navigator.clipboard?.writeText(file.url).then(() => alert('URL copiada!'))
}
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Biblioteca de mídia</h1>
                <p class="text-sm text-[var(--muted)]">Imagens enviadas para o site</p>
            </div>
            <label class="btn-accent cursor-pointer">
                {{ uploading ? 'Enviando...' : 'Enviar imagem' }}
                <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileSelected" />
            </label>
        </div>

        <div v-if="!files.length" class="rounded-2xl border border-[var(--border)] bg-[var(--background)] py-16 text-center text-[var(--muted)]">
            Nenhuma imagem na biblioteca. Envie a primeira acima.
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="file in files" :key="file.path" class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
                <div class="aspect-video bg-[var(--surface)]">
                    <img :src="file.url" :alt="file.name" class="w-full h-full object-cover" />
                </div>
                <div class="p-3">
                    <p class="text-xs font-medium truncate" :title="file.name">{{ file.name }}</p>
                    <p class="text-xs text-[var(--muted)] mt-1">{{ formatSize(file.size) }} · {{ formatDate(file.modified) }}</p>
                    <div class="flex gap-3 mt-3">
                        <button type="button" class="text-xs font-semibold text-[var(--accent)] hover:underline" @click="copyUrl(file)">Copiar URL</button>
                        <button type="button" class="text-xs font-semibold text-red-500 hover:underline" @click="removeFile(file)">Excluir</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>