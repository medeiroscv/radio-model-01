<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface RoleOption {
    id: number
    name: string
}

const props = defineProps<{
    user?: {
        id: number
        name: string
        email: string
        phone?: string
        is_active?: boolean
        roles?: Array<{ id: number; name: string }>
    }
    roles: RoleOption[]
}>()

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    password: '',
    phone: props.user?.phone ?? '',
    is_active: props.user?.is_active ?? true,
    roles: props.user?.roles?.map((r) => r.id) ?? [],
})

const isEdit = !!props.user

function submit() {
    if (isEdit) {
        form.put(`/admin/users/${props.user!.id}`)
    } else {
        form.post('/admin/users')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar usuário' : 'Novo usuário' }}</h1>
                <p class="text-sm text-[var(--muted)]">Gerencie o acesso à equipe</p>
            </div>
            <Link href="/admin/users" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="max-w-2xl space-y-4">
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nome *</label>
                    <input v-model="form.name" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">E-mail *</label>
                    <input v-model="form.email" type="email" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ isEdit ? 'Nova senha (deixe vazio para manter)' : 'Senha *' }}</label>
                    <input v-model="form.password" type="password" :required="!isEdit" class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Telefone</label>
                    <input v-model="form.phone" type="text" class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Funções</label>
                    <div class="flex flex-wrap gap-3">
                        <label v-for="role in roles" :key="role.id" class="flex items-center gap-2 text-sm cursor-pointer">
                            <input v-model="form.roles" type="checkbox" :value="role.id" class="rounded" />
                            {{ role.name }}
                        </label>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input v-model="form.is_active" type="checkbox" class="rounded" />
                    Usuário ativo
                </label>
            </div>

            <button type="submit" class="btn-accent" :disabled="form.processing">
                {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar usuário') }}
            </button>
        </form>
    </AdminLayout>
</template>