<script setup lang="ts">
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface ContactItem {
    id: number
    name: string
    email: string
    phone?: string
    department?: string
    message: string
    created_at?: string
}

defineProps<{
    contacts: {
        data: ContactItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
}>()

function formatDate(value?: string): string {
    if (!value) return ''
    return new Date(value).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold">Mensagens de Contato</h1>
            <p class="text-sm text-[var(--muted)]">Mensagens enviadas pelo formulário do site</p>
        </div>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Nome</th>
                        <th class="px-4 py-3 font-semibold">E-mail</th>
                        <th class="px-4 py-3 font-semibold hidden md:table-cell">Departamento</th>
                        <th class="px-4 py-3 font-semibold">Mensagem</th>
                        <th class="px-4 py-3 font-semibold hidden lg:table-cell">Data</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in contacts.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.name }}</td>
                        <td class="px-4 py-3">{{ item.email }}</td>
                        <td class="px-4 py-3 hidden md:table-cell text-[var(--muted)]">{{ item.department || '—' }}</td>
                        <td class="px-4 py-3 text-[var(--muted)] max-w-xs truncate">{{ item.message }}</td>
                        <td class="px-4 py-3 hidden lg:table-cell text-[var(--muted)]">{{ formatDate(item.created_at) }}</td>
                        <td class="px-4 py-3 text-right">
                            <form :action="`/admin/contacts/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir esta mensagem?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!contacts.data.length">
                        <td colspan="6" class="px-4 py-10 text-center text-[var(--muted)]">Nenhuma mensagem recebida.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="contacts.links" />
    </AdminLayout>
</template>