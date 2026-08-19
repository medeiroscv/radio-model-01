<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

interface PodcastItem {
    id: number
    name: string
    slug: string
    cover?: string | null
    description?: string
    host?: string | null
    episodes_count?: number
}

defineProps<{
    podcasts: PodcastItem[]
}>()
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <div class="text-center mb-12">
                <h1 class="text-3xl lg:text-4xl font-bold mb-2">Podcasts</h1>
                <p class="text-[var(--muted)] max-w-xl mx-auto">Ouça nossos podcasts quando e onde quiser.</p>
            </div>

            <div v-if="!podcasts.length" class="text-center py-16 text-[var(--muted)]">
                Nenhum podcast disponível ainda.
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link v-for="podcast in podcasts" :key="podcast.id" :href="`/podcasts/${podcast.slug}`" class="group rounded-2xl overflow-hidden border border-[var(--border)] transition-shadow hover:shadow-lg">
                    <div class="aspect-square bg-[var(--surface)] flex items-center justify-center overflow-hidden">
                        <img v-if="podcast.cover" :src="podcast.cover" :alt="podcast.name" class="w-full h-full object-cover" />
                        <span v-else class="text-7xl">🎙️</span>
                    </div>
                    <div class="p-5">
                        <h2 class="font-bold text-lg mb-1 group-hover:text-[var(--accent)] transition-colors">{{ podcast.name }}</h2>
                        <p v-if="podcast.host" class="text-xs text-[var(--muted)] mb-2">Apresentado por {{ podcast.host }}</p>
                        <p class="text-sm text-[var(--muted)] mb-3 line-clamp-2">{{ podcast.description }}</p>
                        <span class="text-sm font-semibold text-[var(--accent)]">{{ podcast.episodes_count ?? 0 }} episódios →</span>
                    </div>
                </Link>
            </div>
        </section>
    </AppLayout>
</template>