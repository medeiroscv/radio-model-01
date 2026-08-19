<script setup lang="ts">
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-10">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 text-3xl mb-4">✓</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Instalação concluída!</h1>
            <p class="text-gray-500 text-sm mb-6">
                Sua plataforma foi instalada com sucesso. Agora você pode acessar o painel administrativo para começar a configurar sua rádio.
            </p>
            <div class="space-y-3">
                <a href="/admin" class="block w-full py-3 rounded-xl font-semibold text-white bg-gray-900 hover:bg-gray-800">
                    Acessar painel administrativo
                </a>
                <a href="/" class="block w-full py-3 rounded-xl font-semibold border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Ver site público
                </a>
            </div>
        </div>
    </div>
</template>