<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const showingSidebar = ref(false)
const page = usePage()
const user = page.props.auth.user!

const isActive = (name: string): boolean => page.url.startsWith(name)

const groups = [
    {
        label: 'Painel',
        items: [
            { name: 'admin.dashboard', href: '/admin', label: 'Dashboard', icon: '📊', active: isActive('/admin') && !isActive('/admin/news') && !isActive('/admin/media') && !isActive('/admin/promotions') && !isActive('/admin/programs') && !isActive('/admin/presenters') && !isActive('/admin/schedules') && !isActive('/admin/songs') && !isActive('/admin/artists') && !isActive('/admin/podcasts') && !isActive('/admin/charts') && !isActive('/admin/videos') && !isActive('/admin/banners') && !isActive('/admin/advertisers') && !isActive('/admin/users') && !isActive('/admin/contacts') && !isActive('/admin/newsletters') && !isActive('/admin/station') && !isActive('/admin/stream') },
        ],
    },
    {
        label: 'Conteúdo',
        items: [
            { name: 'admin.news.index', href: '/admin/news', label: 'Notícias', icon: '📰', active: isActive('/admin/news') },
            { name: 'admin.media.index', href: '/admin/media', label: 'Mídia', icon: '🖼️', active: isActive('/admin/media') },
            { name: 'admin.news-categories.index', href: '/admin/news-categories', label: 'Categorias', icon: '🗂️', active: isActive('/admin/news-categories') },
            { name: 'admin.promotions.index', href: '/admin/promotions', label: 'Promoções', icon: '🎁', active: isActive('/admin/promotions') },
        ],
    },
    {
        label: 'Rádio',
        items: [
            { name: 'admin.stream.edit', href: '/admin/stream', label: 'Streaming', icon: '📡', active: isActive('/admin/stream') },
            { name: 'admin.programs.index', href: '/admin/programs', label: 'Programas', icon: '🎙️', active: isActive('/admin/programs') },
            { name: 'admin.presenters.index', href: '/admin/presenters', label: 'Apresentadores', icon: '👤', active: isActive('/admin/presenters') },
            { name: 'admin.schedules.index', href: '/admin/schedules', label: 'Grade de Programação', icon: '🗓️', active: isActive('/admin/schedules') },
            { name: 'admin.songs.index', href: '/admin/songs', label: 'Músicas', icon: '🎵', active: isActive('/admin/songs') },
            { name: 'admin.artists.index', href: '/admin/artists', label: 'Artistas', icon: '🎤', active: isActive('/admin/artists') },
            { name: 'admin.podcasts.index', href: '/admin/podcasts', label: 'Podcasts', icon: '🎙️', active: isActive('/admin/podcasts') },
            { name: 'admin.charts.index', href: '/admin/charts', label: 'Rankings', icon: '🏆', active: isActive('/admin/charts') },
            { name: 'admin.videos.index', href: '/admin/videos', label: 'Vídeos', icon: '🎬', active: isActive('/admin/videos') },
        ],
    },
    {
        label: 'Publicidade',
        items: [
            { name: 'admin.banners.index', href: '/admin/banners', label: 'Banners', icon: '📣', active: isActive('/admin/banners') },
            { name: 'admin.advertisers.index', href: '/admin/advertisers', label: 'Anunciantes', icon: '🏢', active: isActive('/admin/advertisers') },
        ],
    },
    {
        label: 'Comunicação',
        items: [
            { name: 'admin.contacts.index', href: '/admin/contacts', label: 'Contatos', icon: '✉️', active: isActive('/admin/contacts') },
            { name: 'admin.newsletters.index', href: '/admin/newsletters', label: 'Newsletter', icon: '📧', active: isActive('/admin/newsletters') },
        ],
    },
    {
        label: 'Aparência',
        items: [
            { name: 'admin.station.edit', href: '/admin/station', label: 'Identidade da Rádio', icon: '🎨', active: isActive('/admin/station') },
        ],
    },
    {
        label: 'Sistema',
        items: [
            { name: 'admin.users.index', href: '/admin/users', label: 'Usuários', icon: '🔐', active: isActive('/admin/users') },
            { name: 'admin.update.index', href: '/admin/update', label: 'Atualizações', icon: '🔄', active: isActive('/admin/update') },
            { name: 'profile.edit', href: '/profile', label: 'Meu Perfil', icon: '👤', active: isActive('/profile') },
        ],
    },
]

const groupsWithPermission = computed(() =>
    groups.filter((group) =>
        group.items.some((item) => {
            if (!page.props.auth.permissions?.length) return true
            return true
        }),
    ),
)
</script>

<template>
    <div class="min-h-screen bg-[var(--surface)]">
        <!-- Barra superior -->
        <header class="sticky top-0 z-40 flex h-16 items-center gap-4 border-b border-[var(--border)] bg-[var(--background)] px-4 lg:px-6">
            <button type="button" class="lg:hidden text-xl" @click="showingSidebar = !showingSidebar">☰</button>
            <Link href="/" class="font-bold text-lg flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-[var(--primary)] text-white flex items-center justify-center text-sm">📻</span>
                {{ $page.props.station?.name || 'RadioCMS' }}
            </Link>
            <span class="ml-2 hidden sm:inline-flex rounded-full bg-[var(--accent)]/10 text-[var(--accent)] text-xs font-semibold px-3 py-1">Painel Administrativo</span>

            <div class="ml-auto flex items-center gap-3">
                <Link href="/" class="btn-outline !px-4 !py-2 text-sm">Ver site</Link>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-[var(--primary)] text-white flex items-center justify-center text-sm font-bold uppercase">
                        {{ user.name?.charAt(0) || 'A' }}
                    </div>
                    <div class="hidden md:block">
                        <p class="text-sm font-semibold leading-none">{{ user.name }}</p>
                        <p class="text-xs text-[var(--muted)] mt-0.5">{{ user.roles?.map((r) => r.name.replace('-', ' ')).join(', ') || 'Usuário' }}</p>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sidebar -->
            <aside :class="['fixed inset-y-0 left-0 top-16 z-30 w-64 border-r border-[var(--border)] bg-[var(--background)] p-4 transition-transform lg:sticky lg:translate-x-0 lg:h-[calc(100vh-4rem)] lg:overflow-y-auto', showingSidebar ? 'translate-x-0' : '-translate-x-full']">
                <nav class="space-y-6">
                    <div v-for="group in groupsWithPermission" :key="group.label" v-show="!group.items.some((i) => i.href === '/profile')">
                        <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-[var(--muted)] mb-2">{{ group.label }}</p>
                        <div class="space-y-1">
                            <Link
                                v-for="item in group.items"
                                :key="item.name"
                                :href="item.href"
                                :class="['flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors', item.active ? 'bg-[var(--primary)] text-white' : 'text-[var(--muted)] hover:bg-[var(--surface)] hover:text-[var(--text)]']"
                            >
                                <span class="text-base">{{ item.icon }}</span>
                                {{ item.label }}
                            </Link>
                        </div>
                    </div>
                </nav>
            </aside>

            <!-- Overlay mobile -->
            <div v-if="showingSidebar" class="fixed inset-0 z-20 bg-black/40 lg:hidden" @click="showingSidebar = false"></div>

            <!-- Conteúdo -->
            <main class="flex-1 min-w-0 p-4 lg:p-8">
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $page.props.flash.error }}
                </div>
                <slot />
            </main>
        </div>
    </div>
</template>