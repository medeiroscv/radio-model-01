<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

const props = defineProps<{
    advertiser?: {
        id: number
        name: string
        email?: string | null
        phone?: string | null
    }
}>()

const form = useForm({
    name: props.advertiser?.name ?? '',
    email: props.advertiser?.email ?? '',
    phone: props.advertiser?.phone ?? '',
})

const isEdit = !!props.advertiser

function submit() {
    if (isEdit) {
        form.put(`/admin/advertisers/${props.advertiser!.id}`)
    } else {
        form.post('/admin/advertisers')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar anunciante' : 'Novo anunciante' }}</h1>
                <p class="text-sm text-[var(--muted)]">Dados de contato da empresa</p>
            </div>
            <Link href="/admin/advertisers" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="max-w-lg space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nome *</label>
                <input v-model="form.name" type="text" required class="input-app w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">E-mail</label>
                <input v-model="form.email" type="email" class="input-app w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Telefone</label>
                <input v-model="form.phone" type="text" class="input-app w-full" />
            </div>
            <button type="submit" class="btn-accent" :disabled="form.processing">
                {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar anunciante') }}
            </button>
        </form>
    </AdminLayout>
</template>