<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface PresenterItem {
    id: number
    name: string
    slug: string
    photo?: string | null
    is_active: boolean
}

defineProps<{
    presenters: {
        data: PresenterItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Apresentadores</h1>
                <p class="text-sm text-[var(--muted)]">Locutores e apresentadores</p>
            </div>
            <Link href="/admin/presenters/create" class="btn-accent">Novo apresentador</Link>
        </div>

        <form method="get" action="/admin/presenters" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar apresentador..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Apresentador</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in presenters.data" :key="item.id">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[var(--surface)] flex items-center justify-center overflow-hidden">
                                    <img v-if="item.photo" :src="item.photo ?? ''" :alt="item.name" class="w-full h-full object-cover" />
                                    <span v-else class="text-xs">🎙️</span>
                                </div>
                                <span class="font-medium">{{ item.name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600']">
                                {{ item.is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/presenters/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/presenters/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este apresentador?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!presenters.data.length">
                        <td colspan="3" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum apresentador encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="presenters.links" />
    </AdminLayout>
</template>