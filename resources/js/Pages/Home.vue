<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'
import BannerSlot from '../Components/BannerSlot.vue'

interface NewsItem {
    id: number
    title: string
    slug: string
    subtitle?: string
    summary?: string
    featured_image?: string | null
    published_at?: string
    is_featured?: boolean
    views?: number
    category?: { id: number; name: string; slug: string; color?: string } | null
}

interface PromotionItem {
    id: number
    title: string
    slug: string
    description?: string
    image?: string | null
    is_featured?: boolean
    call_to_action?: string
}

interface TopSong {
    position: number
    song?: string
    artist?: string
    cover?: string
}

interface ReleaseItem {
    id: number
    title: string
    slug: string
    cover?: string | null
    released_at?: string
    artist?: { name: string } | null
}

interface VideoItem {
    id: number
    title: string
    slug: string
    thumbnail?: string | null
    platform?: string
    category?: { name: string } | null
}

interface PodcastItem {
    id: number
    name: string
    slug: string
    cover?: string | null
    description?: string
    episodes_count?: number
}

interface TrackItem {
    id: number
    artist?: string | null
    title?: string | null
    cover?: string | null
    played_at?: string
}

defineProps<{
    featuredNews: NewsItem[]
    latestNews: NewsItem[]
    promotions: PromotionItem[]
    topSongs: TopSong[]
    releases: ReleaseItem[]
    videos: VideoItem[]
    podcasts: PodcastItem[]
    currentSchedule: {
        program?: string
        program_slug?: string
        presenter?: string
        start_time?: string
        end_time?: string
    } | null
    recentTracks: TrackItem[]
}>()

function formatDate(value?: string): string {
    if (!value) return ''
    return new Date(value).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' })
}

function timeLabel(value?: string): string {
    if (!value) return ''
    const [h, m] = value.split(':')
    return `${h}:${m}`
}

const hero = (list: NewsItem[]): NewsItem | null => list.find((n) => n.is_featured) ?? list[0] ?? null
const secondary = (list: NewsItem[]): NewsItem[] => (list.length > 1 ? list.slice(0, 4) : [])
</script>

