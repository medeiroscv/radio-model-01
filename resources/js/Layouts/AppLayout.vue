<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import RadioPlayer from '../Components/RadioPlayer.vue'
import BannerSlot from '../Components/BannerSlot.vue'

interface Station {
    name?: string
    frequency?: string
    slogan?: string
    city?: string
    state?: string
    address?: string
    phone?: string
    email?: string
    whatsapp?: string
    website_url?: string
    primary_color?: string
    accent_color?: string
    background_color?: string
    surface_color?: string
    text_color?: string
    muted_color?: string
    border_color?: string
    logo_primary?: string
    logo_small?: string
    favicon?: string
    dark_mode_enabled?: boolean
}

interface MainMenu {
    items: Array<{
        id: number
        label: string
        route?: string
        url?: string
        children: Array<{ id: number; label: string; route?: string; url?: string }>
    }>
}

interface SocialLink {
    platform: string
    url: string
}

const page = usePage()
const station = computed<Station | null>(() => page.props.station as Station | null)
const mainMenu = computed<MainMenu | null>(() => page.props.mainMenu as MainMenu | null)
const socialLinks = computed<SocialLink[]>(() => (page.props.socialLinks as SocialLink[]) ?? [])

const cssVars = computed(() => {
    const s = station.value
    if (!s) return {}
    return {
        '--primary': s.primary_color || '#111827',
        '--accent': s.accent_color || '#ef4444',
        '--background': s.background_color || '#ffffff',
        '--surface': s.surface_color || '#f9fafb',
        '--text': s.text_color || '#111827',
        '--muted': s.muted_color || '#6b7280',
        '--border': s.border_color || '#e5e7eb',
    }
})

const socialIcons: Record<string, string> = {
    instagram: 'M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.2 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.3 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .3-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.2-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.3-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.2-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.3 2.2-.4C8.4 2.2 8.8 2.2 12 2.2zm0 3.3c-3.6 0-6.5 2.9-6.5 6.5s2.9 6.5 6.5 6.5 6.5-2.9 6.5-6.5-2.9-6.5-6.5-6.5zm0 10.8a4.3 4.3 0 1 1 0-8.6 4.3 4.3 0 0 1 0 8.6zm6.8-11a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z',
    facebook: 'M12 2C6.5 2 2 6.5 2 12c0 5 3.7 9.2 8.6 9.9v-7H7.9V12h2.7V9.8c0-2.7 1.6-4.2 4.1-4.2 1.2 0 2.4.2 2.4.2v2.7h-1.4c-1.3 0-1.8.9-1.8 1.8V12h3.1l-.5 2.9h-2.6v7c4.9-.7 8.6-4.9 8.6-9.9 0-5.5-4.5-10-10-10z',
    youtube: 'M23.5 6.2c-.3-1.1-1.1-1.9-2.2-2.2C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.3.5C1.6 4.3.8 5.1.5 6.2 0 8 0 12 0 12s0 4 .5 5.8c.3 1.1 1.1 1.9 2.2 2.2 1.8.5 9.3.5 9.3.5s7.5 0 9.3-.5c1.1-.3 1.9-1.1 2.2-2.2.5-1.8.5-5.8.5-5.8s0-4-.5-5.8zM9.5 15.5v-7l6.5 3.5-6.5 3.5z',
    tiktok: 'M12.5 2h3c.2 2.3 1.2 4 3 5.2-.7.3-1.3.4-2 .5v3.4c0 4.1-3.3 7.4-7.4 7.4-1.3 0-2.6-.3-3.7-1 .4-1.1 1.2-2 2.3-2.5 1.9.9 4.2-.3 4.2-2.6V2.2l.6-.2z',
    x: 'M18.2 2h3.5l-7.6 8.7L23 22h-7l-5.5-7.2L4 22H.5l8.2-9.4L1 2h7.2l4.9 6.5L18.2 2zm-1.2 18h1.9L7 3.9H5L17 20z',
    threads: 'M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 3c1.3 0 2.3.5 3 1.4.6.8.9 1.9.9 3.3v1c0 .3-.2.5-.5.5h-.3c.2 2-1.2 3.6-3.1 3.6-1.4 0-2.4-.8-2.4-2 0-1.2.9-2 2.4-2 .5 0 1 .1 1.4.3v-.2c0-1.3-.9-2.1-2.2-2.1-1.3 0-2.2.9-2.2 2.3v1.3c0 2.5 1.8 4.2 4.4 4.2 1.3 0 2.5-.4 3.4-1.2v1.9c-1 .4-2.2.7-3.4.7-3.6 0-6-2.4-6-5.7v-1c0-3.4 2.4-5.8 6-5.8z',
    whatsapp: 'M12 2C6.5 2 2 6.5 2 12c0 1.9.6 3.8 1.7 5.3L2.4 22l4.8-1.3c1.5.8 3.1 1.3 4.8 1.3 5.5 0 10-4.5 10-10S17.5 2 12 2zm5.4 13.7c-.2.7-1.2 1.3-1.9 1.4-.5.1-1.1.1-1.8-.1-.4-.1-.9-.3-1.6-.6-2.8-1.2-4.6-3.9-4.7-4.1-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.4.2.5.7 1.7.7 1.8.1.1.1.2 0 .4-.1.2-.1.3-.3.5-.2.2-.3.3-.5.6-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.4 2.4 1.5.3.1.4.1.6-.1.2-.2.6-.7.8-1 .2-.3.4-.2.6-.1.2.1 1.4.6 1.6.7.2.1.4.2.4.3.1.1.1.6-.1 1.2z',
    telegram: 'M21.9 4.3c-.2-.5-.8-.8-1.3-.6L2.6 10.1c-.6.2-.7.9-.2 1.2l4.5 3.4 1.7 5.2c.2.5.8.7 1.2.4l2.5-2 4.3 3.2c.4.3 1 .1 1.1-.4l3.7-16c.1-.4-.1-.9-.5-1zM8.1 14.1l8.4-5.2c.2-.1.4.2.2.3l-6.8 6.3c-.2.2-.3.4-.3.7l-.2 2.2c0 .3-.4.4-.6.2l-1-3.3c-.1-.3 0-.7.3-.9z',
}

