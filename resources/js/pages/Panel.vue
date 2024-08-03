<script setup>
import { defineProps } from "vue"
const props = defineProps({
    title: String,
    description: String,
    fields: Array,
    errors: Object,
})
</script>
<template>
    <div class="panel">
        <div class="panel-title">
            <h2 class="text-base font-semibold leading-7">
                {{ title }}
            </h2>
            <p class="mt-1 text-sm leading-6 font-thin">
                {{ description }}
            </p>
        </div>
        <Card class="panel-content">
            <component v-for="(field, index) in fields" :index="index" :key="index" :is="`form-${field.component}`"
                :field="field" :errors="errors" />
        </Card>
    </div>
</template>
