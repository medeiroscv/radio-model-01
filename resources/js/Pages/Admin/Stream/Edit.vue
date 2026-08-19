<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

const props = defineProps<{
    settings: {
        id: number
        provider_type: string
        stream_url?: string
        stream_url_alt?: string
        mount_point?: string
        username?: string
        password?: string
        admin_url?: string
        stats_url?: string
        metadata_url?: string
        is_enabled?: boolean
        history_enabled?: boolean
        polling_interval?: number
    }
    status: {
        online: boolean
        provider: string
        enabled: boolean
        message?: string
        stream_url?: string
        listeners?: number
        now_playing?: string | null
        on_air?: { program?: string } | null
    }
}>()

const form = useForm({
    provider_type: props.settings.provider_type ?? 'generic',
    stream_url: props.settings.stream_url ?? '',
    stream_url_alt: props.settings.stream_url_alt ?? '',
    mount_point: props.settings.mount_point ?? '',
    username: props.settings.username ?? '',
    password: props.settings.password ?? '',
    admin_url: props.settings.admin_url ?? '',
    stats_url: props.settings.stats_url ?? '',
    metadata_url: props.settings.metadata_url ?? '',
    is_enabled: props.settings.is_enabled ?? false,
    history_enabled: props.settings.history_enabled ?? true,
    polling_interval: props.settings.polling_interval ?? 30,
})

const providers = [
    { value: 'generic', label: 'Genérico / Direto', hint: 'URL de stream simples (mp3, aac). Sem metadados.' },
    { value: 'icecast', label: 'Icecast', hint: 'Lê status em /status-json.xsl (se habilitado) ou admin.cgi.' },
    { value: 'shoutcast', label: 'SHOUTcast', hint: 'Usa admin.cgi?viewxml ou 7.html.' },
    { value: 'azuracast', label: 'AzuraCast', hint: 'Usa a API pública /api/nowplaying. Requer API Key.' },
]

function submit() {
    form.post('/admin/stream')
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold">Streaming</h1>
            <p class="text-sm text-[var(--muted)]">Configure o servidor de transmissão da rádio</p>
        </div>

        <div :class="['mb-6 rounded-xl border px-4 py-3 text-sm', status.online ? 'border-green-200 bg-green-50 text-green-700' : status.enabled ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-gray-200 bg-gray-50 text-gray-600']">
            <strong>Status:</strong> {{ status.online ? 'No ar' : status.enabled ? 'Configurado, mas offline' : 'Não configurado' }}
            <span v-if="status.now_playing" class="block mt-1">▶ Tocando: {{ status.now_playing }}</span>
            <span v-if="status.listeners !== undefined && status.listeners !== null" class="block mt-1">Ouvintes: {{ status.listeners }}</span>
            <span v-if="status.message" class="block mt-1">{{ status.message }}</span>
        </div>

        <form @submit.prevent="submit" class="max-w-3xl space-y-4">
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Provedor *</label>
                    <select v-model="form.provider_type" class="input-app w-full">
                        <option v-for="p in providers" :key="p.value" :value="p.value">{{ p.label }}</option>
                    </select>
                    <p class="text-xs text-[var(--muted)] mt-1">{{ providers.find((p) => p.value === form.provider_type)?.hint }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL do stream *</label>
                    <input v-model="form.stream_url" type="url" class="input-app w-full" placeholder="https://servidor:8000/stream" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL alternativa</label>
                    <input v-model="form.stream_url_alt" type="url" class="input-app w-full" placeholder="https://servidor:8000/stream2" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Mount point</label>
                        <input v-model="form.mount_point" type="text" class="input-app w-full" placeholder="/stream" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Intervalo de polling (s)</label>
                        <input v-model.number="form.polling_interval" type="number" min="5" max="300" class="input-app w-full" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Usuário</label>
                        <input v-model="form.username" type="text" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Senha</label>
                        <input v-model="form.password" type="password" class="input-app w-full" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL de admin (admin.cgi)</label>
                    <input v-model="form.admin_url" type="url" class="input-app w-full" placeholder="https://servidor:8000/admin.cgi" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL de estatísticas</label>
                    <input v-model="form.stats_url" type="url" class="input-app w-full" placeholder="https://servidor:8000/status-json.xsl" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL de metadados</label>
                    <input v-model="form.metadata_url" type="url" class="input-app w-full" placeholder="https://servidor:8000/7.html" />
                </div>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input v-model="form.is_enabled" type="checkbox" class="rounded" />
                    Streaming habilitado
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input v-model="form.history_enabled" type="checkbox" class="rounded" />
                    Salvar histórico de músicas tocadas
                </label>
            </div>

            <button type="submit" class="btn-accent" :disabled="form.processing">{{ form.processing ? 'Salvando...' : 'Salvar configurações' }}</button>
        </form>
    </AdminLayout>
</template>