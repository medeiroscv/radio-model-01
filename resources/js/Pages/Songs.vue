<script setup lang="ts">
import AppLayout from '../Layouts/AppLayout.vue'

interface SongItem {
    id: number
    title: string
    slug: string
    cover?: string | null
    description?: string
    artist?: { name: string } | null
}

interface Pagination {
    data: SongItem[]
    links: Array<{ url: string | null; label: string; active: boolean }>
}

defineProps<{
    songs: Pagination
    search: string | null
}>()
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <div class="text-center mb-12">
                <h1 class="text-3xl lg:text-4xl font-bold mb-2">Músicas</h1>
                <p class="text-[var(--muted)]">Explore o catálogo de músicas e artistas</p>
                <div class="mx-auto mt-4 h-1 w-10 rounded-full bg-[var(--accent)]"></div>
            </div>

            <form class="max-w-md mx-auto mb-10" method="get" action="/musicas">
                <input type="search" name="q" :value="search ?? ''" placeholder="Buscar música ou artista..." class="input-app w-full" />
            </form>

            <div v-if="songs.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="song in songs.data" :key="song.id" class="group rounded-2xl overflow-hidden border border-[var(--border)]">
                    <div class="aspect-square bg-[var(--surface)] flex items-center justify-center overflow-hidden">
                        <img v-if="song.cover" :src="song.cover ?? ''" :alt="song.title" class="w-full h-full object-cover" />
                        <span v-else class="text-6xl">🎵</span>
                    </div>
                    <div class="p-4">
                        <h2 class="font-semibold leading-snug group-hover:text-[var(--accent)] transition-colors">{{ song.title }}</h2>
                        <p class="text-xs text-[var(--muted)]">{{ song.artist?.name }}</p>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-16">
                <span class="text-6xl block mb-4">🎵</span>
                <p class="text-[var(--muted)]">Nenhuma música encontrada.</p>
            </div>

            <nav v-if="songs.links.length > 3" class="mt-10 flex justify-center gap-2">
                <a v-for="(link, i) in songs.links" :key="i" :href="link.url ?? ''" v-html="link.label"
                    :class="['rounded-lg px-4 py-2 text-sm', link.active ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface)] border border-[var(--border)] text-[var(--muted)]', !link.url && 'opacity-50 pointer-events-none']" />
            </nav>
        </section>
    </AppLayout>
</template>