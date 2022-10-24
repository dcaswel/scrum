<template>
    <app-layout title="Manage Guidelines">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manage Guidelines
                </h2>
                <HeaderButton @click="copyDialog = true">Copy Guidelines</HeaderButton>
            </div>
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
        <CenteredDialog :open="copyDialog">
            <div>
                <div class="mt-3 text-center sm:mt-5">
                    <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">Copy Guidelines</DialogTitle>
                    <div class="mt-2">
                        <p class="text-md text-gray-500">This will allow you to copy the guidelines from another team that you have access to.</p>
                        <p class="mt-4 p-2 text-left text-sm text-red-600 bg-red-100">Warning: This will overwrite any Guideline Descriptions. Any existing tickets or bullets will be preserved.</p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6">
                <label for="location" class="block text-sm font-medium text-gray-700">Team</label>
                <select
                    id="location"
                    name="location"
                    v-model="copyForm.team"
                    class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    <option v-for="(name, id) in $page.props.teams" :value="id">{{name}}</option>
                </select>
            </div>
            <div class="mt-5 sm:mt-6 text-right">
                <jet-secondary-button @click="copyDialog = false">Cancel</jet-secondary-button>
                <jet-button @click="submitCopy" class="ml-4">Copy</jet-button>
            </div>
        </CenteredDialog>
    </app-layout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout";
import Guideline from "@/Pages/Guidelines/Partials/Guideline";
import Alert from "@/Components/Alert";
import HeaderButton from "@/Components/HeaderButton";
import {DialogTitle} from "@headlessui/vue";
import CenteredDialog from "@/Components/CenteredDialog";
import JetButton from "@/Jetstream/Button";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import {ref} from "vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    guidelines: Array
});

const copyDialog = ref(false);

const copyForm = Inertia.form({
    team: null
});

function submitCopy() {
    copyForm.post(route('guidelines.copy'), {
        preserveScroll: true,
        onSuccess: () => copyDialog.value = false
    })
}

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
