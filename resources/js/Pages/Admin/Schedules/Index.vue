<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'
import Pagination from '../../../Components/Admin/Pagination.vue'

interface ScheduleItem {
    id: number
    start_time: string
    end_time: string
    days_of_week: number[]
    is_active: boolean
    program?: { id: number; name: string } | null
    presenter?: { id: number; name: string } | null
}

defineProps<{
    schedules: {
        data: ScheduleItem[]
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
}>()

const dayNames: Record<number, string> = { 1: 'Seg', 2: 'Ter', 3: 'Qua', 4: 'Qui', 5: 'Sex', 6: 'Sáb', 7: 'Dom' }

function daysLabel(days: number[]): string {
    if (!days.length) return '—'
    return days.map((d) => dayNames[d]).join(', ')
}
</script>

<template>
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold">Grade de Programação</h1>
                <p class="text-sm text-[var(--muted)]">Horários dos programas por dia da semana</p>
            </div>
            <Link href="/admin/schedules/create" class="btn-accent">Novo horário</Link>
        </div>

        <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface)]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                        <th class="px-4 py-3 font-semibold">Horário</th>
                        <th class="px-4 py-3 font-semibold">Programa</th>
                        <th class="px-4 py-3 font-semibold">Apresentador</th>
                        <th class="px-4 py-3 font-semibold">Dias</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    <tr v-for="item in schedules.data" :key="item.id">
                        <td class="px-4 py-3 font-medium">{{ item.start_time }} - {{ item.end_time }}</td>
                        <td class="px-4 py-3">{{ item.program?.name || '—' }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ item.presenter?.name || '—' }}</td>
                        <td class="px-4 py-3 text-[var(--muted)]">{{ daysLabel(item.days_of_week) }}</td>
                        <td class="px-4 py-3">
                            <span :class="['text-xs font-semibold px-2 py-0.5 rounded-full', item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600']">
                                {{ item.is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <Link :href="`/admin/schedules/${item.id}/edit`" class="text-sm font-semibold text-[var(--accent)] hover:underline mr-3">Editar</Link>
                            <form :action="`/admin/schedules/${item.id}`" method="post" class="inline" @submit="(e) => { if (!confirm('Excluir este horário?')) e.preventDefault() }">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr v-if="!schedules.data.length">
                        <td colspan="6" class="px-4 py-10 text-center text-[var(--muted)]">Nenhum horário cadastrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="schedules.links" />
    </AdminLayout>
</template>