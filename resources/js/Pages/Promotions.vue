<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

interface PromotionItem {
    id: number
    title: string
    slug: string
    description?: string
    image?: string | null
    call_to_action?: string
}

interface Pagination {
    data: PromotionItem[]
    links: Array<{ url: string | null; label: string; active: boolean }>
}

defineProps<{
    promotions: Pagination
}>()
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <div class="text-center mb-12">
                <h1 class="text-3xl lg:text-4xl font-bold mb-2">Promoções</h1>
                <p class="text-[var(--muted)]">Participe e concorra a prêmios incríveis</p>
                <div class="mx-auto mt-4 h-1 w-10 rounded-full bg-[var(--accent)]"></div>
            </div>

            <div v-if="promotions.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link v-for="promo in promotions.data" :key="promo.id" :href="`/promocoes/${promo.slug}`" class="group rounded-2xl overflow-hidden border border-[var(--border)] transition-shadow hover:shadow-lg">
                    <div class="aspect-[4/3] bg-[var(--surface)] flex items-center justify-center overflow-hidden">
                        <img v-if="promo.image" :src="promo.image ?? ''" :alt="promo.title" class="w-full h-full object-cover" />
                        <span v-else class="text-6xl">🎁</span>
                    </div>
                    <div class="p-5">
                        <h2 class="font-semibold text-lg mb-1 group-hover:text-[var(--accent)] transition-colors">{{ promo.title }}</h2>
                        <p class="text-sm text-[var(--muted)] mb-3 line-clamp-2">{{ promo.description }}</p>
                        <span class="text-sm font-semibold text-[var(--accent)]">{{ promo.call_to_action || 'Participe agora' }} →</span>
                    </div>
                </Link>
            </div>

            <div v-else class="text-center py-20">
                <span class="text-6xl block mb-4">🎁</span>
                <h2 class="text-xl font-semibold mb-2">Em breve!</h2>
                <p class="text-[var(--muted)]">Novas promoções estão sendo preparadas. Fique ligado!</p>
            </div>

            <nav v-if="promotions.links.length > 3" class="mt-10 flex justify-center gap-2">
                <a v-for="(link, i) in promotions.links" :key="i" :href="link.url ?? ''" v-html="link.label"
                    :class="['rounded-lg px-4 py-2 text-sm', link.active ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface)] border border-[var(--border)] text-[var(--muted)]', !link.url && 'opacity-50 pointer-events-none']" />
            </nav>
        </section>
    </AppLayout>
</template>