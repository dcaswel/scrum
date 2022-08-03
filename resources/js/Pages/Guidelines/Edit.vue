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
                        <li v-for="score in scores">
                            <div class="flex justify-between">
                                <h2 class="text-gray-700 font-bold text-2xl mr-2 flex-1">{{score}}</h2>
                                <button class="text-gray-400 hover:cursor-pointer">
                                    <Pencil />
                                </button>
                            </div>
                            <Guideline :guideline="getGuideline(score)" class="ml-10 mt-2"/>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout";
import Pencil from "@/Components/icons/pencil";
import Guideline from "@/Pages/Guidelines/Partials/Guideline"

const props = defineProps({
    guidelines: Array
});

function getGuideline(score) {
    return props.guidelines.find(guideline => guideline.score == score);
}

const scores = [0.5, 1, 2, 3, 5, 8, 13, 21];
</script>

<style scoped>

</style>
