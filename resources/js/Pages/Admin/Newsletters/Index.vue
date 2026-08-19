<script setup lang="ts">
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface NewsletterItem {
    id: number
    name?: string
    email: string
    is_active: boolean
    created_at?: string
}

defineProps<{
    newsletters: {
        data: NewsletterItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
}>()

function formatDate(value?: string): string {
    if (!value) return ''
    return new Date(value).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold">Inscritos na Newsletter</h1>
            <p class="text-sm text-[var(--muted)]">Pessoas cadastradas para receber novidades</p>
        </div>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Nome</th>
                        <th class="px-4 py-3 font-semibold">E-mail</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold hidden lg:table-cell">Data</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in newsletters.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.name || '—' }}</td>
                        <td class="px-4 py-3">{{ item.email }}</td>
                        <td class="px-4 py-3">
                            <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600']">
                                {{ item.is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-[var(--muted)]">{{ formatDate(item.created_at) }}</td>
                        <td class="px-4 py-3 text-right">
                            <form :action="`/admin/newsletters/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir esta inscrição?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!newsletters.data.length">
                        <td colspan="5" class="px-4 py-10 text-center text-[var(--muted)]">Nenhuma inscrição ainda.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="newsletters.links" />
    </AdminLayout>
</template>