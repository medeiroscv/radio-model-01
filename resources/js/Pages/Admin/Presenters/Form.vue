<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

const props = defineProps<{
    presenter?: {
        id: number
        name: string
        biography?: string
        photo?: string | null
        instagram?: string
        facebook?: string
        tiktok?: string
        x_twitter?: string
        youtube?: string
        whatsapp?: string
        is_active?: boolean
        sort_order?: number
    }
}>()

const form = useForm({
    name: props.presenter?.name ?? '',
    biography: props.presenter?.biography ?? '',
    photo: props.presenter?.photo ?? '',
    instagram: props.presenter?.instagram ?? '',
    facebook: props.presenter?.facebook ?? '',
    tiktok: props.presenter?.tiktok ?? '',
    x_twitter: props.presenter?.x_twitter ?? '',
    youtube: props.presenter?.youtube ?? '',
    whatsapp: props.presenter?.whatsapp ?? '',
    is_active: props.presenter?.is_active ?? true,
    sort_order: props.presenter?.sort_order ?? 0,
})

const isEdit = !!props.presenter

function submit() {
    if (isEdit) {
        form.put(`/admin/presenters/${props.presenter!.id}`)
    } else {
        form.post('/admin/presenters')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar apresentador' : 'Novo apresentador' }}</h1>
                <p class="text-sm text-[var(--muted)]">Preencha os dados do apresentador</p>
            </div>
            <Link href="/admin/presenters" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-4xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nome *</label>
                    <input v-model="form.name" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Biografia</label>
                    <textarea v-model="form.biography" rows="5" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Instagram</label>
                    <input v-model="form.instagram" type="text" class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Facebook</label>
                    <input v-model="form.facebook" type="text" class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">TikTok</label>
                    <input v-model="form.tiktok" type="text" class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">X (Twitter)</label>
                    <input v-model="form.x_twitter" type="text" class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">YouTube</label>
                    <input v-model="form.youtube" type="text" class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">WhatsApp</label>
                    <input v-model="form.whatsapp" type="text" class="input-app w-full" />
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3">
                    <h3 class="font-semibold text-sm">Configuração</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1">Foto</label>
                        <input v-model="form.photo" type="url" class="input-app w-full" placeholder="https://..." />
                    </div>
                    <img v-if="form.photo" :src="form.photo" :alt="form.name" class="rounded-full w-24 h-24 object-cover mx-auto" />
                    <div>
                        <label class="block text-sm font-medium mb-1">Ordem</label>
                        <input v-model.number="form.sort_order" type="number" class="input-app w-full" />
                    </div>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_active" type="checkbox" class="rounded" />
                        Apresentador ativo
                    </label>
                </div>

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar apresentador') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>