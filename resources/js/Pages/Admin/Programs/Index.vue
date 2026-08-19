<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface ProgramItem {
    id: number
    name: string
    slug: string
    category?: string
    is_active: boolean
    presenter?: { id: number; name: string } | null
}

defineProps<{
    programs: {
        data: ProgramItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
}>()
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Programas</h1>
                <p class="text-sm text-[var(--muted)]">Programas da sua programação</p>
            </div>
            <Link href="/admin/programs/create" class="btn-accent">Novo programa</Link>
        </div>

        <form method="get" action="/admin/programs" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar programa..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Nome</th>
                        <th class="px-4 py-3 font-semibold">Categoria</th>
                        <th class="px-4 py-3 font-semibold">Apresentador</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in programs.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.name }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.category || '—' }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.presenter?.name || '—' }}</td>
                        <td class="px-4 py-3">
                            <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600']">
                                {{ item.is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/programs/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/programs/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este programa?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!programs.data.length">
                        <td colspan="5" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum programa encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="programs.links" />
    </AdminLayout>
</template>