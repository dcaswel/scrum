<template>
    <div>
        <div class="mb-4">
            <div class="text-gray-900 font-bold">Description: </div>
            <p class="pl-4">{{guideline.description}}</p>
        </div>
        <div class="flex flex-col sm:flex-row justify-between pr-8">
            <div>
                <h3 class="text-gray-900 underline mb-2 mr-2">Bullet Items</h3>
                <ul>
                    <li v-if="guideline.bullets.length === 0" class="text-gray-400">No Bullet Items</li>
                    <li v-for="bullet in guideline.bullets">- {{bullet.body}}</li>
                </ul>
            </div>
            <div class="min-w-max">
                <h3 class="text-gray-900 underline mb-2">Reference Tickets</h3>
                <ul>
                    <li v-if="guideline.tickets.length === 0" class="text-gray-400">No Tickets</li>
                    <li v-for="ticket in guideline.tickets">- {{ticket.ticket_number}}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    guideline: Object
})
</script>

<style scoped>

</style>
