<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface UserItem {
    id: number
    name: string
    email: string
    is_active: boolean
    created_at?: string
    roles?: Array<{ id: number; name: string }>
}

interface RoleOption {
    id: number
    name: string
}

defineProps<{
    users: {
        data: UserItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    filters: { search?: string }
    roles: RoleOption[]
}>()

function roleLabel(roles?: Array<{ name: string }>): string {
    if (!roles?.length) return 'Sem função'
    return roles.map((r) => r.name.replace('-', ' ')).join(', ')
}
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Usuários</h1>
                <p class="text-sm text-[var(--muted)]">Equipe e permissões de acesso</p>
            </div>
            <Link href="/admin/users/create" class="btn-accent">Novo usuário</Link>
        </div>

        <form method="get" action="/admin/users" class="flex gap-3 mb-6 max-w-md">
            <input type="search" name="search" :value="filters.search ?? ''" placeholder="Buscar por nome ou e-mail..." class="input-app flex-1" />
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Usuário</th>
                        <th class="px-4 py-3 font-semibold">Funções</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in users.data" :key="item.id">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ item.name }}</p>
                            <p class="text-xs text-[var(--muted)]">{{ item.email }}</p>
                        </td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ roleLabel(item.roles) }}</td>
                        <td class="px-4 py-3">
                            <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600']">
                                {{ item.is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/users/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/users/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este usuário?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!users.data.length">
                        <td colspan="4" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum usuário encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="users.links" />
    </AdminLayout>
</template>