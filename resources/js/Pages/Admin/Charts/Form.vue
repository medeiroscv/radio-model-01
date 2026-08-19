<script setup lang="ts">
import { ref, reactive } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface SongOption {
    id: number
    title: string
    artist?: { id: number; name: string } | null
}

interface EntryItem {
    id: number
    position: number
    plays: number
    song_id: number
    song?: SongOption
}

const props = defineProps<{
    chart?: {
        id: number
        name: string
        period: string
        starts_at?: string | null
        ends_at?: string | null
        is_active?: boolean
        entries?: EntryItem[]
    }
    songs: SongOption[]
}>()

const form = useForm({
    name: props.chart?.name ?? '',
    period: props.chart?.period ?? 'weekly',
    starts_at: props.chart?.starts_at ?? '',
    ends_at: props.chart?.ends_at ?? '',
    is_active: props.chart?.is_active ?? true,
})

const isEdit = !!props.chart

const entries = reactive<EntryItem[]>(
    (props.chart?.entries ?? []).map((e) => ({ ...e })),
)

const selectedSong = ref<number | null>(null)
const newPosition = ref<number>(entries.length + 1)

function addEntry() {
    if (!selectedSong.value) return
    entries.push({
        id: 0,
        position: newPosition.value,
        plays: 0,
        song_id: selectedSong.value,
    })
    selectedSong.value = null
    newPosition.value = entries.length + 1
}

function removeEntry(index: number) {
    entries.splice(index, 1)
}

function songLabel(songId: number | null): string {
    if (!songId) return ''
    const song = props.songs.find((s) => s.id === songId)
    return song ? `${song.title}${song.artist ? ' — ' + song.artist.name : ''}` : `Música #${songId}`
}

function submit() {
    if (isEdit) {
        form.put(`/admin/charts/${props.chart!.id}`, {
            onSuccess: () => {
                const payload = entries
                    .filter((e) => e.song_id)
                    .map((e) => ({ song_id: e.song_id, position: e.position, plays: e.plays }))
                useForm({ entries: payload }).post(`/admin/charts/${props.chart!.id}/entries`, { preserveScroll: true })
            },
        })
    } else {
        form.post('/admin/charts')
    }
}

function saveEntries() {
    const payload = entries.filter((e) => e.song_id).map((e) => ({ song_id: e.song_id, position: e.position, plays: e.plays }))
    useForm({ entries: payload }).post(`/admin/charts/${props.chart!.id}/entries`, { preserveScroll: true })
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar ranking' : 'Novo ranking' }}</h1>
                <p class="text-sm text-[var(--muted)]">Configure o ranking e as posições</p>
            </div>
            <Link href="/admin/charts" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-4xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nome *</label>
                    <input v-model="form.name" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Período</label>
                    <select v-model="form.period" class="input-app w-full">
                        <option value="daily">Diário</option>
                        <option value="weekly">Semanal</option>
                        <option value="monthly">Mensal</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Início</label>
                        <input v-model="form.starts_at" type="date" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fim</label>
                        <input v-model="form.ends_at" type="date" class="input-app w-full" />
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input v-model="form.is_active" type="checkbox" class="rounded" />
                    Ranking ativo
                </label>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4">
                    <h3 class="font-semibold text-sm mb-3">Ações</h3>
                    <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                        {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar ranking') }}
                    </button>
                </div>
            </div>
        </form>

        <div v-if="isEdit" class="mt-10 max-w-4xl">
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
                <div class="p-4 border-b border-[var(--border)] flex flex-wrap items-center justify-between gap-3">
                    <h3 class="font-semibold">Posições do ranking</h3>
                    <button type="button" class="btn-primary !px-4 !py-2 text-sm" @click="saveEntries">Salvar posições</button>
                </div>
                <div class="p-4 border-b border-[var(--border)] flex flex-wrap gap-3">
                    <select v-model="selectedSong" class="input-app flex-1 min-w-[220px]">
                        <option :value="null">Selecionar música...</option>
                        <option v-for="s in songs" :key="s.id" :value="s.id">{{ s.title }}{{ s.artist ? ' — ' + s.artist.name : '' }}</option>
                    </select>
                    <input v-model.number="newPosition" type="number" min="1" class="input-app w-24" placeholder="Pos." />
                    <button type="button" class="btn-accent !px-4 !py-2 text-sm" @click="addEntry">Adicionar</button>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-[var(--surface)]">
                        <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                            <th class="px-4 py-3 font-semibold w-16">Pos.</th>
                            <th class="px-4 py-3 font-semibold">Música</th>
                            <th class="px-4 py-3 font-semibold w-32">Tocadas</th>
                            <th class="px-4 py-3 font-semibold w-16 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr v-for="(entry, index) in entries" :key="entry.song_id + '-' + index">
                            <td class="px-4 py-2"><input v-model.number="entry.position" type="number" min="1" class="input-app w-14 text-center" /></td>
                            <td class="px-4 py-2 text-[var(--muted)]">{{ songLabel(entry.song_id) }}</td>
                            <td class="px-4 py-2"><input v-model.number="entry.plays" type="number" min="0" class="input-app w-24" /></td>
                            <td class="px-4 py-2 text-right">
                                <button type="button" class="text-red-500 hover:underline text-sm font-semibold" @click="removeEntry(index)">Remover</button>
                            </td>
                        </tr>
                        <tr v-if="!entries.length">
                            <td colspan="4" class="px-4 py-10 text-center text-[var(--muted)]">Nenhuma posição no ranking. Adicione músicas acima.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>