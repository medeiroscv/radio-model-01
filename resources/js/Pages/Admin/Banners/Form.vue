<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface AdvertiserOption {
    id: number
    name: string
}

const props = defineProps<{
    banner?: {
        id: number
        advertiser_id?: number | null
        title: string
        internal_title?: string
        image_desktop?: string
        image_mobile?: string
        url?: string
        position?: string
        start_date?: string
        end_date?: string
        is_active?: boolean
        sort_order?: number
    }
    advertisers: AdvertiserOption[]
}>()

const form = useForm({
    advertiser_id: props.banner?.advertiser_id ?? null,
    title: props.banner?.title ?? '',
    internal_title: props.banner?.internal_title ?? '',
    image_desktop: props.banner?.image_desktop ?? '',
    image_mobile: props.banner?.image_mobile ?? '',
    url: props.banner?.url ?? '',
    position: props.banner?.position ?? 'home_leaderboard',
    start_date: props.banner?.start_date ?? '',
    end_date: props.banner?.end_date ?? '',
    is_active: props.banner?.is_active ?? true,
    sort_order: props.banner?.sort_order ?? 0,
})

const isEdit = !!props.banner

const positions = [
    { value: 'home_leaderboard', label: 'Home - Topo (leaderboard)' },
    { value: 'home_sidebar', label: 'Home - Barra lateral' },
    { value: 'home_middle', label: 'Home - Meio da página' },
    { value: 'article_top', label: 'Notícia - Topo' },
    { value: 'article_sidebar', label: 'Notícia - Barra lateral' },
    { value: 'global_footer', label: 'Global - Rodapé' },
]

function submit() {
    if (isEdit) {
        form.put(`/admin/banners/${props.banner!.id}`)
    } else {
        form.post('/admin/banners')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar banner' : 'Novo banner' }}</h1>
                <p class="text-sm text-[var(--muted)]">Configure o anúncio</p>
            </div>
            <Link href="/admin/banners" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-4xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Título *</label>
                    <input v-model="form.title" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Título interno</label>
                    <input v-model="form.internal_title" type="text" class="input-app w-full" placeholder="Uso interno do painel" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL de destino</label>
                    <input v-model="form.url" type="url" class="input-app w-full" placeholder="https://..." />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Posição</label>
                    <select v-model="form.position" class="input-app w-full">
                        <option v-for="p in positions" :key="p.value" :value="p.value">{{ p.label }}</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Imagem desktop</label>
                        <input v-model="form.image_desktop" type="url" class="input-app w-full" placeholder="https://..." />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Imagem mobile</label>
                        <input v-model="form.image_mobile" type="url" class="input-app w-full" placeholder="https://..." />
                    </div>
                </div>
                <img v-if="form.image_desktop" :src="form.image_desktop" :alt="form.title" class="rounded-xl w-full max-w-xl object-cover" />
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3">
                    <h3 class="font-semibold text-sm">Configuração</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1">Anunciante</label>
                        <select v-model="form.advertiser_id" class="input-app w-full">
                            <option :value="null">Sem anunciante</option>
                            <option v-for="a in advertisers" :key="a.id" :value="a.id">{{ a.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Início</label>
                        <input v-model="form.start_date" type="date" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Término</label>
                        <input v-model="form.end_date" type="date" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Ordem</label>
                        <input v-model.number="form.sort_order" type="number" class="input-app w-full" />
                    </div>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_active" type="checkbox" class="rounded" />
                        Banner ativo
                    </label>
                </div>

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar banner') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>