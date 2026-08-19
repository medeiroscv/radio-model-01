<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface Latest {
    version: string
    tag: string
    published_at?: string | null
    html_url?: string | null
    body?: string
    source: 'release' | 'branch'
}

defineProps<{
    configured: boolean
    repo: string | null
    currentVersion: string
    latest: Latest | null
    hasUpdate: boolean
}>()

const checking = ref(false)
const updating = ref(false)

const checkForUpdates = () => {
    checking.value = true
    router.post('/admin/update/check', {}, {
        onFinish: () => (checking.value = false),
    })
}

const runUpdate = () => {
    if (!window.confirm('Deseja baixar e instalar a nova versão agora? O site pode ficar indisponível por alguns instantes durante a atualização.')) return
    updating.value = true
    router.post('/admin/update', {}, {
        onFinish: () => (updating.value = false),
    })
}

const formatDate = (date?: string | null): string => {
    if (!date) return '—'
    return new Date(date).toLocaleString('pt-BR')
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold">Atualizações</h1>
            <p class="text-[var(--muted)] text-sm mt-1">Mantenha seu site atualizado pelo GitHub, sem precisar de FTP</p>
        </div>

        <div v-if="!configured" class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-6">
            <div class="flex items-start gap-4">
                <span class="text-3xl">🔌</span>
                <div>
                    <h2 class="font-bold text-lg">Atualização automática não configurada</h2>
                    <p class="text-sm text-[var(--muted)] mt-2 leading-relaxed">
                        Para ativar, informe o repositório público do GitHub onde o projeto está publicado.
                        Edite o arquivo <code class="bg-[var(--surface)] px-1.5 py-0.5 rounded text-xs">.env</code> na raiz do site
                        e adicione:
                    </p>
                    <pre class="mt-3 rounded-xl bg-[var(--surface)] p-4 text-sm overflow-x-auto">UPDATE_REPO=seuusuario/radio-cms</pre>
                    <p class="text-xs text-[var(--muted)] mt-3 leading-relaxed">
                        Para repositórios privados ou para evitar o limite de consultas do GitHub, adicione também
                        <code class="bg-[var(--surface)] px-1.5 py-0.5 rounded text-xs">UPDATE_TOKEN=seu_token_github</code>
                        (Personal Access Token com permissão de leitura).
                    </p>
                    <p class="text-xs text-[var(--muted)] mt-3">
                        Depois de salvar o <code class="bg-[var(--surface)] px-1.5 py-0.5 rounded text-xs">.env</code>,
                        recarregue esta página.
                    </p>
                </div>
            </div>
        </div>

        <div v-else class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-[var(--muted)] mb-1">Versão atual</p>
                    <p class="text-2xl font-bold">v{{ currentVersion }}</p>
                </div>
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-[var(--muted)] mb-1">Última versão</p>
                    <p v-if="latest" class="text-2xl font-bold">v{{ latest.version }}</p>
                    <p v-else class="text-2xl font-bold text-[var(--muted)]">—</p>
                </div>
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-[var(--muted)] mb-1">Status</p>
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

            <div v-if="latest" class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-6">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <h2 class="font-bold text-lg">Release v{{ latest.version }}</h2>
                        <p class="text-xs text-[var(--muted)] mt-1">
                            Fonte: {{ latest.source === 'release' ? 'GitHub Release' : 'branch ' + latest.tag }} ·
                            Publicado em {{ formatDate(latest.published_at) }}
                            <a v-if="latest.html_url" :href="latest.html_url" target="_blank" rel="noopener" class="text-[var(--accent)] font-semibold ml-1">Ver no GitHub ↗</a>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" class="btn-outline !px-4 !py-2 text-sm" :disabled="checking" @click="checkForUpdates">
                            {{ checking ? 'Verificando...' : 'Verificar atualizações' }}
                        </button>
                        <button
                            v-if="hasUpdate"
                            type="button"
                            class="btn-accent !px-4 !py-2 text-sm"
                            :disabled="updating"
                            @click="runUpdate"
                        >
                            {{ updating ? 'Atualizando... aguarde' : 'Atualizar agora' }}
                        </button>
                    </div>
                </div>

                <pre
                    v-if="latest.body"
                    class="mt-4 rounded-xl bg-[var(--surface)] p-4 text-sm whitespace-pre-wrap break-words max-h-72 overflow-y-auto"
                >{{ latest.body }}</pre>
            </div>

            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-6">
                <h2 class="font-bold mb-3">O que acontece durante a atualização</h2>
                <ul class="text-sm text-[var(--muted)] space-y-2 leading-relaxed">
                    <li>🔄 O site baixa a nova versão do repositório <b class="text-[var(--text)]">{{ repo }}</b>;</li>
                    <li>🗂️ Os arquivos são substituídos automaticamente, preservando o <b class="text-[var(--text)]">.env</b>, o <b class="text-[var(--text)]">storage/</b>, os <b class="text-[var(--text)]">uploads</b> e seus dados;</li>
                    <li>📦 Migrations do banco são executadas automaticamente;</li>
                    <li>🧹 Caches são limpos e o site volta a funcionar na nova versão.</li>
                </ul>
                <p class="text-xs text-[var(--muted)] mt-4">
                    Recomenda-se fazer um backup antes de atualizar. Ao publicar novas versões no GitHub,
                    execute <code class="bg-[var(--surface)] px-1.5 py-0.5 rounded text-xs">npm run build</code> antes de commitar
                    para que o frontend já venha compilado no pacote.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>