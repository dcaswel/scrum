<template>
    <app-layout title="Estimation">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Estimation
                </h2>
                <div class="flex">
                    <HeaderButtonLink target="_blank" :href="route('runner')">Runner</HeaderButtonLink>
                    <HeaderButton @click="showGuidelines = true">Guidelines</HeaderButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div id="slots" class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-2 flex flex-wrap">
                <div class="mr-3 mt-11">
                    <div
                        class="rounded-t-xl border-t border-l border-r border-dashed border-gray-500 w-40 h-40 relative p-3">
                        <div class="-top-3 left-6 bg-gray-100 absolute px-2">
                            {{ firstName(me.name) }}
                        </div>
                        <div v-if="me.points !== null"
                             class="bg-white rounded-xl drop-shadow-lg w-full h-40 flex justify-center items-center">
                            <div class="font-bold text-6xl">
                                {{ me.points }}
                            </div>
                        </div>
                    </div>
                </div>
                <ConfettiExplosion v-if="explode" :force="1" :particleCount="300" :colors="['#F00', '#000', '#FFF']"/>
                <div v-for="user in users" class="mr-3 mt-11">
                    <div
                        class="rounded-t-xl border-t border-l border-r border-dashed border-gray-500 w-40 h-40 relative p-3">
                        <div class="-top-3 left-6 bg-gray-100 absolute px-2">
                            {{ firstName(user.name) }}
                        </div>
                        <div v-if="user.points !== null"
                             v-bind:key="user.points"
                             class="bg-white rounded-xl drop-shadow-lg w-full h-40 flex justify-center items-center">
                            <img v-if="!revealed"
                                 src="https://staticassets.goreact.com/logo-goreact-dash-2020.svg"
                                 alt="GoReact" id="logo" data-height-percentage="65" data-actual-width="300"
                                 data-actual-height="98.0625" class="px-2">
                            <div v-if="revealed" class="font-bold text-6xl">
                                {{ user.points }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="cards" class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-2 flex flex-wrap mt-20">
                <div v-for="card in cards" class="flex-initial mt-8 mr-3">
                    <div
                        @click="chooseCard(card)"
                        class="bg-white rounded-xl drop-shadow-lg hover:border border-gray-400 w-32 h-40 flex justify-center items-center"
                        :class="card.used?'bg-gray-400':'hover:cursor-pointer'"
                    >
                        <div class="font-bold text-6xl">
                            {{ card.points }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <guidelines :open="showGuidelines" @closed="showGuidelines = false" :guidelines="guidelines"></guidelines>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Guidelines from "@/Components/Guidelines";
import HeaderButton from "@/Components/HeaderButton";
import HeaderButtonLink from "@/Components/HeaderButtonLink";
import ConfettiExplosion from 'vue-confetti-explosion';

export default defineComponent({
    components: {
        HeaderButtonLink,
        Guidelines,
        AppLayout,
        HeaderButton,
        ConfettiExplosion
    },
    props: {
        team_id: Number,
        me: Object,
        guidelines: Array
    },
    data() {
        return {
            'explode': false,
            'revealed': false,
            'showGuidelines': false,
            'users': [],
            'cards': [
                {
                    points: 0.5,
                    used: false
                },
                {
                    points: 1,
                    used: false
                },
                {
                    points: 2,
                    used: false
                },
                {
                    points: 3,
                    used: false
                },
                {
                    points: 5,
                    used: false
                },
                {
                    points: 8,
                    used: false
                },
                {
                    points: 13,
                    used: false
                },
                {
                    points: 21,
                    used: false
                },
                {
                    points: '?',
                    used: false
                }
            ]
        }
    },
    mounted() {
        Echo.join('team.' + this.team_id)
            .here(users => {
                this.users = users.filter(user => user.id !== this.me.id);
            })
            .joining(user => {
                this.users.push(user);
            })
            .leaving(user => {
                this.users.splice(this.users.indexOf(user), 1);
            })
            .listen('CardChosen', (e) => {
                this.users.forEach(user => {
                    if (user.id === e.user.id) {
                        user.points = e.points;
                    }
                })
            })
            .listen('ResetCards', () => {
                this.clearPoints();
                this.explode = false;
            })
            .listenForWhisper('reveal', () => {
                this.revealed = true;
                const uniqueScores = [...new Set([...this.users.map((user) => user.points), ...[this.me.points]])];
                if (uniqueScores.length === 1) {
                    this.explode = true;
                }
            });
    },
    unmounted() {
        Echo.leave('team.' + this.team_id);
    },
    methods: {
        chooseCard(card) {
            this.me.points = card.points;
            card.used = true;
            this.cards.forEach((curCard) => {
                if (curCard.points !== card.points) {
                    curCard.used = false;
                }
            });
            axios.post('/choose', {points: card.points});
        },
        clearPoints() {
            this.users.forEach(user => user.points = null);
            this.me.points = null;
            this.revealed = false;
            this.cards.forEach(card => card.used = false);
        },
        firstName(name) {
            const array = name.split(' ');
            if (array.length > 1) {
                name = array.slice(0, -1).pop();
            }
            return name;
        }
    }
})
</script>

