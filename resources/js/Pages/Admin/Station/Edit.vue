<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

const props = defineProps<{
    station?: {
        id: number
        name: string
        legal_name?: string
        frequency?: string
        slogan?: string
        city?: string
        state?: string
        country?: string
        timezone?: string
        website_url?: string
        email?: string
        phone?: string
        whatsapp?: string
        address?: string
        logo_primary?: string
        logo_small?: string
        favicon?: string
        primary_color?: string
        accent_color?: string
        background_color?: string
        surface_color?: string
        text_color?: string
        muted_color?: string
        border_color?: string
        font_family?: string
        button_style?: string
        dark_mode_enabled?: boolean
        floating_player_enabled?: boolean
    }
}>()

const form = useForm({
    name: props.station?.name ?? '',
    legal_name: props.station?.legal_name ?? '',
    frequency: props.station?.frequency ?? '',
    slogan: props.station?.slogan ?? '',
    city: props.station?.city ?? '',
    state: props.station?.state ?? '',
    country: props.station?.country ?? '',
    timezone: props.station?.timezone ?? 'America/Sao_Paulo',
    website_url: props.station?.website_url ?? '',
    email: props.station?.email ?? '',
    phone: props.station?.phone ?? '',
    whatsapp: props.station?.whatsapp ?? '',
    address: props.station?.address ?? '',

    logo_primary_upload: null as File | null,
    logo_small_upload: null as File | null,
    favicon_upload: null as File | null,
    remove_logo_primary: false,
    remove_logo_small: false,
    remove_favicon: false,

    primary_color: props.station?.primary_color ?? '#111827',
    accent_color: props.station?.accent_color ?? '#ef4444',
    background_color: props.station?.background_color ?? '#ffffff',
    surface_color: props.station?.surface_color ?? '#f9fafb',
    text_color: props.station?.text_color ?? '#111827',
    muted_color: props.station?.muted_color ?? '#6b7280',
    border_color: props.station?.border_color ?? '#e5e7eb',
    font_family: props.station?.font_family ?? 'Inter',
    button_style: props.station?.button_style ?? 'rounded-full',
    dark_mode_enabled: props.station?.dark_mode_enabled ?? false,
    floating_player_enabled: props.station?.floating_player_enabled ?? true,
})

const logoPrimaryPreview = ref(props.station?.logo_primary ?? '')
const logoSmallPreview = ref(props.station?.logo_small ?? '')
const faviconPreview = ref(props.station?.favicon ?? '')

function fileFromEvent(event: Event): File | null {
    return (event.target as HTMLInputElement).files?.[0] ?? null
}

function previewFile(file: File | null, target: typeof logoPrimaryPreview) {
    if (!file) return
    target.value = URL.createObjectURL(file)
}

function selectLogoPrimary(event: Event) {
    const file = fileFromEvent(event)
    form.logo_primary_upload = file
    form.remove_logo_primary = false
    previewFile(file, logoPrimaryPreview)
}

function selectLogoSmall(event: Event) {
    const file = fileFromEvent(event)
    form.logo_small_upload = file
    form.remove_logo_small = false
    previewFile(file, logoSmallPreview)
}

function selectFavicon(event: Event) {
    const file = fileFromEvent(event)
    form.favicon_upload = file
    form.remove_favicon = false
    previewFile(file, faviconPreview)
}

function removeLogoPrimary() {
    form.logo_primary_upload = null
    form.remove_logo_primary = true
    logoPrimaryPreview.value = ''
}

function removeLogoSmall() {
    form.logo_small_upload = null
    form.remove_logo_small = true
    logoSmallPreview.value = ''
}

function removeFavicon() {
    form.favicon_upload = null
    form.remove_favicon = true
    faviconPreview.value = ''
}

function submit() {
    form.post('/admin/station', {
        forceFormData: true,
        preserveScroll: true,
    })
}

