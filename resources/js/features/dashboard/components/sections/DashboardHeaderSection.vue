<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { ChevronDown, CreditCard, FileText, IdCard, Key, Plus, Search } from 'lucide-vue-next';

const props = defineProps({
    searchQuery: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['update:searchQuery', 'add-item']);

const showAddDropdown = ref(false);
const addMenuRef = ref(null);

const toggleAddDropdown = () => {
    showAddDropdown.value = !showAddDropdown.value;
};

const closeAddDropdown = () => {
    showAddDropdown.value = false;
};

const openAddItem = (type) => {
    closeAddDropdown();
    emit('add-item', type);
};

const updateSearchQuery = (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) {
        return;
    }

    emit('update:searchQuery', target.value);
};

const handleClickOutsideAddMenu = (event) => {
    const target = event.target;
    if (!(target instanceof Node)) {
        return;
    }

    if (addMenuRef.value && !addMenuRef.value.contains(target)) {
        closeAddDropdown();
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutsideAddMenu);
});

onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutsideAddMenu);
});
</script>

<template>
    <header class="sticky top-0 z-10 border-b border-outline-variant bg-surface">
        <div class="px-6 py-5 md:px-8">
            <div class="mb-5 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-on-surface">My Vault</h1>
                    <p class="text-on-surface-variant">Manage your passwords and secure information</p>
                </div>
                <div ref="addMenuRef" class="relative">
                    <button
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white shadow-sm transition-all hover:bg-blue-700"
                        @click="toggleAddDropdown"
                    >
                        <Plus class="h-5 w-5" />
                        Add Item
                        <ChevronDown class="h-4 w-4" />
                    </button>

                    <Transition
                        enter-active-class="transition duration-150 ease-out"
                        enter-from-class="translate-y-1 opacity-0"
                        enter-to-class="translate-y-0 opacity-100"
                        leave-active-class="transition duration-100 ease-in"
                        leave-from-class="translate-y-0 opacity-100"
                        leave-to-class="translate-y-1 opacity-0"
                    >
                        <div
                            v-if="showAddDropdown"
                            class="absolute right-0 top-full z-40 mt-2 w-64 overflow-hidden rounded-xl border border-outline-variant bg-surface shadow-lg"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-surface-container-low"
                                @click="openAddItem('login')"
                            >
                                <Key class="h-4 w-4 text-primary" />
                                <span class="font-medium text-on-surface">Login</span>
                            </button>
                            <button
                                type="button"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-surface-container-low"
                                @click="openAddItem('card')"
                            >
                                <CreditCard class="h-4 w-4 text-primary" />
                                <span class="font-medium text-on-surface">Credit Card</span>
                            </button>
                            <button
                                type="button"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-surface-container-low"
                                @click="openAddItem('note')"
                            >
                                <FileText class="h-4 w-4 text-primary" />
                                <span class="font-medium text-on-surface">Note</span>
                            </button>
                            <button
                                type="button"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-surface-container-low"
                                @click="openAddItem('identity')"
                            >
                                <IdCard class="h-4 w-4 text-primary" />
                                <span class="font-medium text-on-surface">Identity</span>
                            </button>
                        </div>
                    </Transition>
                </div>
            </div>

            <div class="relative">
                <Search class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-on-surface-variant" />
                <input
                    :value="searchQuery"
                    type="text"
                    placeholder="Search passwords, usernames, or websites..."
                    class="w-full rounded-lg border border-outline-variant bg-surface py-3 pl-12 pr-4 text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                    style="font-family: 'DM Sans', sans-serif"
                    @input="updateSearchQuery"
                >
            </div>
        </div>
    </header>
</template>

