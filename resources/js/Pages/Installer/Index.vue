<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

interface Requirement {
    name: string
    passed: boolean
    current: string
}

interface Permission {
    name: string
    path: string
    writable: boolean
}

const props = defineProps<{
    requirements: Requirement[]
    permissions: Permission[]
    step: number
}>()

const step = ref(1)
const testing = ref(false)
const testResult = ref<{ success: boolean; message: string } | null>(null)

const dbForm = useForm({
    db_host: '127.0.0.1',
    db_port: '3306',
    db_database: '',
    db_username: '',
    db_password: '',
})

const stationForm = useForm({
    station_name: '',
    frequency: '',
    slogan: '',
    city: '',
    state: '',
    country: 'Brasil',
    timezone: 'America/Sao_Paulo',
    email: '',
})

const adminForm = useForm({
    admin_name: '',
    admin_email: '',
    admin_password: '',
    admin_password_confirmation: '',
})

const allRequirementsPass = computed(() => props.requirements.every((r) => r.passed))
const allPermissionsWritable = computed(() => props.permissions.every((p) => p.writable))
const installing = ref(false)

function nextStep() {
    if (step.value === 1 && (!allRequirementsPass.value || !allPermissionsWritable.value)) return
    if (step.value === 2) {
        testConnection()
        return
    }
    if (step.value < 5) {
        step.value++
    }
}

function prevStep() {
    if (step.value > 1) step.value--
}

async function testConnection() {
    testing.value = true
    testResult.value = null
    try {
        const response = await fetch('/install/check-database', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                host: dbForm.db_host,
                port: dbForm.db_port,
                database: dbForm.db_database,
                username: dbForm.db_username,
                password: dbForm.db_password,
            }),
        })
        const data = await response.json()
        testResult.value = data
        if (data.success) {
            step.value = 3
        }
    } catch (e: any) {
        testResult.value = { success: false, message: 'Erro ao testar a conexão.' }
    } finally {
        testing.value = false
    }
}

function submit() {
    installing.value = true
    dbForm
        .transform((data) => ({
            ...data,
            ...stationForm.data(),
            ...adminForm.data(),
        }))
        .post('/install', {
            onError: () => {
                installing.value = false
            },
            onFinish: () => {
                installing.value = false
            },
        })
}

const timezones = [
    'America/Sao_Paulo',
    'America/Manaus',
    'America/Santarem',
    'America/Recife',
    'America/Bahia',
    'America/Cuiaba',
    'America/Campo_Grande',
    'America/Noronha',
    'America/Porto_Velho',
    'America/Boa_Vista',
    'America/Rio_Branco',
    'America/Maceio',
    'America/Araguaina',
    'America/Belem',
    'America/Fortaleza',
    'America/Niteroi',
    'America/Salvador',
    'America/Sao_Paulo',
    'America/New_York',
    'Europe/London',
    'Europe/Lisbon',
]
</script>

