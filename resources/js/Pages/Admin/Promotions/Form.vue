<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

const props = defineProps<{
    promotion?: {
        id: number
        title: string
        call_to_action?: string
        rules?: string
        regulations?: string
        description?: string
        image?: string | null
        banner_image?: string | null
        start_date?: string
        end_date?: string
        participate_url?: string
        is_active?: boolean
        is_featured?: boolean
    }
}>()

const form = useForm({
    title: props.promotion?.title ?? '',
    call_to_action: props.promotion?.call_to_action ?? '',
    rules: props.promotion?.rules ?? '',
    regulations: props.promotion?.regulations ?? '',
    description: props.promotion?.description ?? '',
    image: props.promotion?.image ?? '',
    banner_image: props.promotion?.banner_image ?? '',
    start_date: props.promotion?.start_date ?? '',
    end_date: props.promotion?.end_date ?? '',
    participate_url: props.promotion?.participate_url ?? '',
    is_active: props.promotion?.is_active ?? true,
    is_featured: props.promotion?.is_featured ?? false,
})

const isEdit = !!props.promotion

function submit() {
    if (isEdit) {
        form.put(`/admin/promotions/${props.promotion!.id}`)
    } else {
        form.post('/admin/promotions')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar promoção' : 'Nova promoção' }}</h1>
                <p class="text-sm text-[var(--muted)]">Configure a campanha</p>
            </div>
            <Link href="/admin/promotions" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-5xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Título *</label>
                    <input v-model="form.title" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Chamada (CTA)</label>
                    <input v-model="form.call_to_action" type="text" class="input-app w-full" placeholder="Ex: Participe e concorra a prêmios!" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Descrição</label>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Como participar</label>
                    <textarea v-model="form.rules" rows="6" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm font-mono" placeholder="HTML / regras da participação..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Regulamento</label>
                    <textarea v-model="form.regulations" rows="6" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm font-mono" placeholder="HTML / regulamento..."></textarea>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3">
                    <h3 class="font-semibold text-sm">Período</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1">Início</label>
                        <input v-model="form.start_date" type="date" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Término</label>
                        <input v-model="form.end_date" type="date" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">URL de participação</label>
                        <input v-model="form.participate_url" type="url" class="input-app w-full" placeholder="https://..." />
                    </div>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_active" type="checkbox" class="rounded" />
                        Promoção ativa
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_featured" type="checkbox" class="rounded" />
                        Em destaque
                    </label>
                </div>

                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3">
                    <h3 class="font-semibold text-sm">Imagens</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1">Imagem</label>
                        <input v-model="form.image" type="url" class="input-app w-full" placeholder="https://..." />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Banner</label>
                        <input v-model="form.banner_image" type="url" class="input-app w-full" placeholder="https://..." />
                    </div>
                    <img v-if="form.banner_image" :src="form.banner_image" :alt="form.title" class="rounded-xl w-full h-32 object-cover" />
                </div>

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar promoção') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>