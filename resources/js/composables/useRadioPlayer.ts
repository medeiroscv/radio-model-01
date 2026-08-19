import { ref, computed, onMounted, onUnmounted } from 'vue'

interface StreamMetadata {
    artist?: string | null
    title?: string | null
    album?: string | null
    cover?: string | null
    program?: string | null
    presenter?: string | null
}

interface StreamStatus {
    online: boolean
    enabled: boolean
    provider?: string
    stream_url?: string | null
    stream_url_alt?: string | null
    metadata?: StreamMetadata
    listeners?: number | null
    now_playing?: string | null
    on_air?: any
    up_next?: any[]
    checked_at?: string
    message?: string
}

let audio: HTMLAudioElement | null = null
let pollTimer: number | null = null

const isPlaying = ref(false)
const isLoading = ref(false)
const error = ref<string | null>(null)
const volume = ref(0.8)
const isMuted = ref(false)
const currentStatus = ref<StreamStatus | null>(null)
const currentTrack = ref<StreamMetadata>({})

const activeUrl = computed(() => {
    const status = currentStatus.value
    if (!status?.stream_url) return null
    return status.stream_url
})

const nowPlayingLabel = computed(() => {
    const meta = currentTrack.value
    if (meta.artist && meta.title) return `${meta.artist} - ${meta.title}`
    if (meta.title) return meta.title
    return currentStatus.value?.now_playing || null
})

const isOnline = computed(() => currentStatus.value?.online ?? false)

function ensureAudio() {
    if (!audio) {
        const el = document.createElement('audio')
        el.preload = 'none'
        el.volume = volume.value
        el.muted = isMuted.value
        document.body.appendChild(el)
        audio = el

        el.addEventListener('playing', () => {
            isLoading.value = false
            error.value = null
        })
        el.addEventListener('waiting', () => {
            isLoading.value = true
        })
        el.addEventListener('error', () => {
            isLoading.value = false
            isPlaying.value = false
            error.value = 'Não foi possível conectar ao streaming.'
        })
        el.addEventListener('timeupdate', () => {
            if (el.readyState >= 3 && isPlaying.value === false && el.currentTime > 0) {
                isPlaying.value = true
            }
        })
    }
    return audio
}

async function play() {
    if (!activeUrl.value) {
        error.value = 'Streaming não configurado.'
        return
    }

    const el = ensureAudio()
    isLoading.value = true
    error.value = null

    try {
        el.src = activeUrl.value
        await el.play()
        isPlaying.value = true
        isLoading.value = false
    } catch (e: any) {
        error.value = 'Não foi possível iniciar a reprodução.'
        isLoading.value = false
    }
}

function pause() {
    if (audio) {
        audio.pause()
        isPlaying.value = false
    }
}

function toggle() {
    if (isPlaying.value) {
        pause()
    } else {
        play()
    }
}

function setVolume(value: number) {
    volume.value = value
    if (audio) {
        audio.volume = value
    }
}

function toggleMute() {
    isMuted.value = !isMuted.value
    if (audio) {
        audio.muted = isMuted.value
    }
}

function updateStatus(status: StreamStatus) {
    currentStatus.value = status
    if (status.metadata) {
        currentTrack.value = status.metadata
    }
}

async function refreshStatus() {
    try {
        const response = await fetch('/api/stream/status', { headers: { Accept: 'application/json' } })
        if (response.ok) {
            const data = await response.json()
            updateStatus(data)
        }
    } catch (e) {
        // Silencioso - o status antigo permanece
    }
}

function startPolling(interval = 30000) {
    stopPolling()
    pollTimer = window.setInterval(refreshStatus, interval)
}

function stopPolling() {
    if (pollTimer) {
        clearInterval(pollTimer)
        pollTimer = null
    }
}

export function useRadioPlayer(initialStatus: StreamStatus | null = null) {
    if (initialStatus && !currentStatus.value) {
        updateStatus(initialStatus)
    }

    onMounted(() => {
        refreshStatus()
        startPolling(30000)
    })

    onUnmounted(() => {
        stopPolling()
    })

    return {
        isPlaying,
        isLoading,
        error,
        volume,
        isMuted,
        currentStatus,
        currentTrack,
        activeUrl,
        nowPlayingLabel,
        isOnline,
        play,
        pause,
        toggle,
        setVolume,
        toggleMute,
        updateStatus,
        refreshStatus,
        startPolling,
        stopPolling,
    }
}