<template>
    <AppLayout>
        <!-- HERO EDITORIAL -->
        <section class="bg-[var(--primary)] text-white">
            <div class="container-app py-10 lg:py-16">
                <div class="grid grid-cols-1 lg:grid-cols-[45%_55%] gap-8 lg:gap-12 items-center">
                    <div class="space-y-5">
                        <span class="label-category">Destaque</span>
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight tracking-tight">
                            {{ $page.props.station?.slogan || 'Sua rádio, sua história, sempre no ar' }}
                        </h1>
                        <p class="text-white/70 text-base lg:text-lg leading-relaxed max-w-md">
                            Acompanhe nossa programação, participe das promoções e fique por dentro de tudo que acontece no mundo do entretenimento e da música.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <Link href="/noticias" class="btn-accent">Leia mais</Link>
                            <Link href="/programacao" class="btn-outline !text-white !border-white/40 hover:!border-white">Ver programação</Link>
                        </div>
                    </div>
                    <div class="relative aspect-[4/3] lg:aspect-auto lg:h-[420px] rounded-2xl overflow-hidden bg-gradient-to-br from-white/10 to-white/5 flex items-center justify-center">
                        <img v-if="hero(featuredNews)?.featured_image" :src="hero(featuredNews)!.featured_image ?? ''" :alt="hero(featuredNews)!.title" class="w-full h-full object-cover" />
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- BANNER LEADERBOARD -->
        <BannerSlot position="home_leaderboard" className="container-app pt-8" />

        <!-- PROMOÇÕES -->
        <section v-if="promotions.length" class="container-app section-spacing">
            <div class="text-center mb-10">
                <h2 class="section-title">Promoções que você não pode perder!</h2>
                <div class="mx-auto mt-3 h-1 w-10 rounded-full bg-[var(--accent)]"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link v-for="promo in promotions.slice(0, 3)" :key="promo.id" :href="`/promocoes/${promo.slug}`" class="group rounded-2xl overflow-hidden border border-[var(--border)] transition-shadow hover:shadow-lg">
                    <div class="aspect-[4/3] bg-[var(--surface)] flex items-center justify-center overflow-hidden">
                        <img v-if="promo.image" :src="promo.image" :alt="promo.title" class="w-full h-full object-cover" />
                        <span v-else class="text-6xl">🎁</span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold text-lg mb-1">{{ promo.title }}</h3>
                        <p class="text-sm text-[var(--muted)] mb-3 line-clamp-2">{{ promo.description }}</p>
                        <span class="text-sm font-semibold text-[var(--accent)]">{{ promo.call_to_action || 'Participe agora' }} →</span>
                    </div>
                </Link>
            </div>
            <div class="text-center mt-8">
                <Link href="/promocoes" class="btn-outline">Ver promoções</Link>
            </div>
        </section>

        <!-- NOTÍCIAS / ENTRETENIMENTO -->
        <section class="container-app section-spacing">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <h2 class="section-title">Últimas notícias</h2>
                <Link href="/noticias" class="text-xs font-semibold uppercase tracking-wide text-[var(--accent)] border-b-2 border-[var(--accent)] pb-1">Ver todas</Link>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-[65%_35%] gap-6">
                <Link v-if="hero(featuredNews)" :href="`/noticias/${hero(featuredNews)!.slug}`" class="group rounded-2xl overflow-hidden border border-[var(--border)]">
                    <div class="aspect-video bg-[var(--surface)] flex items-center justify-center overflow-hidden">
                        <img v-if="hero(featuredNews)!.featured_image" :src="hero(featuredNews)!.featured_image ?? ''" :alt="hero(featuredNews)!.title" class="w-full h-full object-cover" />
                        <span v-else class="text-7xl">📰</span>
                    </div>
                    <div class="p-6">
                        <span class="label-category mb-3">{{ hero(featuredNews)!.category?.name || 'Destaque' }}</span>
                        <h3 class="text-2xl font-bold mb-2 group-hover:text-[var(--accent)] transition-colors">{{ hero(featuredNews)!.title }}</h3>
                        <p class="text-sm text-[var(--muted)]">{{ formatDate(hero(featuredNews)!.published_at) }}</p>
                    </div>
                </Link>
                <div class="space-y-4">
                    <Link v-for="item in secondary(latestNews)" :key="item.id" :href="`/noticias/${item.slug}`" class="flex gap-4 group cursor-pointer">
                        <div class="w-24 h-20 rounded-lg bg-[var(--surface)] shrink-0 flex items-center justify-center overflow-hidden">
                            <img v-if="item.featured_image" :src="item.featured_image" :alt="item.title" class="w-full h-full object-cover" />
                            <span v-else class="text-xl">📄</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold leading-snug group-hover:text-[var(--accent)] transition-colors">{{ item.title }}</h4>
                            <p class="text-xs text-[var(--muted)] mt-1">{{ formatDate(item.published_at) }} · {{ item.category?.name }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- BANNER MEIO -->
        <BannerSlot position="home_middle" className="container-app" />

        <!-- LANÇAMENTO MUSICAL -->
        <section v-if="releases.length" class="container-app section-spacing">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-purple-600 via-fuchsia-600 to-pink-600 text-white shadow-xl">
                <div class="container-app relative z-10 py-12 lg:py-16 flex flex-wrap items-center justify-between gap-6">
                    <div>
                        <span class="label-category bg-white/20 mb-4">Lançamento</span>
                        <h2 class="text-3xl lg:text-4xl font-bold mb-2">{{ releases[0].title }}</h2>
                        <p class="text-white/80 mb-4">{{ releases[0].artist?.name }} · {{ formatDate(releases[0].released_at) }}</p>
                        <div class="flex flex-wrap gap-3">
                            <Link :href="`/musicas`" class="btn-accent !bg-white !text-gray-900">Ouvir agora</Link>
                        </div>
                    </div>
                    <img v-if="releases[0].cover" :src="releases[0].cover" :alt="releases[0].title" class="w-40 h-40 rounded-2xl object-cover shadow-2xl" />
                </div>
            </div>
        </section>

        <!-- MAIS TOCADAS -->
        <section v-if="topSongs.length" class="container-app section-spacing">
            <div class="text-center mb-10">
                <h2 class="section-title">Mais tocadas</h2>
                <div class="mx-auto mt-3 h-1 w-10 rounded-full bg-[var(--accent)]"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="item in topSongs.slice(0, 4)" :key="item.position" class="flex items-center gap-4 p-4 rounded-xl border border-[var(--border)]">
                    <span class="text-4xl font-black text-[var(--accent)]">{{ String(item.position).padStart(2, '0') }}</span>
                    <div class="w-16 h-16 rounded-lg bg-[var(--surface)] flex items-center justify-center shrink-0 overflow-hidden">
                        <img v-if="item.cover" :src="item.cover" :alt="item.song" class="w-full h-full object-cover" />
                        <span v-else>🎵</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">{{ item.song }}</p>
                        <p class="text-xs text-[var(--muted)] truncate">{{ item.artist }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- VÍDEOS -->
        <section v-if="videos.length" class="container-app section-spacing">
            <h2 class="section-title mb-8">Vídeos</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a v-for="item in videos.slice(0, 3)" :key="item.id" :href="`#video-${item.id}`" class="group rounded-2xl overflow-hidden border border-[var(--border)]">
                    <div class="aspect-video bg-[var(--surface)] flex items-center justify-center relative overflow-hidden">
                        <img v-if="item.thumbnail" :src="item.thumbnail" :alt="item.title" class="w-full h-full object-cover" />
                        <span v-else class="text-5xl">▶️</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold group-hover:text-[var(--accent)] transition-colors">{{ item.title }}</h3>
                        <p class="text-xs text-[var(--muted)] mt-1">{{ item.category?.name }}</p>
                    </div>
                </a>
            </div>
        </section>

        <!-- NO AR / PROGRAMAÇÃO -->
        <section class="bg-[var(--surface)] border-y border-[var(--border)] section-spacing">
            <div class="container-app grid grid-cols-1 lg:grid-cols-[45%_55%] gap-10 lg:gap-16 items-center">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-44 h-44 rounded-full bg-gradient-to-br from-[var(--accent)] to-[var(--primary)] p-1">
                        <div class="w-full h-full rounded-full bg-[var(--surface)] flex items-center justify-center text-7xl">🎙️</div>
                    </div>
                    <p class="text-xs font-bold tracking-widest uppercase text-[var(--muted)]">No ar agora</p>
                </div>
                <div>
                    <h2 class="section-title mb-6">Ao vivo para todo o Brasil!</h2>
                    <div v-if="currentSchedule" class="card p-6 border-l-4 border-l-[var(--accent)]">
                        <p class="text-xs font-bold tracking-widest uppercase text-[var(--accent)] mb-1">No ar</p>
                        <h3 class="text-2xl font-bold">{{ currentSchedule.program }}</h3>
                        <p class="text-[var(--muted)] text-sm">Com {{ currentSchedule.presenter }}</p>
                        <p class="text-xs text-[var(--muted)] mt-2">{{ timeLabel(currentSchedule.start_time) }} - {{ timeLabel(currentSchedule.end_time) }}</p>
                    </div>
                    <div v-else class="card p-6 border-l-4 border-l-[var(--accent)]">
                        <p class="text-xs font-bold tracking-widest uppercase text-[var(--accent)] mb-1">No ar</p>
                        <h3 class="text-2xl font-bold">Música em sequência</h3>
                        <p class="text-[var(--muted)] text-sm">Programação automática</p>
                    </div>
                    <div v-if="recentTracks.length" class="mt-4 space-y-2">
                        <div class="flex items-center justify-between py-2 border-b border-[var(--border)] text-sm">
                            <span class="text-[var(--muted)]">Tocou agora</span>
                            <span class="font-semibold">{{ recentTracks[0].artist }} - {{ recentTracks[0].title }}</span>
                        </div>
                    </div>
                    <Link href="/programacao" class="btn-outline mt-6">Ver programação completa</Link>
                </div>
            </div>
        </section>

        <!-- APLICATIVO -->
        <section class="container-app section-spacing">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <div class="space-y-5">
                    <h2 class="section-title">Leve a rádio no seu bolso</h2>
                    <p class="text-[var(--muted)]">Baixe o aplicativo e ouça onde estiver, com o melhor da programação.</p>
                    <div class="flex gap-4">
                        <a href="#" class="btn-outline">App Store</a>
                        <a href="#" class="btn-outline">Google Play</a>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="w-48 h-48 bg-[var(--surface)] border border-[var(--border)] rounded-3xl flex items-center justify-center text-7xl">📱</div>
                </div>
            </div>
        </section>

        <!-- NEWSLETTER -->
        <section class="bg-[var(--accent)] text-white">
            <div class="container-app py-12 text-center">
                <h2 class="text-2xl lg:text-3xl font-bold mb-2">Fique por dentro de tudo!</h2>
                <p class="opacity-90 mb-6">Receba novidades, promoções e conteúdo exclusivo.</p>
                <form class="max-w-xl mx-auto flex flex-col sm:flex-row gap-3">
                    <input type="text" placeholder="Seu nome" class="flex-1 rounded-full px-5 py-3 text-gray-900 bg-white text-sm" aria-label="Nome" />
                    <input type="email" placeholder="Seu e-mail" class="flex-1 rounded-full px-5 py-3 text-gray-900 bg-white text-sm" aria-label="E-mail" />
                    <button type="submit" class="rounded-full px-6 py-3 bg-[var(--primary)] text-white text-sm font-semibold">Cadastrar</button>
                </form>
            </div>
        </section>
    </AppLayout>
</template>