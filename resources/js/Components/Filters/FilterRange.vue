<template>
    <a @click="$emit('changeRange', value)" 
        :class="[
            currentValue === value ? 'btn-filter-selected' : null, 
            side ? 'rounded-' + side + '-md' : null
        ]" 
        class="btn-filter" href="#" >
        {{ value }}
    </a>
</template>

<style scoped>
    .btn-filter {
        padding: 5px 8px;
        background: #efefef;
        color: #303952;
        margin: 0 4px;
    }

    .btn-filter-selected {
        background: #f3a683;
        font-weight: bold;
    }
</style>

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
