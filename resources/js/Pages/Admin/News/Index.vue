<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface NewsItem {
    id: number
    title: string
    slug: string
    is_published: boolean
    is_featured: boolean
    created_at?: string
    category?: { id: number; name: string } | null
    author?: { id: number; name: string } | null
}

defineProps<{
    news: {
        data: NewsItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string; status?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Notícias</h1>
                <p class="text-sm text-[var(--muted)]">Gerencie as publicações do site</p>
            </div>
            <Link href="/admin/news/create" class="btn-accent">Nova notícia</Link>
        </div>

        <form method="get" action="/admin/news" class="flex flex-wrap gap-3 mb-6">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar por título..." class="input-app flex-1 min-w-[200px]" />
            <select name="status" class="input-app">
                <option value="">Todos os status</option>
                <option value="published" :selected="filters.status === 'published'">Publicados</option>
                <option value="draft" :selected="filters.status === 'draft'">Rascunhos</option>
            </select>
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Título</th>
                        <th class="px-4 py-3 font-semibold hidden md:table-cell">Categoria</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold hidden lg:table-cell">Autor</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in news.data" :key="item.id">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ item.title }}</p>
                            <Link :href="`/noticias/${item.slug}`" target="_blank" class="text-xs text-[var(--accent)] hover:underline">Ver no site</Link>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-[var(--muted)]">{{ item.category?.name || '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5">
                                <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', item.is_published ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700']">
                                    {{ item.is_published ? 'Publicado' : 'Rascunho' }}
                                </span>
                                <span v-if="item.is_featured" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Destaque</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-[var(--muted)]">{{ item.author?.name || '—' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/news/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/news/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir esta notícia?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!news.data.length">
                        <td colspan="5" class="px-4 py-10 text-center text-[var(--muted)]">Nenhuma notícia encontrada.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav v-if="news.links.length > 3" class="mt-6 flex justify-center gap-2">
            <a v-for="(link, i) in news.links" :key="i" :href="link.url ?? ''" v-html="link.label"
                :class="['rounded-lg px-4 py-2 text-sm', link.active ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface)] border border-[var(--border)] text-[var(--muted)]', !link.url && 'opacity-50 pointer-events-none']" />
        </nav>
    </AdminLayout>
</template>