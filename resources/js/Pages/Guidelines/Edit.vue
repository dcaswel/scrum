<template>
    <app-layout title="Manage Guidelines">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Guidelines
            </h2>
        </template>
        <div class="mx-auto max-w-4xl mt-2">
            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="score in scores" class="py-2">
                            <Guideline :guideline="getGuideline(score)" :key="'show' + score" class="mt-2"/>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout";
import Guideline from "@/Pages/Guidelines/Partials/Guideline"

const props = defineProps({
    guidelines: Array
});

function getGuideline(score) {
    return props.guidelines.find(guideline => guideline.score == score) ?? {};
}

const scores = [0.5, 1, 2, 3, 5, 8, 13, 21];
</script>

<style scoped>

</style>
