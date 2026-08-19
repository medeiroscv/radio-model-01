<script setup lang="ts">
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '../../../Layouts/AdminLayout.vue'

interface Category {
    id: number
    name: string
    color?: string
}

interface Tag {
    id: number
    name: string
}

const props = defineProps<{
    news?: {
        id: number
        title: string
        subtitle?: string
        summary?: string
        content?: string
        featured_image?: string | null
        gallery?: string[] | null
        is_featured?: boolean
        is_published?: boolean
        published_at?: string
        news_category_id?: number | null
        tags?: Array<{ id: number; name: string }>
        meta_title?: string | null
        meta_description?: string | null
        canonical_url?: string | null
    }
    categories: Category[]
    tags: Tag[]
}>()

const form = useForm({
    news_category_id: props.news?.news_category_id ?? null,
    title: props.news?.title ?? '',
    subtitle: props.news?.subtitle ?? '',
    summary: props.news?.summary ?? '',
    content: props.news?.content ?? '',
    featured_image: props.news?.featured_image ?? '',
    gallery: props.news?.gallery ?? [],
    is_featured: props.news?.is_featured ?? false,
    is_published: props.news?.is_published ?? false,
    published_at: props.news?.published_at ?? '',
    tag_ids: props.news?.tags?.map((t) => t.id) ?? [],
    meta_title: props.news?.meta_title ?? '',
    meta_description: props.news?.meta_description ?? '',
    canonical_url: props.news?.canonical_url ?? '',
})

const isEdit = !!props.news

function submit() {
    if (isEdit) {
        form.put(`/admin/news/${props.news!.id}`)
    } else {
        form.post('/admin/news')
    }
}

const newGalleryItem = ref('')

function addGalleryItem() {
    if (!newGalleryItem.value.trim()) return
    form.gallery.push(newGalleryItem.value.trim())
    newGalleryItem.value = ''
}

function removeGalleryItem(index: number) {
    form.gallery.splice(index, 1)
}
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ isEdit ? 'Editar notícia' : 'Nova notícia' }}</h1>
                <p class="text-sm text-[var(--muted)]">Preencha os dados da publicação</p>
            </div>
            <Link href="/admin/news" class="btn-outline !px-4 !py-2 text-sm">Voltar</Link>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 max-w-5xl">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Título *</label>
                    <input v-model="form.title" type="text" required class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subtítulo</label>
                    <input v-model="form.subtitle" type="text" class="input-app w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Resumo</label>
                    <textarea v-model="form.summary" rows="3" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Conteúdo</label>
                    <textarea v-model="form.content" rows="14" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm font-mono" placeholder="HTML / conteúdo da matéria..."></textarea>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-4">
                    <h3 class="font-semibold text-sm">Publicação</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1">Categoria</label>
                        <select v-model="form.news_category_id" class="input-app w-full">
                            <option :value="null">Sem categoria</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tags</label>
                        <div class="flex flex-wrap gap-2">
                            <label v-for="tag in tags" :key="tag.id" class="flex items-center gap-2 text-sm cursor-pointer">
                                <input v-model="form.tag_ids" type="checkbox" :value="tag.id" class="rounded" />
                                {{ tag.name }}
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Data de publicação</label>
                        <input v-model="form.published_at" type="datetime-local" class="input-app w-full" />
                    </div>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_featured" type="checkbox" class="rounded" />
                        Notícia em destaque
                    </label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input v-model="form.is_published" type="checkbox" class="rounded" />
                        Publicar
                    </label>
                </div>

                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-4">
                    <h3 class="font-semibold text-sm">Imagem de capa</h3>
                    <input v-model="form.featured_image" type="url" placeholder="https://..." class="input-app w-full" />
                    <img v-if="form.featured_image" :src="form.featured_image" :alt="form.title" class="rounded-xl w-full h-40 object-cover" />
                </div>

                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-4">
                    <h3 class="font-semibold text-sm">Galeria de imagens</h3>
                    <div class="flex gap-2">
                        <input v-model="newGalleryItem" type="url" placeholder="https://..." class="input-app flex-1" />
                        <button type="button" class="btn-outline !px-4 !py-2 text-sm" @click="addGalleryItem">Adicionar</button>
                    </div>
                    <div v-if="form.gallery.length" class="space-y-2">
                        <div v-for="(img, index) in form.gallery" :key="index" class="flex items-center gap-3 rounded-xl border border-[var(--border)] p-2">
                            <img :src="img" alt="" class="w-14 h-10 rounded-md object-cover shrink-0" />
                            <span class="text-xs text-[var(--muted)] truncate flex-1">{{ img }}</span>
                            <button type="button" class="text-red-500 hover:underline text-sm" @click="removeGalleryItem(index)">Remover</button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-[var(--border)] bg-[var(--background)] p-4 space-y-4">
                    <h3 class="font-semibold text-sm">SEO</h3>
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta título</label>
                        <input v-model="form.meta_title" type="text" class="input-app w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta descrição</label>
                        <textarea v-model="form.meta_description" rows="2" class="w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">URL canônica</label>
                        <input v-model="form.canonical_url" type="url" placeholder="https://..." class="input-app w-full" />
                    </div>
                </div>

                <button type="submit" class="btn-accent w-full" :disabled="form.processing">
                    {{ form.processing ? 'Salvando...' : (isEdit ? 'Salvar alterações' : 'Criar notícia') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>