<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface ArtistOption {
    id: number
    name: string
}

const props = defineProps<{
    song?: {
        id: number
        artist_id?: number | null
        title: string
        cover?: string | null
        description?: string
        spotify_url?: string
        youtube_url?: string
        deezer_url?: string
        apple_music_url?: string
        external_url?: string
        is_featured?: boolean
        is_release?: boolean
        released_at?: string
    }
    artists: ArtistOption[]
}>()

const form = useForm({
    artist_id: props.song?.artist_id ?? null,
    title: props.song?.title ?? '',
    cover: props.song?.cover ?? '',
    description: props.song?.description ?? '',
    spotify_url: props.song?.spotify_url ?? '',
    youtube_url: props.song?.youtube_url ?? '',
    deezer_url: props.song?.deezer_url ?? '',
    apple_music_url: props.song?.apple_music_url ?? '',
    external_url: props.song?.external_url ?? '',
    is_featured: props.song?.is_featured ?? false,
    is_release: props.song?.is_release ?? false,
    released_at: props.song?.released_at ?? '',
})

const isEdit = !!props.song

function submit() {
    if (isEdit) {
        form.put(`/admin/songs/${props.song!.id}`)
    } else {
        form.post('/admin/songs')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar música' : 'Nova música' }}</h1>
                <p class="text-sm text-[var(--muted)]">Cadastre músicas do catálogo</p>
            </div>
            <Link href="/admin/songs" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
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
                <div>
                    <label class="block text-sm font-medium mb-1">Spotify</label>
                    <input v-model="form.spotify_url" type="url" class="input-app w-full" placeholder="https://..." />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">YouTube</label>
                    <input v-model="form.youtube_url" type="url" class="input-app w-full" placeholder="https://..." />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Deezer</label>
                    <input v-model="form.deezer_url" type="url" class="input-app w-full" placeholder="https://..." />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Apple Music</label>
                    <input v-model="form.apple_music_url" type="url" class="input-app w-full" placeholder="https://..." />
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
                        <label class="block text-sm font-medium mb-1">Artista</label>
                        <select v-model="form.artist_id" class="input-app w-full">
                            <option :value="null">Sem artista</option>
                            <option v-for="a in artists" :key="a.id" :value="a.id">{{ a.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Capa</label>
                        <input v-model="form.cover" type="url" class="input-app w-full" placeholder="https://..." />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Data de lançamento</label>
                        <input v-model="form.released_at" type="date" class="input-app w-full" />
                    </div>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_release" type="checkbox" class="rounded" />
                        É lançamento
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_featured" type="checkbox" class="rounded" />
                        Em destaque
                    </label>
                </div>

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar música') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>