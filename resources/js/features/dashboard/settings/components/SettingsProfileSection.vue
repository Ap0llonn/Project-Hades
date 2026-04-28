<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <label class="block">
            <span class="mb-2 block text-sm font-medium text-on-surface-variant">Full Name</span>
            <input type="text" value="Sam Doe" class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
        </label>
        <label class="block">
            <span class="mb-2 block text-sm font-medium text-on-surface-variant">Email</span>
            <input type="email" value="sam@vaultguardian.test" class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
        </label>
        <label class="block md:col-span-2">
            <span class="mb-2 block text-sm font-medium text-on-surface-variant">Timezone</span>
            <select class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
                <option>America/Toronto</option>
                <option>America/New_York</option>
                <option>Europe/Paris</option>
            </select>
        </label>
    </div>
</template>
