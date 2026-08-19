<script setup lang="ts">
import AppLayout from '../Layouts/AppLayout.vue'

interface Presenter {
    id: number
    name: string
    slug: string
    biography?: string
    photo?: string | null
}

defineProps<{
    presenters: Presenter[]
}>()
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <div class="text-center mb-12">
                <h1 class="text-3xl lg:text-4xl font-bold mb-2">A Rádio</h1>
                <p class="text-[var(--muted)]">Conheça nossa história e nossa equipe</p>
                <div class="mx-auto mt-4 h-1 w-10 rounded-full bg-[var(--accent)]"></div>
            </div>

            <div class="max-w-3xl mx-auto space-y-8 text-center">
                <div>
                    <h2 class="text-2xl font-bold mb-3">{{ $page.props.station?.name || 'Nossa rádio' }}</h2>
                    <p class="text-[var(--muted)] leading-relaxed">
                        {{ $page.props.station?.slogan || 'Levando informação e entretenimento para você.' }}
                    </p>
                </div>
                <div v-if="$page.props.station?.frequency" class="inline-flex items-center gap-2 rounded-full bg-[var(--surface)] border border-[var(--border)] px-5 py-2">
                    <span class="text-sm text-[var(--muted)]">Frequência</span>
                    <span class="font-bold">{{ $page.props.station.frequency }}</span>
                </div>
            </div>
        </section>

        <section v-if="presenters.length" class="container-app section-spacing">
            <h2 class="section-title mb-8 text-center">Nossa equipe</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div v-for="presenter in presenters" :key="presenter.id" class="flex flex-col items-center gap-3 rounded-2xl border border-[var(--border)] p-6">
                    <div class="w-24 h-24 rounded-full bg-[var(--surface)] flex items-center justify-center overflow-hidden text-3xl">
                        <img v-if="presenter.photo" :src="presenter.photo" :alt="presenter.name" class="w-full h-full object-cover" />
                        <span v-else>🎙️</span>
                    </div>
                    <div class="text-center">
                        <h3 class="font-semibold">{{ presenter.name }}</h3>
                        <p class="text-xs text-[var(--muted)] mt-1">{{ presenter.biography }}</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>