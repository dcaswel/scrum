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
                            <Guideline :guideline="getGuideline(score)" :score="score" :key="'show' + score" class="mt-2"/>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div v-if="$page.props.flash.status">
            <Alert v-if="$page.props.flash.status === 'guideline-created'" :ttl="2">
                <template #primary>Guideline Created!</template>
            </Alert>
            <Alert v-if="$page.props.flash.status === 'guideline-updated'" :ttl="2">
                <template #primary>Guideline Updated!</template>
            </Alert>
        </div>
    </app-layout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout";
import Guideline from "@/Pages/Guidelines/Partials/Guideline"
import Alert from "@/Components/Alert";

const props = defineProps({
    guidelines: Array
});

function getGuideline(score) {
    return props.guidelines.find(guideline => guideline.score == score) ?? {
        description: '',
        bullets: [],
        tickets: []
    };
}

const scores = [0.5, 1, 2, 3, 5, 8, 13, 21];
</script>

<style scoped>

</style>
