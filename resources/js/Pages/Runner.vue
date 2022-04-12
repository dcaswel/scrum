<template>
    <app-layout title="Dashboard">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Estimation
                </h2>
                <div class="flex">
                    <button
                        @click="reset"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-full
                            text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2
                            focus:ring-offset-2 focus:ring-indigo-500 mr-2"
                    >Reset
                    </button>
                    <button
                        @click="reveal"
                        type="button"
                        class="inline-flex items-center px-5 py-2 border border-transparent text-base font-medium rounded-full
                                shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2
                                focus:ring-indigo-500"
                    >Reveal
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-2 h-10 font-bold mb-5 flex">
                <div class="items-center inline-flex w-32">Result: {{ this.result }}</div>
                <button
                    @click="calculateResult"
                    v-if="result !== null"
                    type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-full
                            text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2
                            focus:ring-offset-2 focus:ring-indigo-500 ml-5"
                >Recalculate</button>
            </div>
            <div id="slots" class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-2 flex flex-wrap">
                <div v-for="user in users" class="mr-3 mt-4">
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
        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

export default defineComponent({
    components: {
        AppLayout,
    },
    props: {
        team_id: Number
    },
    data() {
        return {
            'revealed': false,
            'users': [],
            'result': null,
            'total': 0
        }
    },
    created() {
        Echo.join('team.' + this.team_id)
            .here(users => {
                this.users = users;
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
            .listen('ResetCards', e => {
                this.clearPoints();
            })
            .listenForWhisper('reveal', e => this.revealed = true);
    },
    methods: {
        reveal() {
            this.revealed = true;
            this.calculateResult();
            Echo.join('team.' + this.team_id).whisper('reveal', {});
        },
        reset() {
            this.clearPoints();
            axios.delete('/reset/' + this.team_id);
        },
        clearPoints() {
            this.users.forEach(user => user.points = null);
            this.revealed = false;
            this.result = null;
        },
        firstName(name) {
            const array = name.split(' ');
            if (array.length > 1) {
                name = array.slice(0, -1).pop();
            }
            return name;
        },
        calculateResult() {
            this.total = 0;
            const values = this.translateValues();
            if (values) {
                let max = values[0];
                let min = values[0];
                values.forEach(value => {
                    if (value > max) {
                        max = value;
                    }
                    if (value < min) {
                        min = value;
                    }
                });
                if (Math.abs(min - max) <= 2) {
                    this.result = (this.total / values.length).toFixed(1);
                } else {
                    this.result = 'Revote';
                }
            }
        },
        translateValues() {
            const arrMap = {
                0.5: 1,
                1: 2,
                2: 3,
                3: 4,
                5: 5,
                8: 6,
                13: 7,
                21: 8
            };
            let questionFound = false;
            const arr = this.users.map(user => {
                if (user.points === '?') {
                    questionFound = true;
                    return false;
                }
                const points = parseFloat(user.points);
                this.total += points;
                return arrMap[points];
            });
            if (questionFound) {
                return [];
            }
            return arr;
        }
    }
})
</script>

