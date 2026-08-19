<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../../Layouts/AdminLayout.vue'

const props = defineProps<{
    podcast: {
        id: number
        name: string
    }
    episode?: {
        id: number
        title: string
        description?: string | null
        audio_url?: string | null
        duration?: string | null
        image?: string | null
        external_url?: string | null
        published_at?: string | null
        is_published?: boolean
    }
}>()

const form = useForm({
    title: props.episode?.title ?? '',
    description: props.episode?.description ?? '',
    audio_url: props.episode?.audio_url ?? '',
    duration: props.episode?.duration ?? '',
    image: props.episode?.image ?? '',
    external_url: props.episode?.external_url ?? '',
    published_at: props.episode?.published_at ? props.episode.published_at.slice(0, 10) : '',
    is_published: props.episode?.is_published ?? false,
})

const isEdit = !!props.episode

function submit() {
    if (isEdit) {
        form.put(`/admin/podcasts/${props.podcast.id}/episodes/${props.episode!.id}`)
    } else {
        form.post(`/admin/podcasts/${props.podcast.id}/episodes`)
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar episódio' : 'Novo episódio' }}</h1>
                <p class="text-sm text-[var(--muted)]">{{ podcast.name }}</p>
            </div>
            <Link :href="`/admin/podcasts/${podcast.id}/episodes`" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-3xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Título *</label>
                    <input v-model="form.title" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Descrição</label>
                    <textarea v-model="form.description" rows="6" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL do áudio</label>
                    <input v-model="form.audio_url" type="url" class="input-app w-full" placeholder="https://..." />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Link externo</label>
                    <input v-model="form.external_url" type="url" class="input-app w-full" placeholder="https://..." />
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3">
                    <h3 class="font-semibold text-sm">Configuração</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1">Duração</label>
                        <input v-model="form.duration" type="text" class="input-app w-full" placeholder="Ex: 45:30" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Imagem do episódio</label>
                        <input v-model="form.image" type="url" class="input-app w-full" placeholder="https://..." />
                        <img v-if="form.image" :src="form.image" alt="Prévia" class="w-full aspect-video object-cover rounded-xl border border-[var(--border)] mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Data de publicação</label>
                        <input v-model="form.published_at" type="date" class="input-app w-full" />
                    </div>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_published" type="checkbox" class="rounded" />
                        Publicado
                    </label>
                </div>

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar episódio') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>