<script setup lang="ts">
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useRadioPlayer } from '../composables/useRadioPlayer'

const page = usePage()
const station = computed(() => page.props.station as any)
const initialStatus = computed(() => page.props.streamStatus as any)

const player = useRadioPlayer(initialStatus.value)

defineEmits(['toggle-expand'])
</script>

<template>
    <div class="w-full bg-[var(--surface)] border border-[var(--border)] rounded-xl px-4 py-3 flex items-center gap-4 overflow-hidden">
        <!-- Estado Ao Vivo -->
        <div class="hidden md:flex items-center gap-2 shrink-0">
            <span class="relative flex h-2.5 w-2.5">
                <span v-if="player.isOnline.value" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="player.isOnline.value ? 'bg-red-500' : 'bg-gray-400'"></span>
            </span>
            <span class="text-xs font-bold tracking-widest uppercase text-[var(--muted)]">
                {{ player.isOnline.value ? 'Ao vivo' : 'Offline' }}
            </span>
        </div>

        <!-- Botão Play/Pause -->
        <button
            type="button"
            class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-white transition-transform hover:scale-105"
            :class="player.isOnline.value ? 'bg-[var(--accent)]' : 'bg-[var(--primary)]'"
            @click="player.toggle()"
            :aria-label="player.isPlaying.value ? 'Pausar' : 'Ouvir ao vivo'"
        >
            <svg v-if="player.isLoading.value" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else-if="player.isPlaying.value" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M6.75 5.25a.75.75 0 01.75-.75H9a.75.75 0 01.75.75v13.5a.75.75 0 01-.75.75H7.5a.75.75 0 01-.75-.75V5.25zm7.5 0a.75.75 0 01.75-.75H16.5a.75.75 0 01.75.75v13.5a.75.75 0 01-.75.75h-1.25a.75.75 0 01-.75-.75V5.25z" clip-rule="evenodd" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd" />
            </svg>
        </button>

        <!-- Informações da rádio -->
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <span class="font-bold text-sm truncate">{{ station?.name || 'Rádio' }}</span>
                <span v-if="station?.frequency" class="text-[10px] font-bold bg-[var(--accent)] text-white rounded-full px-2 py-0.5 shrink-0">{{ station.frequency }}</span>
            </div>
            <div class="text-xs text-[var(--muted)] truncate">
                <span v-if="player.nowPlayingLabel.value">{{ player.nowPlayingLabel.value }}</span>
                <span v-else-if="player.currentStatus.value?.on_air">
                    {{ player.currentStatus.value.on_air.program }} <template v-if="player.currentStatus.value.on_air.presenter">· {{ player.currentStatus.value.on_air.presenter }}</template>
                </span>
                <span v-else>{{ station?.slogan || 'Ouvir ao vivo' }}</span>
            </div>
        </div>

        <!-- Volume (desktop) -->
        <div class="hidden md:flex items-center gap-2 shrink-0">
            <button type="button" class="p-1 text-[var(--muted)] hover:text-[var(--text)]" @click="player.toggleMute()" :aria-label="player.isMuted.value ? 'Ativar som' : 'Silenciar'">
                <svg v-if="player.isMuted.value || player.volume.value === 0" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 9l-6 6M16 9l6 6" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072M17.95 6a9 9 0 010 12M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
            </button>
            <input
                type="range"
                min="0"
                max="1"
                step="0.05"
                :value="player.volume.value"
                @input="player.setVolume(parseFloat(($event.target as HTMLInputElement).value))"
                class="w-20 accent-[var(--accent)]"
                aria-label="Volume"
            />
        </div>

        <!-- Mensagem de erro -->
        <div v-if="player.error.value" class="absolute inset-0 flex items-center justify-center bg-black/60 text-white text-xs px-4 rounded-xl">
            {{ player.error.value }}
        </div>
    </div>
</template>