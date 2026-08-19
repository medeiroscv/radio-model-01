<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../../Layouts/AdminLayout.vue'
import Pagination from '../../../../Components/Admin/Pagination.vue'

interface EpisodeItem {
    id: number
    title: string
    duration?: string | null
    is_published: boolean
    published_at?: string | null
}

interface PodcastItem {
    id: number
    name: string
    cover?: string | null
}

defineProps<{
    podcast: PodcastItem
    episodes: {
        data: EpisodeItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">{{ podcast.name }} — Episódios</h1>
                <p class="text-sm text-[var(--muted)]">Gerencie os episódios do podcast</p>
            </div>
            <div class="flex gap-3">
                <Link href="/admin/podcasts" class="btn-outline !px-4 !py-2 text-sm">Podcasts</Link>
                <Link :href="`/admin/podcasts/${podcast.id}/episodes/create`" class="btn-accent">Novo episódio</Link>
            </div>
        </div>

        <form method="get" :action="`/admin/podcasts/${podcast.id}/episodes`" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar episódio..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Episódio</th>
                        <th class="px-4 py-3 font-semibold">Duração</th>
                        <th class="px-4 py-3 font-semibold">Publicado</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in episodes.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.title }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.duration || '—' }}</td>
                        <td class="px-4 py-3">
                            <span v-if="item.is_published" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                                {{ item.published_at ? new Date(item.published_at).toLocaleDateString('pt-BR') : 'Publicado' }}
                            </span>
                            <span v-else class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Rascunho</span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/podcasts/${podcast.id}/episodes/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/podcasts/${podcast.id}/episodes/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este episódio?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!episodes.data.length">
                        <td colspan="4" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum episódio encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="episodes.links" />
    </AdminLayout>
</template>