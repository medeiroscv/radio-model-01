<script setup lang="ts">
defineProps<{
    links: Array<{ url: string | null; label: string; active: boolean }>
}>()
</script>

<template>
    <nav v-if="links.length > 3" class="mt-6 flex justify-center gap-2">
        <a v-for="(link, i) in links" :key="i" :href="link.url ?? ''" v-html="link.label"
            :class="['rounded-lg px-4 py-2 text-sm', link.active ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface)] border border-[var(--border)] text-[var(--muted)]', !link.url && 'opacity-50 pointer-events-none']" />
    </nav>
</template>