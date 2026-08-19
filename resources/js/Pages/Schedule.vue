<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

interface Slot {
    id: number
    start_time: string
    end_time: string
    program?: { id: number; name: string; slug: string } | null
    presenter?: { id: number; name: string } | null
}

interface Program {
    id: number
    name: string
    slug: string
    description?: string
    category?: string
    presenter?: { name: string } | null
}

interface Presenter {
    id: number
    name: string
    slug: string
    photo?: string | null
    biography?: string
}

defineProps<{
    week: Record<number, Slot[]>
    programs: Program[]
    presenters: Presenter[]
}>()

const dayNames = ['', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo']

function timeLabel(value: string): string {
    return value.slice(0, 5)
}
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <div class="text-center mb-12">
                <h1 class="text-3xl lg:text-4xl font-bold mb-2">Programação</h1>
                <p class="text-[var(--muted)]">Toda a nossa grade de programas</p>
                <div class="mx-auto mt-4 h-1 w-10 rounded-full bg-[var(--accent)]"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
                <div v-for="day in 7" :key="day" class="border border-[var(--border)] rounded-xl p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-[var(--muted)] mb-3">{{ dayNames[day] }}</p>
                    <div v-if="week[day]?.length" class="space-y-2">
                        <div v-for="slot in week[day]" :key="slot.id" class="text-sm">
                            <p class="font-semibold">{{ timeLabel(slot.start_time) }} - {{ timeLabel(slot.end_time) }}</p>
                            <p>{{ slot.program?.name }}</p>
                            <p class="text-xs text-[var(--muted)]">{{ slot.presenter?.name }}</p>
                        </div>
                    </div>
                    <p v-else class="text-xs text-[var(--muted)]">Sem programação</p>
                </div>
            </div>
        </section>

        <section v-if="programs.length" class="container-app section-spacing">
            <h2 class="section-title mb-8">Programas</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="program in programs" :key="program.id" class="rounded-2xl border border-[var(--border)] p-6">
                    <span class="label-category mb-2">{{ program.category || 'Programa' }}</span>
                    <h3 class="text-xl font-bold mb-1">
                        <Link :href="`/programas/${program.slug}`" class="hover:text-[var(--accent)] transition-colors">{{ program.name }}</Link>
                    </h3>
                    <p v-if="program.presenter?.name" class="text-sm text-[var(--muted)] mb-3">Com {{ program.presenter.name }}</p>
                    <p class="text-sm text-[var(--muted)]">{{ program.description }}</p>
                </div>
            </div>
        </section>

        <section v-if="presenters.length" class="container-app section-spacing">
            <h2 class="section-title mb-8">Nossa equipe</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div v-for="presenter in presenters" :key="presenter.id" class="flex flex-col items-center gap-3 rounded-2xl border border-[var(--border)] p-6">
                    <div class="w-24 h-24 rounded-full bg-[var(--surface)] flex items-center justify-center overflow-hidden text-3xl">
                        <img v-if="presenter.photo" :src="presenter.photo" :alt="presenter.name" class="w-full h-full object-cover" />
                        <span v-else>🎙️</span>
                    </div>
                    <div class="text-center">
                        <h3 class="font-semibold">{{ presenter.name }}</h3>
                        <p class="text-xs text-[var(--muted)]">{{ presenter.biography }}</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>