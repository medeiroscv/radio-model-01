<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

interface PromotionDetail {
    id: number
    title: string
    slug: string
    call_to_action?: string
    rules?: string
    regulations?: string
    description?: string
    image?: string | null
    banner_image?: string | null
    start_date?: string
    end_date?: string
    participate_url?: string
}

defineProps<{
    promotion: PromotionDetail
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
                <Link href="/promocoes" class="hover:text-[var(--accent)]">Promoções</Link>
                <span class="mx-2">›</span>
                <span>{{ promotion.title }}</span>
            </nav>

            <div v-if="promotion.banner_image || promotion.image" class="mb-8 rounded-2xl overflow-hidden">
                <img :src="promotion.banner_image || promotion.image || ''" :alt="promotion.title" class="w-full h-auto object-cover" />
            </div>

            <h1 class="text-3xl lg:text-4xl font-bold mb-3">{{ promotion.title }}</h1>
            <p v-if="promotion.call_to_action" class="text-lg text-[var(--accent)] font-semibold mb-4">{{ promotion.call_to_action }}</p>
            <div class="flex flex-wrap gap-4 text-sm text-[var(--muted)] mb-6">
                <span v-if="promotion.start_date">Início: {{ formatDate(promotion.start_date) }}</span>
                <span v-if="promotion.end_date">Término: {{ formatDate(promotion.end_date) }}</span>
            </div>

            <div v-if="promotion.description" class="mb-8 text-[var(--muted)] leading-relaxed">{{ promotion.description }}</div>

            <div v-if="promotion.rules" class="mb-8">
                <h2 class="text-xl font-bold mb-3">Como participar</h2>
                <div class="prose prose-lg max-w-none" v-html="promotion.rules"></div>
            </div>

            <div v-if="promotion.regulations" class="mb-8">
                <h2 class="text-xl font-bold mb-3">Regulamento</h2>
                <div class="prose prose-sm max-w-none text-[var(--muted)]" v-html="promotion.regulations"></div>
            </div>

            <a v-if="promotion.participate_url" :href="promotion.participate_url" target="_blank" class="btn-accent">Participar</a>
        </article>
    </AppLayout>
</template>