<template>
    <div class="min-h-screen bg-gray-100 py-10 px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Logo / título -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gray-900 text-white text-2xl font-black mb-3">📻</div>
                <h1 class="text-2xl font-bold text-gray-900">RadioCMS</h1>
                <p class="text-gray-500 text-sm">Instalação da plataforma para emissoras de rádio</p>
            </div>

            <!-- Stepper -->
            <div class="flex items-center justify-center gap-2 mb-8">
                <template v-for="s in 5" :key="s">
                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-colors"
                        :class="s === step ? 'bg-gray-900 text-white' : s < step ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-500'"
                    >
                        <span v-if="s < step">✓</span>
                        <span v-else>{{ s }}</span>
                    </div>
                    <div v-if="s < 5" class="w-8 h-0.5" :class="s < step ? 'bg-emerald-500' : 'bg-gray-200'"></div>
                </template>
            </div>

            <!-- Etapa 1: Requisitos -->
            <div v-if="step === 1" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold mb-1">Verificação do servidor</h2>
                <p class="text-sm text-gray-500 mb-6">Verificando requisitos e permissões do servidor.</p>

                <div class="space-y-2 mb-6">
                    <div v-for="req in requirements" :key="req.name" class="flex items-center justify-between p-3 rounded-lg" :class="req.passed ? 'bg-emerald-50' : 'bg-red-50'">
                        <span class="text-sm font-medium" :class="req.passed ? 'text-emerald-800' : 'text-red-800'">{{ req.name }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">{{ req.current }}</span>
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" :class="req.passed ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'">
                                {{ req.passed ? '✓' : '✕' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div v-for="perm in permissions" :key="perm.name" class="flex items-center justify-between p-3 rounded-lg" :class="perm.writable ? 'bg-emerald-50' : 'bg-red-50'">
                        <div>
                            <span class="text-sm font-medium" :class="perm.writable ? 'text-emerald-800' : 'text-red-800'">{{ perm.name }}</span>
                            <p class="text-xs text-gray-500 font-mono">{{ perm.path }}</p>
                        </div>
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" :class="perm.writable ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'">
                            {{ perm.writable ? '✓' : '✕' }}
                        </span>
                    </div>
                </div>

                <button
                    type="button"
                    class="mt-6 w-full py-3 rounded-xl font-semibold text-white transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="allRequirementsPass && allPermissionsWritable ? 'bg-gray-900 hover:bg-gray-800' : 'bg-gray-300'"
                    :disabled="!allRequirementsPass || !allPermissionsWritable"
                    @click="nextStep"
                >
                    Continuar
                </button>
            </div>

            <!-- Etapa 2: Banco de dados -->
            <div v-else-if="step === 2" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold mb-1">Conexão com o banco de dados</h2>
                <p class="text-sm text-gray-500 mb-6">Informe os dados do MySQL da sua hospedagem.</p>

                <form @submit.prevent="testConnection" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Host</label>
                        <input v-model="dbForm.db_host" type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" placeholder="localhost" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Porta</label>
                            <input v-model="dbForm.db_port" type="number" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Banco de dados</label>
                            <input v-model="dbForm.db_database" type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" placeholder="radio_cms" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Usuário</label>
                        <input v-model="dbForm.db_username" type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" placeholder="root" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Senha</label>
                        <input v-model="dbForm.db_password" type="password" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                    </div>

                    <div v-if="testResult" class="p-3 rounded-lg text-sm" :class="testResult.success ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-800'">
                        {{ testResult.message }}
                    </div>

                    <div class="flex gap-3">
                        <button type="button" class="flex-1 py-3 rounded-xl font-semibold border border-gray-300 text-gray-700" @click="prevStep">Voltar</button>
                        <button type="submit" class="flex-1 py-3 rounded-xl font-semibold text-white bg-gray-900 hover:bg-gray-800" :disabled="testing">
                            {{ testing ? 'Testando...' : 'Testar conexão' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Etapa 3: Informações da rádio -->
            <div v-else-if="step === 3" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold mb-1">Informações da rádio</h2>
                <p class="text-sm text-gray-500 mb-6">Dados principais da sua emissora.</p>

                <form @submit.prevent="step = 4" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nome da rádio *</label>
                        <input v-model="stationForm.station_name" type="text" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" placeholder="Rádio Exemplo FM" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Frequência</label>
                            <input v-model="stationForm.frequency" type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" placeholder="94.5 FM" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Slogan</label>
                            <input v-model="stationForm.slogan" type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" placeholder="A rádio da sua vida" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Cidade</label>
                            <input v-model="stationForm.city" type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Estado</label>
                            <input v-model="stationForm.state" type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">País</label>
                            <input v-model="stationForm.country" type="text" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Fuso horário</label>
                            <select v-model="stationForm.timezone" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm">
                                <option v-for="tz in timezones" :key="tz" :value="tz">{{ tz }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">E-mail de contato *</label>
                        <input v-model="stationForm.email" type="email" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                    </div>

                    <div class="flex gap-3">
                        <button type="button" class="flex-1 py-3 rounded-xl font-semibold border border-gray-300 text-gray-700" @click="prevStep">Voltar</button>
                        <button type="submit" class="flex-1 py-3 rounded-xl font-semibold text-white bg-gray-900 hover:bg-gray-800">Continuar</button>
                    </div>
                </form>
            </div>

            <!-- Etapa 4: Administrador -->
            <div v-else-if="step === 4" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold mb-1">Administrador inicial</h2>
                <p class="text-sm text-gray-500 mb-6">Crie a conta do administrador do sistema.</p>

                <form @submit.prevent="step = 5" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nome completo *</label>
                        <input v-model="adminForm.admin_name" type="text" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">E-mail *</label>
                        <input v-model="adminForm.admin_email" type="email" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Senha *</label>
                        <input v-model="adminForm.admin_password" type="password" required minlength="8" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                        <p class="text-xs text-gray-400 mt-1">Mínimo de 8 caracteres.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Confirmar senha *</label>
                        <input v-model="adminForm.admin_password_confirmation" type="password" required class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm" />
                    </div>

                    <div class="flex gap-3">
                        <button type="button" class="flex-1 py-3 rounded-xl font-semibold border border-gray-300 text-gray-700" @click="prevStep">Voltar</button>
                        <button type="submit" class="flex-1 py-3 rounded-xl font-semibold text-white bg-gray-900 hover:bg-gray-800">Revisar e instalar</button>
                    </div>
                </form>
            </div>

            <!-- Etapa 5: Revisão e instalação -->
            <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold mb-1">Revisão</h2>
                <p class="text-sm text-gray-500 mb-6">Confira os dados antes de instalar.</p>

                <div class="bg-gray-50 rounded-lg p-4 mb-6 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Rádio</span><span class="font-medium">{{ stationForm.station_name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Frequência</span><span class="font-medium">{{ stationForm.frequency || '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Banco</span><span class="font-medium">{{ dbForm.db_database }} @ {{ dbForm.db_host }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Administrador</span><span class="font-medium">{{ adminForm.admin_name }} ({{ adminForm.admin_email }})</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Fuso</span><span class="font-medium">{{ stationForm.timezone }}</span></div>
                </div>

                <div v-if="$page.props.flash?.error" class="p-3 mb-4 rounded-lg bg-red-50 text-red-800 text-sm">
                    {{ $page.props.flash.error }}
                </div>

                <button
                    type="button"
                    class="w-full py-3 rounded-xl font-semibold text-white bg-gray-900 hover:bg-gray-800 disabled:opacity-60"
                    :disabled="installing"
                    @click="submit"
                >
                    {{ installing ? 'Instalando... isso pode levar alguns minutos.' : 'Instalar plataforma' }}
                </button>
            </div>
        </div>
    </div>
</template>