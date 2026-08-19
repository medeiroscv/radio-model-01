<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface PresenterOption {
    id: number
    name: string
}

const props = defineProps<{
    program?: {
        id: number
        name: string
        description?: string
        image?: string | null
        category?: string
        social_links?: Record<string, string> | null
        is_active?: boolean
        sort_order?: number
        presenter_id?: number | null
    }
    presenters: PresenterOption[]
}>()

const form = useForm({
    presenter_id: props.program?.presenter_id ?? null,
    name: props.program?.name ?? '',
    description: props.program?.description ?? '',
    image: props.program?.image ?? '',
    category: props.program?.category ?? '',
    social_links: props.program?.social_links ?? {},
    is_active: props.program?.is_active ?? true,
    sort_order: props.program?.sort_order ?? 0,
})

const isEdit = !!props.program

function submit() {
    if (isEdit) {
        form.put(`/admin/programs/${props.program!.id}`)
    } else {
        form.post('/admin/programs')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar programa' : 'Novo programa' }}</h1>
                <p class="text-sm text-[var(--muted)]">Preencha os dados do programa</p>
            </div>
            <Link href="/admin/programs" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-4xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nome *</label>
                    <input v-model="form.name" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Categoria</label>
                    <input v-model="form.category" type="text" class="input-app w-full" placeholder="Ex: Musical, Jornalismo, Entretenimento" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Descrição</label>
                    <textarea v-model="form.description" rows="5" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3">
                    <h3 class="font-semibold text-sm">Configuração</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1">Apresentador</label>
                        <select v-model="form.presenter_id" class="input-app w-full">
                            <option :value="null">Sem apresentador</option>
                            <option v-for="p in presenters" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Ordem</label>
                        <input v-model.number="form.sort_order" type="number" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Imagem</label>
                        <input v-model="form.image" type="url" class="input-app w-full" placeholder="https://..." />
                    </div>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_active" type="checkbox" class="rounded" />
                        Programa ativo
                    </label>
                </div>

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar programa') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>