<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppLayout from '../Layouts/AppLayout.vue'

const form = useForm({
    name: '',
    email: '',
    phone: '',
    department: '',
    message: '',
})

function submit() {
    form.post('/contato', {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <AppLayout>
        <section class="container-app section-spacing">
            <div class="text-center mb-12">
                <h1 class="text-3xl lg:text-4xl font-bold mb-2">Contato</h1>
                <p class="text-[var(--muted)]">Fale com a gente</p>
                <div class="mx-auto mt-4 h-1 w-10 rounded-full bg-[var(--accent)]"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-bold mb-2">Informações</h2>
                        <ul class="space-y-2 text-sm text-[var(--muted)]">
                            <li v-if="$page.props.station?.phone">📞 {{ $page.props.station.phone }}</li>
                            <li v-if="$page.props.station?.whatsapp">💬 {{ $page.props.station.whatsapp }}</li>
                            <li v-if="$page.props.station?.email">✉️ {{ $page.props.station.email }}</li>
                            <li v-if="$page.props.station?.address">📍 {{ $page.props.station.address }}</li>
                            <li v-if="$page.props.station?.city">{{ $page.props.station.city }}<template v-if="$page.props.station?.state">, {{ $page.props.station.state }}</template></li>
                        </ul>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nome</label>
                            <input v-model="form.name" type="text" required class="input-app" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">E-mail</label>
                            <input v-model="form.email" type="email" required class="input-app" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Telefone</label>
                            <input v-model="form.phone" type="tel" class="input-app" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Departamento</label>
                            <select v-model="form.department" class="input-app">
                                <option value="">Selecione</option>
                                <option value="comercial">Comercial</option>
                                <option value="jornalismo">Jornalismo</option>
                                <option value="programacao">Programação</option>
                                <option value="tecnico">Técnico</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Mensagem</label>
                        <textarea v-model="form.message" rows="5" required class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                    </div>
                    <button type="submit" class="btn-primary" :disabled="form.processing">Enviar mensagem</button>
                </form>
            </div>
        </section>
    </AppLayout>
</template>