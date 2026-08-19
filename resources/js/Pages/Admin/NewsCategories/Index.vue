<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface Category {
    id: number
    name: string
    slug: string
    color?: string
    sort_order?: number
}

defineProps<{
    categories: Category[]
}>()

const form = useForm({
    name: '',
    color: '#ef4444',
    sort_order: 0,
})

function submit() {
    form.post('/admin/news-categories', {
        onSuccess: () => form.reset('name', 'color', 'sort_order'),
    })
}

function remove(category: Category) {
    if (!confirm(`Excluir a categoria "${category.name}"?`)) return
    useForm({}).delete(`/admin/news-categories/${category.id}`)
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold">Categorias de Notícias</h1>
            <p class="text-sm text-[var(--muted)]">Organize as notícias por assunto</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-6 max-w-4xl">
            <form @submit.prevent="submit" class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-3 h-fit">
                <h3 class="font-semibold text-sm">Nova categoria</h3>
                <input v-model="form.name" type="text" placeholder="Nome (ex: Esportes)" required class="input-app w-full" />
                <div>
                    <label class="block text-sm font-medium mb-1">Cor</label>
                    <input v-model="form.color" type="color" class="w-full h-10 rounded-xl border border-[var(--border)] bg-[var(--surface)]" />
                </div>
                <input v-model.number="form.sort_order" type="number" placeholder="Ordem" class="input-app w-full" />
                <button type="submit" class="btn-accent w-full" :disabled="form.processing">Adicionar</button>
            </form>

            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-[var(--surface)]">
                        <tr class="text-left text-xs uppercase tracking-wide text-[var(--muted)]">
                            <th class="px-4 py-3 font-semibold">Nome</th>
                            <th class="px-4 py-3 font-semibold">Slug</th>
                            <th class="px-4 py-3 font-semibold">Cor</th>
                            <th class="px-4 py-3 font-semibold text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr v-for="cat in categories" :key="cat.id">
                            <td class="px-4 py-3 font-medium">{{ cat.name }}</td>
                            <td class="px-4 py-3 text-[var(--muted)]">{{ cat.slug }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block w-6 h-6 rounded-full border border-[var(--border)]" :style="{ backgroundColor: cat.color }"></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button type="button" class="text-sm font-semibold text-red-500 hover:underline" @click="remove(cat)">Excluir</button>
                            </td>
                        </tr>
                        <tr v-if="!categories.length">
                            <td colspan="4" class="px-4 py-10 text-center text-[var(--muted)]">Nenhuma categoria criada.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>