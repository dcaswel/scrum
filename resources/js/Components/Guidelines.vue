<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="fixed inset-0 overflow-hidden" @close="$emit('closed')">
            <div class="absolute inset-0 overflow-hidden">
                <DialogOverlay class="absolute inset-0" />

                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex sm:pl-16">
                    <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
                        <div class="w-screen max-w-2xl">
                            <div class="h-full flex flex-col py-6 bg-white shadow-xl overflow-y-scroll">
                                <div class="px-4 sm:px-6">
                                    <div class="flex items-start justify-between">
                                        <DialogTitle class="text-lg font-medium text-gray-900"> Estimation Guidelines </DialogTitle>
                                        <div class="ml-3 h-7 flex items-center">
                                            <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" @click="$emit('closed')">
                                                <span class="sr-only">Close panel</span>
                                                <XIcon class="h-6 w-6" aria-hidden="true" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6 relative flex-1 px-4 sm:px-6">
                                    <ul role="list" class="divide-y divide-gray-200">
                                        <li v-for="guideline in guidelines" class="relative bg-white py-5 px-4 hover:bg-gray-50">
                                            <div class="flex justify-between space-x-3">
                                                <div class="min-w-0 flex-1">
                                                    <div class="block focus:outline-none">
                                                        <p class="text-sm font-medium text-gray-900"><span class="font-bold text-lg">{{ guideline.score }}</span> - {{ guideline.description }}</p>
                                                        <p v-for="bullet in guideline.bullets" class="text-sm text-gray-500">- {{ bullet.body }}</p>
                                                        <p v-if="guideline.tickets.length > 0" class="text-sm text-gray-500">- Reference Tickets:
                                                            <span v-for="(ticket, index) in guideline.tickets">
                                                                <a :href="getUrl(ticket)" target="_blank" class="text-blue-700">{{ticket.ticket_number}}</a>
                                                                <span v-if="index + 1 < guideline.tickets.length">, </span>
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { defineProps } from 'vue'
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XIcon } from '@heroicons/vue/outline'

defineProps({
    open: Boolean,
    guidelines: Array
});

const getUrl = (ticket) => {
    if (ticket.url) {
        return ticket.url;
    }

    return 'https://goreact.atlassian.net/browse/' + ticket.ticket_number;
}
</script>
