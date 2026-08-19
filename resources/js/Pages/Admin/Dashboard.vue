<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '../../Layouts/AdminLayout.vue'

interface Stats {
    news: number
    promotions: number
    songs: number
    videos: number
    schedules: number
    banners: number
    contacts: number
    newsletters: number
}

defineProps<{
    stats: Stats
    latestNews: Array<{ id: number; title: string; is_published: boolean; created_at?: string }>
    recentContacts: Array<{ id: number; name: string; email: string; department?: string; created_at?: string }>
    recentTracks: Array<{ id: number; artist?: string; title?: string; played_at?: string }>
}>()

const cards = (s: Stats) => [
    { label: 'Notícias', value: s.news, href: '/admin/news', icon: '📰', color: 'from-blue-500 to-indigo-600' },
    { label: 'Promoções', value: s.promotions, href: '/admin/promotions', icon: '🎁', color: 'from-pink-500 to-rose-600' },
    { label: 'Músicas', value: s.songs, href: '/admin/songs', icon: '🎵', color: 'from-purple-500 to-fuchsia-600' },
    { label: 'Vídeos', value: s.videos, href: '/admin/videos', icon: '🎬', color: 'from-orange-500 to-amber-600' },
    { label: 'Horários', value: s.schedules, href: '/admin/schedules', icon: '🗓️', color: 'from-teal-500 to-emerald-600' },
    { label: 'Banners', value: s.banners, href: '/admin/banners', icon: '🖼️', color: 'from-cyan-500 to-sky-600' },
    { label: 'Contatos', value: s.contacts, href: '/admin/contacts', icon: '✉️', color: 'from-slate-500 to-gray-600' },
    { label: 'Newsletter', value: s.newsletters, href: '/admin/newsletters', icon: '📧', color: 'from-lime-500 to-green-600' },
]
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold">Dashboard</h1>
            <p class="text-[var(--muted)] text-sm mt-1">Visão geral do conteúdo da sua rádio</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <Link
                v-for="card in cards(stats)"
                :key="card.label"
                :href="card.href"
                class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-5 hover:shadow-lg transition-shadow group"
            >
                <div :class="['w-10 h-10 rounded-xl bg-gradient-to-br text-white flex items-center justify-center text-lg mb-3', card.color]">{{ card.icon }}</div>
                <p class="text-2xl font-bold">{{ card.value }}</p>
                <p class="text-xs text-[var(--muted)] group-hover:text-[var(--text)]">{{ card.label }}</p>
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold">Últimas notícias</h2>
                    <Link href="/admin/news" class="text-xs font-semibold text-[var(--accent)]">Ver todas</Link>
                </div>
                <div v-if="latestNews.length" class="space-y-3">
                    <div v-for="item in latestNews" :key="item.id" class="flex items-center justify-between gap-3 py-2 border-b border-[var(--border)] last:border-0">
                        <p class="text-sm font-medium truncate">{{ item.title }}</p>
                        <span :class="['shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full', item.is_published ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700']">
                            {{ item.is_published ? 'Publicado' : 'Rascunho' }}
                        </span>
                    </div>
                </div>
                <p v-else class="text-sm text-[var(--muted)]">Nenhuma notícia ainda.</p>
            </div>

            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold">Últimas mensagens</h2>
                    <Link href="/admin/contacts" class="text-xs font-semibold text-[var(--accent)]">Ver todas</Link>
                </div>
                <div v-if="recentContacts.length" class="space-y-3">
                    <div v-for="item in recentContacts" :key="item.id" class="py-2 border-b border-[var(--border)] last:border-0">
                        <p class="text-sm font-medium">{{ item.name }} <span class="text-[var(--muted)] font-normal">· {{ item.department || 'Geral' }}</span></p>
                        <p class="text-xs text-[var(--muted)] truncate">{{ item.email }}</p>
                    </div>
                </div>
                <p v-else class="text-sm text-[var(--muted)]">Nenhuma mensagem ainda.</p>
            </div>
        </div>
    </AdminLayout>
</template>