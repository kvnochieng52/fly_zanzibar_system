<template>
    <div class="application-logo d-flex align-items-center">
        <img
            src="/images/logo.png"
            alt="Zanzibar Fly System Logo"
            class="img-fluid"
            :style="{
                maxWidth: size + 'px',
                height: 'auto'
            }"
        />
        <span v-if="showText" class="ml-2 font-weight-bold text-primary" :style="{ fontSize: (size * 0.6) + 'px' }">
            Zanzibar Fly System
        </span>
    </div>
</template>

<script setup>
defineProps({
    size: {
        type: Number,
        default: 32
    },
    showText: {
        type: Boolean,
        default: false
    }
})
</script>
