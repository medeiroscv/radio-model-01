<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface PodcastItem {
    id: number
    name: string
    cover?: string | null
    host?: string | null
    is_active: boolean
    episodes_count: number
}

defineProps<{
    podcasts: {
        data: PodcastItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Podcasts</h1>
                <p class="text-sm text-[var(--muted)]">Podcasts e episódios da rádio</p>
            </div>
            <Link href="/admin/podcasts/create" class="btn-accent">Novo podcast</Link>
        </div>

        <form method="get" action="/admin/podcasts" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar podcast..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="item in podcasts.data" :key="item.id" class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
                <div class="aspect-[4/3] bg-[var(--surface)] flex items-center justify-center overflow-hidden">
                    <img v-if="item.cover" :src="item.cover" :alt="item.name" class="w-full h-full object-cover" />
                    <span v-else class="text-5xl">🎙️</span>
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <h3 class="font-semibold text-lg">{{ item.name }}</h3>
                        <span v-if="!item.is_active" class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 shrink-0">Inativo</span>
                    </div>
                    <p v-if="item.host" class="text-sm text-[var(--muted)] mb-1">Apresentado por {{ item.host }}</p>
                    <p class="text-sm text-[var(--muted)]">{{ item.episodes_count }} episódios publicados</p>
                    <div class="mt-4 flex items-center gap-3 text-sm">
                        <Link :href="`/admin/podcasts/${item.id}/episodes`" class="font-semibold text-[var(--accent)] hover:underline">Episódios</Link>
                        <Link :href="`/admin/podcasts/${item.id}/edit`" class="font-semibold text-[var(--accent)] hover:underline">Editar</Link>
                        <form :action="`/admin/podcasts/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este podcast?')) e.preventDefault() }">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                            <button type="submit" class="font-semibold text-red-500 hover:underline">Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
            <div v-if="!podcasts.data.length" class="col-span-full rounded-2xl border border-[var(--border)] bg-[var(--background)] p-10 text-center text-[var(--muted)]">
                Nenhum podcast encontrado.
            </div>
        </div>

        <Pagination :links="podcasts.links" />
    </AdminLayout>
</template>