const fonts = ['Inter', 'Roboto', 'Poppins', 'Montserrat', 'Open Sans', 'Lato', 'Nunito', 'Ubuntu', 'Merriweather', 'Playfair Display']
const buttonStyles = [
    { value: 'rounded-full', label: 'Arredondado (pill)' },
    { value: 'rounded-xl', label: 'Suave' },
    { value: 'rounded-lg', label: 'Levemente arredondado' },
    { value: 'rounded-none', label: 'Quadrado' },
]
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold">Identidade da Rádio</h1>
            <p class="text-sm text-[var(--muted)]">Informações gerais, logomarcas e tema visual do site</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6 max-w-4xl">
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-4">
                <h2 class="font-semibold">Informações básicas</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nome *</label>
                        <input v-model="form.name" type="text" required class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Razão social</label>
                        <input v-model="form.legal_name" type="text" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Frequência</label>
                        <input v-model="form.frequency" type="text" class="input-app w-full" placeholder="FM 100.0" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Slogan</label>
                        <input v-model="form.slogan" type="text" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Cidade</label>
                        <input v-model="form.city" type="text" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Estado</label>
                        <input v-model="form.state" type="text" class="input-app w-full" maxlength="2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">País</label>
                        <input v-model="form.country" type="text" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fuso horário</label>
                        <select v-model="form.timezone" class="input-app w-full">
                            <option value="America/Sao_Paulo">America/Sao_Paulo</option>
                            <option value="America/Manaus">America/Manaus</option>
                            <option value="America/Fortaleza">America/Fortaleza</option>
                            <option value="America/Recife">America/Recife</option>
                            <option value="America/Bahia">America/Bahia</option>
                            <option value="America/Belem">America/Belem</option>
                            <option value="America/Cuiaba">America/Cuiaba</option>
                            <option value="America/Campo_Grande">America/Campo_Grande</option>
                            <option value="America/Porto_Velho">America/Porto_Velho</option>
                            <option value="America/Boa_Vista">America/Boa_Vista</option>
                            <option value="UTC">UTC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">E-mail</label>
                        <input v-model="form.email" type="email" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Telefone</label>
                        <input v-model="form.phone" type="text" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">WhatsApp</label>
                        <input v-model="form.whatsapp" type="text" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Site</label>
                        <input v-model="form.website_url" type="url" class="input-app w-full" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">Endereço</label>
                        <input v-model="form.address" type="text" class="input-app w-full" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-5">
                <div>
                    <h2 class="font-semibold">Logos e ícone</h2>
                    <p class="text-xs text-[var(--muted)] mt-1">
                        Envie os arquivos diretamente. As imagens ficam armazenadas no próprio site e são preservadas nas atualizações.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-[var(--border)] p-4 space-y-3">
                        <div>
                            <p class="text-sm font-semibold">Logo principal</p>
                            <p class="text-xs text-[var(--muted)]">Usada no cabeçalho do site. PNG, JPG, WEBP ou GIF, até 8 MB.</p>
                        </div>
                        <div class="h-28 rounded-xl bg-[var(--surface)] flex items-center justify-center p-3 overflow-hidden">
                            <img v-if="logoPrimaryPreview" :src="logoPrimaryPreview" alt="Prévia da logo principal" class="max-h-full max-w-full object-contain" />
                            <span v-else class="text-xs text-[var(--muted)]">Nenhuma imagem</span>
                        </div>
                        <input type="file" accept="image/png,image/jpeg,image/webp,image/gif" class="block w-full text-xs" @change="selectLogoPrimary" />
                        <p v-if="form.errors.logo_primary_upload" class="text-xs text-red-600">{{ form.errors.logo_primary_upload }}</p>
                        <button v-if="logoPrimaryPreview" type="button" class="text-xs font-semibold text-red-600" @click="removeLogoPrimary">Remover logo principal</button>
                    </div>

                    <div class="rounded-xl border border-[var(--border)] p-4 space-y-3">
                        <div>
                            <p class="text-sm font-semibold">Logo pequeno</p>
                            <p class="text-xs text-[var(--muted)]">Versão compacta para telas menores. PNG, JPG, WEBP ou GIF, até 4 MB.</p>
                        </div>
                        <div class="h-28 rounded-xl bg-[var(--surface)] flex items-center justify-center p-3 overflow-hidden">
                            <img v-if="logoSmallPreview" :src="logoSmallPreview" alt="Prévia da logo pequena" class="max-h-full max-w-full object-contain" />
                            <span v-else class="text-xs text-[var(--muted)]">Nenhuma imagem</span>
                        </div>
                        <input type="file" accept="image/png,image/jpeg,image/webp,image/gif" class="block w-full text-xs" @change="selectLogoSmall" />
                        <p v-if="form.errors.logo_small_upload" class="text-xs text-red-600">{{ form.errors.logo_small_upload }}</p>
                        <button v-if="logoSmallPreview" type="button" class="text-xs font-semibold text-red-600" @click="removeLogoSmall">Remover logo pequeno</button>
                    </div>

                    <div class="rounded-xl border border-[var(--border)] p-4 space-y-3">
                        <div>
                            <p class="text-sm font-semibold">Favicon</p>
                            <p class="text-xs text-[var(--muted)]">Ícone da aba do navegador. PNG, ICO, JPG ou WEBP, até 2 MB.</p>
                        </div>
                        <div class="h-28 rounded-xl bg-[var(--surface)] flex items-center justify-center p-3 overflow-hidden">
                            <img v-if="faviconPreview" :src="faviconPreview" alt="Prévia do favicon" class="h-16 w-16 object-contain" />
                            <span v-else class="text-xs text-[var(--muted)]">Nenhum ícone</span>
                        </div>
                        <input type="file" accept="image/png,image/x-icon,image/vnd.microsoft.icon,image/jpeg,image/webp,.ico" class="block w-full text-xs" @change="selectFavicon" />
                        <p v-if="form.errors.favicon_upload" class="text-xs text-red-600">{{ form.errors.favicon_upload }}</p>
                        <button v-if="faviconPreview" type="button" class="text-xs font-semibold text-red-600" @click="removeFavicon">Remover favicon</button>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-4">
                <h2 class="font-semibold">Tema visual</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div v-for="color in [
                        { key: 'primary_color', label: 'Primária' },
                        { key: 'accent_color', label: 'Destaque' },
                        { key: 'background_color', label: 'Fundo' },
                        { key: 'surface_color', label: 'Superfície' },
                        { key: 'text_color', label: 'Texto' },
                        { key: 'muted_color', label: 'Texto suave' },
                        { key: 'border_color', label: 'Borda' },
                    ]" :key="color.key">
                        <label class="block text-sm font-medium mb-1">{{ color.label }}</label>
                        <input v-model="form[color.key as keyof typeof form]" type="color" class="w-full h-10 rounded-xl border border-[var(--border)] bg-[var(--surface)]" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Fonte</label>
                        <select v-model="form.font_family" class="input-app w-full">
                            <option v-for="font in fonts" :key="font" :value="font">{{ font }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Estilo dos botões</label>
                        <select v-model="form.button_style" class="input-app w-full">
                            <option v-for="bs in buttonStyles" :key="bs.value" :value="bs.value">{{ bs.label }}</option>
                        </select>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input v-model="form.dark_mode_enabled" type="checkbox" class="rounded" />
                    Habilitar modo escuro
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input v-model="form.floating_player_enabled" type="checkbox" class="rounded" />
                    Player flutuante
                </label>
            </div>

            <button type="submit" class="btn-accent" :disabled="form.processing">
                {{ form.processing ? 'Salvando...' : 'Salvar configurações' }}
            </button>
        </form>
    </AdminLayout>
</template>
