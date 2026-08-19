<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface BannerItem {
    id: number
    title: string
    image_desktop?: string
    is_active: boolean
    start_date?: string
    end_date?: string
    advertiser?: { id: number; name: string } | null
}

defineProps<{
    banners: {
        data: BannerItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Banners</h1>
                <p class="text-sm text-[var(--muted)]">Publicidade exibida no site</p>
            </div>
            <Link href="/admin/banners/create" class="btn-accent">Novo banner</Link>
        </div>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Banner</th>
                        <th class="px-4 py-3 font-semibold">Anunciante</th>
                        <th class="px-4 py-3 font-semibold">Período</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in banners.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.title }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.advertiser?.name || '—' }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.start_date || '—' }} até {{ item.end_date || '—' }}</td>
                        <td class="px-4 py-3">
                            <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600']">
                                {{ item.is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/banners/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/banners/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este banner?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!banners.data.length">
                        <td colspan="5" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum banner cadastrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="banners.links" />
    </AdminLayout>
</template>