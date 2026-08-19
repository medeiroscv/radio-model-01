<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

interface Banner {
    id: number
    title: string
    image_desktop?: string | null
    image_mobile?: string | null
    url?: string | null
    position: string
}

const props = defineProps<{
    position: string
    className?: string
}>()

const page = usePage()
const banners = computed<Banner[]>(() => (page.props.activeBanners as Banner[]) ?? [])
const list = computed(() => banners.value.filter((b) => b.position === props.position))
</script>

<template>
    <div v-if="list.length" class="banner-slot" :class="className">
        <a
            v-for="banner in list"
            :key="banner.id"
            :href="banner.url || '#'"
            target="_blank"
            rel="noopener sponsored"
            class="block"
        >
            <img
                v-if="banner.image_desktop"
                :src="banner.image_desktop"
                :alt="banner.title"
                class="w-full h-auto rounded-2xl border border-[var(--border)]"
            />
            <div v-else class="rounded-2xl border border-dashed border-[var(--border)] py-8 text-center text-sm text-[var(--muted)]">
                {{ banner.title }}
            </div>
        </a>
    </div>
</template>