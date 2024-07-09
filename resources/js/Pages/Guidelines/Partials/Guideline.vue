<template>
    <div>
        <div class="flex justify-between">
            <h2 class="text-gray-700 font-bold text-2xl mr-2 flex-1">{{ score }}</h2>
            <button class="text-gray-400 hover:cursor-pointer" @click="editing = true">
                <Pencil/>
            </button>
        </div>
        <div class="ml-10" v-if="!editing">
            <div class="mb-4">
                <div class="text-gray-900 font-bold">Description:</div>
                <div class="bg-gray-50 overflow-hidden rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <p class="pl-4" v-if="guideline.description">{{ guideline.description }}</p>
                        <p class="text-gray-400" v-else>No Description</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 overflow-hidden rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row justify-between pr-8">
                        <div>
                            <h3 class="text-gray-900 underline font-bold mb-2 mr-2">Bullet Items</h3>
                            <ul>
                                <li v-if="guideline.bullets?.length === 0" class="text-gray-400">No Bullet Items</li>
                                <li v-for="bullet in guideline.bullets">- {{ bullet.body }}</li>
                            </ul>
                        </div>
                        <div class="min-w-max">
                            <h3 class="text-gray-900 underline font-bold mb-2">Reference Tickets</h3>
                            <ul>
                                <li v-if="guideline.tickets?.length === 0" class="text-gray-400">No Tickets</li>
                                <li v-for="ticket in guideline.tickets">- {{ ticket.ticket_number }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ml-10" v-if="editing">
            <form @submit.prevent="updateGuideline">
                <div class="bg-white overflow-hidden shadow-lg rounded-lg border">
                    <div class="px-4 py-5 sm:p-6">
                        <!-- Content goes here -->
                        <div class="mb-2">
                            <jet-label for="description" value="Description"/>
                            <jet-input id="description" type="text" class="mt-1 block w-full" v-model="form.description"
                                       autocomplete="description"/>
                            <jet-input-error :message="form.errors.description" class="mt-2"/>
                        </div>

                        <divider message="Bullet Points"/>

                        <div class="my-2">
                            <ul>
                                <li v-for="(bullet, index) in form.bullets">
                                    <div class="flex">
                                        <jet-input :id="'bullet' + bullet.id" type="text" class="flex-1 mt-1 block" v-model="bullet.body"
                                                   autocomplete="body"/>
                                        <button @click="removeBullet(index)" type="button" class="text-red-600 ml-2"><TrashIcon class="h-6 w-6"/></button>
                                    </div>
                                    <p class="text-sm text-red-600" v-if="form.errors[`bullets.${index}.body`]">{{form.errors[`bullets.${index}.body`]}}</p>
                                </li>
                                <li>
                                    <jet-secondary-button @click="addBullet" type="button" class="mt-2"><PlusCircleIcon class="w-6 h-6 mr-3" /> Add Bullet </jet-secondary-button>
                                </li>
                            </ul>
                        </div>

                        <divider message="Reference Tickets"/>

                        <div class="my-2">
                            <ul>
                                <li v-for="(ticket, index) in form.tickets">
                                    <div class="flex">
                                        <jet-input :id="'ticket' + ticket.id" type="text" class="flex-1 mt-1 block"
                                                   v-model="ticket.ticket_number" autocomplete="body"/>
                                        <button @click="removeTicket(index)" type="button" class="text-red-600 ml-2"><TrashIcon class="h-6 w-6"/></button>
                                    </div>
                                    <p class="text-sm text-red-600" v-if="form.errors[`tickets.${index}.ticket_number`]">{{form.errors[`tickets.${index}.ticket_number`]}}</p>
                                </li>
                                <li>
                                    <jet-secondary-button @click="addTicket" type="button" class="mt-2"><PlusCircleIcon class="w-6 h-6 mr-3" /> Add Ticket </jet-secondary-button>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 text-right">
                        <!-- Footer -->
                        <jet-secondary-button type="button" @click="editing = false">Cancel</jet-secondary-button>
                        <jet-button type="submit" class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save
                        </jet-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import TrashIcon from "@heroicons/vue/outline/TrashIcon";
import PlusCircleIcon from "@heroicons/vue/outline/PlusCircleIcon";
import Pencil from "@/Components/icons/pencil.vue";
import JetButton from '@/Jetstream/Button.vue';
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue';
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import {Inertia} from "@inertiajs/inertia";
import {ref} from "vue";
import Divider from "@/Components/Divider.vue";

const props = defineProps({
    guideline: Object,
    score: Number
})

let editing = ref(false);

const form = Inertia.form({
    score: props.score,
    description: props.guideline.description,
    bullets: props.guideline.bullets,
    tickets: props.guideline.tickets
});

function updateGuideline() {
    if (props.guideline.id) {
        form.put(route('guidelines.update', props.guideline.id), {
            preserveScroll: true,
            onSuccess: () => editing.value = false
        });
    } else {
        form.post(route('guidelines.create'), {
            preserveScroll: true,
            onSuccess: () => editing.value = false
        });
    }
}

function addBullet() {
    form.bullets.push({body: ''});
}

function removeBullet(index) {
    form.bullets.splice(index, 1);
}

function addTicket() {
    form.tickets.push({ticket_number: ''});
}

function removeTicket(index) {
    form.tickets.splice(index, 1);
}
</script>

<style scoped>

</style>
