<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface CategoryOption {
    id: number
    name: string
}

const props = defineProps<{
    video?: {
        id: number
        title: string
        description?: string
        thumbnail?: string | null
        video_url?: string
        platform?: string
        video_id?: string
        news_category_id?: number | null
        is_featured?: boolean
        is_published?: boolean
        published_at?: string
    }
    categories: CategoryOption[]
}>()

const form = useForm({
    title: props.video?.title ?? '',
    description: props.video?.description ?? '',
    thumbnail: props.video?.thumbnail ?? '',
    video_url: props.video?.video_url ?? '',
    platform: props.video?.platform ?? 'youtube',
    video_id: props.video?.video_id ?? '',
    news_category_id: props.video?.news_category_id ?? null,
    is_featured: props.video?.is_featured ?? false,
    is_published: props.video?.is_published ?? false,
    published_at: props.video?.published_at ?? '',
})

const isEdit = !!props.video

function submit() {
    if (isEdit) {
        form.put(`/admin/videos/${props.video!.id}`)
    } else {
        form.post('/admin/videos')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar vídeo' : 'Novo vídeo' }}</h1>
                <p class="text-sm text-[var(--muted)]">Adicione vídeos ao site</p>
            </div>
            <Link href="/admin/videos" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-4xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Título *</label>
                    <input v-model="form.title" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Descrição</label>
                    <textarea v-model="form.description" rows="4" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Plataforma</label>
                        <select v-model="form.platform" class="input-app w-full">
                            <option value="youtube">YouTube</option>
                            <option value="vimeo">Vimeo</option>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">ID do vídeo</label>
                        <input v-model="form.video_id" type="text" class="input-app w-full" placeholder="Ex: dQw4w9WgXcQ" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL do vídeo</label>
                    <input v-model="form.video_url" type="url" class="input-app w-full" placeholder="https://..." />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Thumbnail</label>
                    <input v-model="form.thumbnail" type="url" class="input-app w-full" placeholder="https://..." />
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3">
                    <h3 class="font-semibold text-sm">Publicação</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1">Categoria</label>
                        <select v-model="form.news_category_id" class="input-app w-full">
                            <option :value="null">Sem categoria</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Data de publicação</label>
                        <input v-model="form.published_at" type="datetime-local" class="input-app w-full" />
                    </div>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_featured" type="checkbox" class="rounded" />
                        Em destaque
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_published" type="checkbox" class="rounded" />
                        Publicar
                    </label>
                </div>

                <img v-if="form.thumbnail" :src="form.thumbnail" :alt="form.title" class="rounded-xl w-full aspect-video object-cover" />

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar vídeo') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>