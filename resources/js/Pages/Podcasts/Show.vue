<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'
import Pagination from '../../Components/Admin/Pagination.vue'

interface Episode {
    id: number
    title: string
    slug: string
    description?: string | null
    audio_url?: string | null
    duration?: string | null
    image?: string | null
    published_at?: string
}

defineProps<{
    podcast: {
        id: number
        name: string
        cover?: string | null
        description?: string
        host?: string | null
        rss_url?: string | null
    }
    episodes: {
        data: Episode[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
}>()

function formatDate(value?: string): string {
    if (!value) return ''
    return new Date(value).toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' })
}
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <nav class="text-xs text-[var(--muted)] mb-6">
                <Link href="/podcasts" class="hover:text-[var(--accent)]">Podcasts</Link>
                <span class="mx-2">›</span>
                <span>{{ podcast.name }}</span>
            </nav>

            <header class="flex flex-wrap gap-8 items-center mb-12">
                <div class="w-40 h-40 rounded-2xl bg-[var(--surface)] flex items-center justify-center overflow-hidden shrink-0 border border-[var(--border)]">
                    <img v-if="podcast.cover" :src="podcast.cover" :alt="podcast.name" class="w-full h-full object-cover" />
                    <span v-else class="text-7xl">🎙️</span>
                </div>
                <div class="flex-1 min-w-[260px]">
                    <h1 class="text-3xl lg:text-4xl font-bold mb-2">{{ podcast.name }}</h1>
                    <p v-if="podcast.host" class="text-sm text-[var(--muted)] mb-3">Apresentado por {{ podcast.host }}</p>
                    <p class="text-[var(--muted)] max-w-2xl">{{ podcast.description }}</p>
                </div>
            </header>

            <h2 class="section-title mb-6">Episódios</h2>

            <div v-if="!episodes.data.length" class="text-center py-16 text-[var(--muted)]">
                Nenhum episódio publicado ainda.
            </div>

            <div class="space-y-4">
                <article v-for="episode in episodes.data" :key="episode.id" class="flex gap-5 p-4 rounded-2xl border border-[var(--border)]">
                    <div class="w-24 h-24 rounded-xl bg-[var(--surface)] flex items-center justify-center shrink-0 overflow-hidden">
                        <img v-if="episode.image || podcast.cover" :src="(episode.image || podcast.cover) ?? ''" :alt="episode.title" class="w-full h-full object-cover" />
                        <span v-else class="text-4xl">▶️</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold mb-1">{{ episode.title }}</h3>
                        <p class="text-xs text-[var(--muted)] mb-2">{{ formatDate(episode.published_at) }}<template v-if="episode.duration"> · {{ episode.duration }}</template></p>
                        <p v-if="episode.description" class="text-sm text-[var(--muted)] line-clamp-2 mb-3">{{ episode.description }}</p>
                        <div v-if="episode.audio_url" class="flex flex-wrap gap-2">
                            <audio :src="episode.audio_url" controls class="h-10 w-full max-w-md"></audio>
                        </div>
                    </div>
                </article>
            </div>

            <Pagination :links="episodes.links" />
        </section>
    </AppLayout>
</template>