<script setup lang="ts">

import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg 
        xmlns="http://www.w3.org/2000/svg" 
        viewBox="0 0 24 24" 
        fill="currentColor" 
        :class="className" 
        v-bind="$attrs"
    >
        <path d="M12 11c1.66 0 3-1.34 3-3S13.66 5 12 5 9 6.34 9 8s1.34 3 3 3z" />
        <path d="M12 13c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z" />
        <path d="M5 10c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" opacity="0.7" />
        <path d="M5 12c-1.55 0-4.67.78-4.67 2.33V17h3.2v-1.63c0-.8.15-1.55.45-2.22C3.3 12.24 4.14 12 5 12z" opacity="0.7" />
        <path d="M19 10c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" opacity="0.7" />
        <path d="M19 12c.86 0 1.7.24 2.35.59.3.67.45 1.42.45 2.22V17h3.2v-2.67C23.67 12.78 20.55 12 19 12z" opacity="0.7" />
    </svg>
</template>
