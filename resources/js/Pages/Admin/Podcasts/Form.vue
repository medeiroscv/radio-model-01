<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

const props = defineProps<{
    podcast?: {
        id: number
        name: string
        cover?: string | null
        description?: string | null
        host?: string | null
        rss_url?: string | null
        is_active?: boolean
    }
}>()

const form = useForm({
    name: props.podcast?.name ?? '',
    cover: props.podcast?.cover ?? '',
    description: props.podcast?.description ?? '',
    host: props.podcast?.host ?? '',
    rss_url: props.podcast?.rss_url ?? '',
    is_active: props.podcast?.is_active ?? true,
})

const isEdit = !!props.podcast

function submit() {
    if (isEdit) {
        form.put(`/admin/podcasts/${props.podcast!.id}`)
    } else {
        form.post('/admin/podcasts')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar podcast' : 'Novo podcast' }}</h1>
                <p class="text-sm text-[var(--muted)]">Dados do podcast</p>
            </div>
            <Link href="/admin/podcasts" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-3xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nome *</label>
                    <input v-model="form.name" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Apresentador (host)</label>
                    <input v-model="form.host" type="text" class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Descrição</label>
                    <textarea v-model="form.description" rows="6" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL do feed RSS</label>
                    <input v-model="form.rss_url" type="url" class="input-app w-full" placeholder="https://..." />
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3">
                    <h3 class="font-semibold text-sm">Capa</h3>
                    <input v-model="form.cover" type="url" class="input-app w-full" placeholder="https://..." />
                    <img v-if="form.cover" :src="form.cover" alt="Prévia" class="w-full aspect-square object-cover rounded-xl border border-[var(--border)]" />
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_active" type="checkbox" class="rounded" />
                        Podcast ativo
                    </label>
                </div>

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar podcast') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>