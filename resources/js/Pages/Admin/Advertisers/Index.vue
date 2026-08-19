<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface AdvertiserItem {
    id: number
    name: string
    email?: string | null
    phone?: string | null
    banners_count: number
}

defineProps<{
    advertisers: {
        data: AdvertiserItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Anunciantes</h1>
                <p class="text-sm text-[var(--muted)]">Empresas anunciantes da rádio</p>
            </div>
            <Link href="/admin/advertisers/create" class="btn-accent">Novo anunciante</Link>
        </div>

        <form method="get" action="/admin/advertisers" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar anunciante..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Nome</th>
                        <th class="px-4 py-3 font-semibold">E-mail</th>
                        <th class="px-4 py-3 font-semibold">Telefone</th>
                        <th class="px-4 py-3 font-semibold">Banners</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in advertisers.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.name }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.email || '—' }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.phone || '—' }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.banners_count }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/advertisers/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/advertisers/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este anunciante?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!advertisers.data.length">
                        <td colspan="5" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum anunciante encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="advertisers.links" />
    </AdminLayout>
</template>