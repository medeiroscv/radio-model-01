<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

interface ScheduleSlot {
    id: number
    start_time: string
    end_time: string
    days_of_week: number[]
    presenter?: { id: number; name: string } | null
}

defineProps<{
    program: {
        id: number
        name: string
        description?: string | null
        image?: string | null
        category?: string | null
        presenter?: { id: number; name: string; photo?: string | null; bio?: string | null } | null
        schedules: ScheduleSlot[]
    }
}>()

const dayNames = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']

function dayList(days: number[]): string {
    if (!days?.length) return '—'
    return days.map((d) => dayNames[d] ?? d).join(', ')
}
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <nav class="text-xs text-[var(--muted)] mb-6">
                <Link href="/programacao" class="hover:text-[var(--accent)]">Programação</Link>
                <span class="mx-2">›</span>
                <span>{{ program.name }}</span>
            </nav>

            <header class="flex flex-wrap gap-8 items-center mb-10">
                <div class="w-40 h-40 rounded-2xl bg-[var(--surface)] flex items-center justify-center overflow-hidden shrink-0 border border-[var(--border)]">
                    <img v-if="program.image" :src="program.image" :alt="program.name" class="w-full h-full object-cover" />
                    <span v-else class="text-7xl">🎙️</span>
                </div>
                <div class="flex-1 min-w-[260px]">
                    <span v-if="program.category" class="label-category mb-2">{{ program.category }}</span>
                    <h1 class="text-3xl lg:text-4xl font-bold mb-2">{{ program.name }}</h1>
                    <p class="text-[var(--muted)] max-w-2xl">{{ program.description }}</p>
                </div>
            </header>

            <div v-if="program.schedules?.length" class="mb-10">
                <h2 class="section-title mb-6">Horários</h2>
                <div class="rounded-2xl border border-[var(--border)] overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-[var(--surface)]">
                            <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                                <th class="px-4 py-3 font-semibold">Dias</th>
                                <th class="px-4 py-3 font-semibold">Horário</th>
                                <th class="px-4 py-3 font-semibold">Apresentador</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border)]">
                            <tr v-for="slot in program.schedules" :key="slot.id">
                                <td class="px-4 py-3 font-medium">{{ dayList(slot.days_of_week) }}</td>
                                <td class="px-4 py-3">{{ slot.start_time }} — {{ slot.end_time }}</td>
                                <td class="px-4 py-3">{{ slot.presenter?.name || program.presenter?.name || '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="program.presenter" class="flex gap-5 p-5 rounded-2xl border border-[var(--border)] max-w-2xl">
                <div class="w-20 h-20 rounded-full bg-[var(--surface)] flex items-center justify-center shrink-0 overflow-hidden">
                    <img v-if="program.presenter.photo" :src="program.presenter.photo" :alt="program.presenter.name" class="w-full h-full object-cover" />
                    <span v-else class="text-3xl">👤</span>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-[var(--muted)] mb-1">Apresentação</p>
                    <p class="font-bold">{{ program.presenter.name }}</p>
                    <p v-if="program.presenter.bio" class="text-sm text-[var(--muted)] mt-1">{{ program.presenter.bio }}</p>
                </div>
            </div>
        </section>
    </AppLayout>
</template>