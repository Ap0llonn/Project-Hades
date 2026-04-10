<script setup>
defineProps({
    id: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    autocomplete: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    helper: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="space-y-2">
        <label :for="id" class="block text-sm font-semibold text-on-surface">
            {{ label }}
        </label>
        <input
            :id="id"
            :name="name || id"
            :type="type"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :required="required"
            class="w-full rounded-xl border border-outline-variant/70 bg-surface-container-lowest px-4 py-3 text-sm text-on-surface shadow-sm outline-none transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20"
        >
        <p v-if="helper" class="text-xs text-on-surface-variant">
            {{ helper }}
        </p>
    </div>
</template>
