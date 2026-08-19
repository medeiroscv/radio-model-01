<script setup lang="ts">
import AppLayout from '../Layouts/AppLayout.vue'

interface Entry {
    id: number
    position: number
    plays: number
    song: {
        id: number
        title: string
        cover?: string | null
        artist?: { name: string } | null
    }
}

interface ChartItem {
    id: number
    name: string
    period: string
    starts_at?: string | null
    ends_at?: string | null
    entries: Entry[]
}

defineProps<{
    charts: ChartItem[]
}>()

const periodLabel: Record<string, string> = { daily: 'Diário', weekly: 'Semanal', monthly: 'Mensal' }

function formatDate(value?: string | null): string {
    if (!value) return ''
    return new Date(value).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <div class="text-center mb-12">
                <h1 class="text-3xl lg:text-4xl font-bold mb-2">Rankings</h1>
                <p class="text-[var(--muted)] max-w-xl mx-auto">As músicas mais tocadas na nossa rádio.</p>
            </div>

            <div v-if="!charts.length" class="text-center py-16 text-[var(--muted)]">
                Nenhum ranking disponível ainda.
            </div>

            <div v-for="chart in charts" :key="chart.id" class="mb-12">
                <div class="flex flex-wrap items-baseline justify-between gap-2 mb-6">
                    <h2 class="section-title">{{ chart.name }}</h2>
                    <span class="text-sm text-[var(--muted)]">
                        {{ periodLabel[chart.period] || chart.period }}
                        <template v-if="chart.starts_at || chart.ends_at">
                            · {{ formatDate(chart.starts_at) }} — {{ formatDate(chart.ends_at) }}
                        </template>
                    </span>
                </div>

                <div v-if="!chart.entries.length" class="text-center py-8 text-[var(--muted)] border border-dashed border-[var(--border)] rounded-2xl">
                    Sem posições neste período.
                </div>

                <ol class="space-y-3">
                    <li v-for="entry in chart.entries" :key="entry.id" class="flex items-center gap-4 p-4 rounded-2xl border border-[var(--border)] bg-[var(--background)]">
                        <span class="text-3xl font-black w-12 text-center" :class="entry.position <= 3 ? 'text-[var(--accent)]' : 'text-[var(--muted)]'">{{ entry.position }}</span>
                        <div class="w-14 h-14 rounded-lg bg-[var(--surface)] flex items-center justify-center shrink-0 overflow-hidden">
                            <img v-if="entry.song?.cover" :src="entry.song.cover" :alt="entry.song.title" class="w-full h-full object-cover" />
                            <span v-else>🎵</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate">{{ entry.song?.title }}</p>
                            <p class="text-xs text-[var(--muted)]">{{ entry.song?.artist?.name }}</p>
                        </div>
                        <span class="text-xs text-[var(--muted)] shrink-0">{{ entry.plays }} toca{{ entry.plays === 1 ? 'da' : 'das' }}</span>
                    </li>
                </ol>
            </div>
        </section>
    </AppLayout>
</template>