<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'
import BannerSlot from '../../Components/BannerSlot.vue'

interface NewsDetail {
    id: number
    title: string
    slug: string
    subtitle?: string
    summary?: string
    content?: string
    featured_image?: string | null
    gallery?: string[] | null
    published_at?: string
    views?: number
    category?: { id: number; name: string; slug: string; color?: string } | null
    author?: { id: number; name: string } | null
    tags?: Array<{ id: number; name: string }>
}

defineProps<{
    news: NewsDetail
    related: NewsDetail[]
}>()

function formatDate(value?: string): string {
    if (!value) return ''
    return new Date(value).toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' })
}
</script>

<template>
    <AppLayout>
        <article class="container-app section-spacing max-w-4xl">
            <nav class="text-xs text-[var(--muted)] mb-6">
                <Link href="/noticias" class="hover:text-[var(--accent)]">Notícias</Link>
                <span class="mx-2">›</span>
                <span v-if="news.category">{{ news.category.name }}</span>
            </nav>

            <header class="mb-8">
                <span v-if="news.category" class="label-category mb-3" :style="{ color: news.category.color }">{{ news.category.name }}</span>
                <h1 class="text-3xl lg:text-4xl font-bold leading-tight mb-3">{{ news.title }}</h1>
                <p v-if="news.subtitle" class="text-lg text-[var(--muted)]">{{ news.subtitle }}</p>
                <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-[var(--muted)]">
                    <span>{{ formatDate(news.published_at) }}</span>
                    <span v-if="news.author">Por {{ news.author.name }}</span>
                    <span>{{ news.views ?? 0 }} visualizações</span>
                </div>
            </header>

            <div v-if="news.featured_image" class="mb-8 rounded-2xl overflow-hidden">
                <img :src="news.featured_image" :alt="news.title" class="w-full h-auto object-cover" />
            </div>

            <div v-if="news.summary" class="mb-6 p-4 rounded-xl bg-[var(--surface)] border-l-4 border-l-[var(--accent)] text-[var(--muted)] italic">{{ news.summary }}</div>

            <div v-if="news.content" class="prose prose-lg max-w-none" v-html="news.content"></div>

            <div v-if="news.gallery?.length" class="mt-8">
                <h2 class="section-title mb-6">Galeria</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <a v-for="(img, index) in news.gallery" :key="index" :href="img" target="_blank" rel="noopener" class="group rounded-2xl overflow-hidden border border-[var(--border)]">
                        <img :src="img" :alt="`${news.title} - foto ${index + 1}`" class="w-full aspect-video object-cover group-hover:scale-105 transition-transform" />
                    </a>
                </div>
            </div>

            <div v-if="news.tags?.length" class="flex flex-wrap gap-2 mt-8">
                <span v-for="tag in news.tags" :key="tag.id" class="rounded-full px-4 py-1 text-xs bg-[var(--surface)] border border-[var(--border)]">#{{ tag.name }}</span>
            </div>

            <BannerSlot position="article_top" className="mt-10" />
        </article>

        <section v-if="related.length" class="container-app section-spacing pt-0">
            <h2 class="section-title mb-8">Leia também</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link v-for="item in related" :key="item.id" :href="`/noticias/${item.slug}`" class="group rounded-2xl overflow-hidden border border-[var(--border)]">
                    <div class="aspect-video bg-[var(--surface)] flex items-center justify-center overflow-hidden">
                        <img v-if="item.featured_image" :src="item.featured_image" :alt="item.title" class="w-full h-full object-cover" />
                        <span v-else class="text-5xl">📰</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold leading-snug group-hover:text-[var(--accent)] transition-colors">{{ item.title }}</h3>
                    </div>
                </Link>
            </div>
        </section>
    </AppLayout>
</template>