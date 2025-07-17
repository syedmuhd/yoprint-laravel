<template>
    <div class="p-6 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-4">Upload CSV File</h2>

        <form @submit.prevent="uploadFile" enctype="multipart/form-data" class="mb-6 flex gap-4 items-center">
            <input type="file" @change="onFileChange" class="border border-gray-300 p-2 rounded" accept=".csv">
            <button type="submit" :disabled="isUploading || !selectedFile"
                class="px-4 py-2 bg-red-500 text-white rounded disabled:opacity-50">
                <span v-if="isUploading">Uploading...</span>
                <span v-else>Upload</span>
            </button>
        </form>

        <h3 class="text-xl font-semibold mb-2">Uploaded Files</h3>

        <table class="w-full border-collapse text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="border px-3 py-2 text-left">Time</th>
            <th class="border px-3 py-2 text-left">File Name</th>
            <th class="border px-3 py-2 text-left">Status</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="upload in uploads" :key="upload.id" class="border-t">
            <td class="border px-3 py-2">{{ upload.created_at_formatted }}</td>
            <td class="border px-3 py-2">{{ upload.filename }}</td>
            <td class="border px-3 py-2">
                <span v-if="upload.status === 'Pending' || upload.status === 'Processing'" class="flex items-center gap-1 text-gray-600">
                    <svg class="w-4 h-4 animate-spin text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    {{ upload.status }}
                </span>

                <span v-else-if="upload.status === 'Completed'" class="flex items-center gap-1 text-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Completed
                </span>

                <span v-else-if="upload.status === 'Failed'" class="flex items-center gap-1 text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Failed
                </span>

                <span v-else>
                    {{ upload.status }}
                </span>
            </td>
        </tr>

        <tr v-if="uploads.length === 0">
            <td colspan="3" class="text-center text-gray-400 py-4">No uploads yet.</td>
        </tr>
    </tbody>
</table>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const uploads = ref([])
const selectedFile = ref(null)
const isUploading = ref(false)

const fetchUploads = async () => {
    const res = await fetch('/uploads')
    uploads.value = await res.json()
}

const onFileChange = (event) => {
    selectedFile.value = event.target.files[0]
}

const uploadFile = async () => {
    if (!selectedFile.value) return

    isUploading.value = true
    const formData = new FormData()
    formData.append('file', selectedFile.value)

    try {
        await fetch('/upload', {
            method: 'POST',
            body: formData,
        })

        selectedFile.value = null
        await fetchUploads()
    } catch (err) {
        console.error('Upload failed', err)
        alert('Upload failed')
    } finally {
        isUploading.value = false
    }
}

const listenToBroadcast = () => {
    window.Echo.channel('uploads')
        .listen('UploadStatusUpdated', (e) => {
            const index = uploads.value.findIndex(u => u.id === e.upload.id)
            if (index !== -1) {
                uploads.value[index] = e.upload
            } else {
                uploads.value.unshift(e.upload)
            }
        })
}

onMounted(() => {
    fetchUploads()
    listenToBroadcast()
})
</script>

<style scoped>
table th,
table td {
    border: 1px solid #ccc;
}
</style>
