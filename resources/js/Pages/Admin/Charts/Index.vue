<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface ChartItem {
    id: number
    name: string
    period: string
    is_active: boolean
    entries_count: number
    starts_at?: string | null
    ends_at?: string | null
}

const periodLabel: Record<string, string> = { daily: 'Diário', weekly: 'Semanal', monthly: 'Mensal' }

defineProps<{
    charts: {
        data: ChartItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Rankings</h1>
                <p class="text-sm text-[var(--muted)]">Rankings e paradas de sucessos</p>
            </div>
            <Link href="/admin/charts/create" class="btn-accent">Novo ranking</Link>
        </div>

        <form method="get" action="/admin/charts" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar ranking..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Ranking</th>
                        <th class="px-4 py-3 font-semibold">Período</th>
                        <th class="px-4 py-3 font-semibold">Posições</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in charts.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.name }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ periodLabel[item.period] || item.period }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.entries_count }}</td>
                        <td class="px-4 py-3">
                            <span v-if="item.is_active" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700">Ativo</span>
                            <span v-else class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Inativo</span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/charts/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/charts/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este ranking?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!charts.data.length">
                        <td colspan="5" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum ranking encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="charts.links" />
    </AdminLayout>
</template>