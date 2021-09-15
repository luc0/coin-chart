<template>
    <a @click="$emit('changeCoin', value)" 
        :class="[
            currentValue === value ? 'bg-indigo-50 border-indigo-500' : null, 
            side ? 'rounded-' + side + '-md' : null
        ]" 
        class="z-10 relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" href="#" >
        {{ value }}
    </a>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        value: String,
        currentValue: String,
        side: String
    },
    setup() {
        return {}
    },
})
</script>
