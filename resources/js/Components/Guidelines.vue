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
                                        <li v-for="score in scores" class="relative bg-white py-5 px-4 hover:bg-gray-50 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600">
                                            <div class="flex justify-between space-x-3">
                                                <div class="min-w-0 flex-1">
                                                    <a href="#" class="block focus:outline-none">
                                                        <span class="absolute inset-0" aria-hidden="true" />
                                                        <p class="text-sm font-medium text-gray-900"><span class="font-bold text-lg">{{ score.number }}</span> - {{ score.primary }}</p>
                                                        <p v-for="secondary in score.secondary" class="text-sm text-gray-500 truncate">- {{ secondary }}</p>
                                                    </a>
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

<script>
import { ref } from 'vue'
import {defineComponent} from 'vue'
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XIcon } from '@heroicons/vue/outline'

export default defineComponent({
    components: {
        Dialog,
        DialogOverlay,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        XIcon,
    },
    props: {
        open: ref(false)
    },
    data() {
        return {
            scores: [
                {
                    number: '0.5',
                    primary: 'Text changes, feature flag change, usually no testing required',
                    secondary: []
                },
                {
                    number: '1',
                    primary: 'Some testing required but not extensive, minor migration script for example',
                    secondary: [
                        'Reference ticket: CAE-72'
                    ]
                },
                {
                    number: '2',
                    primary: 'Smaller front end change, or smaller back end change, not both sides',
                    secondary: [
                        'Migration that might be a little more extensive (regular complexity)',
                        'Reference ticket: CAE-75'
                    ]
                },
                {
                    number: '3',
                    primary: '"low 5 :)" - could have changes on both fe & be but less complex or fairly simple on both sides',
                    secondary: [
                        'Reference Tickets: CAE-169'
                    ]
                },
                {
                    number: '5',
                    primary: 'front end + backend changes w/ tests on boths sides',
                    secondary: [
                        'Working with legacy code / new tech, some unknown',
                        'Backend/frontend only but more complex / extensive testing',
                        'Reference tickets: CAE-5'
                    ]
                },
                {
                    number: '8',
                    primary: '"high 5" - some unknowns, higher complexity',
                    secondary: [
                        'Cypress tests, rebaselining schema, new seed data',
                        'pipeline changes or CI/CD changes',
                        'Reference ticket: DEV-12920'
                    ]
                },
                {
                    number: '13',
                    primary: 'Largest recommendable size for a sprint, consider breaking down but might be acceptable based on the story',
                    secondary: [
                        'Extensive front-end and back-end changes, extensive testing, multiple components, hybrid implications',
                        'Unknowns, new technologies',
                        'Reference tickets: CAE-87, CAE-84'
                    ]
                },
                {
                    number: '21',
                    primary: 'Needs to be broken up, can\'t be broken up but will take more than a sprint to complete',
                    secondary: [
                        'php memory leak on tests',
                        'go-modules test timeouts on CI'
                    ]
                },
            ]
        }
    }
});
</script>
