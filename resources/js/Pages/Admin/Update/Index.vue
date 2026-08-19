<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface Latest {
    version: string
    tag: string
    published_at?: string | null
    html_url?: string | null
    body?: string
    source: 'release' | 'branch'
    commit?: string | null
}

interface UpdateStatus {
    status: string
    phase: string | null
    processed: number
    total: number
    progress: number
    version?: string | null
    message: string
}

const props = defineProps<{
    configured: boolean
    repo: string | null
    currentVersion: string
    latest: Latest | null
    hasUpdate: boolean
    updateStatus: UpdateStatus
}>()

const checking = ref(false)
const updating = ref(false)
const status = ref<UpdateStatus>(props.updateStatus)
const error = ref('')
const progress = computed(() => Math.max(0, Math.min(100, status.value?.progress ?? 0)))

const csrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

async function request(url: string, method = 'POST'): Promise<UpdateStatus> {
    const response = await fetch(url, {
        method,
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf(),
        },
    })

    const data = await response.json().catch(() => ({}))

    if (!response.ok) {
        throw new Error(data.message || `Erro HTTP ${response.status}`)
    }

    return data
}

const checkForUpdates = () => {
    checking.value = true
    router.post('/admin/update/check', {}, {
        onFinish: () => (checking.value = false),
    })
}

async function continueUpdate() {
    updating.value = true
    error.value = ''

    try {
        while (true) {
            if (status.value.phase === 'copy') {
                status.value = await request('/admin/update/step')
                await new Promise(resolve => setTimeout(resolve, 80))
                continue
            }

            if (status.value.phase === 'finalize') {
                status.value = await request('/admin/update/finalize')
                continue
            }

            break
        }

        if (status.value.status === 'complete') {
            setTimeout(() => window.location.reload(), 700)
        }
    } catch (e: any) {
        error.value = e?.message || 'Falha durante a atualização.'

        try {
            status.value = await request('/admin/update/status', 'GET')
        } catch {
            // Mantém o erro original.
        }
    } finally {
        updating.value = false
    }
}

const runUpdate = async () => {
    if (!window.confirm(
        'Deseja baixar e instalar a nova versão agora? Seus dados, uploads e configurações serão preservados.'
    )) return

    updating.value = true
    error.value = ''

    try {
        status.value = await request('/admin/update/prepare')
        await continueUpdate()
    } catch (e: any) {
        error.value = e?.message || 'Não foi possível iniciar a atualização.'
        updating.value = false
    }
}

const resetUpdate = async () => {
    if (!window.confirm(
        'Limpar o estado da atualização interrompida e tentar novamente?'
    )) return

    try {
        status.value = await request('/admin/update/reset')
        error.value = ''
    } catch (e: any) {
        error.value = e?.message || 'Não foi possível limpar o estado.'
    }
}

const formatDate = (date?: string | null): string =>
    date ? new Date(date).toLocaleString('pt-BR') : '—'

onMounted(async () => {
    if (
        ['prepared', 'running'].includes(status.value.status) ||
        ['copy', 'finalize'].includes(status.value.phase || '')
    ) {
        await continueUpdate()
    }
})
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold">Atualizações</h1>
            <p class="text-[var(--muted)] text-sm mt-1">
                Atualização segura por etapas, sem FTP e sem manter uma requisição longa aberta.
            </p>
        </div>

        <div
            v-if="!configured"
            class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-6"
        >
            <h2 class="font-bold text-lg">Atualização automática não configurada</h2>
            <p class="text-sm text-[var(--muted)] mt-2">
                Adicione no <code>.env</code>:
            </p>
            <pre class="mt-3 rounded-xl bg-[var(--surface)] p-4 text-sm overflow-x-auto">UPDATE_REPO=medeiroscv/radio-model-01</pre>
        </div>

        <div v-else class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-[var(--muted)] mb-1">
                        Versão atual
                    </p>
                    <p class="text-2xl font-bold">v{{ currentVersion }}</p>
                </div>

                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-[var(--muted)] mb-1">
                        Última versão
                    </p>
                    <p v-if="latest" class="text-2xl font-bold">v{{ latest.version }}</p>
                    <p v-else class="text-2xl font-bold text-[var(--muted)]">—</p>
                </div>

                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-[var(--muted)] mb-1">
                        Status
                    </p>
                    <span
                        v-if="hasUpdate"
                        class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-700"
                    >
                        Atualização disponível
                    </span>
                    <span
                        v-else
                        class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700"
                    >
                        Atualizado
                    </span>
                </div>
            </div>

            <div
                v-if="updating || status.phase"
                class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-6"
            >
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-bold">Progresso da atualização</h2>
                        <p class="text-sm text-[var(--muted)] mt-1">{{ status.message }}</p>
                    </div>
                    <strong>{{ progress }}%</strong>
                </div>

                <div class="mt-4 h-3 rounded-full bg-[var(--surface)] overflow-hidden">
                    <div
                        class="h-full bg-[var(--accent)] transition-all duration-300"
                        :style="{ width: progress + '%' }"
                    ></div>
                </div>

                <p v-if="status.total" class="text-xs text-[var(--muted)] mt-2">
                    {{ status.processed }} de {{ status.total }} arquivos processados
                </p>
            </div>

            <div
                v-if="error || status.status === 'error'"
                class="rounded-2xl border border-red-300 bg-red-50 p-5 text-red-800"
            >
                <p class="font-semibold">{{ error || status.message }}</p>
                <button
                    type="button"
                    class="mt-3 underline text-sm font-semibold"
                    @click="resetUpdate"
                >
                    Limpar estado e tentar novamente
                </button>
            </div>

            <div
                v-if="latest"
                class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-6"
            >
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <h2 class="font-bold text-lg">Versão disponível</h2>
                        <p class="text-xs text-[var(--muted)] mt-1">
                            Fonte:
                            {{ latest.source === 'release' ? 'GitHub Release' : 'branch ' + latest.tag }}
                            · {{ formatDate(latest.published_at) }}
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            class="btn-outline !px-4 !py-2 text-sm"
                            :disabled="checking || updating"
                            @click="checkForUpdates"
                        >
                            {{ checking ? 'Verificando...' : 'Verificar atualizações' }}
                        </button>

                        <button
                            v-if="hasUpdate"
                            type="button"
                            class="btn-accent !px-4 !py-2 text-sm"
                            :disabled="updating"
                            @click="runUpdate"
                        >
                            {{ updating ? 'Atualizando...' : 'Atualizar agora' }}
                        </button>
                    </div>
                </div>

                <pre
                    v-if="latest.body"
                    class="mt-4 rounded-xl bg-[var(--surface)] p-4 text-sm whitespace-pre-wrap break-words max-h-72 overflow-y-auto"
                >{{ latest.body }}</pre>
            </div>

            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-6">
                <h2 class="font-bold mb-3">Atualização protegida</h2>
                <ul class="text-sm text-[var(--muted)] space-y-2 leading-relaxed">
                    <li>O pacote é baixado e validado antes de substituir arquivos.</li>
                    <li>Os arquivos são aplicados em pequenos lotes para evitar Gateway Timeout.</li>
                    <li>
                        <b class="text-[var(--text)]">.env</b>,
                        <b class="text-[var(--text)]">storage</b>,
                        uploads e dados do site são preservados.
                    </li>
                    <li>Migrations e caches são processados apenas ao final.</li>
                </ul>
            </div>
        </div>
    </AdminLayout>
</template>
