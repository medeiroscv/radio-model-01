<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface VideoItem {
    id: number
    title: string
    slug: string
    platform?: string
    is_published: boolean
    is_featured: boolean
    category?: { id: number; name: string } | null
}

defineProps<{
    videos: {
        data: VideoItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Vídeos</h1>
                <p class="text-sm text-[var(--muted)]">Vídeos publicados no site</p>
            </div>
            <Link href="/admin/videos/create" class="btn-accent">Novo vídeo</Link>
        </div>

        <form method="get" action="/admin/videos" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar vídeo..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Título</th>
                        <th class="px-4 py-3 font-semibold">Categoria</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in videos.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.title }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.category?.name || '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5">
                                <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', item.is_published ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700']">
                                    {{ item.is_published ? 'Publicado' : 'Rascunho' }}
                                </span>
                                <span v-if="item.is_featured" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Destaque</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/videos/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/videos/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este vídeo?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!videos.data.length">
                        <td colspan="4" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum vídeo encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="videos.links" />
    </AdminLayout>
</template>