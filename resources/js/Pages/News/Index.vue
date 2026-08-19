<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

interface NewsItem {
    id: number
    title: string
    slug: string
    subtitle?: string
    summary?: string
    featured_image?: string | null
    published_at?: string
    views?: number
    category?: { id: number; name: string; slug: string; color?: string } | null
}

interface Category {
    id: number
    name: string
    slug: string
    color?: string
}

interface Pagination {
    data: NewsItem[]
    links: Array<{ url: string | null; label: string; active: boolean }>
    current_page: number
    last_page: number
}

defineProps<{
    news: Pagination
    categories: Category[]
    activeCategory: string | null
    search: string | null
}>()

function formatDate(value?: string): string {
    if (!value) return ''
    return new Date(value).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <h1 class="text-3xl lg:text-4xl font-bold">Notícias</h1>
                <nav class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-wide">
                    <Link href="/noticias" :class="['pb-1', !activeCategory ? 'text-[var(--accent)] border-b-2 border-[var(--accent)]' : 'text-[var(--muted)] hover:text-[var(--text)]']">Todas</Link>
                    <Link v-for="cat in categories" :key="cat.id" :href="`/noticias?category=${cat.slug}`" :class="['pb-1', activeCategory === cat.slug ? 'text-[var(--accent)] border-b-2 border-[var(--accent)]' : 'text-[var(--muted)] hover:text-[var(--text)]']">{{ cat.name }}</Link>
                </nav>
            </div>

            <div v-if="news.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link v-for="item in news.data" :key="item.id" :href="`/noticias/${item.slug}`" class="group rounded-2xl overflow-hidden border border-[var(--border)] transition-shadow hover:shadow-lg">
                    <div class="aspect-video bg-[var(--surface)] flex items-center justify-center overflow-hidden">
                        <img v-if="item.featured_image" :src="item.featured_image ?? ''" :alt="item.title" class="w-full h-full object-cover" />
                        <span v-else class="text-6xl">📰</span>
                    </div>
                    <div class="p-5">
                        <span v-if="item.category" class="label-category mb-2" :style="{ color: item.category.color }">{{ item.category.name }}</span>
                        <h2 class="font-semibold text-lg leading-snug mb-2 group-hover:text-[var(--accent)] transition-colors">{{ item.title }}</h2>
                        <p v-if="item.summary" class="text-sm text-[var(--muted)] line-clamp-2">{{ item.summary }}</p>
                        <p class="text-xs text-[var(--muted)] mt-3">{{ formatDate(item.published_at) }}</p>
                    </div>
                </Link>
            </div>

            <div v-else class="text-center py-20">
                <span class="text-6xl block mb-4">📰</span>
                <h2 class="text-xl font-semibold mb-2">Nenhuma notícia publicada ainda</h2>
                <p class="text-[var(--muted)]">Nossa equipe editorial está trabalhando nas próximas publicações.</p>
            </div>

            <nav v-if="news.links.length > 3" class="mt-10 flex justify-center gap-2">
                <a v-for="(link, i) in news.links" :key="i" :href="link.url ?? ''" v-html="link.label"
                    :class="['rounded-lg px-4 py-2 text-sm', link.active ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface)] border border-[var(--border)] text-[var(--muted)]', !link.url && 'opacity-50 pointer-events-none']" />
            </nav>
        </section>
    </AppLayout>
</template>