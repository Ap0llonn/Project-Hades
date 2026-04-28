<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <label class="block">
            <span class="mb-2 block text-sm font-medium text-on-surface-variant">Auto-lock timeout</span>
            <select class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
                <option>5 minutes</option>
                <option selected>15 minutes</option>
                <option>30 minutes</option>
            </select>
        </label>
        <label class="block">
            <span class="mb-2 block text-sm font-medium text-on-surface-variant">Clipboard clear timeout</span>
            <select class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
                <option>30 seconds</option>
                <option selected>60 seconds</option>
                <option>120 seconds</option>
            </select>
        </label>
    </div>
</template>
