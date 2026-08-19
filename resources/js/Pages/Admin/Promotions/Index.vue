<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface PromotionItem {
    id: number
    title: string
    slug: string
    is_active: boolean
    is_featured: boolean
    start_date?: string
    end_date?: string
    created_at?: string
}

defineProps<{
    promotions: {
        data: PromotionItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Promoções</h1>
                <p class="text-sm text-[var(--muted)]">Gerencie as campanhas e sorteios</p>
            </div>
            <Link href="/admin/promotions/create" class="btn-accent">Nova promoção</Link>
        </div>

        <form method="get" action="/admin/promotions" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar promoção..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Título</th>
                        <th class="px-4 py-3 font-semibold">Período</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in promotions.data" :key="item.id">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ item.title }}</p>
                            <Link :href="`/promocoes/${item.slug}`" target="_blank" class="text-xs text-[var(--accent)] hover:underline">Ver no site</Link>
                        </td>
                        <td class="px-4 py-3 text-[var(--muted)]">
                            <span v-if="item.start_date || item.end_date">{{ item.start_date || '—' }} até {{ item.end_date || '—' }}</span>
                            <span v-else>Sem período</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5">
                                <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600']">
                                    {{ item.is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                                <span v-if="item.is_featured" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Destaque</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/promotions/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/promotions/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir esta promoção?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!promotions.data.length">
                        <td colspan="4" class="px-4 py-10 text-center text-[var(--muted)]">Nenhuma promoção encontrada.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav v-if="promotions.links.length > 3" class="mt-6 flex justify-center gap-2">
            <a v-for="(link, i) in promotions.links" :key="i" :href="link.url ?? ''" v-html="link.label"
                :class="['rounded-lg px-4 py-2 text-sm', link.active ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface)] border border-[var(--border)] text-[var(--muted)]', !link.url && 'opacity-50 pointer-events-none']" />
        </nav>
    </AdminLayout>
</template>