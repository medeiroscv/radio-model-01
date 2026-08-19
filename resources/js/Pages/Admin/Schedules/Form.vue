<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface Option {
    id: number
    name: string
}

const props = defineProps<{
    schedule?: {
        id: number
        program_id: number
        presenter_id?: number | null
        start_time: string
        end_time: string
        days_of_week: number[]
        is_active?: boolean
    }
    programs: Option[]
    presenters: Option[]
}>()

const form = useForm({
    program_id: props.schedule?.program_id ?? null,
    presenter_id: props.schedule?.presenter_id ?? null,
    start_time: props.schedule?.start_time ?? '08:00',
    end_time: props.schedule?.end_time ?? '10:00',
    days_of_week: props.schedule?.days_of_week ?? [],
    is_active: props.schedule?.is_active ?? true,
})

const isEdit = !!props.schedule

const days = [
    { value: 1, label: 'Segunda' },
    { value: 2, label: 'Terça' },
    { value: 3, label: 'Quarta' },
    { value: 4, label: 'Quinta' },
    { value: 5, label: 'Sexta' },
    { value: 6, label: 'Sábado' },
    { value: 7, label: 'Domingo' },
]

function toggleDay(value: number) {
    const idx = form.days_of_week.indexOf(value)
    if (idx === -1) form.days_of_week.push(value)
    else form.days_of_week.splice(idx, 1)
}

function submit() {
    if (isEdit) {
        form.put(`/admin/schedules/${props.schedule!.id}`)
    } else {
        form.post('/admin/schedules')
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar horário' : 'Novo horário' }}</h1>
                <p class="text-sm text-[var(--muted)]">Defina quando o programa vai ao ar</p>
            </div>
            <Link href="/admin/schedules" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="space-y-4 max-w-2xl">
            <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Programa *</label>
                    <select v-model="form.program_id" required class="input-app w-full">
                        <option :value="null" disabled>Selecione o programa</option>
                        <option v-for="p in programs" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Apresentador</label>
                    <select v-model="form.presenter_id" class="input-app w-full">
                        <option :value="null">Sem apresentador</option>
                        <option v-for="p in presenters" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Início *</label>
                        <input v-model="form.start_time" type="time" required class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fim *</label>
                        <input v-model="form.end_time" type="time" required class="input-app w-full" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Dias da semana *</label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="day in days"
                            :key="day.value"
                            type="button"
                            @click="toggleDay(day.value)"
                            :class="['rounded-full px-4 py-2 text-xs font-semibold border transition-colors', form.days_of_week.includes(day.value) ? 'bg-[var(--primary)] text-white border-[var(--primary)]' : 'bg-[var(--surface)] border-[var(--border)] text-[var(--muted)]']"
                        >
                            {{ day.label }}
                        </button>
                    </div>
                    <p v-if="!form.days_of_week.length" class="text-xs text-red-500 mt-1">Selecione ao menos um dia.</p>
                </div>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input v-model="form.is_active" type="checkbox" class="rounded" />
                    Horário ativo
                </label>
            </div>

            <button type="submit" class="btn-accent" :disabled="form.processing || !form.days_of_week.length">
                {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar horário') }}
            </button>
        </form>
    </AdminLayout>
</template>