<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();
const props = defineProps({
    role: {
        type: Object,
        default: null
    },
    permissions: {
        type: Object,
        required: true
    },
    availablePermissions: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    permissions: [] as string[]
});

watch(() => props.role, (newRole) => {
    if (newRole) {
        form.name = newRole.name;
        form.permissions = [...newRole.permissions];
    }
}, { immediate: true });

const open = () => {
    (document.getElementById('edit_role_modal') as HTMLDialogElement).showModal();
};

const close = () => {
    (document.getElementById('edit_role_modal') as HTMLDialogElement).close();
    emit('close');
};

const submit = () => {
    form.put(`/roles-permissions/${props.role.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Role updated successfully');
            close();
        },
        onError: () => {
            toast.error('Failed to update role');
        }
    });
};

defineExpose({ open });
</script>

<template>
    <dialog id="edit_role_modal" class="modal">
        <div class="modal-box w-11/12 max-w-5xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="close">✕</button>
            </form>
            <h3 class="font-bold text-lg mb-4">Edit Role: {{ role?.name }}</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                    <input v-model="form.name" type="text" placeholder="Enter role name" class="input input-bordered w-full"
                        :class="{ 'input-error': form.errors.name }">
                    <span class="text-xs text-red-500" v-if="form.errors.name">{{ form.errors.name }}</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(groupPermissions, resource) in permissions" :key="resource"
                            class="border rounded-lg p-3">
                            <h4 class="font-medium mb-2 capitalize">{{ resource }}</h4>
                            <div class="space-y-2">
                                <label v-for="permission in groupPermissions" :key="permission.id"
                                    class="flex items-center gap-2">
                                    <input type="checkbox" v-model="form.permissions" :value="permission.name"
                                        class="checkbox checkbox-sm">
                                    <span class="text-sm">{{ permission.name.split('.')[1] }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <span class="text-xs text-red-500" v-if="form.errors.permissions">{{ form.errors.permissions }}</span>
                </div>

                <div class="modal-action">
                    <button class="btn btn-primary" @click.prevent="submit" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner"></span>
                        Update Role
                    </button>
                    <button class="btn" @click="close">Cancel</button>
                </div>
            </div>
        </div>
    </dialog>
</template>
