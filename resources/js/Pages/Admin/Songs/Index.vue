<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface SongItem {
    id: number
    title: string
    slug: string
    is_release: boolean
    is_featured: boolean
    artist?: { id: number; name: string } | null
}

defineProps<{
    songs: {
        data: SongItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Músicas</h1>
                <p class="text-sm text-[var(--muted)]">Catálogo de músicas e lançamentos</p>
            </div>
            <Link href="/admin/songs/create" class="btn-accent">Nova música</Link>
        </div>

        <form method="get" action="/admin/songs" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar música..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Música</th>
                        <th class="px-4 py-3 font-semibold">Artista</th>
                        <th class="px-4 py-3 font-semibold">Flags</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in songs.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.title }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.artist?.name || '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5">
                                <span v-if="item.is_release" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Lançamento</span>
                                <span v-if="item.is_featured" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Destaque</span>
                                <span v-if="!item.is_release && !item.is_featured" class="text-xs text-[var(--muted)]">—</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/songs/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/songs/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir esta música?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!songs.data.length">
                        <td colspan="4" class="px-4 py-10 text-center text-[var(--muted)]">Nenhuma música encontrada.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="songs.links" />
    </AdminLayout>
</template>