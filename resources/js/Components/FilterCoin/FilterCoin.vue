<template>
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
    },
    setup() {
        return {}
    },
})
</script>
