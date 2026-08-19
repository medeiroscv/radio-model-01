<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface ArtistItem {
    id: number
    name: string
    slug: string
    photo?: string | null
    songs_count: number
}

defineProps<{
    artists: {
        data: ArtistItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Artistas</h1>
                <p class="text-sm text-[var(--muted)]">Artistas do catálogo de músicas</p>
            </div>
            <Link href="/admin/artists/create" class="btn-accent">Novo artista</Link>
        </div>

        <form method="get" action="/admin/artists" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar artista..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Artista</th>
                        <th class="px-4 py-3 font-semibold">Músicas</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in artists.data" :key="item.id">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img v-if="item.photo" :src="item.photo" :alt="item.name" class="w-9 h-9 rounded-full object-cover" />
                                <span v-else class="w-9 h-9 rounded-full bg-[var(--surface)] flex items-center justify-center text-xs font-bold">{{ item.name.charAt(0) }}</span>
                                <span class="font-medium">{{ item.name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.songs_count }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/artists/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/artists/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este artista?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!artists.data.length">
                        <td colspan="3" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum artista encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="artists.links" />
    </AdminLayout>
</template>