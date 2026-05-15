<script setup>
const props = defineProps({
    firstName: {
        type: String,
        default: '',
    },
    lastName: {
        type: String,
        default: '',
    },
    email: {
        type: String,
        default: '',
    },
    statusMessage: {
        type: String,
        default: '',
    },
    errorMessage: {
        type: String,
        default: '',
    },
});

const emit = defineEmits([
    'update:firstName',
    'update:lastName',
]);

const onFirstNameInput = (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) {
        return;
    }

    emit('update:firstName', target.value);
};

const onLastNameInput = (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) {
        return;
    }

    emit('update:lastName', target.value);
};
</script>

<template>
    <div class="space-y-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <label class="block">
                <span class="mb-2 block text-sm font-medium text-on-surface-variant">First Name</span>
                <input
                    type="text"
                    :value="props.firstName"
                    class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none"
                    autocomplete="given-name"
                    @input="onFirstNameInput"
                >
            </label>

            <label class="block">
                <span class="mb-2 block text-sm font-medium text-on-surface-variant">Last Name</span>
                <input
                    type="text"
                    :value="props.lastName"
                    class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none"
                    autocomplete="family-name"
                    @input="onLastNameInput"
                >
            </label>
        </div>

        <label class="block">
            <span class="mb-2 block text-sm font-medium text-on-surface-variant">Email</span>
            <input
                type="email"
                :value="props.email"
                readonly
                class="w-full cursor-not-allowed rounded-lg border border-outline-variant bg-surface-container-low px-4 py-2.5 text-on-surface-variant focus:outline-none"
            >
            <span class="mt-2 block text-xs text-on-surface-variant">Email cannot be changed here.</span>
        </label>

        <p v-if="props.statusMessage" class="text-sm text-primary">{{ props.statusMessage }}</p>
        <p v-if="props.errorMessage" class="text-sm text-red-600">{{ props.errorMessage }}</p>
    </div>
</template>