const handleRoute = (item: { route?: string; url?: string }) => {
    if (item.route) {
        return route(item.route)
    }
    return item.url || '#'
}

const newsletterEmail = ref('')
const newsletterName = ref('')
const newsletterSent = ref(false)
const newsletterSending = ref(false)

function subscribeNewsletter() {
    if (newsletterSending.value) return
    newsletterSending.value = true
    router.post('/newsletter', {
        name: newsletterName.value,
        email: newsletterEmail.value,
        consent: true,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newsletterSent.value = true
            newsletterSending.value = false
        },
        onError: () => {
            newsletterSending.value = false
        },
    })
}

const pageTitle = computed(() => String(page.props.pageTitle ?? ''))
const pageDescription = computed(() => String(page.props.pageDescription ?? ''))

const fullTitle = computed(() =>
    pageTitle.value ? `${pageTitle.value} | ${station.value?.name ?? 'RadioCMS'}` : (station.value?.name ?? 'RadioCMS'),
)

const ogImage = computed(() => String(page.props.ogImage ?? '') || station.value?.logo_primary || '')
const canonical = computed(() => String(page.props.canonical ?? '') || window.location.href.split('?')[0])
</script>

<template>
    <div class="min-h-screen flex flex-col" :style="cssVars">
        <Head>
            <title>{{ fullTitle }}</title>
            <meta name="description" :content="pageDescription || station?.slogan || ''" />
            <meta property="og:title" :content="fullTitle" />
            <meta property="og:description" :content="pageDescription || station?.slogan || ''" />
            <meta property="og:type" content="website" />
            <meta property="og:url" :content="canonical" />
            <link rel="canonical" :href="canonical" />
            <link v-if="ogImage" rel="image_src" :href="ogImage" />
            <meta v-if="ogImage" property="og:image" :content="ogImage" />
            <link v-if="station?.favicon" rel="icon" :href="station.favicon" />
        </Head>
        <!-- Header -->
        <header class="border-b" :style="{ borderColor: 'var(--border)' }">
            <div class="container-app flex items-center justify-between gap-6 py-4">
                <Link href="/" class="shrink-0">
                    <template v-if="station?.logo_primary || station?.logo_small">
                        <img v-if="station?.logo_primary" :src="station.logo_primary" :alt="station.name" class="hidden sm:block h-10 w-auto max-w-[220px] object-contain" />
                        <img :src="station.logo_small || station.logo_primary" :alt="station.name" class="sm:hidden h-9 w-auto max-w-[150px] object-contain" />
                    </template>
                    <div v-else class="flex items-baseline gap-2">
                        <span class="text-xl font-bold tracking-tight">{{ station?.name || 'Rádio' }}</span>
                        <span v-if="station?.frequency" class="text-xs font-semibold text-white bg-[var(--accent)] rounded-full px-2 py-0.5">{{ station.frequency }}</span>
                    </div>
                </Link>

                <nav class="hidden lg:flex items-center gap-6">
                    <template v-if="mainMenu?.items?.length">
                        <div v-for="item in mainMenu.items" :key="item.id" class="relative group">
                            <Link :href="handleRoute(item)" class="text-sm font-medium text-[var(--muted)] hover:text-[var(--text)] transition-colors py-2">
                                {{ item.label }}
                            </Link>
                            <div v-if="item.children?.length" class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                <div class="card shadow-lg p-2 min-w-[180px]">
                                    <Link v-for="child in item.children" :key="child.id" :href="handleRoute(child)" class="block px-3 py-2 text-sm rounded-lg hover:bg-[var(--surface)]">
                                        {{ child.label }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <Link href="/" class="text-sm font-medium text-[var(--muted)] hover:text-[var(--text)]">Ao Vivo</Link>
                        <Link href="/promocoes" class="text-sm font-medium text-[var(--muted)] hover:text-[var(--text)]">Promoções</Link>
                        <Link href="/noticias" class="text-sm font-medium text-[var(--muted)] hover:text-[var(--text)]">Notícias</Link>
                        <Link href="/podcasts" class="text-sm font-medium text-[var(--muted)] hover:text-[var(--text)]">Podcasts</Link>
                        <Link href="/rankings" class="text-sm font-medium text-[var(--muted)] hover:text-[var(--text)]">Rankings</Link>
                        <Link href="/programacao" class="text-sm font-medium text-[var(--muted)] hover:text-[var(--text)]">Programação</Link>
                        <Link href="/contato" class="text-sm font-medium text-[var(--muted)] hover:text-[var(--text)]">Contato</Link>
                    </template>
                </nav>

                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-1">
                        <a v-for="link in socialLinks" :key="link.platform" :href="link.url" target="_blank" rel="noopener" class="p-2 text-[var(--muted)] hover:text-[var(--text)] transition-colors" :aria-label="link.platform">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path :d="socialIcons[link.platform] || ''" />
                            </svg>
                        </a>
                    </div>
                    <button type="button" class="p-2 rounded-full text-[var(--muted)] hover:text-[var(--text)] hover:bg-[var(--surface)] transition-colors" aria-label="Buscar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Conteúdo -->
        <main class="flex-1">
            <BannerSlot position="top" className="container-app pt-4" />
            <slot />
        </main>

        <!-- Player global fixo -->
        <div class="sticky bottom-0 z-40 border-t" :style="{ borderColor: 'var(--border)' }">
            <div class="container-app py-3">
                <RadioPlayer />
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-[var(--primary)] text-white mt-auto">
            <BannerSlot position="global_footer" className="container-app pt-8" />
            <div class="container-app py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <p class="text-xl font-bold mb-3">{{ station?.name || 'Rádio' }}</p>
                    <p class="text-sm opacity-80 mb-2">{{ station?.slogan }}</p>
                    <div class="flex gap-2">
                        <a v-for="link in socialLinks" :key="link.platform" :href="link.url" target="_blank" rel="noopener" class="p-2 bg-white/10 rounded-full hover:bg-white/20 transition-colors" :aria-label="link.platform">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path :d="socialIcons[link.platform] || ''" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="font-semibold mb-3 text-sm uppercase tracking-wide opacity-80">Navegação</p>
                    <ul class="space-y-2 text-sm">
                        <li><Link href="/" class="opacity-80 hover:opacity-100">Início</Link></li>
                        <li><Link href="/promocoes" class="opacity-80 hover:opacity-100">Promoções</Link></li>
                        <li><Link href="/noticias" class="opacity-80 hover:opacity-100">Notícias</Link></li>
                        <li><Link href="/podcasts" class="opacity-80 hover:opacity-100">Podcasts</Link></li>
                        <li><Link href="/rankings" class="opacity-80 hover:opacity-100">Rankings</Link></li>
                        <li><Link href="/programacao" class="opacity-80 hover:opacity-100">Programação</Link></li>
                        <li><Link href="/contato" class="opacity-80 hover:opacity-100">Contato</Link></li>
                    </ul>
                </div>

                <div>
                    <p class="font-semibold mb-3 text-sm uppercase tracking-wide opacity-80">A Rádio</p>
                    <ul class="space-y-2 text-sm opacity-80">
                        <li v-if="station?.frequency">Frequência: {{ station.frequency }}</li>
                        <li v-if="station?.city">{{ station.city }}<template v-if="station?.state">, {{ station.state }}</template></li>
                        <li v-if="station?.address">{{ station.address }}</li>
                        <li v-if="station?.phone">{{ station.phone }}</li>
                        <li v-if="station?.email">{{ station.email }}</li>
                    </ul>
                </div>

                <div>
                    <p class="font-semibold mb-3 text-sm uppercase tracking-wide opacity-80">Contato</p>
                    <ul class="space-y-2 text-sm">
                        <li v-if="station?.whatsapp">
                            <a :href="`https://wa.me/${station.whatsapp.replace(/\D/g, '')}`" class="opacity-80 hover:opacity-100">WhatsApp</a>
                        </li>
                        <li>
                            <Link href="/contato" class="opacity-80 hover:opacity-100">Enviar mensagem</Link>
                        </li>
                        <li v-if="station?.website_url">
                            <a :href="station.website_url" target="_blank" class="opacity-80 hover:opacity-100">Site da rádio</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10 bg-black/20">
                <div class="container-app py-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <p class="text-lg font-bold mb-1">Fique por dentro</p>
                        <p class="text-sm opacity-80">Receba as novidades da {{ station?.name || 'rádio' }} no seu e-mail.</p>
                    </div>
                    <form v-if="!newsletterSent" @submit.prevent="subscribeNewsletter" class="flex flex-col sm:flex-row gap-3">
                        <input v-model="newsletterName" type="text" placeholder="Seu nome" class="flex-1 rounded-xl bg-white/10 border border-white/20 px-4 py-2.5 text-sm placeholder-white/50 outline-none focus:border-white/40" />
                        <input v-model="newsletterEmail" type="email" required placeholder="Seu e-mail" class="flex-1 rounded-xl bg-white/10 border border-white/20 px-4 py-2.5 text-sm placeholder-white/50 outline-none focus:border-white/40" />
                        <button type="submit" class="rounded-xl px-6 py-2.5 text-sm font-semibold text-white bg-[var(--accent)] hover:opacity-90 transition-opacity" :disabled="newsletterSending">
                            {{ newsletterSending ? 'Enviando...' : 'Assinar' }}
                        </button>
                    </form>
                    <div v-else class="text-sm font-medium text-green-300">Inscrição confirmada! Obrigado.</div>
                </div>
            </div>
            <div class="border-t border-white/10">
                <div class="container-app py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs opacity-70">
                    <p>© {{ new Date().getFullYear() }} {{ station?.name || 'Rádio' }}. Todos os direitos reservados.</p>
                    <p>Desenvolvido com RadioCMS</p>
                </div>
            </div>
        </footer>
    </div>
</template>