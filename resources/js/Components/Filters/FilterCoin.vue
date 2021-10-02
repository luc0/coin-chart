<template>
    <!-- <a @click="$emit('changeCoin', value)" 
        :class="[
            currentValue === value ? 'bg-indigo-50 border-indigo-500' : null, 
            side ? 'rounded-' + side + '-md' : null
        ]" 
        class="z-10 relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50" href="#" >
        {{ value }}
    </a> -->
    <label @click="$emit('changeCoin', value)" class="inline-flex items-center mt-3">
        <input type="checkbox" class="form-checkbox h-5 w-5 text-gray-600" :checked="currentValue.indexOf(value) != -1">
        <span class="ml-2 text-gray-700">
            {{ value }}
        </span>
    </label>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        value: String,
        currentValue: Array,
        // side: String
    },
    setup() {
        return {}
    },
})
</script>
