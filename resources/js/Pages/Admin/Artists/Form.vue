<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

const props = defineProps<{
    artist?: {
        id: number
        name: string
        photo?: string | null
        bio?: string | null
    }
}>()

const form = useForm({
    name: props.artist?.name ?? '',
    photo: props.artist?.photo ?? '',
    bio: props.artist?.bio ?? '',
})

const isEdit = !!props.artist

function submit() {
    if (isEdit) {
        form.put(`/admin/artists/${props.artist!.id}`)
    } else {
        form.post('/admin/artists')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar artista' : 'Novo artista' }}</h1>
                <p class="text-sm text-[var(--muted)]">Dados do artista do catálogo</p>
            </div>
            <Link href="/admin/artists" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-3xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nome *</label>
                    <input v-model="form.name" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Biografia</label>
                    <textarea v-model="form.bio" rows="8" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3">
                    <h3 class="font-semibold text-sm">Foto</h3>
                    <input v-model="form.photo" type="url" class="input-app w-full" placeholder="https://..." />
                    <img v-if="form.photo" :src="form.photo" alt="Prévia" class="w-full aspect-square object-cover rounded-xl border border-[var(--border)]" />
                </div>

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar artista') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>